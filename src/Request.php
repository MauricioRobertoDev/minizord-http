<?php

namespace Minizord\Http;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

final class Request extends AbstractRequest implements RequestInterface
{
    /**
     * Representação de uma solicitação de saída do lado do cliente.
     *
     * @param StreamInterface|resource|string $body
     */
    public function __construct(
        string $method = 'GET',
        UriInterface|string $uri = '',
        array $headers = [],
        mixed $body = null,
        string $version = '1.1'
    ) {
        $this->init(
            method: $method,
            uri: $uri,
            headers: $headers,
            version: $version,
            body: $body,
        );
    }
}
