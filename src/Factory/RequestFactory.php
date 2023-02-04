<?php

namespace Minizord\Http\Factory;

use Minizord\Http\Request;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;

class RequestFactory implements RequestFactoryInterface
{
    /**
     * Cria uma Request.
     *
     * @param  string              $method
     * @param  UriInterface|string $uri
     * @return RequestInterface
     */
    public function createRequest(string $method, $uri) : RequestInterface
    {
        return new Request(method: $method, uri: $uri);
    }
}
