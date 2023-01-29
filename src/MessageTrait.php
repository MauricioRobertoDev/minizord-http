<?php

namespace Minizord\Http;

use Psr\Http\Message\StreamInterface as PsrStreamInterface;

trait MessageTrait
{
    protected string $protocol = '1.1';
    protected array $headers   = [];
    protected PsrStreamInterface $body;

    public function getProtocolVersion() : string
    {
        return $this->protocol;
    }

    public function withProtocolVersion($version) : self
    {
        $clone           = clone $this;
        $clone->protocol = $version;

        return $clone;
    }

    public function getHeaders()
    {
    }

    public function hasHeader($name)
    {
    }

    public function getHeader($name)
    {
    }

    public function getHeaderLine($name)
    {
    }

    public function withHeader($name, $value)
    {
    }

    public function withAddedHeader($name, $value)
    {
    }

    public function withoutHeader($name)
    {
    }

    public function getBody()
    {
    }

    public function withBody(PsrStreamInterface $body)
    {
    }
}
