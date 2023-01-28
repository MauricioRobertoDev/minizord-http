<?php

use Minizord\Http\Uri;
use Psr\Http\Message\UriInterface as PsrUriInterface;

test('Deve ser uma instância da PsrUriInterface', function () {
    $url = 'HTTPs://domain.com.br/nothing/?query=query_value#fragment';
    $uri = new Uri($url);

    expect($uri instanceof PsrUriInterface)->toBeTrue();
    expect($uri)->toBeInstanceOf(PsrUriInterface::class);
});

test('Deve retornar o scheme da url correto e normalizado', function () {
    $url = 'HTTPs://domain.com.br/nothing/?query=query_value#fragment';
    $uri = new Uri($url);

    expect($uri->getScheme())->toBe('https');

    $url = 'HTTP://domain.com.br/nothing/?query=query_value#fragment';
    $uri = new Uri($url);

    expect($uri->getScheme())->toBe('http');

    $url = '://domain.com.br/nothing/?query=query_value#fragment';
    $uri = new Uri($url);

    expect($uri->getScheme())->toBe('');
});
