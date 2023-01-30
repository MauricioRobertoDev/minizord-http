<?php

use Minizord\Http\MessageTrait;
use Minizord\Http\Stream;
use Psr\Http\Message\StreamInterface as PsrStreamInterface;

test('Deve retornar o (protocol version) da requisição', function () {
    $message = new class() {
        use MessageTrait;
    };

    expect($message->getProtocolVersion())->toBe('1.1');
});

test('Deve retornar uma nova instância com o (protocol version) passado', function () {
    $message = new class() {
        use MessageTrait;
    };

    expect($message->getProtocolVersion())->toBe('1.1');
    expect($message->withProtocolVersion('1.0'))->not()->toBe($message);
    expect($message->withProtocolVersion('1.0')->getProtocolVersion())->toBe('1.0');
    expect(fn () => $message->withProtocolVersion('12.8'))->toThrow(InvalidArgumentException::class);
    expect(fn () => $message->withProtocolVersion(''))->toThrow(InvalidArgumentException::class);
    expect(fn () => $message->withProtocolVersion(' '))->toThrow(InvalidArgumentException::class);
});

test('Deve retornar os (headers) da requisição', function () {
    $message = new class() {
        use MessageTrait;
    };

    expect($message->getHeaders())->toBe([]);
    expect($message->withHeader('any_name', ['any_value'])->getHeaders())->toBe(['any_name' => ['any_value']]);
});

test('Deve retornar os dados de determinado (header) da requisição', function () {
    $message = new class() {
        use MessageTrait;
    };

    expect($message->getHeaders())->toBe([]);
    expect($message->withHeader('any_name', ['any_value'])->getHeader('any_name'))->toBe(['any_value']);
    expect($message->withHeader('aNY_nAme', ['any_value'])->getHeader('any_name'))->toBe(['any_value']);
    expect(fn () => $message->withHeader('any:name', ['any_value']))->toThrow(InvalidArgumentException::class);
});

test('Deve retornar se determinado (header) existe na requisição', function () {
    $message = new class() {
        use MessageTrait;
    };

    expect($message->hasHeader('any_header'))->toBeFalse();
    expect($message->withHeader('any_name', ['any_value'])->hasHeader('any_name'))->toBeTrue();
});

test('Deve retornar os valores de determinado (header) em uma string', function () {
    $message = new class() {
        use MessageTrait;
    };

    expect($message->getHeaderLine('any_header'))->toBe('');
    expect($message->withHeader('any_name', ['any_value'])->getHeaderLine('any_name'))->toBe('any_value');
});

test('Deve retornar uma nova instância com o (header) passado', function () {
    $message = new class() {
        use MessageTrait;
    };

    expect($message->getHeaders())->toBe([]);
    expect($message->withHeader('any_name', ['any_value'])->getHeaders())->toBe(['any_name' => ['any_value']]);
    expect($message->withHeader('aNY_nAme_2', 'pplication/json')->getHeaders())->toBe(['aNY_nAme_2' => ['pplication/json']]);
    expect($message->withHeader('any_name_3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.93 Safari/537.36')->getHeaders())->toBe(['any_name_3' => ['Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.93 Safari/537.36']]);
    expect(fn () => $message->withHeader('any_name_2', ''))->toThrow(InvalidArgumentException::class);
    expect(fn () => $message->withHeader('any_name_2', ' '))->toThrow(InvalidArgumentException::class);
    expect(fn () => $message->withHeader('any_name_3', 888))->toThrow(InvalidArgumentException::class);
    expect(fn () => $message->withHeader(888, 'any_value_3'))->toThrow(InvalidArgumentException::class);
    expect(fn () => $message->withHeader('any_name', "gzip, deflate\r\n"))->toThrow(InvalidArgumentException::class);
});

test('Deve retornar uma nova instância com o (header) passado e adicionado os novos valores', function () {
    $message = new class() {
        use MessageTrait;
    };

    expect($message->getHeaders())->toBe([]);
    $message = $message->withAddedHeader('any_name_1', ['any_value_1']);
    expect($message->getHeaders())->toBe(['any_name_1' => ['any_value_1']]);
    expect($message->withAddedHeader('any_name_1', ['any_value_2'])->getHeaders())->toBe(['any_name_1' => ['any_value_1', 'any_value_2']]);
    expect(fn () => $message->withAddedHeader('any_name_2', ''))->toThrow(InvalidArgumentException::class);
    expect(fn () => $message->withAddedHeader('any_name_2', ' '))->toThrow(InvalidArgumentException::class);
    expect(fn () => $message->withAddedHeader('any_name_3', 888))->toThrow(InvalidArgumentException::class);
    expect(fn () => $message->withAddedHeader(888, 'any_value_3'))->toThrow(InvalidArgumentException::class);
    expect(fn () => $message->withAddedHeader('any_name', "gzip, deflate\r\n"))->toThrow(InvalidArgumentException::class);
});

test('Deve retornar uma nova instância sem o (header) passado', function () {
    $message = new class() {
        use MessageTrait;
    };

    expect($message->getHeaders())->toBe([]);
    $message = $message->withAddedHeader('any_name_1', ['any_value_1']);
    expect($message->getHeaders())->toBe(['any_name_1' => ['any_value_1']]);
    expect($message->withoutHeader('any_name_1')->getHeaders())->toBe([]);
});

test('Deve retornar a stream', function () {
    $message = new class() {
        use MessageTrait;
    };

    expect($message->getBody())->toBeInstanceOf(PsrStreamInterface::class);

    $message = $message->withBody(new Stream('batata'));

    expect($message->getBody())->toBeInstanceOf(PsrStreamInterface::class);
});
