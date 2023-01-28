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

    $url = 'username:password@hostname:9090/';
    $uri = new Uri($url);
    expect($uri->getUserInfo())->toBe('');
});

test('Deve retornar o (port) da url correto', function () {
    $url = 'http://username:password@hostname:9090/path?arg=value#anchor';
    $uri = new Uri($url);
    expect($uri->getPort())->toBe(9090);

    $url = 'http://hostname:4444/path?arg=value#anchor';
    $uri = new Uri($url);
    expect($uri->getPort())->toBe(4444);

    $url = 'http://example:80/';
    $uri = new Uri($url);
    expect($uri->getPort())->toBe(null);

    $url = 'https://example:443/';
    $uri = new Uri($url);
    expect($uri->getPort())->toBe(null);

    $url = 'example:8888/';
    $uri = new Uri($url);
    expect($uri->getPort())->toBe(8888);

    $url = 'example.com/';
    $uri = new Uri($url);
    expect($uri->getPort())->toBe(null);

    $url = 'https://example.com/';
    $uri = new Uri($url);
    expect($uri->getPort())->toBe(null);
});

test('Deve retornar o (query) da url correto', function () {
    $url = 'http://username:password@hostname:9090/path?arg=value#anchor';
    $uri = new Uri($url);
    expect($uri->getQuery())->toBe('arg%3Dvalue');

    $url = 'http://hostname:4444/path?arg=value&arg2=value2#anchor';
    $uri = new Uri($url);
    expect($uri->getQuery())->toBe('arg%3Dvalue%26arg2%3Dvalue2');

    $url = 'http://hostname:4444/path#anchor';
    $uri = new Uri($url);
    expect($uri->getQuery())->toBe('');
});

test('Deve retornar o (fragment) da url correto', function () {
    $url = 'http://username:password@hostname:9090/path?arg=value#anchor';
    $uri = new Uri($url);
    expect($uri->getFragment())->toBe('anchor');

    $url = 'http://hostname:4444/path?arg=value&arg2=value2#anchõr';
    $uri = new Uri($url);
    expect($uri->getFragment())->toBe('anch%C3%B5r');
});

test('Deve retornar o (authority) da url correto', function () {
    $url = 'http://username:password@hostname:9090/';
    $uri = new Uri($url);
    expect($uri->getAuthority())->toBe('username:password@hostname:9090');

    $url = 'http://username@hostname:9090/';
    $uri = new Uri($url);
    expect($uri->getAuthority())->toBe('username@hostname:9090');

    $url = 'http://:password@hostname:9090/';
    $uri = new Uri($url);
    expect($uri->getAuthority())->toBe('hostname:9090');

    $url = 'http://username:password@hostname:80/';
    $uri = new Uri($url);
    expect($uri->getAuthority())->toBe('username:password@hostname');

    $url = 'https://username:password@hostname:443/';
    $uri = new Uri($url);
    expect($uri->getAuthority())->toBe('username:password@hostname');
});
