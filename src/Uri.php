<?php

namespace Minizord\Http;

use Minizord\Http\Contract\UriInterface;

class Uri implements UriInterface
{
    private const STANDARD_SCHEME_PORTS = ['http' => 80, 'https' => 443];

    private string $scheme;
    private string $host;
    private string $user;
    private string $pass;
    private ?string $port;
    private string $query;

    public function __construct(string $url)
    {
        $parts          = parse_url($url);
        $this->scheme   = strtolower($parts['scheme'] ?? '');
        $this->host     = strtolower($parts['host'] ?? '');
        $this->user     = $parts['user'] ?? '';
        $this->pass     = $parts['pass'] ?? '';
        $this->query    = rawurlencode($parts['query'] ?? '');
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

    public function getAuthority()
    {
    }

    public function getPath()
    {
    }

    public function getFragment()
    {
    }

    public function withScheme($scheme)
    {
    }

    public function withUserInfo($user, $password = null)
    {
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
        if (isset(self::STANDARD_SCHEME_PORTS[$this->scheme]) && self::STANDARD_SCHEME_PORTS[$this->scheme] === $port) {
            $this->port = null;
        } else {
            $this->port = $port;
        }
    }
}
