<?php

namespace Minizord\Http;

use InvalidArgumentException;
use Psr\Http\Message\StreamInterface as PsrStreamInterface;

trait MessageTrait
{
    /**
     * Versão do protocolo.
     *
     * @var string
     */
    protected string $protocol   = '1.1';

    /**
     * Headers e seus valores.
     *
     * @var array
     */
    protected array $headers     = [];

    /**
     * Nome do header normalizado e o seu original.
     *
     * @var array
     */
    protected array $headerNames = [];

    /**
     * O corpo da requisição uma stream.
     *
     * @var PsrStreamInterface
     */
    protected ?PsrStreamInterface $body = null;

    /**
     * Retorna o a versão do protoloco ex. 1.1 1.0.
     *
     * @return string
     */
    public function getProtocolVersion() : string
    {
        return $this->protocol;
    }

    /**
     * Retorna uma nova instância com a versão do protocolo passado.
     *
     * @param  string $version
     * @return self
     */
    public function withProtocolVersion($version) : self
    {
        $this->validateProtocolVersion($version);
        $clone           = clone $this;
        $clone->protocol = $version;

        return $clone;
    }

    /**
     * Retornar os headers.
     *
     * @return array
     */
    public function getHeaders() : array
    {
        return $this->headers;
    }

    /**
     * Checa se um header existe ou não.
     *
     * @param  string $name
     * @return bool
     */
    public function hasHeader($name) : bool
    {
        return $this->getOriginalHeaderName($name) ?? false;
    }

    /**
     * Pega os valores de determinado header.
     *
     * @param  string $name
     * @return array
     */
    public function getHeader($name) : array
    {
        if ($this->hasHeader($name)) {
            return $this->headers[$this->getOriginalHeaderName($name)];
        }

        return [];
    }

    /**
     * Retorna os valores de determinado header em uma string.
     *
     * @param  string $name
     * @return string
     */
    public function getHeaderLine($name) : string
    {
        return join(', ', $this->getHeader($name));
    }

    /**
     * Retorna uma nova instância com o header passado.
     *
     * @param  string          $name
     * @param  string|string[] $value
     * @return self
     */
    public function withHeader($name, $value) : self
    {
        if (!is_string($name)) {
            throw new InvalidArgumentException('Argumento 1 deve ser uma string');
        }

        if (!is_string($value) && !is_array($value)) {
            throw new InvalidArgumentException('Argumento 2 deve ser uma string ou um array de strings');
        }

        $clone                                 = clone $this;
        $clone->setHeaders([$name => $value]);

        return $clone;
    }

    /**
     * Retorna uma nova instância com os valores passados adicionado ao header existente.
     *
     * @param  string          $name
     * @param  string|string[] $value
     * @return self
     */
    public function withAddedHeader($name, $value) : self
    {
        if (!is_string($name)) {
            throw new InvalidArgumentException('Argumento 1 deve ser uma string');
        }

        if (!is_string($value) && !is_array($value)) {
            throw new InvalidArgumentException('Argumento 2 deve ser uma string ou um array de strings');
        }

        $clone   = clone $this;
        $clone->setHeaders([$name => $value], true);

        return $clone;
    }

    /**
     * Retorna uma nova instância sem o header passado.
     *
     * @param  string $name
     * @return self
     */
    public function withoutHeader($name) : self
    {
        $clone = clone $this;
        unset($clone->headers[$this->getOriginalHeaderName($name)], $clone->headerNames[strtolower($name)]);

        return $clone;
    }

    /**
     * Retorna a stream atual, ou uma nova caso não exista.
     *
     * @return PsrStreamInterface
     */
    public function getBody() : PsrStreamInterface
    {
        if (!isset($this->body)) {
            $this->body = new Stream('');
        }

        return $this->body;
    }

    /**
     * Retorna uma nova instância com a stream passada.
     *
     * @param  PsrStreamInterface $body
     * @return self
     */
    public function withBody(PsrStreamInterface $body) : self
    {
        $clone         = clone $this;
        $clone->body   = $body;

        return $clone;
    }

    /**
     * Retorna o header tem todos os valores passados.
     *
     * @param  string       $name
     * @param  string|array $values
     * @return bool
     */
    public function inHeader(string $name, string|array $values) : bool
    {
        if (!$this->hasHeader($name)) {
            return false;
        }

        if (is_string($values)) {
            $values = [$values];
        }

        $headerValues = $this->getHeader($name);

        foreach ($values as $value) {
            if (!in_array($value, $headerValues)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Retorna de o header algum dos valores passados.
     *
     * @param  string $name
     * @param  array  $values
     * @return bool
     */
    public function inHeaderAny(string $name, array $values) : bool
    {
        if (!$this->hasHeader($name)) {
            return false;
        }

        $headerValues = $this->getHeader($name);

        foreach ($values as $value) {
            if (in_array($value, $headerValues)) {
                return true;
            }
        }

        return false;
    }

    // private

    /**
     * Valida a versão do protocolo.
     *
     * @param  string $version
     * @return void
     */
    private function validateProtocolVersion($version) : void
    {
        if (empty($version)) {
            throw new InvalidArgumentException('Protocol version não pode ser vazio');
        }

        if (!preg_match('#^(1\.[01])$#', $version)) {
            throw new InvalidArgumentException('Protocol version não suportado');
        }
    }

    /**
     * Valida o nome do header.
     *
     * @param  string $name
     * @return string
     */
    private function filterHeaderName(string $name) : string
    {
        $name = trim((string) $name, " \t");

        if (!preg_match("/^[a-zA-Z0-9'`#$%&*+\.^_|~!\-]+$/", $name)) {
            throw new InvalidArgumentException('Nome do header deve estar de acordo com a RFC 7230');
        }

        return $name;
    }

    /**
     * Valida o valor do header.
     *
     * @param  string|array $value
     * @return array
     */
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

    /**
     * Retorna o nome original do header.
     *
     * @param  string $name
     * @return void
     */
    private function getOriginalHeaderName(string $name) : string | null
    {
        return $this->headerNames[strtolower($name)] ?? null;
    }

    /**
     * Adiciona vários headers ou apenas os valores caso já exista.
     *
     * @param  array $headers
     * @return void
     */
    private function setHeaders(array $headers, bool $merge = false) : void
    {
        foreach ($headers as $header => $value) {
            $header     = $this->filterHeaderName($header);
            $value      = $this->filterHeaderValue($value);

            if ($this->hasHeader($header) && $merge) {
                $header                 = $this->getOriginalHeaderName($header);
                $this->headers[$header] = [...$this->getHeader($header), ...$value];
            } elseif ($this->hasHeader($header) && $merge === false) {
                $header                 = $this->getOriginalHeaderName($header);
                $this->headers[$header] = [...$value];
            } else {
                $this->headerNames[strtolower($header)] = $header;
                $this->headers[$header]                 = $value;
            }
        }
    }
}
