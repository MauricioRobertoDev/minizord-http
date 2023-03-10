<?php

namespace Minizord\Http;

use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriInterface;

abstract class AbstractServerRequest extends AbstractRequest implements ServerRequestInterface
{
    /**
     * Atributos da request.
     *
     * @var array<string, string>
     */
    protected array $attributes;

    /**
     * Server params tipicamente $_SERVER.
     *
     * @var array<string, string>
     */
    protected array $serverParams  = [];

    /**
     * Cookie params tipicamente $_COOKIE.
     *
     * @var array<string, string>
     */
    protected array $cookieParams  = [];

    /**
     * Query params tipicamente $_GET.
     *
     * @var array<string, string>
     */
    protected array $queryParams   = [];

    /**
     * Array arquivos upados.
     *
     * @var array<UploadedFile>
     */
    protected array $uploadedFiles          = [];

    /**
     * O corpo da requisilção já parseado.
     */
    protected object|array|null $parsedBody = null;

    /**
     * Retorna uma nova intância com os cookies passados.
     */
    public function withCookieParams(array $cookies): static
    {
        $clone               = clone $this;
        $clone->cookieParams = $cookies;

        return $clone;
    }

    /**
     * Retorna uma nova intância com os query params passados.
     */
    public function withQueryParams(array $query): static
    {
        $clone              = clone $this;
        $clone->queryParams = $query;

        return $clone;
    }

    public function withServerParams(array $server): static
    {
        $clone               = clone $this;
        $clone->serverParams = $server;

        return $clone;
    }

    /**
     * Retorna uma nova instância com os UploadedFiles passados.
     *
     * @param array<UploadedFile> $uploadedFiles
     */
    public function withUploadedFiles(array $uploadedFiles): static
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
    public function withParsedBody($data): static
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
    public function withAttribute($name, $value): static
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
    public function withoutAttribute($name): static
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
    protected function validateUploadedFiles(array $uploadedFiles): void
    {
        foreach ($uploadedFiles as $uploadedFile) {
            if (! $uploadedFile instanceof UploadedFileInterface) {
                throw new InvalidArgumentException('O array só deve conter UploadedFileInterface');
            }
        }
    }

    /**
     * Configura os dados na request.
     */
    protected function init(
        string $method =  'GET',
        UriInterface|string $uri = '',
        array $headers = [],
        string $version = '1.1',
        mixed $body = null,
        object|array|null $parsedBody = null,
        array $serverParams = [],
        array $uploadedFiles = [],
        array $cookieParams = [],
        array $queryParams = [],
        array $attributes = [],
    ): void {
        $this->validateUploadedFiles($uploadedFiles);

        $this->attributes    = $attributes;
        $this->serverParams  = $serverParams;
        $this->cookieParams  = $cookieParams;
        $this->uploadedFiles = $uploadedFiles;
        $this->queryParams   = $queryParams;
        $this->parsedBody    = $parsedBody;

        parent::init($method, $uri, $headers, $version, $body, );
    }
}
