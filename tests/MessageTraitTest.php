<?php

use Minizord\Http\MessageTrait;

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
    expect($message->withProtocolVersion('12.8'))->not()->toBe($message);
    expect($message->withProtocolVersion('12.8')->getProtocolVersion())->toBe('12.8');
});

test('Deve retornar os (headers) da requisição', function () {
    $message = new class() {
        use MessageTrait;
    };

    expect($message->getHeaders())->toBe([]);
    expect($message->withHeader('any_name', ['any_value'])->getHeaders())->toBe(['any_name' => ['any_value']]);
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
    expect($message->withHeader('any_name_2', 'any_value_2')->getHeaders())->toBe(['any_name_2' => ['any_value_2']]);
    expect($message->withHeader('any_name_2', '')->getHeaders())->toBe(['any_name_2' => ['']]);
    expect(fn () => $message->withHeader('any_name_3', 888))->toThrow(InvalidArgumentException::class);
    expect(fn () => $message->withHeader(888, 'any_value_3'))->toThrow(InvalidArgumentException::class);
});

test('Deve retornar uma nova instância sem o (header) passado', function () {
    $message = new class() {
        use MessageTrait;
    };

    expect($message->getHeaders())->toBe([]);

    $message = $message->withHeader('any_name', ['any_value']);
    expect($message->getHeaders())->toBe(['any_name' => ['any_value']]);

    expect($message->withoutHeader('any_name')->getHeaders())->toBe([]);
});
