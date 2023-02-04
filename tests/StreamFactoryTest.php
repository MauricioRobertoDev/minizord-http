<?php

use Minizord\Http\Factory\StreamFactory;
use Psr\Http\Message\StreamInterface;

test('Deve criar uma Stream passando o conteúdo', function () {
    $factory = new StreamFactory();
    $stream  = $factory->createStream('batata');

    expect($stream)->toBeInstanceOf(StreamInterface::class);
    expect($stream->getContents())->toBe('batata');
});

test('Deve criar uma Stream passando o filename', function () {
    $factory          = new StreamFactory();
    $tempFileName     = tempnam(sys_get_temp_dir(), 'for-test');
    $stream           = $factory->createStreamFromFile($tempFileName);

    expect($stream)->toBeInstanceOf(StreamInterface::class);
});

test('Deve criar uma Stream passando o resource', function () {
    $factory          = new StreamFactory();
    $tempFileName     = tempnam(sys_get_temp_dir(), 'for-test');
    $resource         = fopen($tempFileName, 'w+b');
    $stream           = $factory->createStreamFromResource($resource);

    expect($stream)->toBeInstanceOf(StreamInterface::class);
});

test('Deve estourar um erro caso não seja possível abrir o arquivo', function () {
    $factory = new StreamFactory();

    expect(fn () => $factory->createStreamFromFile('non/exists/file.txt'))->toThrow(InvalidArgumentException::class);
});

test('Deve estourar um erro caso passe algo que não é um resource', function () {
    $factory          = new StreamFactory();

    expect(fn () =>  $factory->createStreamFromResource('string'))->toThrow(InvalidArgumentException::class);
});
