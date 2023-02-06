<?php

use Minizord\Http\Factory\RequestData;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriInterface;

beforeEach(function () {
    $this->server = [
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
});

/*
 * getHeaders()
 */
test('Deve retornar os headers já tratados', function () {
    $headers = (new RequestData())->getHeaders($this->server);

    $expect = [
        'Host'          => 'batata.com',
        'Content-Type'  => 'text/html; charset=UTF-8',
        'Authorization' => 'Bearer token',
    ];

    expect($headers)->toBe($expect);
});

/*
 * getProtocolVersion()
 */
test('Deve retornar a versão do protocolo', function () {
    $version = (new RequestData())->getProtocolVersion($this->server);

    $expect = '1.0';

    expect($version)->toBe($expect);
});

test('Deve retornar a versão padrão caso não tenha SERVER_PROTOCOL', function () {
    $version = (new RequestData())->getProtocolVersion([]);

    $expect = '1.1';

    expect($version)->toBe($expect);
});

/*
 * getMethod()
 */
test('Deve retornar o método', function () {
    $version = (new RequestData())->getMethod($this->server);

    $expect = 'POST';

    expect($version)->toBe($expect);
});

test('Deve retornar o método padrão caso não tenha REQUEST_METHOD', function () {
    $version = (new RequestData())->getMethod([]);

    $expect = 'GET';

    expect($version)->toBe($expect);
});

/*
 * getUri()
 */
test('Deve retornar a uri da request', function () {
    $uri = (new RequestData())->getUri($this->server);

    expect($uri)->toBeInstanceOf(UriInterface::class);
    expect($uri->getScheme())->toBe('https');
    expect($uri->getHost())->toBe('batata.com');
    expect($uri->getPort())->toBe(null);
    expect($uri->getPath())->toBe('/path');
    expect($uri->getQuery())->toBe('arg=value&batata=tomate');
    expect((string) $uri)->toBe('https://batata.com/path?arg=value&batata=tomate');
});

/*
 * getUploadedFiles()
 */
test('Deve as files como UploadedFile', function () {
    $files = [
        'uploads' => [
            'name'     => ['file2' => 'MyFile.jpg'],
            'type'     => ['file1' => 'text/plain'],
            'tmp_name' => ['file1' => '/tmp/php/php1h4j1o', 'file2' => '/tmp/php/php6hst32'],
            'error'    => ['file1' => UPLOAD_ERR_OK,        'file2' => UPLOAD_ERR_OK],
            'size'     => ['file1' => 123,                  'file2' => 98174],
        ],
    ];

    $uploadedFiles = (new RequestData())->getUploadedFiles($files);

    expect($uploadedFiles)->toHaveCount(2);
    expect($uploadedFiles['file1'])->toBeInstanceOf(UploadedFileInterface::class);
    expect($uploadedFiles['file2'])->toBeInstanceOf(UploadedFileInterface::class);
    expect($uploadedFiles['file1']->getSize())->toBe(123);
    expect($uploadedFiles['file2']->getSize())->toBe(98174);
    expect($uploadedFiles['file1']->getError())->toBe(0);
    expect($uploadedFiles['file2']->getError())->toBe(0);
    expect($uploadedFiles['file1']->getClientFilename())->toBe(null);
    expect($uploadedFiles['file2']->getClientFilename())->toBe('MyFile.jpg');
    expect($uploadedFiles['file1']->getClientMediaType())->toBe('text/plain');
    expect($uploadedFiles['file2']->getClientMediaType())->toBe(null);
});
