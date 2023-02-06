<?php

use Minizord\Http\AbstractServerRequest;
use Minizord\Http\Factory\ServerRequestFactory;
use Minizord\Http\UploadedFile;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

beforeAll(function () {
    $_SERVER = [
        'SERVER_PROTOCOL'    => 'HTTP/1.0',
        'REQUEST_METHOD'     => 'POST',
        'QUERY_STRING'       => 'arg=value&batata=tomate',
        'HTTPS'              => 'no-empty',
        'SERVER_PORT'        => '443',
        'REQUEST_URI'        => '/path?arg=value&batata=tomate',
        'HTTP_HOST'          => 'batata.com',
        'CONTENT_TYPE'       => 'text/html; charset=UTF-8',
        'HTTP_AUTHORIZATION' => 'Bearer token',
    ];

    $_GET = [
        'arg'    => 'value',
        'batata' => 'tomate',
    ];

    $_COOKIE = [
        'buh' => 'buuh',
    ];

    $_FILES = [
        'uploads' => [
            'name'     => ['file2' => 'MyFile.jpg'],
            'type'     => ['file1' => 'text/plain'],
            'tmp_name' => ['file1' => '/tmp/php/php1h4j1o', 'file2' => '/tmp/php/php6hst32'],
            'error'    => ['file1' => UPLOAD_ERR_OK,        'file2' => UPLOAD_ERR_OK],
            'size'     => ['file1' => 123,                  'file2' => 98174],
        ],
    ];
});

/*
 * createServerRequest()
 */
test('Deve criar uma ServerRequest', function () {
    $factory       = new ServerRequestFactory();
    $serverRequest = $factory->createServerRequest('POST', 'https://example.com', []);

    expect($serverRequest)->toBeInstanceOf(ServerRequestInterface::class);
});

/*
 * createFromGlobals()
 */
test('Deve criar uma ServerRequest baseado nas variáveis globais', function () {
    $factory       = new ServerRequestFactory();
    $serverRequest = $factory->createFromGlobals();

    expect($serverRequest)->toBeInstanceOf(ServerRequestInterface::class);
    expect($serverRequest->getServerParams())->toBe($_SERVER);
    expect($serverRequest->getQueryParams())->toBe($_GET);
    expect($serverRequest->getCookieParams())->toBe($_COOKIE);
    expect($serverRequest->getParsedBody())->toBe($_POST);
    expect($serverRequest->getUploadedFiles())->toHaveCount(2);
    expect($serverRequest->getUploadedFiles()['file1'])->toBeInstanceOf(UploadedFile::class);
    expect($serverRequest->getUploadedFiles()['file2'])->toBeInstanceOf(UploadedFile::class);
    expect($serverRequest->getHeaders())->toBe([
        'Host'          => ['batata.com'],
        'Content-Type'  => ['text/html; charset=UTF-8'],
        'Authorization' => ['Bearer token'],
    ]);
    expect($serverRequest->getMethod())->toBe('POST');
    expect($serverRequest->getUri())->toBeInstanceOf(UriInterface::class);
    expect($serverRequest->getUri()->getHost())->toBe('batata.com');
    expect($serverRequest->getUri()->getPort())->toBe(null);
    expect($serverRequest->getUri()->getPath())->toBe('/path');
    expect($serverRequest->getUri()->getScheme())->toBe('https');
});

/*
 * createFromGlobals()
 */
test('Deve injetar as variáveis em uma request própria', function () {
    $request = new class() extends AbstractServerRequest {
    };
    $factory       = new ServerRequestFactory();
    $serverRequest = $factory->injectFromGlobals($request);

    expect($serverRequest)->toBeInstanceOf(ServerRequestInterface::class);
    expect($serverRequest->getServerParams())->toBe($_SERVER);
    expect($serverRequest->getQueryParams())->toBe($_GET);
    expect($serverRequest->getCookieParams())->toBe($_COOKIE);
    expect($serverRequest->getParsedBody())->toBe($_POST);
    expect($serverRequest->getUploadedFiles())->toHaveCount(2);
    expect($serverRequest->getUploadedFiles()['file1'])->toBeInstanceOf(UploadedFile::class);
    expect($serverRequest->getUploadedFiles()['file2'])->toBeInstanceOf(UploadedFile::class);
    expect($serverRequest->getHeaders())->toBe([
        'Host'          => ['batata.com'],
        'Content-Type'  => ['text/html; charset=UTF-8'],
        'Authorization' => ['Bearer token'],
    ]);
    expect($serverRequest->getMethod())->toBe('POST');
    expect($serverRequest->getUri())->toBeInstanceOf(UriInterface::class);
    expect($serverRequest->getUri()->getHost())->toBe('batata.com');
    expect($serverRequest->getUri()->getPort())->toBe(null);
    expect($serverRequest->getUri()->getPath())->toBe('/path');
    expect($serverRequest->getUri()->getScheme())->toBe('https');
});
