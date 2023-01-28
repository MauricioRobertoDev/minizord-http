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

test('Deve retornar o (path) da url correto', function () {
    $url = 'http://example.com/path';
    $uri = new Uri($url);
    expect($uri->getPath())->toBe('%2Fpath');

    $url = 'http://example.com/path/';
    $uri = new Uri($url);
    expect($uri->getPath())->toBe('%2Fpath%2F');

    $url = 'http://example.com/';
    $uri = new Uri($url);
    expect($uri->getPath())->toBe('%2F');

    $url = 'http://example.com';
    $uri = new Uri($url);
    expect($uri->getPath())->toBe('');

    $url = 'http://example.com/path/path';
    $uri = new Uri($url);
    expect($uri->getPath())->toBe('%2Fpath%2Fpath');

    $url = 'http://example.com/path/path/';
    $uri = new Uri($url);
    expect($uri->getPath())->toBe('%2Fpath%2Fpath%2F');
});

test('Deve retornar uma nova instância com o (scheme) passado', function () {
    $url = 'http://example.com/path?arg=value#fragment';
    $uri = new Uri($url);

    expect($uri)->toBe($uri);

    expect($uri->withScheme(''))->not()->toBe($uri);
    expect($uri->withScheme('')->getScheme())->toBe('');

    expect($uri->withScheme('http'))->not()->toBe($uri);
    expect($uri->withScheme('http')->getScheme())->toBe('http');

    expect($uri->withScheme('https'))->not()->toBe($uri);
    expect($uri->withScheme('https')->getScheme())->toBe('https');

    expect(fn () => $uri->withScheme('invalid_scheme'))->toThrow(InvalidArgumentException::class);
});

test('Deve retornar uma nova instância com o (user info) passado', function () {
    $url = 'http://username:password@example.com/path?arg=value#fragment';
    $uri = new Uri($url);

    expect($uri)->toBe($uri);

    expect($uri->withUserInfo(''))->not()->toBe($uri);
    expect($uri->withUserInfo('')->getUserInfo())->toBe('');

    expect($uri->withUserInfo('user'))->not()->toBe($uri);
    expect($uri->withUserInfo('user')->getUserInfo())->toBe('user');

    expect($uri->withUserInfo('user', 'pass'))->not()->toBe($uri);
    expect($uri->withUserInfo('user', 'pass')->getUserInfo())->toBe('user:pass');

    expect($uri->withUserInfo('', 'pass'))->not()->toBe($uri);
    expect($uri->withUserInfo('', 'pass')->getUserInfo())->toBe('');
});

test('Deve retornar uma nova instância com o (host) passado', function () {
    $url = 'http://example.com/path?arg=value#fragment';
    $uri = new Uri($url);

    expect($uri)->toBe($uri);

    expect($uri->withHost('domain.com'))->not()->toBe($uri);
    expect($uri->withHost('domain.com')->getHost())->toBe('domain.com');

    expect($uri->withHost(''))->not()->toBe($uri);
    expect($uri->withHost('')->getHost())->toBe('');

    expect(fn () => $uri->withHost(14444))->toThrow(InvalidArgumentException::class);
});
