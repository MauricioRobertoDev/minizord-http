<?php

namespace Minizord\Http;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

final class ServerRequest extends AbstractServerRequest implements ServerRequestInterface
{
    /**
     * Representação de uma solicitação HTTP recebida do lado do servidor.
     */
    public function __construct(
        array $serverParams = [],
        array $uploadedFiles = [],
        array $cookieParams = [],
        array $queryParams = [],
        UriInterface|string $uri = '',
        array $headers = [],
        string $method =  'GET',
        mixed $body = null,
        string $version = '1.1',
        array $attributes = [],
    ) {
        $this->init($method, $uri, $headers, $body, $version, $serverParams, $uploadedFiles, $cookieParams, $queryParams, $attributes);
    }
}
