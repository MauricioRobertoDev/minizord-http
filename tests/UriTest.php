<?php

use Minizord\Http\Uri;
use Psr\Http\Message\UriInterface as PsrUriInterface;

test('Deve ser uma instância da PsrUriInterface', function () {
    $url = 'HTTPs://domain.com.br/nothing/?query=query_value#fragment';
    $uri = new Uri($url);

    expect($uri instanceof PsrUriInterface)->toBeTrue();
    expect($uri)->toBeInstanceOf(PsrUriInterface::class);
});

test('Deve retornar o (scheme) da url correto e normalizado', function () {
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

test('Deve retornar o (host) da url correto e normalizado', function () {
    $url = 'https://domain.com.br/nothing/?query=query_value#fragment';
    $uri = new Uri($url);

    expect($uri->getHost())->toBe('domain.com.br');

    $url = 'https://domain.com/nothing/?query=query_value#fragment';
    $uri = new Uri($url);

    expect($uri->getHost())->toBe('domain.com');

    $url = '/nothing/?query=query_value#fragment';
    $uri = new Uri($url);

    expect($uri->getHost())->toBe('');
});

test('Deve retornar o (user info) da url correto', function () {
    $url = 'http://username:password@hostname:9090/path?arg=value#anchor';
    $uri = new Uri($url);

    expect($uri)->toBeInstanceOf(PsrUriInterface::class);

    expect($uri->getUserInfo())->toBe('username:password');

    $url = 'http://UsernAme:PassW0rd@example.com/';
    $uri = new Uri($url);

    expect($uri->getUserInfo())->toBe('UsernAme:PassW0rd');

    $url = 'http://UsernAme@example.com/';
    $uri = new Uri($url);
    expect($uri->getUserInfo())->toBe('UsernAme');

    $url = 'http://:password@example.com/';
    $uri = new Uri($url);
    expect($uri->getUserInfo())->toBe('');
});
