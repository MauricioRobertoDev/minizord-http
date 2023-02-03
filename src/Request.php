<?php

namespace Minizord\Http;

use Minizord\Http\Contract\RequestInterface;
use Psr\Http\Message\UriInterface as PsrPsrUriInterface;

class Request implements RequestInterface
{
    use RequestTrait;
    use MessageTrait;

    /**
     * Representação de uma solicitação de saída do lado do cliente.
     *
     * @param string                    $method
     * @param PsrUriInterface|string    $uri
     * @param array                     $headers
     * @param PsrStreamInterface|string $body
     * @param string                    $version
     */
    public function __construct(string $method, $uri, array $headers = [], $body = null, string $version = '1.1')
    {
        $this->validateMethod($method);
        $this->validateProtocolVersion($version);

        if (!($uri instanceof PsrPsrUriInterface)) {
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
