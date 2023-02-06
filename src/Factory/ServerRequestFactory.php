<?php

namespace Minizord\Http\Factory;

use Minizord\Http\AbstractServerRequest;
use Minizord\Http\ServerRequest;
use Psr\Http\Message\ServerRequestFactoryInterface;

final class ServerRequestFactory implements ServerRequestFactoryInterface
{
    public function __construct(private RequestData $requestData = new RequestData())
    {
    }

    /**
     * Crima uma ServerRequest.
     *
     * @param UriInterface|string   $uri
     * @param array<string, string> $serverParams
     */
    public function createServerRequest(string $method, $uri, array $serverParams = []): ServerRequest
    {
        return new ServerRequest(method: $method, uri: $uri, serverParams: $serverParams);
    }

    /**
     * Undocumented function.
     *
     * @param array<string, string>|null        $server
     * @param array<string, array<string>>|null $files
     * @param array<string, string>|null        $cookies
     * @param array<string, string>|null        $get
     * @param array<string, string>|null        $post
     */
    public function createFromGlobals(
        array|null $server = null,
        array|null $files = null,
        array|null $cookies = null,
        array|null $get = null,
        array|null $post = null,
    ): ServerRequest {
        $server  ??= $_SERVER;
        $cookies ??= $_COOKIE;
        $get     ??= $_GET;
        $files   ??= $_FILES;
        $post    ??= $_POST;

        return new ServerRequest(
            serverParams: $server,
            cookieParams: $cookies,
            queryParams: $get,
            uploadedFiles: $this->requestData->getUploadedFiles($files),
            method: $this->requestData->getMethod($server),
            version: $this->requestData->getProtocolVersion($server),
            uri: $this->requestData->getUri($server),
            headers: $this->requestData->getHeaders($server),
            body: '',
            parsedBody: $post,
        );
    }

    /**
     * Undocumented function.
     *
     * @param array<string, string>|null        $server
     * @param array<string, array<string>>|null $files
     * @param array<string, string>|null        $cookies
     * @param array<string, string>|null        $get
     * @param array<string, string>|null        $post
     */
    public function injectFromGlobals(
        AbstractServerRequest|null $request = null,
        array|null $server = null,
        array|null $files = null,
        array|null $cookies = null,
        array|null $get = null,
        array|null $post = null,
    ): AbstractServerRequest {
        $server  ??= $_SERVER;
        $cookies ??= $_COOKIE;
        $get     ??= $_GET;
        $files   ??= $_FILES;
        $post    ??= $_POST;
        $request ??= new ServerRequest();

        return $request
            ->withServerParams($server)
            ->withCookieParams($cookies)
            ->withQueryParams($get)
            ->withParsedBody($post)
            ->withUploadedFiles($this->requestData->getUploadedFiles($files))
            ->withMethod($this->requestData->getMethod($server))
            ->withProtocolVersion($this->requestData->getProtocolVersion($server))
            ->withUri($this->requestData->getUri($server))
            ->withHeaders($this->requestData->getHeaders($server));
    }
}
