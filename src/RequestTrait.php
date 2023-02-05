<?php

namespace Minizord\Http;

use InvalidArgumentException;
use Psr\Http\Message\UriInterface;

trait RequestTrait
{
    use MessageTrait;

    /**
     * Método http.
     */
    private string $method = 'GET';

    /**
     * Destino da solicitação.
     */
    private string|null $requestTarget = null;

    /**
     * Uri da request.
     */
    private UriInterface $uri;

    /**
     * Retorna o destino da solicitação.
     */
    public function getRequestTarget(): string
    {
        if ($this->requestTarget !== null) {
            return $this->requestTarget;
        }

        $target = $this->uri->getPath();
        $query  = $this->uri->getQuery();

        if ($target && $query) {
            $target .= '?' . $query;
        }

        return $target ? $target : '/';
    }

    /**
     * Retorna uma nova instância com o request-target passado.
     *
     * @param string $requestTarget
     */
    public function withRequestTarget($requestTarget): self
    {
        if (! is_string($requestTarget) || preg_match('/\s/', $requestTarget)) {
            throw new InvalidArgumentException('Request target não deve conter espaços');
        }

        $clone                = clone $this;
        $clone->requestTarget = $requestTarget;

        return $clone;
    }

    /**
     * Retorna o método http da request.
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Retorna uma nova instância com o método http passado.
     *
     * @param string $method
     */
    public function withMethod($method): self
    {
        if (! is_string($method)) {
            throw new InvalidArgumentException('O método deve ser uma string');
        }

        $this->validateMethod($method);

        $clone         = clone $this;
        $clone->method = $method;

        return $clone;
    }

    /**
     * Retorna a uri da request.
     */
    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    /**
     * Retorna uma nova instância com a uri passada.
     *
     * @param bool $preserveHost
     */
    public function withUri(UriInterface $uri, $preserveHost = false): self
    {
        $clone      = clone $this;
        $clone->uri = $uri;

        // mandou preservar o host e nós de fato temos o host para preservar.
        if ($preserveHost && $this->hasHeader('Host')) {
            return $clone;
        }

        $clone->setHostFromUri();

        return $clone;
    }

    /**
     * Seta o host baseado no host da Uri.
     */
    protected function setHostFromUri(): void
    {
        // nós não temos o host para preservar e nem a uri tem
        if (! $this->uri->getHost()) {
            return;
        }

        // nós não temos o host mas a uri tem, nós adicionamos a porta
        $host = $this->uri->getHost();

        if ($this->uri->getPort()) {
            $host .= ':' . $this->uri->getPort();
        }

        $this->setHeaders(['Host' => $host]);
    }

    /**
     * Valida o método http.
     */
    private function validateMethod(string $method): void
    {
        if (! preg_match('/^[a-zA-Z]+$/', $method)) {
            throw new InvalidArgumentException('O método deve ser uma string');
        }
    }
}
