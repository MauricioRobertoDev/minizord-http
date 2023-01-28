<?php

namespace Minizord\Http;

use Minizord\Http\Contract\UriInterface;

class Uri implements UriInterface
{
    private string $scheme;
    private string $host;
    private string $user;
    private string $pass;

    public function __construct(string $url)
    {
        $parts          = parse_url($url);
        $this->scheme   = strtolower($parts['scheme'] ?? '');
        $this->host     = strtolower($parts['host'] ?? '');
        $this->user     = $parts['user'] ?? '';
        $this->pass     = $parts['pass'] ?? '';
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

    public function getAuthority()
    {
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
