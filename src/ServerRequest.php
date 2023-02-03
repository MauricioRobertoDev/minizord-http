<?php

namespace Minizord\Http;

use InvalidArgumentException;
use Minizord\Http\Contract\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface as PsrUploadedFileInterface;
use Psr\Http\Message\UriInterface as PsrPsrUriInterface;

class ServerRequest implements ServerRequestInterface
{
    use MessageTrait;
    use RequestTrait;

    private array $attributes;
    private array $serverParams  = [];
    private array $cookieParams  = [];
    private array $queryParams   = [];
    // UploadedFileInterface
    private array $uploadedFiles          = [];
    private null|array|object $parsedBody = null;

    // Representação de uma solicitação HTTP recebida do lado do servidor.
    public function __construct(string $method, $uri, array $headers = [], $body = null, array $serverParams = [], array $attributes = [], string $version = '1.1')
    {
        $this->validateMethod($method);
        $this->validateProtocolVersion($version);

        if (!($uri instanceof PsrPsrUriInterface)) {
            $uri = new Uri($uri);
        }

        $this->method       = $method;
        $this->uri          = $uri;
        $this->serverParams = $serverParams;
        $this->protocol     = $version;
        $this->attributes   = $attributes;

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
        foreach ($uploadedFiles as $uploadedFile) {
            if (!$uploadedFile instanceof PsrUploadedFileInterface) {
                throw new InvalidArgumentException('O array só deve conter PsrUploadedFileInterface');
            }
        }

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

    public function getParsedBody() : null|array|object
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
}
