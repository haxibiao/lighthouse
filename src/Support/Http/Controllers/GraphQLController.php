<?php

namespace Nuwave\Lighthouse\Support\Http\Controllers;

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
     * 查询体兼容
     * @param String $query
     * @return String|void
     */
    private function handleQuery($input){

        $query = data_get($input,'query');
        if(!str_contains($query,'orderBy: {')){
            return $input;
        }
        $subStr = str_before(
            str_after($query,'orderBy: {'),
            '}'
        );

        $oldStr 	= 'orderBy: {'.$subStr.'}';
        $delimiterList 	= explode(', ',$subStr);
        $arr = array();
        foreach ($delimiterList as $delimiter){
            $keyAngValue =  explode(': ',$delimiter);
            $arr[$keyAngValue[0]] = $keyAngValue[1];
        }
        $newStr = 'orderBy: [{ column: '.$arr['field'].', order: '.$arr['order'].'}]';
        $newQuery = str_replace($oldStr,$newStr,$query);
        data_set($input,'query', $newQuery);
        return $input;
    }
}
