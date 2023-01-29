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
        $this->validateProtocolVersion($version);
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

        $name  = $this->filterHeaderName($name);
        $value = $this->filterHeaderValue($value);

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
        unset($clone->headers[strtolower(($name))]);

        return $clone;
    }

    public function getBody()
    {
    }

    public function withBody(PsrStreamInterface $body)
    {
    }

    // private
    private function validateProtocolVersion($version) : void
    {
        if (empty($version)) {
            throw new InvalidArgumentException('Protocol version não pode ser vazio');
        }

        if (!preg_match('#^(1\.[01])$#', $version)) {
            throw new InvalidArgumentException('Protocol version não suportado');
        }
    }

    private function filterHeaderName(string $name) : string
    {
        $name = trim($name);

        if (!preg_match("/^[a-zA-Z0-9'`#$%&*+\.^_|~!\-]+$/", $name)) {
            throw new InvalidArgumentException('Nome do header deve estar de acordo com a RFC 7230', 1);
        }

        return $name;
    }

    private function filterHeaderValue(string|array $value) : array
    {
        if (is_string($value)) {
            $value = [$value];
        }

        foreach ($value as $content) {
            $content = trim($content);
            if (!preg_match("/^[\x{09}\x{20}\x{21}\x{23}-\x{7E}]+$/u", $content)) {
                throw new InvalidArgumentException('O conteúdo do header deve estar de acordo com a RFC 7230', 1);
            }
        }

        return $value;
    }
}
