<?php

namespace Minizord\Http;

use InvalidArgumentException;
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

    public function getHeaders() : array
    {
        return $this->headers;
    }

    public function hasHeader($name) : bool
    {
        return isset($this->headers[strtolower($name)]);
    }

    public function getHeader($name) : array
    {
        if ($this->hasHeader($name)) {
            return $this->headers[$name];
        }

        return [];
    }

    public function getHeaderLine($name) : string
    {
        return join(', ', $this->getHeader($name));
    }

    public function withHeader($name, $value) : self
    {
        if (!is_string($name)) {
            throw new InvalidArgumentException('Argumento 1 deve ser uma string');
        }

        if (!is_string($value) && !is_array($value)) {
            throw new InvalidArgumentException('Argumento 2 deve ser uma string ou um array de strings');
        }

        $name = strtolower($name);
        if (is_string($value)) {
            $value = [$value];
        }

        $clone                 = clone $this;
        $clone->headers[$name] = $value;

        return $clone;
    }

    public function withAddedHeader($name, $value) : self
    {
        if (!is_string($name)) {
            throw new InvalidArgumentException('Argumento 1 deve ser uma string');
        }

        if (!is_string($value) && !is_array($value)) {
            throw new InvalidArgumentException('Argumento 2 deve ser uma string ou um array de strings');
        }

        $name = strtolower($name);
        if (is_string($value)) {
            $value = [$value];
        }

        $clone                 = clone $this;
        $clone->headers[$name] = array_merge($this->getHeader($name), $value);

        return $clone;
    }

    public function withoutHeader($name) : self
    {
        $clone = clone $this;
        unset($clone->headers[$name]);

        return $clone;
    }

    public function getBody()
    {
    }

    public function withBody(PsrStreamInterface $body)
    {
    }
}
