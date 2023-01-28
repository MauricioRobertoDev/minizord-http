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
    private string $pass;
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
        $this->query    = rawurlencode($parts['query'] ?? '');
        $this->fragment = rawurlencode($parts['fragment'] ?? '');
        $this->path     = rawurlencode($parts['path'] ?? '');
        $this->setPort($parts['port'] ?? null);
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
        $clone->pass     = $password;

        return $clone;
    }

    public function withHost($host)
    {
    }

    public function withPort($port)
    {
    }

    public function withPath($path)
    {
    }

    public function withQuery($query)
    {
    }

    public function withFragment($fragment)
    {
    }

    public function __toString()
    {
    }

    // private
    private function setPort(?int $port) : void
    {
        if (isset(self::SCHEMES[$this->scheme]) && self::SCHEMES[$this->scheme] === $port) {
            $this->port = null;
        } else {
            $this->port = $port;
        }
    }
}
