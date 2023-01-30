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
     * @param string                 $method
     * @param PsrUriInterface|string $uri
     * @param array                  $headers
     * @param PsrStream|string       $body
     * @param string                 $version
     */
    public function __construct(string $method, $uri, array $headers = [], $body = null, string $version = '1.1')
    {
        if (!($uri instanceof PsrPsrUriInterface)) {
            $uri = new Uri($uri);
        }

        $this->method = $method;
        $this->uri    = $uri;
        $this->setHeaders($headers);
        $this->validateProtocolVersion($version);
        $this->protocol = $version;

        if (!$this->hasHeader('Host')) {
            $this->setHostFromUri();
        }

        if ($body) {
            $this->body = new Stream($body);
        }
    }
}
