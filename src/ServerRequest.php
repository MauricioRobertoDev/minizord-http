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
        string $method =  'GET',
        string $version = '1.1',
        UriInterface|string $uri = '',
        array $headers = [],
        mixed $body = null,
        object|array|null $parsedBody = null,
        array $attributes = [],
    ) {
        $this->init(
            method: $method,
            uri: $uri,
            headers: $headers,
            version: $version,
            body: $body,
            parsedBody: $parsedBody,
            serverParams: $serverParams,
            uploadedFiles: $uploadedFiles,
            cookieParams: $cookieParams,
            queryParams: $queryParams,
            attributes: $attributes
        );
    }
}
