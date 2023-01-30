<?php

use Minizord\Http\RequestTrait;
use Minizord\Http\Uri;

test('Deve uma nova instância com o método passado', function () {
    $request = new class() {
        use RequestTrait;
    };

    expect($request->withMethod('post')->getMethod())->toBe('post');
    expect($request->withMethod('pOst')->getMethod())->toBe('pOst');
    expect(fn () => $request->withMethod(888)->getMethod())->toThrow(InvalidArgumentException::class);
    expect(fn () => $request->withMethod(null)->getMethod())->toThrow(InvalidArgumentException::class);
    expect(fn () => $request->withMethod(' ')->getMethod())->toThrow(InvalidArgumentException::class);
    expect(fn () => $request->withMethod('@')->getMethod())->toThrow(InvalidArgumentException::class);
});

test('Deve uma nova instância com o request target passado', function () {
    $request = new class() {
        use RequestTrait;
    };

    expect($request->withRequestTarget('/batata')->getRequestTarget())->toBe('/batata');
    expect(fn () =>$request->withRequestTarget('/path/ path'))->toThrow(InvalidArgumentException::class);
});

test('Deve uma nova instância com o a uri passada', function () {
    $request = new class() {
        use RequestTrait;
    };

    $uri = new Uri('https://example.com');
    expect($request->withUri($uri)->getUri())->toBe($uri);

    $request = $request->withHeader('host', 'batatinha.com');
    expect($request->withUri($uri, true)->getUri())->toBe($uri);
    expect($request->withUri($uri, true)->getHeader('host'))->toBe(['batatinha.com']);

    $request = $request->withoutHeader('host');
    $uri     = $uri->withHost('');
    expect($request->withUri($uri, true)->getUri())->toBe($uri);
    expect($request->withUri($uri, true)->getHeader('host'))->toBe([]);

    $uri = $uri->withHost('batata.com')->withPort(8888);
    expect($request->withUri($uri, true)->getHeader('host'))->toBe(['batata.com:8888']);
    expect($request->withUri($uri, false)->getHeader('host'))->toBe(['batata.com:8888']);
});
