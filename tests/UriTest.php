<?php

use Minizord\Http\Uri;
use Psr\Http\Message\UriInterface;

/*
 * Instâncialização
 */
test('Deve ser uma instância da UriInterface', function () {
    $url = 'HTTPs://domain.com.br/nothing/?query=query_value#fragment';
    $uri = new Uri($url);

    expect($uri)->toBeInstanceOf(UriInterface::class);
    expect($uri->getHost())->toBe('domain.com.br');
    expect($uri->getFragment())->toBe('fragment');
    expect($uri->getQuery())->toBe('query=query_value');
    expect($uri->getPath())->toBe('/nothing/');
    expect($uri->getUserInfo())->toBe('');
    expect($uri->getScheme())->toBe('https');
    expect($uri->getPort())->toBe(null);
});

/*
 * getScheme()
 */
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

/*
 * getHost()
 */
test('Deve retornar o (host) da url correto e normalizado', function () {
    $url = 'https://domain.com.br/nothing/?query=query_value#fragment';
    $uri = new Uri($url);
    expect($uri->getHost())->toBe('domain.com.br');

    $url = 'https://domaIN.com/nothing/?query=query_value#fragment';
    $uri = new Uri($url);
    expect($uri->getHost())->toBe('domain.com');

    $url = '/nothing/?query=query_value#fragment';
    $uri = new Uri($url);
    expect($uri->getHost())->toBe('');
});

/*
 * getUserInfo()
 */
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

/*
 * getPort()
 */
test('Deve retornar o (port) da url correto', function () {
    $url = 'http://username:password@hostname:9090/path?arg=value#anchor';
    $uri = new Uri($url);
    expect($uri->getPort())->toBe(9090);

    $url = 'http://hostname:4444/path?arg=value#anchor';
    $uri = new Uri($url);
    expect($uri->getPort())->toBe(4444);

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

test('Deve null caso a porta seja a porta padrão do scheme', function () {
    $url = 'http://example:80/';
    $uri = new Uri($url);
    expect($uri->getPort())->toBe(null);

    $url = 'https://example:443/';
    $uri = new Uri($url);
    expect($uri->getPort())->toBe(null);
});

/*
 * getQuery()
 */
test('Deve retornar o (query) da url correto', function () {
    $url = 'http://username:password@hostname:9090/path?arg=value#anchor';
    $uri = new Uri($url);
    expect($uri->getQuery())->toBe('arg=value');

    $url = 'http://hostname:4444/path?arg=value&arg2=value2#anchor';
    $uri = new Uri($url);
    expect($uri->getQuery())->toBe('arg=value&arg2=value2');

    $url = 'http://hostname:4444/path#anchor';
    $uri = new Uri($url);
    expect($uri->getQuery())->toBe('');
});

/*
 * getFragment()
 */
test('Deve retornar o (fragment) da url correto', function () {
    $url = 'http://username:password@hostname:9090/path?arg=value#anchor';
    $uri = new Uri($url);
    expect($uri->getFragment())->toBe('anchor');

    $url = 'http://hostname:4444/path?arg=value&arg2=value2#anchõr';
    $uri = new Uri($url);
    expect($uri->getFragment())->toBe('anch%C3%B5r');
});

/*
 * getAuthority()
 */
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

/*
 * getPath()
 */
test('Deve retornar o (path) da url correto', function () {
    $url = 'http://example.com%2Fpath';
    $uri = new Uri($url);
    expect($uri->getPath())->toBe('/path');

    $url = 'http://example.com/path';
    $uri = new Uri($url);
    expect($uri->getPath())->toBe('/path');

    $url = 'http://example.com/path/';
    $uri = new Uri($url);
    expect($uri->getPath())->toBe('/path/');

    $url = 'http://example.com/';
    $uri = new Uri($url);
    expect($uri->getPath())->toBe('/');

    $url = 'http://example.com';
    $uri = new Uri($url);
    expect($uri->getPath())->toBe('');

    $url = 'http://example.com/path/path';
    $uri = new Uri($url);
    expect($uri->getPath())->toBe('/path/path');

    $url = 'http://example.com/path/path/';
    $uri = new Uri($url);
    expect($uri->getPath())->toBe('/path/path/');
});

/*
 * withScheme()
 */
test('Deve retornar uma nova instância com o (scheme) passado', function () {
    $url = 'http://example.com/path?arg=value#fragment';
    $uri = new Uri($url);

    expect($uri)->toBe($uri);

    expect($uri->withScheme(''))->not()->toBe($uri);
    expect($uri->withScheme('')->getScheme())->toBe('');

    expect($uri->withScheme('http'))->not()->toBe($uri);
    expect($uri->withScheme('hTtp')->getScheme())->toBe('http');

    expect($uri->withScheme('https'))->not()->toBe($uri);
    expect($uri->withScheme('htTps')->getScheme())->toBe('https');
});

test('Deve estourar um erro caso passe um scheme inválido', function () {
    $url = 'http://example.com/path?arg=value#fragment';
    $uri = new Uri($url);

    expect($uri)->toBe($uri);
    expect(fn () => $uri->withScheme('invalid_scheme'))->toThrow(InvalidArgumentException::class);
    expect(fn () => $uri->withScheme(888))->toThrow(InvalidArgumentException::class);
});

/*
 * withUserInfo()
 */
test('Deve retornar uma nova instância com o (user info) passado', function () {
    $url = 'http://username:password@example.com/path?arg=value#fragment';
    $uri = new Uri($url);

    expect($uri)->toBe($uri);

    expect($uri->withUserInfo('user'))->not()->toBe($uri);
    expect($uri->withUserInfo('user')->getUserInfo())->toBe('user');

    expect($uri->withUserInfo('user', null))->not()->toBe($uri);
    expect($uri->withUserInfo('user', null)->getUserInfo())->toBe('user');

    expect($uri->withUserInfo('user', 'pass'))->not()->toBe($uri);
    expect($uri->withUserInfo('user', 'pass')->getUserInfo())->toBe('user:pass');

    expect($uri->withUserInfo('usEr', 'p4ss$'))->not()->toBe($uri);
    expect($uri->withUserInfo('usEr', 'p4ss$')->getUserInfo())->toBe('usEr:p4ss$');

    expect($uri->withUserInfo('', 'p4ss$'))->not()->toBe($uri);
    expect($uri->withUserInfo('', 'p4ss$')->getUserInfo())->toBe('');
});

test('Deve estourar um error caso passe um usuário ou senha inválidos', function () {
    $url = 'http://username:password@example.com/path?arg=value#fragment';
    $uri = new Uri($url);

    expect($uri)->toBe($uri);
    expect(fn () => $uri->withUserInfo(null, 'p4ss$'))->toThrow(InvalidArgumentException::class);
    expect(fn () => $uri->withUserInfo(888, 'p4ss$'))->toThrow(InvalidArgumentException::class);
    expect(fn () => $uri->withUserInfo('user', 888))->toThrow(InvalidArgumentException::class);
});

/*
 * withHost()
 */
test('Deve retornar uma nova instância com o (host) passado', function () {
    $url = 'http://example.com/path?arg=value#fragment';
    $uri = new Uri($url);

    expect($uri)->toBe($uri);

    expect($uri->withHost('domain.com'))->not()->toBe($uri);
    expect($uri->withHost('domain.com')->getHost())->toBe('domain.com');

    expect($uri->withHost(''))->not()->toBe($uri);
    expect($uri->withHost('')->getHost())->toBe('');
});

test('Deve  estourar um erro caso tenha passado um host inválido', function () {
    $url = 'http://example.com/path?arg=value#fragment';
    $uri = new Uri($url);

    expect($uri)->toBe($uri);
    expect(fn () => $uri->withHost(14444))->toThrow(InvalidArgumentException::class);
    expect(fn () => $uri->withHost(null))->toThrow(InvalidArgumentException::class);
});

/*
 * withPort()
 */
test('Deve retornar uma nova instância com o (port) passado', function () {
    $url = 'http://example.com/path?arg=value#fragment';
    $uri = new Uri($url);

    expect($uri)->toBe($uri);

    expect($uri->withPort('any_string'))->not()->toBe($uri);
    expect($uri->withPort('any_string')->getPort())->toBe(0);

    expect($uri->withPort('80'))->not()->toBe($uri);
    expect($uri->withPort('80')->getPort())->toBe(null);

    expect($uri->withPort(80))->not()->toBe($uri);
    expect($uri->withPort(80)->getPort())->toBe(null);

    expect($uri->withPort('8080'))->not()->toBe($uri);
    expect($uri->withPort('8080')->getPort())->toBe(8080);

    expect($uri->withPort(8080))->not()->toBe($uri);
    expect($uri->withPort(8080)->getPort())->toBe(8080);
});

test('Deve estourar um erro caso passe uma porta inválida', function () {
    $url = 'http://example.com/path?arg=value#fragment';
    $uri = new Uri($url);

    expect($uri)->toBe($uri);
    expect(fn () => $uri->withPort(-1))->toThrow(InvalidArgumentException::class);
    expect(fn () => $uri->withPort(65536))->toThrow(InvalidArgumentException::class);
});

/*
 * withPath()
 */
test('Deve retornar uma nova instância com o (path) passado', function () {
    $url = 'http://example.com/path?arg=value#fragment';
    $uri = new Uri($url);

    expect($uri)->toBe($uri);

    expect($uri->withPath('any_string'))->not()->toBe($uri);
    expect($uri->withPath('any_string')->getPath())->toBe('any_string');

    expect($uri->withPath('/any_string'))->not()->toBe($uri);
    expect($uri->withPath('/any_string')->getPath())->toBe('/any_string');

    expect($uri->withPath('any string'))->not()->toBe($uri);
    expect($uri->withPath('any string')->getPath())->toBe('any%20string');

    expect($uri->withPath('any%20string'))->not()->toBe($uri);
    expect($uri->withPath('any%20string')->getPath())->toBe('any%20string');

    expect($uri->withPath(''))->not()->toBe($uri);
    expect($uri->withPath('')->getPath())->toBe('');
});

test('Deve estourar um erro caso passe um path inválido', function () {
    $url = 'http://example.com/path?arg=value#fragment';
    $uri = new Uri($url);

    expect($uri)->toBe($uri);
    expect(fn () => $uri->withPath(null))->toThrow(InvalidArgumentException::class);
    expect(fn () => $uri->withPath(65536))->toThrow(InvalidArgumentException::class);
});

/*
 * withQuery()
 */
test('Deve retornar uma nova instância com a query string passada', function () {
    $url = 'http://example.com/path?arg=value#fragment';
    $uri = new Uri($url);

    expect($uri)->toBe($uri);
    expect($uri->getQuery())->toBe('arg=value');

    expect($uri->withQuery('?arg=value'))->not()->toBe($uri);
    expect($uri->withQuery('?arg=value')->getQuery())->toBe('arg=value');

    expect($uri->withQuery('arg=value'))->not()->toBe($uri);
    expect($uri->withQuery('arg=value')->getQuery())->toBe('arg=value');

    expect($uri->withQuery('arg = value'))->not()->toBe($uri);
    expect($uri->withQuery('arg = value')->getQuery())->toBe('arg%20=%20value');

    expect($uri->withQuery('arg%20%3D%20value'))->not()->toBe($uri);
    expect($uri->withQuery('arg%20%3D%20value')->getQuery())->toBe('arg%20%3D%20value');

    expect($uri->withQuery(''))->not()->toBe($uri);
    expect($uri->withQuery('')->getQuery())->toBe('');
});

test('Deve estourar um erro caso passe uma query string inválida', function () {
    $url = 'http://example.com/path?arg=value#fragment';
    $uri = new Uri($url);

    expect($uri)->toBe($uri);
    expect(fn () => $uri->withQuery(null))->toThrow(InvalidArgumentException::class);
    expect(fn () => $uri->withQuery(65536))->toThrow(InvalidArgumentException::class);
});

/*
 * withFragment()
 */
test('Deve retornar uma nova instância com o fragment passado', function () {
    $url = 'http://example.com/path?arg=value#fragment';
    $uri = new Uri($url);

    expect($uri)->toBe($uri);
    expect($uri->getFragment())->toBe('fragment');

    expect($uri->withFragment('#fragment'))->not()->toBe($uri);
    expect($uri->withFragment('#fragment')->getFragment())->toBe('fragment');

    expect($uri->withFragment('fragment'))->not()->toBe($uri);
    expect($uri->withFragment('fragment')->getFragment())->toBe('fragment');

    expect($uri->withFragment('fragment fragment'))->not()->toBe($uri);
    expect($uri->withFragment('fragment fragment')->getFragment())->toBe('fragment%20fragment');

    expect($uri->withFragment('fragment%20fragment'))->not()->toBe($uri);
    expect($uri->withFragment('fragment%20fragment')->getFragment())->toBe('fragment%20fragment');

    expect($uri->withFragment(''))->not()->toBe($uri);
    expect($uri->withFragment('')->getFragment())->toBe('');

    expect(fn () => $uri->withFragment(65536))->toThrow(InvalidArgumentException::class);
});

test('Deve estourar um erro caso passe um fragment inválida', function () {
    $url = 'http://example.com/path?arg=value#fragment';
    $uri = new Uri($url);

    expect($uri)->toBe($uri);
    expect(fn () => $uri->withFragment(null))->toThrow(InvalidArgumentException::class);
    expect(fn () => $uri->withFragment(65536))->toThrow(InvalidArgumentException::class);
});

/*
 * __toString()
 */
test('Deve retornar a string da uri', function () {
    $url = 'http://example.com/path?arg=value#fragment';
    $uri = new Uri($url);
    expect((string) $uri)->toBe('http://example.com/path?arg=value#fragment');

    $url = 'http://example.com/path/path crazy?arg=value crazy#fragment crazy';
    $uri = new Uri($url);
    expect((string) $uri)->toBe('http://example.com/path/path%20crazy?arg=value%20crazy#fragment%20crazy');

    $url = 'http://example.com:8080/path/path?arg=value#fragment';
    $uri = new Uri($url);
    expect((string) $uri)->toBe('http://example.com:8080/path/path?arg=value#fragment');

    $url = 'http://example.com:80/path/path?arg=value#fragment';
    $uri = new Uri($url);
    expect((string) $uri)->toBe('http://example.com/path/path?arg=value#fragment');

    $url = 'http://user:pass@example.com:80/path/path?arg=value#fragment';
    $uri = new Uri($url);
    expect((string) $uri)->toBe('http://user:pass@example.com/path/path?arg=value#fragment');

    $url = 'http://:pass@example.com:80/path/path?arg=value#fragment';
    $uri = new Uri($url);
    expect((string) $uri)->toBe('http://example.com/path/path?arg=value#fragment');
});
