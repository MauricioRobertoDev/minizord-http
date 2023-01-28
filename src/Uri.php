<?php

namespace Minizord\Http;

use InvalidArgumentException;
use Minizord\Http\Contract\UriInterface;

class Uri implements UriInterface
{
    //são são especificos de alguma parte da url e são usados no path, user info, query string e fragment, não precisamos encodar esses caracteres
    private const CHAR_UNRESERVED  = 'a-zA-Z0-9\-\.\_\~';

    // são usados em user info, query string e fragment por isso não devemos encodar eles ao lidar com esses tipos
    private const CHAR_SUB_DELIMS  = '\!\$\&\'\(\)\*\+\,\;\=';

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
        $parts          = parse_url(urldecode($url));
        $this->scheme   = strtolower($parts['scheme'] ?? '');
        $this->host     = strtolower($parts['host'] ?? '');
        $this->user     = $parts['user'] ?? '';
        $this->pass     = $parts['pass'] ?? '';
        $this->query    = $this->filterQueryAndFragment($parts['query'] ?? '');
        $this->fragment = $this->filterQueryAndFragment($parts['fragment'] ?? '');
        $this->path     = $this->filterPath($parts['path'] ?? '');
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
        $clone->path = $this->filterPath($path);

        return $clone;
    }

    public function withQuery($query)
    {
        if (!is_string($query)) {
            throw new InvalidArgumentException('Query deve ser uma string');
        }

        $clone        = clone $this;
        $clone->query = $this->filterQueryAndFragment($query);

        return $clone;
    }

    public function withFragment($fragment)
    {
        if (!is_string($fragment)) {
            throw new InvalidArgumentException('Fragment deve ser uma string');
        }

        $clone           = clone $this;
        $clone->fragment =  $this->filterQueryAndFragment($fragment);

        return $clone;
    }

    public function __toString()
    {
        $string = '';

        if ($this->getScheme()) {
            $string .= $this->getScheme() . ':';
        }

        if ($this->getAuthority()) {
            $string .= '//' . $this->getAuthority();
        }

        if ($this->getPath()) {
            $path = '';

            if ('/' !== $this->getPath()[0]) {
                if ('' !== $this->getAuthority()) {
                    $path = '/' . $this->getPath();
                }
            }

            $string .= $path;
        }

        if ($this->getQuery() !== '') {
            $string .= '?' . $this->getQuery();
        }

        if ($this->getFragment() !== '') {
            $string .= '#' . $this->getFragment();
        }

        return $string;
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

    private function filterPath(string $path) : string
    {
        // está separado assim para que você possa interpretar de uma melhor forma
        $regex = '/(?:' . '[^' . self::CHAR_UNRESERVED . self::CHAR_SUB_DELIMS . '\%\/\@' . ']+' . '|%(?![A-Fa-f0-9]{2})' . ')/';

        return preg_replace_callback($regex, [$this, 'rawUrlEncode'], $path);
    }

    private function filterQueryAndFragment(string $string) : string
    {
        $string = ltrim(ltrim($string, '?'), '#');
        // está separado assim para que você possa interpretar de uma melhor forma
        $regex = '/(?:' . '[^' . self::CHAR_UNRESERVED . self::CHAR_SUB_DELIMS . '\%\/\@\?' . ']+' . '|%(?![A-Fa-f0-9]{2})' . ')/';

        return preg_replace_callback($regex, [$this, 'rawUrlEncode'], $string);
    }

    private function rawUrlEncode(array $match)
    {
        return rawurlencode($match[0]);
    }
}
