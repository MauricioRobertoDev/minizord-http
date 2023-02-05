<?php

namespace Minizord\Http;

use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriInterface;

class ServerRequest implements ServerRequestInterface
{
    use MessageTrait;
    use RequestTrait;

    /**
     * Atributos da request.
     *
     * @var array<string, string>
     */
    private array $attributes;

    /**
     * Server params tipicamente $_SERVER.
     *
     * @var array<string, string>
     */
    private array $serverParams  = [];

    /**
     * Cookie params tipicamente $_COOKIE.
     *
     * @var array<string, string>
     */
    private array $cookieParams  = [];

    /**
     * Query params tipicamente $_GET.
     *
     * @var array<string, string>
     */
    private array $queryParams   = [];

    /**
     * Array arquivos upados.
     *
     * @var array<UploadedFile>
     */
    private array $uploadedFiles          = [];

    /**
     * O corpo da requisilção já parseado.
     */
    private object|array|null $parsedBody = null;

    /**
     * Representação de uma solicitação HTTP recebida do lado do servidor.
     */
    public function __construct(
        array $serverParams = [],
        array $uploadedFiles = [],
        array $cookieParams = [],
        array $queryParams = [],
        UriInterface|string $uri = '',
        array $headers = [],
        string $method =  'GET',
        mixed $body = null,
        string $version = '1.1',
        array $attributes = [],
    ) {
        $this->validateMethod($method);
        $this->validateProtocolVersion($version);
        $this->validateUploadedFiles($uploadedFiles);

        if (! ($uri instanceof UriInterface)) {
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

        if (! $this->hasHeader('Host')) {
            $this->setHostFromUri();
        }

        if ($body) {
            $this->body = new Stream($body);
        }
    }

    /**
     * Retorna uma nova intância com os cookies passados.
     */
    public function withCookieParams(array $cookies): self
    {
        $clone               = clone $this;
        $clone->cookieParams = $cookies;

        return $clone;
    }

    /**
     * Retorna uma nova intância com os query params passados.
     */
    public function withQueryParams(array $query): self
    {
        $clone              = clone $this;
        $clone->queryParams = $query;

        return $clone;
    }

    /**
     * Retorna uma nova instância com os UploadedFiles passados.
     *
     * @param array<UploadedFile> $uploadedFiles
     */
    public function withUploadedFiles(array $uploadedFiles): self
    {
        $this->validateUploadedFiles($uploadedFiles);

        $clone                = clone $this;
        $clone->uploadedFiles = $uploadedFiles;

        return $clone;
    }

    /**
     * Retorna uma nova instância com o body já parseado passado.
     *
     * @param object|array|null $data
     */
    public function withParsedBody($data): self
    {
        if (! is_array($data) && ! is_object($data) && $data !== null) {
            throw new InvalidArgumentException('Os dados devem ser um object, array ou null');
        }

        $clone             = clone $this;
        $clone->parsedBody = $data;

        return $clone;
    }

    /**
     * Retorna uma nova instância com o atributo passado.
     *
     * @param string $name
     * @param string $value
     */
    public function withAttribute($name, $value): self
    {
        $clone                    = clone $this;
        $clone->attributes[$name] = $value;

        return $clone;
    }

    /**
     * Retorna uma nova intância sem o atributo passado.
     *
     * @param string $name
     */
    public function withoutAttribute($name): self
    {
        $clone = clone $this;
        unset($clone->attributes[$name]);

        return $clone;
    }

    /**
     * Retorna o server params.
     *
     * @return array<string, string>
     */
    public function getServerParams(): array
    {
        return $this->serverParams;
    }

    /**
     * Retorna o cookie params.
     *
     * @return array<string, string>
     */
    public function getCookieParams(): array
    {
        return $this->cookieParams;
    }

    /**
     * Retorna o query params.
     *
     * @return array<string, string>
     */
    public function getQueryParams(): array
    {
        if ($this->queryParams !== []) {
            return $this->queryParams;
        }

        $queryParams = [];

        parse_str($this->getUri()->getQuery(), $queryParams);

        return $queryParams;
    }

    /**
     * Returna os arquivos upados.
     *
     * @return array<string|int, UploadedFiles>
     */
    public function getUploadedFiles(): array
    {
        return $this->uploadedFiles;
    }

    /**
     * Returna o body já parseado.
     */
    public function getParsedBody(): object|array|null
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

    /**
     * Retorna todos os atributos.
     *
     * @return array<string, string>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Retorna um atributo específico.
     *
     * @param string      $name
     * @param string|null $default
     *
     * @return string|null
     */
    public function getAttribute($name, $default = null): mixed
    {
        return $this->attributes[$name] ?? $default;
    }

    /**
     * Vallida os arquivos upados.
     *
     * @param array<UploadedFile> $uploadedFiles
     */
    private function validateUploadedFiles(array $uploadedFiles): void
    {
        foreach ($uploadedFiles as $uploadedFile) {
            if (! $uploadedFile instanceof UploadedFileInterface) {
                throw new InvalidArgumentException('O array só deve conter UploadedFileInterface');
            }
        }
    }
}
