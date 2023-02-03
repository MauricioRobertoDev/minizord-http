<?php

namespace Minizord\Http;

use InvalidArgumentException;
use Minizord\Http\Contract\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface as PsrUploadedFileInterface;
use Psr\Http\Message\UriInterface as PsrUriInterface;

class ServerRequest implements ServerRequestInterface
{
    use MessageTrait;
    use RequestTrait;

    /**
     * Atributos da request.
     *
     * @var array
     */
    private array $attributes;

    /**
     * Server params tipicamente $_SERVER.
     *
     * @var array
     */
    private array $serverParams  = [];

    /**
     * Cookie params tipicamente $_COOKIE.
     *
     * @var array
     */
    private array $cookieParams  = [];

    /**
     * Query params tipicamente $_GET.
     *
     * @var array
     */
    private array $queryParams   = [];
    // UploadedFileInterface
    /**
     * Array arquivos upados.
     *
     * @var PsrUploadedFileInterface[]
     */
    private array $uploadedFiles          = [];

    /**
     * @var null|array|object
     */
    private null|array|object $parsedBody = null;

    // Representação de uma solicitação HTTP recebida do lado do servidor.
    public function __construct(
        array $serverParams = [],
        array $uploadedFiles = [],
        array $cookieParams = [],
        array $queryParams = [],
        PsrUriInterface|string $uri = '',
        array $headers = [],
        string $method =  'GET',
        mixed $body = null,
        string $version = '1.1',
        array $attributes = [],
    ) {
        $this->validateMethod($method);
        $this->validateProtocolVersion($version);
        $this->validateUploadedFiles($uploadedFiles);

        if (!($uri instanceof PsrUriInterface)) {
            $uri = new Uri($uri);
        }

        $this->method        = $method;
        $this->uri           = $uri;
        $this->protocol      = $version;
        $this->attributes    = $attributes;
        $this->serverParams  = $serverParams;
        $this->cookieParams  = $cookieParams;
        $this->uploadedFiles = $uploadedFiles;
        $this->queryParams   = $queryParams;

        $this->setHeaders($headers);

        if (!$this->hasHeader('Host')) {
            $this->setHostFromUri();
        }

        if ($body) {
            $this->body = new Stream($body);
        }
    }

    public function withCookieParams(array $cookies) : self
    {
        $clone               = clone $this;
        $clone->cookieParams = $cookies;

        return $clone;
    }

    public function withQueryParams(array $query) : self
    {
        $clone              = clone $this;
        $clone->queryParams = $query;

        return $clone;
    }

    public function withUploadedFiles(array $uploadedFiles) : self
    {
        $this->validateUploadedFiles($uploadedFiles);

        $clone                = clone $this;
        $clone->uploadedFiles = $uploadedFiles;

        return $clone;
    }

    public function withParsedBody($data) : self
    {
        if (!is_array($data) && !is_object($data) && $data !== null) {
            throw new InvalidArgumentException('Os dados devem ser um object, array ou null');
        }

        $clone             = clone $this;
        $clone->parsedBody = $data;

        return $clone;
    }

    public function withAttribute($name, $value) : self
    {
        $clone                    = clone $this;
        $clone->attributes[$name] = $value;

        return $clone;
    }

    public function withoutAttribute($name) : self
    {
        $clone = clone $this;
        unset($clone->attributes[$name]);

        return $clone;
    }

    public function getServerParams() : array
    {
        return $this->serverParams;
    }

    public function getCookieParams() : array
    {
        return $this->cookieParams;
    }

    public function getQueryParams() : array
    {
        if (!empty($this->queryParams)) {
            return $this->queryParams;
        }

        $queryParams = [];

        parse_str($this->getUri()->getQuery(), $queryParams);

        return $queryParams;
    }

    public function getUploadedFiles() : array
    {
        return $this->uploadedFiles;
    }

    public function getParsedBody() : object|array|null
    {
        if ($this->parsedBody !== null) {
            return $this->parsedBody;
        }

        if ($this->inHeaderAny('content-type', ['application/x-www-form-urlencoded', 'multipart/form-data'])) {
            return $_POST;
        }

        if ($this->inHeader('content-type', 'application/json')) {
            return json_decode($this->getBody());
        }

        return $this->parsedBody;
    }

    public function getAttributes() : array
    {
        return $this->attributes;
    }

    public function getAttribute($name, $default = null) : mixed
    {
        return $this->attributes[$name] ?? $default;
    }

    private function validateUploadedFiles(array $uploadedFiles) : void
    {
        foreach ($uploadedFiles as $uploadedFile) {
            if (!$uploadedFile instanceof PsrUploadedFileInterface) {
                throw new InvalidArgumentException('O array só deve conter PsrUploadedFileInterface');
            }
        }
    }
}
