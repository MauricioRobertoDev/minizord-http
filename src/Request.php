<?php

namespace Minizord\Http;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class Request implements RequestInterface
{
    use RequestTrait;
    use MessageTrait;

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
        $this->validateMethod($method);
        $this->validateProtocolVersion($version);

        if (! ($uri instanceof UriInterface)) {
            $uri = new Uri($uri);
        }

        $this->method   = $method;
        $this->uri      = $uri;
        $this->protocol = $version;

        $this->setHeaders($headers);

        if (! $this->hasHeader('Host')) {
            $this->setHostFromUri();
        }

        if ($body) {
            $this->body = new Stream($body);
        }
    }
}
