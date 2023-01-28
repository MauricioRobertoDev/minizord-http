<?php

namespace Minizord\Http;

use Minizord\Http\Contract\UriInterface;

class Uri implements UriInterface
{
    private string $scheme;
    private string $host;

    public function __construct(string $url)
    {
        $parts        = parse_url($url);
        $this->scheme = strtolower($parts['scheme'] ?? '');
        $this->host   = strtolower($parts['host'] ?? '');
    }

    public function getScheme()
    {
        return $this->scheme;
    }

    public function getAuthority()
    {
    }

    public function getUserInfo()
    {
    }

    public function getHost()
    {
        return $this->host;
    }

    public function getPort()
    {
    }

    public function getPath()
    {
    }

    public function getQuery()
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
}
