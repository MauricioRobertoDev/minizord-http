<?php

use Minizord\Http\Helper\FWraper;
use Minizord\Http\Stream;
use Psr\Http\Message\StreamInterface as PsrStreamInterface;

test('Deve instânciar uma classe stream', function () {
    $stream = new Stream();
    expect($stream)->toBeInstanceOf(PsrStreamInterface::class);

    $stream = new Stream('batata');
    expect($stream)->toBeInstanceOf(PsrStreamInterface::class);

    expect(fn () => new Stream(888))->toThrow(InvalidArgumentException::class);
    expect(fn () => new Stream(null))->toThrow(InvalidArgumentException::class);

    $mock = mock(new FWraper())->shouldReceive('stream_get_meta_data')->andReturn(['seekable' => false, 'mode'     => 'w+b'])->getMock();
    expect(fn () => new Stream('batata', $mock))->toThrow(RuntimeException::class);
});

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
    $stream->close();
    expect($stream->getMetadata('mode'))->toBe(null);
});

test('Deve retornar a (content) da stream', function () {
    $stream = new Stream('batata');
    expect($stream->getContents())->toBe('batata');

    $stream->write(' tomate');
    $stream->rewind();
    expect($stream->getContents())->toBe('batata tomate');

    $stream->close();
    expect(fn () => $stream->getContents())->toThrow(RuntimeException::class);
});

test('Deve retornar o (size) da stream', function () {
    $stream = new Stream('batata');

    expect($stream->getSize())->toBe(6);

    $stream->read(6);
    $stream->write(' tomate');
    $stream->rewind();

    expect($stream->getContents())->toBe('batata tomate');
    expect($stream->getSize())->toBe(13);

    $stream->close();
    expect($stream->getSize())->toBe(null);
});

test('Deve ler o conteúdo da stream', function () {
    $stream = new Stream('batata tomate alface');
    expect($stream->read(7))->toBe('batata ');

    $stream->rewind();
    expect($stream->read(13))->toBe('batata tomate');

    $stream->seek(7);
    expect($stream->read(13))->toBe('tomate alface');

    $stream = new Stream(fopen('./tests/for-test.txt', 'w'));
    expect(fn () => $stream->read(13))->toThrow(RuntimeException::class);

    $mock = mock(new FWraper())
        ->shouldReceive('fread')
        ->once()
        ->andReturn(false)
        ->getMock();

    $stream = new Stream(fopen('./tests/for-test.txt', 'w+'), $mock);

    expect(fn () => $stream->read(13))->toThrow(RuntimeException::class);
});

test('Deve retornar se é legível ou não', function () {
    $stream = new Stream('batata tomate alface');
    expect($stream->isReadable())->toBeTrue();

    $stream = new Stream(fopen('./tests/for-test.txt', 'w'));
    expect($stream->isReadable())->toBeFalse();

    $stream = new Stream(fopen('./tests/for-test.txt', 'r'));
    expect($stream->isReadable())->toBeTrue();
});

test('Deve escrever no conteúdo da stream', function () {
    $stream = new Stream('batata');
    expect($stream->getContents())->toBe('batata');
    $stream->write(' tomate');
    $stream->rewind();
    expect($stream->getContents())->toBe('batata tomate');
    $stream->rewind();
    $stream->write('alface');
    $stream->rewind();
    expect($stream->getContents())->toBe('alface tomate');

    $stream = new Stream(fopen('./tests/for-test.txt', 'r'));
    expect($stream->isWritable())->toBeFalse();
    expect(fn () => $stream->write('alface'))->toThrow(RuntimeException::class);

    $mock   = mock(new FWraper())->shouldReceive('fwrite')->once()->andReturn(false)->getMock();
    $stream = new Stream(fopen('./tests/for-test.txt', 'r+'), $mock);

    expect($stream->isWritable())->toBeTrue();
    expect(fn () => $stream->write('alface'))->toThrow(RuntimeException::class);
});

test('Deve retornar se é gravável ou não', function () {
    $stream = new Stream('batata tomate alface');
    expect($stream->isWritable())->toBeTrue();

    $stream = new Stream(fopen('./tests/for-test.txt', 'w'));
    expect($stream->isWritable())->toBeTrue();

    $stream = new Stream(fopen('./tests/for-test.txt', 'r'));
    expect($stream->isWritable())->toBeFalse();
});

test('Deve setar o ponteiro da stream em 0', function () {
    $stream = new Stream('batata tomate alface');
    expect($stream->read(6))->toBe('batata');
    expect($stream->read(6))->toBe(' tomat');
    $stream->rewind();
    expect($stream->read(6))->toBe('batata');
});

test('Deve setar o ponteiro da stream no lugar desejado', function () {
    $stream = new Stream('batata tomate alface');

    $stream->seek(7);
    expect($stream->read(6))->toBe('tomate');

    $stream->seek(0);
    expect($stream->read(6))->toBe('batata');

    $stream->seek(14);
    expect($stream->read(6))->toBe('alface');

    $mock   = mock(new FWraper())->shouldReceive('fseek')->andReturn(-1)->getMock();
    expect(fn () =>  new Stream('batata tomate alface', $mock))->toThrow(RuntimeException::class);
});

test('Deve retornar se é possível manipular o ponteiro ou não', function () {
    $stream = new Stream('batata tomate alface');

    expect($stream->isSeekable())->toBeTrue();
});

test('Deve retornar se está no fim da stream ou não', function () {
    $stream = new Stream('batata tomate alface'); // 20
    expect($stream->eof())->toBeFalse();

    $stream->read(6);
    expect($stream->eof())->toBeFalse();

    $stream->read(14);
    expect($stream->eof())->toBeFalse();

    $stream->read(15);
    expect($stream->eof())->toBeTrue();
});

test('Deve retornar a posição do ponteiro', function () {
    $stream = new Stream('batata tomate alface'); // 20
    expect($stream->tell())->toBe(0);

    $stream->seek(6);
    expect($stream->tell())->toBe(6);

    $stream->seek(14);
    expect($stream->tell())->toBe(14);

    $stream->seek(15);
    expect($stream->tell())->toBe(15);

    $mock   = mock(new FWraper())->shouldReceive('ftell')->andReturn(false)->getMock();
    $stream = new Stream('batata', $mock);
    expect(fn () =>  $stream->tell())->toThrow(RuntimeException::class);
});

test('Deve retornar a stream da classe e remover ela da classe', function () {
    $stream = new Stream('batata tomate alface');

    expect($stream->hasStream())->toBeTrue();
    expect(is_resource($stream->detach()))->toBeTrue();
    expect($stream->hasStream())->toBeFalse();
    expect($stream->detach())->toBeNull();
});

test('Deve remover a strema da classe', function () {
    $stream = new Stream('batata tomate alface');

    expect($stream->hasStream())->toBeTrue();
    $stream->close();
    expect($stream->hasStream())->toBeFalse();
});

test('Deve retornar todo o conteúdo que tem na stream', function () {
    $stream = new Stream('batata tomate alface');

    expect((string) $stream)->toBe('batata tomate alface');
    $stream->seek(6);
    expect((string) $stream)->toBe('batata tomate alface');

    $mock   = mock(new FWraper())->shouldReceive('stream_get_contents')->once()->andThrow(new RuntimeException())->getMock();
    $stream = new Stream('batata', $mock);
    expect((string) $stream)->toBe('');
});
