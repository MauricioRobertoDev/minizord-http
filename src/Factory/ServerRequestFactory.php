<?php

namespace Minizord\Http\Factory;

use Minizord\Http\ServerRequest;
use Psr\Http\Message\ServerRequestFactoryInterface;

final class ServerRequestFactory implements ServerRequestFactoryInterface
{
    /**
     * Crima uma ServerRequest.
     *
     * @param UriInterface|string   $uri
     * @param array<string, string> $serverParams
     */
    public function createServerRequest(string $method, $uri, array $serverParams = []): ServerRequest
    {
        return new ServerRequest(method: $method, uri: $uri, serverParams: $serverParams);
    }
}
