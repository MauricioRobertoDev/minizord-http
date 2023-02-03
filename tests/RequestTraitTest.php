<?php

use Minizord\Http\RequestTrait;
use Minizord\Http\Uri;

/*
 * withMethod()
 */
test('Deve uma nova instância com o método passado', function () {
    $request = new class() {
        use RequestTrait;
    };

    expect($request->withMethod('post')->getMethod())->toBe('post');
    expect($request->withMethod('pOst')->getMethod())->toBe('pOst');
});

test('Deve estourar uma erro caso passe um método inválido', function () {
    $request = new class() {
        use RequestTrait;
    };

    expect(fn () => $request->withMethod(888)->getMethod())->toThrow(InvalidArgumentException::class);
    expect(fn () => $request->withMethod(null)->getMethod())->toThrow(InvalidArgumentException::class);
    expect(fn () => $request->withMethod(' ')->getMethod())->toThrow(InvalidArgumentException::class);
    expect(fn () => $request->withMethod('@')->getMethod())->toThrow(InvalidArgumentException::class);
});

/*
 * withRequestTarget()
 */
test('Deve uma nova instância com o request target passado', function () {
    $request = new class() {
        use RequestTrait;
    };

    expect($request->withRequestTarget('/batata')->getRequestTarget())->toBe('/batata');
    expect($request->withRequestTarget('/batata/?arg=value')->getRequestTarget())->toBe('/batata/?arg=value');
    expect($request->withRequestTarget('/batata?arg=value')->getRequestTarget())->toBe('/batata?arg=value');
});

test('Deve estourar um erro caso passe um target inválido', function () {
    $request = new class() {
        use RequestTrait;
    };

    expect(fn () =>$request->withRequestTarget('/path/ path'))->toThrow(InvalidArgumentException::class);
    expect(fn () =>$request->withRequestTarget(null))->toThrow(InvalidArgumentException::class);
    expect(fn () =>$request->withRequestTarget(888))->toThrow(InvalidArgumentException::class);
});

/*
 * withUri()
 */
test('Deve uma nova instância com a uri passada e setando o host da uri na request', function () {
    $request = new class() {
        use RequestTrait;
    };
    $request = $request->withHeader('host', 'batatinha.com');

    $uri = new Uri('https://example.com');
    $uri = $uri->withHost('tomatinho.com');

    expect($request->withUri($uri)->getUri())->toBe($uri);
    expect($request->withUri($uri)->getHeader('host'))->toBe(['tomatinho.com']);
});

test('Deve retornar uma nova instância com a nova uri e preservando o host atual da request', function () {
    $request = new class() {
        use RequestTrait;
    };

    $uri     = new Uri('https://example.com');
    $request = $request->withHeader('host', 'batatinha.com');

    expect($request->withUri($uri, true)->getUri())->toBe($uri);
    expect($request->withUri($uri, true)->getHeader('host'))->toBe(['batatinha.com']);
});

test('Deve retornar uma nova instância com a nova uri e sem nenhum host pois nenhum tem', function () {
    $request = new class() {
        use RequestTrait;
    };

    $uri     = new Uri('https://example.com');
    $uri     = $uri->withHost('');

    expect($request->withUri($uri, true)->getUri())->toBe($uri);
    expect($request->withUri($uri, true)->getHeader('host'))->toBe([]);
});

test('Deve retornar uma nova instância com a nova uri com host da api já que a request não tem', function () {
    $request = new class() {
        use RequestTrait;
    };

    $uri     = new Uri('https://example.com');
    $uri     = $uri->withHost('batata.com')->withPort(8888);

    expect($request->withUri($uri, true)->getHeader('host'))->toBe(['batata.com:8888']);
    expect($request->withUri($uri, false)->getHeader('host'))->toBe(['batata.com:8888']);
});
