<?php

namespace Minizord\Http;

use InvalidArgumentException;
use Psr\Http\Message\StreamInterface as PsrStreamInterface;

trait MessageTrait
{
    protected string $protocol   = '1.1';
    protected array $headers     = [];
    protected array $headerNames = [];
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
        return $this->getOriginalHeaderName($name) ?? false;
    }

    public function getHeader($name) : array
    {
        if ($this->hasHeader($name)) {
            return $this->headers[$this->getOriginalHeaderName($name)];
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

        $clone                                 = clone $this;
        $clone->headerNames[strtolower($name)] = $name;
        $clone->headers[$name]                 = $value;

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

        $name   = $this->filterHeaderName($name);
        $values = $this->filterHeaderValue($value);

        $clone                                 = clone $this;
        $clone->headerNames[strtolower($name)] = $name;
        $clone->headers[$name]                 = [...$this->getHeader($name), ...$values];

        return $clone;
    }

    public function withoutHeader($name) : self
    {
        $clone = clone $this;
        unset($clone->headers[$this->getOriginalHeaderName(($name))]);

        return $clone;
    }

    public function getBody() : PsrStreamInterface
    {
        if (!isset($this->stream)) {
            $this->stream = new Stream('');
        }

        return $this->stream;
    }

    public function withBody(PsrStreamInterface $body) : self
    {
        $clone         = clone $this;
        $clone->stream = $body;

        return $clone;
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
        $name = trim((string) $name, " \t");

        if (!preg_match("/^[a-zA-Z0-9'`#$%&*+\.^_|~!\-]+$/", $name)) {
            throw new InvalidArgumentException('Nome do header deve estar de acordo com a RFC 7230');
        }

        return $name;
    }

    private function filterHeaderValue(string|array $value) : array
    {
        if (is_string($value)) {
            $value = [$value];
        }

        foreach ($value as $content) {
            $content = trim((string) $content, " \t");
            if (!preg_match("/^[\x{09}\x{20}\x{21}\x{23}-\x{7E}]+$/u", $content)) {
                throw new InvalidArgumentException('O conteúdo do header deve estar de acordo com a RFC 7230');
            }
        }

        return $value;
    }

    private function getOriginalHeaderName(string $name) : string | null
    {
        return $this->headerNames[strtolower($name)] ?? null;
    }
}
