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

    /**
     * Scheme da uri, http ou https.
     *
     * @var string
     */
    private string $scheme;

    /**
     * Basicamente o domínio, mas pode ser um número como ip.
     *
     * @var string
     */
    private string $host;

    /**
     * Usuário para autenticação.
     *
     * @var string
     */
    private string $user;

    /**
     * Senha para autenticação.
     *
     * @var string|null
     */
    private ?string $pass;

    /**
     * Porta.
     *
     * @var string|null
     */
    private ?string $port;

    /**
     * Query string, o que vem depois do ?
     *
     * @var string
     */
    private string $query;

    /**
     * Fragment, o que vem depois do #.
     *
     * @var string
     */
    private string $fragment;

    /**
     * Caminho da uri, logo cochecido como o /alguma-coisa.
     *
     * @var string
     */
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

    /**
     * Retorna o scheme.
     *
     * @return string
     */
    public function getScheme() : string
    {
        return $this->scheme;
    }

    /**
     * Retorna o host.
     *
     * @return string
     */
    public function getHost() : string
    {
        return $this->host;
    }

    /**
     * Retorna o usuário e senha caso tenha username[:password].
     *
     * @return string
     */
    public function getUserInfo() : string
    {
        $userinfo = $this->user;

        if ($this->user && $this->pass != '') {
            $userinfo .= ':' . $this->pass;
        }

        return $userinfo;
    }

    /**
     * Retorna a porta caso exista e não seja a padrão.
     *
     * @return null|int
     */
    public function getPort() : null|int
    {
        return $this->port;
    }

    /**
     * Retorna a query string.
     *
     * @return string
     */
    public function getQuery() : string
    {
        return $this->query;
    }

    /**
     * Retorna o fragment.
     *
     * @return string
     */
    public function getFragment() : string
    {
        return $this->fragment;
    }

    /**
     * Retorna a autoridade no formato: [user-info@]host[:port].
     *
     * @return string
     */
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

    /**
     * Retorna o path.
     *
     * @return string
     */
    public function getPath() : string
    {
        return $this->path;
    }

    /**
     * Retorna uma nova instância com o scheme passado.
     *
     * @param  string $scheme
     * @return Uri
     */
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

    /**
     * Retorna uma nova instância com o user-info passado.
     *
     * @param  string $user
     * @param  string $password
     * @return Uri
     */
    public function withUserInfo($user, $password = null) : Uri
    {
        $clone           = clone $this;
        $clone->user     = $user;
        $clone->pass     = $user ? $password : null;

        return $clone;
    }

    /**
     * Retorna uma nova instância com o host passado.
     *
     * @param  string $host
     * @return Uri
     */
    public function withHost($host) : Uri
    {
        if (!is_string($host)) {
            throw new InvalidArgumentException('Host deve ser uma string');
        }

        $clone       = clone $this;
        $clone->host = strtolower($host);

        return $clone;
    }

    /**
     * Retorna uma nova instância com a porta passada.
     *
     * @param  string|int $port
     * @return Uri
     */
    public function withPort($port) : Uri
    {
        $port = $this->filterPort($port);

        $clone       = clone $this;
        $clone->port = $port;

        return $clone;
    }

    /**
     * Retorna uma nova instância com o path passado.
     *
     * @param  string $path
     * @return Uri
     */
    public function withPath($path) : Uri
    {
        if (!is_string($path)) {
            throw new InvalidArgumentException('Path deve ser uma string');
        }

        $clone       = clone $this;
        $clone->path = $this->filterPath($path);

        return $clone;
    }

    /**
     * Retorna uma nova instância com a query-string passada.
     *
     * @param  string $query
     * @return void
     */
    public function withQuery($query)
    {
        if (!is_string($query)) {
            throw new InvalidArgumentException('Query deve ser uma string');
        }

        $clone        = clone $this;
        $clone->query = $this->filterQueryAndFragment($query);

        return $clone;
    }

    /**
     * Retorna uma nova instância com o fragment passado.
     *
     * @param  string $fragment
     * @return void
     */
    public function withFragment($fragment)
    {
        if (!is_string($fragment)) {
            throw new InvalidArgumentException('Fragment deve ser uma string');
        }

        $clone           = clone $this;
        $clone->fragment =  $this->filterQueryAndFragment($fragment);

        return $clone;
    }

    /**
     * Retorna uma string com dados da uri.
     *
     * @return string
     */
    public function __toString()
    {
        $string = '';

        if ($this->getScheme() != null) {
            $string .= $this->getScheme() . ':';
        }

        if ($this->getAuthority() != null) {
            $string .= '//' . $this->getAuthority();
        }

        if ($this->getPath() != null) {
            $path = '';

            if ($this->getAuthority()) {
                $path = '/' . ltrim($this->getPath(), '/');
            }

            $string .= $path;
        }

        if ($this->getQuery() != null) {
            $string .= '?' . $this->getQuery();
        }

        if ($this->getFragment() != null) {
            $string .= '#' . $this->getFragment();
        }

        return $string;
    }

    /**
     * Retorna uma porta filtrada e null caso seja a padrão.
     *
     * @param  string|int|null $port
     * @return int|null
     */
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

    /**
     * Retorna o path formatado pela RFC 3986.
     *
     * @param  string $path
     * @return string
     */
    private function filterPath(string $path) : string
    {
        // está separado assim para que você possa interpretar de uma melhor forma
        $regex = '/(?:' . '[^' . self::CHAR_UNRESERVED . self::CHAR_SUB_DELIMS . '\%\/\@' . ']+' . '|%(?![A-Fa-f0-9]{2})' . ')/';

        return preg_replace_callback($regex, [$this, 'rawUrlEncode'], $path);
    }

    /**
     * Retorna a query stirng ou fragment formatado pela RFC 3986.
     *
     * @param  string $string
     * @return string
     */
    private function filterQueryAndFragment(string $string) : string
    {
        $string = ltrim(ltrim($string, '?'), '#');
        // está separado assim para que você possa interpretar de uma melhor forma
        $regex = '/(?:' . '[^' . self::CHAR_UNRESERVED . self::CHAR_SUB_DELIMS . '\%\/\@\?' . ']+' . '|%(?![A-Fa-f0-9]{2})' . ')/';

        return preg_replace_callback($regex, [$this, 'rawUrlEncode'], $string);
    }

    /**
     * Formata os caracteres para se adequarem a RFC 3986.
     *
     * @param  array $match
     * @return void
     */
    private function rawUrlEncode(array $match)
    {
        return rawurlencode($match[0]);
    }
}
