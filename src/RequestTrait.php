<?php

namespace Minizord\Http;

use InvalidArgumentException;
use Psr\Http\Message\UriInterface as PsrUriInterface;

trait RequestTrait
{
    use MessageTrait;

    /**
     * Método http.
     *
     * @var string|null
     */
    private $method;
    /**
     * Objetivo da request.
     *
     * @var mixed
     */
    private $requestTarget;

    /**
     * Uri da request.
     *
     * @var [type]
     */
    private $uri;

    /**
     * Basicamente a url solicitada.
     *
     * @return void
     */
    public function getRequestTarget()
    {
        return $this->requestTarget;
    }

    /**
     * Retorna uma nova instância com o request-target passado.
     *
     * @param  mixed $requestTarget
     * @return void
     */
    public function withRequestTarget($requestTarget)
    {
        if (preg_match('/\s/', $requestTarget)) {
            throw new InvalidArgumentException('Request target não deve conter espaços');
        }

        $clone                = clone $this;
        $clone->requestTarget = $requestTarget;

        return $clone;
    }

    /**
     * Retorna o método http da request.
     *
     * @return void
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Retorna uma nova instância com o método http passado.
     *
     * @param  string $method
     * @return void
     */
    public function withMethod($method)
    {
        if (!is_string($method)) {
            throw new InvalidArgumentException('O método deve ser uma string');
        }

        $this->validateMethod($method);

        $clone         = clone $this;
        $clone->method = $method;

        return $clone;
    }

    /**
     * Retorna a uri da request.
     *
     * @return void
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Retorna uma nova instância com a uri passada.
     *
     * @param  PsrUriInterface $uri
     * @param  bool            $preserveHost
     * @return void
     */
    public function withUri(PsrUriInterface $uri, $preserveHost = false)
    {
        $clone      = clone $this;
        $clone->uri = $uri;

        // mandou preservar o hosto e nós de fato temos o host para preservar.
        if ($preserveHost && $this->hasHeader('Host')) {
            return $clone;
        }

        // nós não temos o host para preservar e nem a uri tem
        if (!$uri->getHost()) {
            return $clone;
        }

        // nós não temos o host mas a uri tem, nós adicionamos a porta
        $host = $uri->getHost();
        if ($uri->getPort()) {
            $host .= ':' . $uri->getPort();
        }

        // removemos o header host do clone e adicionamos o que pegamos da uri
        $clone                      = $clone->withoutHeader('host');
        $clone->headerNames['host'] = 'Host';
        $clone->headers['Host']     = [$host];

        return $clone;
    }

    // tchar = "!" / "#" / "$" / "%" / "&" / "'" / "*" / "+" / "-" / "." / "^" / "_" / "`" / "|" / "~" / DIGIT / ALPHA
    /**
     * Valida o método http.
     *
     * @param  string $method
     * @return void
     */
    private function validateMethod(string $method)
    {
        if (!preg_match('/^[\!\#\$\%\&\'\*\+\-\.\^\_\`\|\~a-zA-Z0-9]+$/', $method)) {
            throw new InvalidArgumentException('O método deve ser uma string');
        }
    }
}
