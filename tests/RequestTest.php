<?php

use Minizord\Http\Request;
use Minizord\Http\Stream;
use Psr\Http\Message\RequestInterface as PsrRequestInterface;

test('Deve criar uma nova response', function () {
    $request = new Request('get', 'https://batata.com', []);
    expect($request)->toBeInstanceOf(PsrRequestInterface::class);
    expect($request->getHeader('host'))->toBe(['batata.com']);
    expect($request->getBody())->toBeInstanceOf(Stream::class);
    expect($request->getBody()->getContents())->toBe('');

    $request = new Request('get', 'https://batata.com', [], 'batata');
    expect($request)->toBeInstanceOf(PsrRequestInterface::class);
    expect($request->getBody())->toBeInstanceOf(Stream::class);
    expect($request->getBody()->getContents())->toBe('batata');

    $resource = fopen('./tests/for-test.txt', 'rw+b');
    fwrite($resource, 'batatinha');

    $request = new Request('get', 'https://batata.com', [], $resource);
    expect($request)->toBeInstanceOf(PsrRequestInterface::class);
    expect($request->getBody())->toBeInstanceOf(Stream::class);
    expect($request->getBody()->getContents())->toBe('batatinha');
});
