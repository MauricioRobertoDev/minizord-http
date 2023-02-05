<?php

namespace Minizord\Http;

use InvalidArgumentException;
use Psr\Http\Message\UriInterface;

final class Uri implements UriInterface
{
    private const CHAR_UNRESERVED  = 'a-zA-Z0-9\-\.\_\~';

    private const CHAR_SUB_DELIMS  = '\!\$\&\'\(\)\*\+\,\;\=';

    private const SCHEMES = ['http' => 80, 'https' => 443];

    /**
     * Scheme da uri, http ou https.
     */
    private string $scheme;

    /**
     * Basicamente o domínio, mas pode ser um número como ip.
     */
    private string $host;

    /**
     * Usuário para autenticação.
     */
    private string|null $user = null;

    /**
     * Senha para autenticação.
     */
    private string|null $pass = null;

    /**
     * Porta.
     */
    private string|null $port = null;

    /**
     * Query string, o que vem depois do ?
     */
    private string $query;

    /**
     * Fragment, o que vem depois do #.
     */
    private string $fragment;

    /**
     * Caminho da uri, logo cochecido como o /alguma-coisa.
     */
    private string $path;

    /**
     * Representação de uma uri.
     */
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

    /**
     * Retorna uma string com dados da uri.
     */
    public function __toString(): string
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

            if ($this->getAuthority()) {
                $path = '/' . ltrim($this->getPath(), '/');
            }

            $string .= $path;
        }

        if ($this->getQuery()) {
            $string .= '?' . $this->getQuery();
        }

        if ($this->getFragment()) {
            $string .= '#' . $this->getFragment();
        }

        return $string;
    }

    /**
     * Retorna o scheme.
     */
    public function getScheme(): string
    {
        return $this->scheme;
    }

    /**
     * Retorna o host.
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * Retorna o usuário e senha caso tenha username[:password].
     */
    public function getUserInfo(): string
    {
        $userinfo = $this->user;

        if ($this->user && $this->pass) {
            $userinfo .= ':' . $this->pass;
        }

        return $userinfo ?? '';
    }

    /**
     * Retorna a porta caso exista e não seja a padrão.
     */
    public function getPort(): int|null
    {
        return $this->port;
    }

    /**
     * Retorna a query string.
     */
    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * Retorna o fragment.
     */
    public function getFragment(): string
    {
        return $this->fragment;
    }

    /**
     * Retorna a autoridade no formato: [user-info@]host[:port].
     */
    public function getAuthority(): string
    {
        $authority = '';

        if ($this->getUserInfo()) {
            $authority = $this->getUserInfo() . '@';
        }

        $authority .= $this->getHost();

        if ($this->getPort()) {
            $authority .= ':' . $this->getPort();
        }

        return $authority;
    }

    /**
     * Retorna o path.
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Retorna uma nova instância com o scheme passado.
     *
     * @param string $scheme
     */
    public function withScheme($scheme): Uri
    {
        if (! is_string($scheme)) {
            throw new InvalidArgumentException('O scheme deve ser uma string');
        }

        $scheme        = strtolower($scheme);
        $clone         = clone $this;

        if ($scheme !== null && $scheme !== '' && ! in_array($scheme, array_keys(self::SCHEMES))) {
            throw new InvalidArgumentException('Scheme não inválido, os schemes suportados são: ' . join(', ', array_keys(self::SCHEMES)));
        }

        $clone->scheme = $scheme;

        return $clone;
    }

    /**
     * Retorna uma nova instância com o user-info passado.
     *
     * @param string      $user
     * @param string|null $password
     */
    public function withUserInfo($user, $password = null): Uri
    {
        if (! is_string($user)) {
            throw new InvalidArgumentException('O usuário deve ser uma string');
        }

        if ($password && ! is_string($password)) {
            throw new InvalidArgumentException('A senha deve ser uma string');
        }

        $clone           = clone $this;
        $clone->user     = $user;
        $clone->pass     = $user ? $password : null;

        return $clone;
    }

    /**
     * Retorna uma nova instância com o host passado.
     *
     * @param string $host
     */
    public function withHost($host): Uri
    {
        if (! is_string($host)) {
            throw new InvalidArgumentException('Host deve ser uma string');
        }

        $clone       = clone $this;
        $clone->host = strtolower($host);

        return $clone;
    }

    /**
     * Retorna uma nova instância com a porta passada.
     *
     * @param string|int $port
     */
    public function withPort($port): Uri
    {
        $port = $this->filterPort($port);

        $clone       = clone $this;
        $clone->port = $port;

        return $clone;
    }

    /**
     * Retorna uma nova instância com o path passado.
     *
     * @param string $path
     */
    public function withPath($path): Uri
    {
        if (! is_string($path)) {
            throw new InvalidArgumentException('Path deve ser uma string');
        }

        $clone       = clone $this;
        $clone->path = $this->filterPath($path);

        return $clone;
    }

    /**
     * Retorna uma nova instância com a query-string passada.
     *
     * @param string $query
     */
    public function withQuery($query): Uri
    {
        if (! is_string($query)) {
            throw new InvalidArgumentException('Query deve ser uma string');
        }

        $clone        = clone $this;
        $clone->query = $this->filterQueryAndFragment($query);

        return $clone;
    }

    /**
     * Retorna uma nova instância com o fragment passado.
     *
     * @param string $fragment
     */
    public function withFragment($fragment): Uri
    {
        if (! is_string($fragment)) {
            throw new InvalidArgumentException('Fragment deve ser uma string');
        }

        $clone           = clone $this;
        $clone->fragment =  $this->filterQueryAndFragment($fragment);

        return $clone;
    }

    /**
     * Retorna uma porta filtrada e null caso seja a padrão.
     */
    private function filterPort(string|int|null $port): ?int
    {
        if (! $port) {
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

    /**
     * Retorna o path formatado pela RFC 3986.
     */
    private function filterPath(string $path): string
    {
        $regex = '/(?:[^' . self::CHAR_UNRESERVED . self::CHAR_SUB_DELIMS . '\%\/\@]+|%(?![A-Fa-f0-9]{2}))/';

        return preg_replace_callback($regex, [$this, 'rawUrlEncode'], $path);
    }

    /**
     * Retorna a query stirng ou fragment formatado pela RFC 3986.
     */
    private function filterQueryAndFragment(string $string): string
    {
        $string = ltrim(ltrim($string, '?'), '#');
        $regex  = '/(?:[^' . self::CHAR_UNRESERVED . self::CHAR_SUB_DELIMS . '\%\/\@\?]+|%(?![A-Fa-f0-9]{2}))/';

        return preg_replace_callback($regex, [$this, 'rawUrlEncode'], $string);
    }

    /**
     * Formata os caracteres para se adequarem a RFC 3986.
     *
     * @param array<string> $match
     */
    private function rawUrlEncode(array $match): string
    {
        return rawurlencode($match[0]);
    }
}
