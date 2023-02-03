<?php

use Minizord\Http\Stream;
use Psr\Http\Message\StreamInterface as PsrStreamInterface;

/*
 * Instâncialização
 */
test('Deve instânciar uma classe stream passando nada', function () {
    $stream = new Stream();

    expect($stream)->toBeInstanceOf(PsrStreamInterface::class);
    expect($stream->hasStream())->toBeTrue();
    expect($stream->getSize())->toBe(0);
    expect($stream->isReadable())->toBeTrue();
    expect($stream->isWritable())->toBeTrue();
    expect($stream->isSeekable())->toBeTrue();
    expect($stream->getContents())->toBe('');
    expect($stream->getMetadata('wrapper_type'))->toBe('PHP');
    expect($stream->getMetadata('stream_type'))->toBe('TEMP');
    expect($stream->getMetadata('uri'))->toBe('php://temp');
});

test('Deve instânciar uma classe stream passando uma string de dados', function () {
    $stream = new Stream('batata');

    expect($stream)->toBeInstanceOf(PsrStreamInterface::class);
    expect($stream->hasStream())->toBeTrue();
    expect($stream->getSize())->toBe(6);
    expect($stream->isReadable())->toBeTrue();
    expect($stream->isWritable())->toBeTrue();
    expect($stream->isSeekable())->toBeTrue();
    expect($stream->getMetadata('wrapper_type'))->toBe('PHP');
    expect($stream->getMetadata('stream_type'))->toBe('TEMP');
    expect($stream->getMetadata('uri'))->toBe('php://temp');
    expect((string) $stream)->toBe('batata');
});

test('Deve instânciar uma classe stream passando um resource', function () {
    $tempFileName     = tempnam(sys_get_temp_dir(), 'for-test');
    $resource         = fopen($tempFileName, 'rw+');
    $stream           = new Stream($resource);

    expect($stream)->toBeInstanceOf(PsrStreamInterface::class);
    expect($stream->hasStream())->toBeTrue();
    expect($stream->getSize())->toBe(0);
    expect($stream->isReadable())->toBeTrue();
    expect($stream->isWritable())->toBeTrue();
    expect($stream->isSeekable())->toBeTrue();
    expect($stream->getMetadata('wrapper_type'))->toBe('plainfile');
    expect($stream->getMetadata('stream_type'))->toBe('STDIO');
    expect($stream->getMetadata('uri'))->toBe($tempFileName);
    expect((string) $stream)->toBe('');
});

test('Deve estourar um erro caso passe um argumento inválido', function () {
    expect(fn () => new Stream(888))->toThrow(InvalidArgumentException::class);
    expect(fn () => new Stream(null))->toThrow(InvalidArgumentException::class);
});

/*
 * getMetadata()
 */
test('Deve retornar a (metadata) da stream', function () {
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

test('Deve retornar um array vazio caso não tenha um resource', function () {
    $stream = new Stream('batata');
    $stream->close();

    expect($stream->getMetadata())->toBe([]);
});

/*
 * getContents()
 */
test('Deve retornar uma string com o restante dos dados', function () {
    $stream = new Stream('batata tomate alface');

    expect($stream->getContents())->toBe('batata tomate alface');

    $stream->rewind();
    $stream->read(7);

    expect($stream->getContents())->toBe('tomate alface');

    $stream->rewind();
    $stream->read(14);

    expect($stream->getContents())->toBe('alface');

    $stream->rewind();
    $stream->read(20);

    expect($stream->getContents())->toBe('');
});

test('Deve estourar um erro tente pegar o conteúdo de uma stream que não tenha um resource', function () {
    $stream = new Stream('batata tomate alface');
    $stream->close();

    expect(fn () => $stream->getContents())->toThrow(RuntimeException::class);
});

/*
 * getSize()
 */
test('Deve retornar o tamanho do resource na stream', function () {
    $stream = new Stream('batata');

    expect($stream->getSize())->toBe(6);
});

test('Deve retornar o tamanho como null caso não tenha resource na stream', function () {
    $stream = new Stream('batata');

    expect($stream->getSize())->toBe(6);

    $stream->close();

    expect($stream->getSize())->toBeNull();
});

/*
 * read()
 */
test('Deve ler o conteúdo do resource na stream', function () {
    $stream = new Stream('batata tomate alface');

    expect($stream->isReadable())->toBeTrue();
    expect($stream->read(7))->toBe('batata ');

    $stream->rewind();
    expect($stream->read(13))->toBe('batata tomate');

    $stream->seek(7);
    expect($stream->read(13))->toBe('tomate alface');
});

test('Deve estourar um erro caso o resource na stream não seja legível', function () {
    $tempFileName     = tempnam(sys_get_temp_dir(), 'for-test');
    $resource         = fopen($tempFileName, 'w');
    fwrite($resource, 'batata');
    $stream           = new Stream($resource);

    expect($stream->isReadable())->toBeFalse();
    expect(fn () => $stream->read(13))->toThrow(RuntimeException::class);
});

/*
 * write()
 */
test('Deve escrever o conteúdo no resource da stream', function () {
    $stream = new Stream();

    expect($stream->isWritable())->toBeTrue();
    expect($stream->isReadable())->toBeTrue();

    $stream->write('batata');

    expect((string) $stream)->toBe('batata');

    $stream->seek(6);
    $stream->write(' tomate');

    expect((string) $stream)->toBe('batata tomate');

    $stream->seek(0);
    $stream->write('alface');

    expect((string) $stream)->toBe('alface tomate');
});

test('Deve estourar um errocaso o resource na stream não seja gravável', function () {
    $tempFileName     = tempnam(sys_get_temp_dir(), 'for-test');
    $resource         = fopen($tempFileName, 'r');
    $stream           = new Stream($resource);

    expect($stream->isWritable())->toBeFalse();
    expect(fn () => $stream->write('batata'))->toThrow(RuntimeException::class);
});

/*
 * seek()
 */
test('Deve manipular o ponteiro do resource', function () {
    $stream = new Stream('batata tomate alface');

    $stream->seek(0);

    expect($stream->tell())->toBe(0);

    $stream->seek(7);

    expect($stream->tell())->toBe(7);
});

test('Deve estourar um erro ao tentar manipular o ponteiro sem ter um resource na stream', function () {
    $stream = new Stream('batata tomate alface');

    $stream->seek(0);

    expect($stream->tell())->toBe(0);

    $stream->close();

    expect(fn () => $stream->seek(0))->toThrow(RuntimeException::class);
});

/*
 * eof()
 */
test('Deve retornar true caso não tenha nenhum resource na stream', function () {
    $stream = new Stream('batata');

    expect($stream->eof())->toBeFalse();

    $stream->read(1024);

    expect($stream->eof())->toBeTrue();

    $stream->rewind();

    expect($stream->eof())->toBeFalse();

    $stream->close();

    expect($stream->eof())->toBeTrue();
});

/*
 * tell()
 */
test('Deve retornar a posição atual do ponteiro no resource da stream', function () {
    $stream = new Stream('batata tomate alface'); //20

    expect($stream->tell())->toBe(0);

    $stream->read(6);

    expect($stream->tell())->toBe(6);

    $stream->read(6);

    expect($stream->tell())->toBe(12);

    $stream->read(6);

    expect($stream->tell())->toBe(18);

    $stream->read(6);

    expect($stream->tell())->toBe(20);
});

test('Deve estourar um erro ao tentar buscar a posição atual do ponteiro sem um resource na stream', function () {
    $stream = new Stream('batata tomate alface');
    $stream->read(6);

    expect($stream->tell())->toBe(6);

    $stream->close();

    expect(fn () => $stream->tell())->toThrow(RuntimeException::class);
});

/*
 * detach()
 */
test('Deve remover o resource  da stream e retorna-lo', function () {
    $stream = new Stream('batata tomate alface');

    expect(stream_get_contents($stream->detach()))->toBe('batata tomate alface');
    expect($stream->hasStream())->toBeFalse();
});

test('Deve retornar null caso não exista nenhum resource na stream', function () {
    $stream = new Stream('batata tomate alface');

    $stream->detach();

    expect($stream->hasStream())->toBeFalse();
    expect($stream->detach())->toBeNull();
});

/*
 * close()
 */
test('Deve fechar o resource e remover da stream', function () {
    $stream = new Stream('batata tomate alface');
    $stream->close();

    expect($stream->hasStream())->toBeFalse();
});
