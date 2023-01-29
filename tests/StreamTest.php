<?php

use Minizord\Http\Stream;
use Psr\Http\Message\StreamInterface as PsrStreamInterface;

test('Deve instânciar uma classe stream', function () {
    $stream = new Stream();
    expect($stream)->toBeInstanceOf(PsrStreamInterface::class);

    $stream = new Stream('batata');
    expect($stream)->toBeInstanceOf(PsrStreamInterface::class);
});

test('Deve retornar a (metadata)', function () {
    $stream = new Stream('batata');

    expect($stream->getMetadata())->toBe([
        'wrapper_type' => 'PHP',
        'stream_type'  => 'TEMP',
        'mode'         => 'w+b',
        'unread_bytes' => 0,
        'seekable'     => true,
        'uri'          => 'php://temp',
    ]);
    expect($stream->getMetadata('wrapper_type'))->toBe('PHP');
    expect($stream->getMetadata('mode'))->toBe('w+b');
});
