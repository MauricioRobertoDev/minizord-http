<?php

use Minizord\Http\ServerRequest;
use Minizord\Http\Stream;
use Minizord\Http\UploadedFile;
use Minizord\Http\Uri;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;

/*
 * Instâncialização
 */
test('Deve criar uma nova server request', function () {
    $request = new ServerRequest(headers: ['Host' => ['batata.com']]);
    expect($request)->toBeInstanceOf(ServerRequestInterface::class);
    expect($request->getHeader('host'))->toBe(['batata.com']);
    expect($request->getBody())->toBeInstanceOf(Stream::class);
    expect($request->getBody()->getContents())->toBe('');
    expect($request->getServerParams())->toBe([]);

    $request = new ServerRequest(body: 'batata');
    expect($request)->toBeInstanceOf(ServerRequestInterface::class);
    expect($request->getBody())->toBeInstanceOf(Stream::class);
    expect($request->getBody()->getContents())->toBe('batata');
    expect($request->getServerParams())->toBe([]);

    $tempFileName     = tempnam(sys_get_temp_dir(), 'for-test');
    $resource         = fopen($tempFileName, 'rw+b');
    fwrite($resource, 'batatinha');
    $request = new ServerRequest(body: $resource);
    expect($request)->toBeInstanceOf(ServerRequestInterface::class);
    expect($request->getBody())->toBeInstanceOf(Stream::class);
    expect($request->getBody()->getContents())->toBe('batatinha');
    expect($request->getServerParams())->toBe([]);
});

/*
 * withCookieParams()
 */
test('Deve criar uma nova instância com os server params passados', function () {
    $request = new ServerRequest();

    $serverParams = ['CONTENT_TYPE' => 'aplication/json'];

    expect($request->withServerParams($serverParams))->not()->toBe($request);
    expect($request->withServerParams($serverParams)->getServerParams())->toBe($serverParams);
});

/*
 * withCookieParams()
 */
test('Deve criar uma nova instância com os cookies passados', function () {
    $request = new ServerRequest();

    $cookieParames = ['any_cookie' => 'any_cookie_value'];

    expect($request->withCookieParams($cookieParames))->not()->toBe($request);
    expect($request->withCookieParams($cookieParames)->getCookieParams())->toBe($cookieParames);
});

/*
 * withQueryParams()
 */
test('Deve criar uma nova instância com os dados de query params passados', function () {
    $request = new ServerRequest();

    $queryParams = ['any_query_name' => 'any_query_value'];

    expect($request)->toBe($request);
    expect($request->withQueryParams($queryParams))->not()->toBe($request);
    expect($request->withQueryParams($queryParams)->getQueryParams())->toBe($queryParams);

    $uri     = new Uri('https://example.com/?any_uri_query_name=any_uri_query_value');
    $request = new ServerRequest(uri: $uri);

    expect($request)->toBe($request);
    expect($request->getQueryParams())->toBe(['any_uri_query_name' => 'any_uri_query_value']);
});

/*
 * withParsedBody()
 */
test('Deve criar uma nova instância com body passado', function () {
    $request = new ServerRequest();
    $body    = ['any_name' => 'any_value'];

    expect($request->withParsedBody($body))->not()->toBe($request);
    expect($request->withParsedBody($body)->getParsedBody())->toBe($body);
    expect(fn () =>$request->withParsedBody(888))->toThrow(InvalidArgumentException::class);

    $_POST   = ['any_post_value' => 'any_post_value'];
    $request = new ServerRequest(headers: ['Content-Type' => 'application/x-www-form-urlencoded']);

    expect($request->withParsedBody(null))->not()->toBe($request);
    expect($request->withParsedBody(null)->getParsedBody())->toBe($_POST);

    $request = new ServerRequest(headers: ['Content-Type' => 'application/json'], body: json_encode($body));
    expect(($request->getParsedBody())->any_name)->toBe('any_value');

    $request = new ServerRequest();
    expect(($request->getParsedBody()))->toBeNull();
});

/*
 * withAttribute()
 */
test('Deve criar uma nova instância com attribute passado', function () {
    $request = new ServerRequest();

    $attributeName  = 'any_name';
    $attributeValue = 'any_value';

    expect($request)->toBe($request);
    expect($request->withAttribute($attributeName, $attributeValue))->not()->toBe($request);
    expect($request->withAttribute($attributeName, $attributeValue)->getAttributes())->toBe([$attributeName  => $attributeValue]);
    expect($request->withAttribute($attributeName, $attributeValue)->getAttribute($attributeName))->toBe($attributeValue);
});

/*
 * withoutAttribute()
 */
test('Deve criar uma nova instância sem o attribute passado', function () {
    $request = new ServerRequest();

    $attributeName  = 'any_name';
    $attributeValue = 'any_value';
    $request        = $request->withAttribute($attributeName, $attributeValue);

    expect($request->withAttribute($attributeName, $attributeValue)->getAttributes())->toBe([$attributeName  => $attributeValue]);
    expect($request->withoutAttribute($attributeName)->getAttributes())->toBe([]);
});

/*
 * withUploadedFiles()
 */
test('Deve retornar uma nova instância com os arquivos de upload passados', function () {
    $up1     = new UploadedFile('batata', 1024, UPLOAD_ERR_OK);
    $up2     = new UploadedFile('tomate', 1024, UPLOAD_ERR_OK);

    expect($up1)->toBeInstanceOf(UploadedFileInterface::class);
    expect($up2)->toBeInstanceOf(UploadedFileInterface::class);

    $ups = [$up1, $up2];

    $request = new ServerRequest();

    expect($request->getUploadedFiles())->toBe([]);

    expect($request->withUploadedFiles($ups)->getUploadedFiles())->toBe($ups);
});

test('Deve estourar um erro ao passar algo que não seja um UploadedFileInterface', function () {
    $request = new ServerRequest();

    expect(fn () => $request->withUploadedFiles(['any_value']))->toThrow(InvalidArgumentException::class);
});
