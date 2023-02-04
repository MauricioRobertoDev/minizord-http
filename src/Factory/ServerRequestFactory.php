<?php

namespace Minizord\Http\Factory;

use Minizord\Http\ServerRequest;
use Psr\Http\Message\ServerRequestFactoryInterface;

class ServerRequestFactory implements ServerRequestFactoryInterface
{
    /**
     * Crima uma ServerRequest.
     *
     * @param  string              $method
     * @param  UriInterface|string $uri
     * @param  array               $serverParams
     * @return ServerRequest
     */
    public function createServerRequest(string $method, $uri, array $serverParams = []) : ServerRequest
    {
        return new ServerRequest(method: $method, uri: $uri, serverParams: $serverParams);
    }
}
