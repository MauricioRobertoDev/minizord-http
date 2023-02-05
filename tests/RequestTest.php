<?php

use Minizord\Http\Request;
use Minizord\Http\Stream;
use Psr\Http\Message\RequestInterface;

/*
 * Instâncialização
 */
test('Deve criar uma nova request', function () {
    $request = new Request('get', 'https://batata.com', []);

    expect($request)->toBeInstanceOf(RequestInterface::class);
    expect($request->getHeader('host'))->toBe(['batata.com']);
    expect($request->getBody())->toBeInstanceOf(Stream::class);
    expect($request->getBody()->getContents())->toBe('');
    expect($request->getRequestTarget())->toBe('/');

    $request = new Request('get', 'https://batata.com/path?search=batata', [], 'batata');

    expect($request)->toBeInstanceOf(RequestInterface::class);
    expect($request->getBody())->toBeInstanceOf(Stream::class);
    expect($request->getBody()->getContents())->toBe('batata');
    expect($request->getRequestTarget())->toBe('/path?search=batata');

    $tempFileName     = tempnam(sys_get_temp_dir(), 'for-test');
    $resource         = fopen($tempFileName, 'rw+b');
    fwrite($resource, 'batatinha');
    $request = new Request('get', 'https://batata.com/path?search=batata#fragment', [], $resource);

    expect($request)->toBeInstanceOf(RequestInterface::class);
    expect($request->getBody())->toBeInstanceOf(Stream::class);
    expect($request->getBody()->getContents())->toBe('batatinha');
    expect($request->getRequestTarget())->toBe('/path?search=batata');
});
