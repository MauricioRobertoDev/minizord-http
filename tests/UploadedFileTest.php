<?php

use Minizord\Http\Contract\UploadedFileInterface;
use Minizord\Http\Stream;
use Minizord\Http\UploadedFile;
use Psr\Http\Message\StreamInterface as PsrStreamInterface;
use Psr\Http\Message\UploadedFileInterface as PsrUploadedFileInterface;

/*
 * Instâncialização
 */
test('Deve criar uma instância de UploadedFile passando o caminho do arquivo', function () {
    $tempFileName     = tempnam(sys_get_temp_dir(), 'for-test');
    $uploadedFile     = new UploadedFile($tempFileName, 1024, UPLOAD_ERR_OK, 'file.txt', 'text/plain');

    expect($uploadedFile)->toBeInstanceOf(PsrUploadedFileInterface::class);
    expect($uploadedFile->getClientFilename())->toBe('file.txt');
    expect($uploadedFile->getClientMediaType())->toBe('text/plain');
    expect($uploadedFile->getSize())->toBe(1024);
    expect($uploadedFile->getError())->toBe(UPLOAD_ERR_OK);
    expect($uploadedFile->getErrorMessage())->toBe(UploadedFile::ERRORS[UPLOAD_ERR_OK]);

    $uploadedFile->getStream()->close();
});

test('Deve criar uma instância de UploadedFile passando um resource', function () {
    $tempFileName     = tempnam(sys_get_temp_dir(), 'for-test');
    $resource         = fopen($tempFileName, 'w+');

    $uploadedFile = new UploadedFile($resource, 1024, UPLOAD_ERR_OK, 'file.txt', 'text/plain');

    expect($uploadedFile)->toBeInstanceOf(PsrUploadedFileInterface::class);
    expect($uploadedFile->getClientFilename())->toBe('file.txt');
    expect($uploadedFile->getClientMediaType())->toBe('text/plain');
    expect($uploadedFile->getSize())->toBe(1024);
    expect($uploadedFile->getError())->toBe(UPLOAD_ERR_OK);
    expect($uploadedFile->getErrorMessage())->toBe(UploadedFile::ERRORS[UPLOAD_ERR_OK]);

    fclose($resource);
    $uploadedFile->getStream()->close();
});

test('Deve criar uma instância de UploadedFile passando uma classe de stream', function () {
    $tempFileName     = tempnam(sys_get_temp_dir(), 'for-test');
    $resource         = fopen($tempFileName, 'w+');
    $stream           = new Stream($resource);
    $uploadedFile     = new UploadedFile($stream, 1024, UPLOAD_ERR_OK, 'file.txt', 'text/plain');

    expect($uploadedFile)->toBeInstanceOf(PsrUploadedFileInterface::class);
    expect($uploadedFile->getClientFilename())->toBe('file.txt');
    expect($uploadedFile->getClientMediaType())->toBe('text/plain');
    expect($uploadedFile->getSize())->toBe(1024);
    expect($uploadedFile->getError())->toBe(UPLOAD_ERR_OK);
    expect($uploadedFile->getErrorMessage())->toBe(UploadedFile::ERRORS[UPLOAD_ERR_OK]);

    fclose($resource);
    $uploadedFile->getStream()->close();
});

test('Deve criar uma instância de UploadedFile passando um error de UPLOAD_ERR_* válido', function () {
    $uploadedFile   = new UploadedFile('file.txt', 1024, UPLOAD_ERR_PARTIAL);

    expect($uploadedFile)->toBeInstanceOf(UploadedFileInterface::class);
    expect($uploadedFile->getSize())->toBe(1024);
    expect($uploadedFile->getError())->toBe(UPLOAD_ERR_PARTIAL);
    expect($uploadedFile->getErrorMessage())->toBe(UploadedFile::ERRORS[UPLOAD_ERR_PARTIAL]);
});

test('Deve estourar um erro caso passe argumentos inválidos', function () {
    expect(fn () => new UploadedFile(888, 1024, UPLOAD_ERR_OK))->toThrow(InvalidArgumentException::class);
    expect(fn () => new UploadedFile(null, 1024, UPLOAD_ERR_OK))->toThrow(InvalidArgumentException::class);
    expect(fn () => new UploadedFile('', 1024, UPLOAD_ERR_OK))->toThrow(InvalidArgumentException::class);
    expect(fn () => new UploadedFile(' ', 1024, UPLOAD_ERR_OK))->toThrow(InvalidArgumentException::class);
    expect(fn () => new UploadedFile('file.txt', 1024, 888))->toThrow(InvalidArgumentException::class);
    expect(fn () => new UploadedFile('file.txt', 1024, 888))->toThrow(InvalidArgumentException::class);
});

/*
 * getStream()
 */
test('Deve retornar uma Stream se está tudo certo com o resource', function () {
    $tempFileName     = tempnam(sys_get_temp_dir(), 'for-test');
    $resource         = fopen($tempFileName, 'w+');
    fwrite($resource, 'batata');
    $uploadedFile     = new UploadedFile($resource, 1024, UPLOAD_ERR_OK);

    expect($uploadedFile)->toBeInstanceOf(PsrUploadedFileInterface::class);
    expect($uploadedFile->getStream())->toBeInstanceOf(PsrStreamInterface::class);
    expect($uploadedFile->getStream()->getContents())->toBe('batata');

    fclose($resource);
    $uploadedFile->getStream()->close();
});

test('Deve estourar um erro caso tente pegar a Stream mas há um UPLOAD_ERR_*', function () {
    $tempFileName     = tempnam(sys_get_temp_dir(), 'for-test');
    $uploadedFile     = new UploadedFile($tempFileName, 1024, UPLOAD_ERR_PARTIAL);

    expect($uploadedFile)->toBeInstanceOf(PsrUploadedFileInterface::class);
    expect(fn () => $uploadedFile->getStream())->toThrow(RuntimeException::class);

    $resource         = fopen($tempFileName, 'w+');
    $uploadedFile     = new UploadedFile($resource, 1024, UPLOAD_ERR_PARTIAL);

    expect($uploadedFile)->toBeInstanceOf(PsrUploadedFileInterface::class);
    expect(fn () => $uploadedFile->getStream())->toThrow(RuntimeException::class);

    fclose($resource);
});

test('Deve estourar um erro caso tente pegar a Stream depois que o arquivo foi movido', function () {
    $tempFileName     = tempnam(sys_get_temp_dir(), 'for-test');
    $uploadedFile     = new UploadedFile($tempFileName, 1024, UPLOAD_ERR_OK);

    expect($uploadedFile)->toBeInstanceOf(PsrUploadedFileInterface::class);
    expect($uploadedFile->getStream())->toBeInstanceOf(PsrStreamInterface::class);

    $targetPath = sys_get_temp_dir() . '/buh';
    $uploadedFile->moveTo($targetPath);

    expect(file_exists($targetPath))->toBeTrue();
    expect(fn () => $uploadedFile->getStream())->toThrow(RuntimeException::class);

    unlink($targetPath);
});

test('Deve estourar um erro caso tente pegar a Stream e o file path passado não pode ser aberto', function () {
    $tempFileName     = tempnam(sys_get_temp_dir(), 'for-test');
    $uploadedFile     = new UploadedFile($tempFileName, 1024, UPLOAD_ERR_OK);

    expect($uploadedFile)->toBeInstanceOf(PsrUploadedFileInterface::class);

    chmod($tempFileName, 0000);

    expect(fn () => $uploadedFile->getStream())->toThrow(RuntimeException::class);
});

/*
 * moveTo()
 */

test('Deve estourar um erro caso tente mover o arquivo mas há um UPLOAD_ERR_*', function () {
    $tempFileName     = tempnam(sys_get_temp_dir(), 'for-test');
    $uploadedFile     = new UploadedFile($tempFileName, 1024, UPLOAD_ERR_PARTIAL);

    expect($uploadedFile)->toBeInstanceOf(PsrUploadedFileInterface::class);
    expect(fn () => $uploadedFile->moveTo('path/to/void'))->toThrow(RuntimeException::class);

    $resource         = fopen($tempFileName, 'w+');
    $uploadedFile     = new UploadedFile($resource, 1024, UPLOAD_ERR_PARTIAL);

    expect($uploadedFile)->toBeInstanceOf(PsrUploadedFileInterface::class);
    expect(fn () => $uploadedFile->moveTo('path/to/void'))->toThrow(RuntimeException::class);

    fclose($resource);
});

test('Deve estourar um erro caso tente mover o arquivo com argumentos inválidos', function () {
    $tempFileName     = tempnam(sys_get_temp_dir(), 'for-test');
    $uploadedFile     = new UploadedFile($tempFileName, 1024, UPLOAD_ERR_OK);

    expect($uploadedFile)->toBeInstanceOf(PsrUploadedFileInterface::class);
    expect(fn () => $uploadedFile->moveTo(''))->toThrow(InvalidArgumentException::class);
    expect(fn () => $uploadedFile->moveTo(' '))->toThrow(InvalidArgumentException::class);
    expect(fn () => $uploadedFile->moveTo(null))->toThrow(InvalidArgumentException::class);
    expect(fn () => $uploadedFile->moveTo(888))->toThrow(InvalidArgumentException::class);
    expect(fn () => $uploadedFile->moveTo('path/to/void/void.txt'))->toThrow(RuntimeException::class);

    $uploadedFile->getStream()->close();
});

test('Deve estourar um erro caso tente mover o arquivo para um arquivo que não possa ser aberto', function () {
    $resourceFilename    = tempnam(sys_get_temp_dir(), 'for-test');
    $destinationFileName = tempnam(sys_get_temp_dir(), 'for-test');
    $resource            = fopen($resourceFilename, 'w+');
    $uploadedFile        = new UploadedFile($resource, 1024, UPLOAD_ERR_OK);

    chmod($destinationFileName, 0444); // somente leitura

    expect($uploadedFile)->toBeInstanceOf(PsrUploadedFileInterface::class);
    expect($resourceFilename)->not()->toBe($destinationFileName);
    expect(fn () => $uploadedFile->moveTo($destinationFileName))->toThrow(RuntimeException::class);

    fclose($resource);
    $uploadedFile->getStream()->close();
});

test('Deve gravar o conteúdo do arquivo atual para o novo arquivo', function () {
    $resourceFilename    = tempnam(sys_get_temp_dir(), 'for-test');
    $destinationFileName = tempnam(sys_get_temp_dir(), 'for-test');

    $resource            = fopen($resourceFilename, 'w+b');
    fwrite($resource, 'batata'); // o arquivo upado tem algo escrito
    $uploadedFile        = new UploadedFile($resource, 1024, UPLOAD_ERR_OK);

    expect($uploadedFile)->toBeInstanceOf(PsrUploadedFileInterface::class);
    expect($resourceFilename)->not()->toBe($destinationFileName);
    expect($uploadedFile->getStream()->getContents())->toBe('batata');

    $destinationResource = fopen($destinationFileName, 'rw+b');

    expect(stream_get_contents($destinationResource))->toBe(''); // o arquivo de destino não tem nada

    fclose($destinationResource);

    $uploadedFile->moveTo($destinationFileName); // movendo o conteúdo para o arquivo de destino

    expect($uploadedFile->hasBeenMoved())->toBeTrue();

    $destinationResource = fopen($destinationFileName, 'rw+b');
    fseek($destinationResource, 0);
    expect(stream_get_contents($destinationResource))->toBe('batata'); // o conteúdo foi movido realmente

    fclose($resource);
    fclose($destinationResource);
});
