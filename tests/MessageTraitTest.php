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
