<?php

namespace Minizord\Http;

use Minizord\Http\Contract\RequestInterface;
use Psr\Http\Message\StreamInterface as PsrStreamInterface;
use Psr\Http\Message\UriInterface as PsrUriInterface;

class Request implements RequestInterface
{
    use RequestTrait;
    use MessageTrait;

    /**
     * Representação de uma solicitação de saída do lado do cliente.
     *
     * @param string                             $method
     * @param PsrUriInterface|string             $uri
     * @param array                              $headers
     * @param PsrStreamInterface|resource|string $body
     * @param string                             $version
     */
    public function __construct(
        string $method = 'GET',
        PsrUriInterface|string $uri = '',
        array $headers = [],
        mixed $body = null,
        string $version = '1.1'
    ) {
        $this->validateMethod($method);
        $this->validateProtocolVersion($version);

        if (!($uri instanceof PsrUriInterface)) {
            $uri = new Uri($uri);
        }

        $this->method   = $method;
        $this->uri      = $uri;
        $this->protocol = $version;

        $this->setHeaders($headers);

        if (!$this->hasHeader('Host')) {
            $this->setHostFromUri();
        }

        if ($body) {
            $this->body = new Stream($body);
        }
    }
}
