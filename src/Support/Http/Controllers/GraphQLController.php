<?php

namespace Nuwave\Lighthouse\Support\Http\Controllers;

use GraphQL\Language\Parser;
use GraphQL\Language\Source;
use GraphQL\Server\Helper;
use Illuminate\Contracts\Events\Dispatcher as EventsDispatcher;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Laragraph\Utils\RequestParser;
use Nuwave\Lighthouse\Events\StartRequest;
use Nuwave\Lighthouse\GraphQL;
use Nuwave\Lighthouse\Support\Contracts\CreatesResponse;
use Symfony\Component\HttpFoundation\Response;

class GraphQLController
{
    public function __invoke(
        Request $request,
        GraphQL $graphQL,
        EventsDispatcher $eventsDispatcher,
        RequestParser $requestParser,
        Helper $graphQLHelper,
        CreatesResponse $createsResponse
    ): Response {

        $request = $this->prepRequest($request);

        $eventsDispatcher->dispatch(
            new StartRequest($request)
        );

        $result = $graphQL->executeRequest($request, $requestParser, $graphQLHelper);

        return $createsResponse->createResponse($result);
    }

    /**
     * 兼容 webonyx/graphql-php v0.13.8
     * @param $request
     * @return mixed
     */
    private function prepRequest($request){

        $input = $request->all();
        $input = $this->handleQuery($input);
        $input = $this->handlePagination($input);
        $input = $this->handleArgs($input);

        // Create an Illuminate request from a Symfony instance.
        $request = Request::createFromBase($request);
        $request->initialize(
            $request->query->all(),
            $input,
            $request->attributes->all(),
            $request->cookies->all(),
            $request->files->all(),
            $request->server->all(),
            json_encode($input)
        );
        return $request->replace($input);
    }
    /**
	 * 分页参数兼容
	 * @param $input
	 * @return mixed
	 */
    private function handlePagination($input){
        $source = data_get($input,'query');
        $documentNode = Parser::parse(new Source($source ?? '', 'GraphQL'));
        $arguments = array();
        foreach ($documentNode->definitions as $definition) {
            foreach (data_get($definition,'selectionSet.selections',[]) as $selection){
                // 处理分页嵌套(三层嵌套)
                foreach (data_get($selection,'selectionSet.selections',[]) as $selec){
                    foreach (data_get($selec,'selectionSet.selections',[]) as $selecNode){
                        foreach (data_get($selecNode,'arguments',[]) as $argument){
                            $arguments[] = data_get($argument,'name.value');
                        }
                    }
                }
                // 处理嵌套(两层嵌套)
                foreach (data_get($selection,'selectionSet.selections',[]) as $selec){
                    foreach (data_get($selec,'arguments',[]) as $argument){
                        $arguments[] = data_get($argument,'name.value');
                    }
                }
                // 处理最外层入参
                foreach (data_get($selection,'arguments',[]) as $argument){
                    $arguments[] = data_get($argument,'name.value');
                }
            }
        }
        $containFirstArg = in_array('first',$arguments);

        if(!$containFirstArg){
            return $input;
        }
        $source = str_replace('$first','##flag##',$source);
        $source = str_replace('first:','count:',$source);
        $source = str_replace('##flag##','$first',$source);

        data_set($input,'query',$source);
        return $input;
    }

    /**
     * 参数兼容
     * @param $request
     * @return mixed
     */
    private function handleArgs($input){
        $args = config('lighthouse.convert_args');
        if(!$args){
            return $input;
        }
        foreach ( $args as $name => $method ){
			$contains = Arr::has($input, 'variables.'.$name);
            if($contains){
				$value = data_get($input,'variables.'.$name);
                $newValue = call_user_func($method,$value);
                data_set($input,'variables.'.$name, $newValue);
            }
        }
        return $input;
    }
    /**
     * 查询体兼容(主要处理分页参数的兼容问题)
     * @param String $query
     * @return String|void
     */
    private function handleQuery($input){
        $source = data_get($input,'query');
        $documentNode = Parser::parse(new Source($source ?? '', 'GraphQL'));
        foreach ($documentNode->definitions as $definition) {
            foreach (data_get($definition,'selectionSet.selections',[]) as $selection){
                foreach (data_get($selection,'arguments',[]) as $argument){
                    if(data_get($argument,'name.value') == 'orderBy'){

                        $node = $argument->value;
                        if($node instanceof  \GraphQL\Language\AST\ObjectValueNode){
                            foreach ($node->fields as $field){
                                $value = data_get($field,'name.value');
                                if($value  === 'field'){
                                    $subStr = str_before(
                                        str_after($source,'orderBy'),
                                        '}'
                                    );
                                    $oldStr 	= 'orderBy'.$subStr.'}';
                                    $subStr = strtr($subStr, array(' '=>''));
                                    $newStr = str_replace('field:','column:',$subStr);
                                    $newStr = 'orderBy'.$newStr.'}';
                                    $newQuery = str_replace($oldStr,$newStr,$source);
                                    data_set($input,'query', $newQuery);
                                    return $input;
                                }
                            }
                        }

                        // 排序列表
                        if($node instanceof  \GraphQL\Language\AST\ListValueNode){
                            $values = data_get($argument,'value.values',[]);
                            foreach ($values as $value){
                                foreach ($value->fields as $field){
                                    $nameNodeName = data_get($field,'name.value');
                                    if($nameNodeName  === 'field'){
                                        $subStr = str_before(
                                            str_after($source,'orderBy'),
                                            ']'
                                        );
                                        $oldStr 	= 'orderBy'.$subStr.']';
                                        $subStr = strtr($subStr, array(' '=>''));
                                        $newStr = str_replace('field:','column:',$subStr);
                                        $newStr = 'orderBy'.$newStr.']';
                                        $newQuery = str_replace($oldStr,$newStr,$source);
                                        data_set($input,'query', $newQuery);
                                        return $input;
                                    }
                                }
                            }
                        }
                    }

                    $arguments[] = data_get($argument,'name.value');
                }
            }
        }
        return $input;
    }
}
