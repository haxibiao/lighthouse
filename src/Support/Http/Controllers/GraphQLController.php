<?php

namespace Nuwave\Lighthouse\Support\Http\Controllers;

use GraphQL\Server\Helper;
use Illuminate\Contracts\Events\Dispatcher as EventsDispatcher;
use Illuminate\Http\Request;
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
        $args = config('lighthouse.convert_args');
        if(!$args){
            return $request;
        }

        $input          = $request->all();
        foreach ( $args as $name => $method ){
            $value  = data_get($input,'variables.'.$name);
            if($value){
                $newValue = call_user_func($method,$value);
                data_set($input,'variables.'.$name, $newValue);
            }
        }
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
}
