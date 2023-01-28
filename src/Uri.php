<?php

namespace Minizord\Http;

use InvalidArgumentException;
use Minizord\Http\Contract\UriInterface;

class Uri implements UriInterface
{
    private const SCHEMES = ['http' => 80, 'https' => 443];

    private string $scheme;
    private string $host;
    private string $user;
    private ?string $pass;
    private ?string $port;
    private string $query;
    private string $fragment;
    private string $path;

    public function __construct(string $url)
    {
        $parts          = parse_url($url);
        $this->scheme   = strtolower($parts['scheme'] ?? '');
        $this->host     = strtolower($parts['host'] ?? '');
        $this->user     = $parts['user'] ?? '';
        $this->pass     = $parts['pass'] ?? '';
        $this->query    = rawurlencode(rawurldecode($parts['query'] ?? ''));
        $this->fragment = rawurlencode(rawurldecode($parts['fragment'] ?? ''));
        $this->path     = rawurlencode(rawurldecode($parts['path'] ?? ''));
        $this->port     = $this->filterPort($parts['port'] ?? null);
    }

    public function getScheme() : string
    {
        return $this->scheme;
    }

    public function getHost() : string
    {
        return $this->host;
    }

    public function getUserInfo() : string
    {
        $userinfo = $this->user;

        if ($this->user && $this->pass != '') {
            $userinfo .= ':' . $this->pass;
        }

        return $userinfo;
    }

    public function getPort() : null|int
    {
        return $this->port;
    }

    public function getQuery() : string
    {
        return $this->query;
    }

    public function getFragment() : string
    {
        return $this->fragment;
    }

    public function getAuthority() : string
    {
        $authority = '';

        if ($this->getUserInfo() != '') {
            $authority = $this->getUserInfo() . '@';
        }

        $authority .= $this->getHost();

        if ($this->getPort() != '') {
            $authority .= ':' . $this->getPort();
        }

        return $authority;
    }

    public function getPath() : string
    {
        return $this->path;
    }

    public function withScheme($scheme) : Uri
    {
        $scheme        = strtolower($scheme);
        $clone         = clone $this;

        if ($scheme !== null && $scheme !== '' && !in_array($scheme, array_keys(self::SCHEMES))) {
            throw new InvalidArgumentException('Scheme não inválido, os schemes suportados são: ' . join(', ', array_keys(self::SCHEMES)));
        }

        $clone->scheme = $scheme;

        return $clone;
    }

    public function withUserInfo($user, $password = null) : Uri
    {
        $clone           = clone $this;
        $clone->user     = $user;
        $clone->pass     = $user ? $password : null;

        return $clone;
    }

    public function withHost($host) : Uri
    {
        if (!is_string($host)) {
            throw new InvalidArgumentException('Host deve ser uma string');
        }

        $clone       = clone $this;
        $clone->host = strtolower($host);

        return $clone;
    }

    public function withPort($port) : Uri
    {
        $port = $this->filterPort($port);

        $clone       = clone $this;
        $clone->port = $port;

        return $clone;
    }

    public function withPath($path) : Uri
    {
        if (!is_string($path)) {
            throw new InvalidArgumentException('Path deve ser uma string');
        }

        $clone       = clone $this;
        $clone->path = rawurlencode(rawurldecode($path));

        return $clone;
    }

    public function withQuery($query)
    {
        if (!is_string($query)) {
            throw new InvalidArgumentException('Query deve ser uma string');
        }

        $clone        = clone $this;
        $clone->query = rawurlencode(rawurldecode($query));

        return $clone;
    }

    public function withFragment($fragment)
    {
    }

    public function __toString()
    {
    }

    private function filterPort(string|int|null $port) : ?int
    {
        if (null === $port) {
            return null;
        }

        $port = (int) $port; // se for uma string que não é um número $port será 0
        if ($port < 0 || $port > 65535) {
            throw new InvalidArgumentException('Porta inválida: ' . $port . '. Deve estar entre 0 e 65535');
        }

        // é uma porta padrão
        if (isset(self::SCHEMES[$this->scheme]) && self::SCHEMES[$this->scheme] === $port) {
            return null;
        }

        return $port;
    }
}
