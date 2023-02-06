<?php

namespace Minizord\Http\Factory;

use Minizord\Http\Request;
use Psr\Http\Message\RequestFactoryInterface;

final class RequestFactory implements RequestFactoryInterface
{
    /**
     * Cria uma Request.
     *
     * @param UriInterface|string $uri
     */
    public function createRequest(string $method, $uri): Request
    {
        return new Request(method: $method, uri: $uri);
    }
}
