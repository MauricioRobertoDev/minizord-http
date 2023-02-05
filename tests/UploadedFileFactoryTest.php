<?php

use Minizord\Http\Factory\UploadedFileFactory;
use Minizord\Http\Stream;
use Psr\Http\Message\UploadedFileInterface;

test('Deve criar uma UploadedFile', function () {
    $factory          = new UploadedFileFactory();
    $tempFileName     = tempnam(sys_get_temp_dir(), 'for-test');
    $resource         = fopen($tempFileName, 'w+b');
    $stream           = new Stream($resource);
    $uploadedFile     = $factory->createUploadedFile($stream, 1024, UPLOAD_ERR_OK, 'file.txt', 'text/plain');

    expect($uploadedFile)->toBeInstanceOf(UploadedFileInterface::class);
});

test('Deve crias várias UploadedFile pela variável com apenas 1 file enviado', function () {
    $factory          = new UploadedFileFactory();
    $tempFileName     = tempnam(sys_get_temp_dir(), 'for-test');
    $resource         = fopen($tempFileName, 'w+b');
    fwrite($resource, 'batata');
    fclose($resource);

    // nomeado
    $files = [
        'file1' => [
            'name'     => 'MyFile.txt',
            'type'     => 'text/plain',
            'tmp_name' => $tempFileName,
            'error'    => 0,
            'size'     => 123,
        ],
    ];

    $result = $factory->createUploadedFilesFromGlobal($files);

    expect($result)->toHaveCount(1);
    expect($result[0])->toBeInstanceOf(UploadedFileInterface::class);
    expect($result[0]->getSize())->toBe(123);
    expect($result[0]->getError())->toBe(0);
    expect($result[0]->getClientFilename())->toBe('MyFile.txt');
    expect($result[0]->getClientMediaType())->toBe('text/plain');
    expect($result[0]->getStream()->getContents())->toBe('batata');

    // não nomeado
    $files = [
        [
            'name'     => 'MyFile.txt',
            'type'     => 'text/plain',
            'tmp_name' => '/tmp/php/php1h4j1o',
            'error'    => 0,
            'size'     => 123,
        ],
    ];

    $result = $factory->createUploadedFilesFromGlobal($files);

    expect($result)->toHaveCount(1);
    expect($result[0])->toBeInstanceOf(UploadedFileInterface::class);
    expect($result[0]->getSize())->toBe(123);
    expect($result[0]->getError())->toBe(0);
    expect($result[0]->getClientFilename())->toBe('MyFile.txt');
    expect($result[0]->getClientMediaType())->toBe('text/plain');
});

test('Deve crias várias UploadedFile pela variável com vários files enviados', function () {
    $factory = new UploadedFileFactory();

    // nomeado
    // file1 não tem name e file2 não tem type
    $files = [
        'uploads' => [
            'name'     => ['file2' => 'MyFile.jpg'],
            'type'     => ['file1' => 'text/plain'],
            'tmp_name' => ['file1' => '/tmp/php/php1h4j1o', 'file2' => '/tmp/php/php6hst32'],
            'error'    => ['file1' => UPLOAD_ERR_OK,        'file2' => UPLOAD_ERR_OK],
            'size'     => ['file1' => 123,                  'file2' => 98174],
        ],
    ];

    $result = $factory->createUploadedFilesFromGlobal($files);

    expect($result)->toHaveCount(2);
    expect($result['file1'])->toBeInstanceOf(UploadedFileInterface::class);
    expect($result['file2'])->toBeInstanceOf(UploadedFileInterface::class);
    expect($result['file1']->getSize())->toBe(123);
    expect($result['file2']->getSize())->toBe(98174);
    expect($result['file1']->getError())->toBe(0);
    expect($result['file2']->getError())->toBe(0);
    expect($result['file1']->getClientFilename())->toBe(null);
    expect($result['file2']->getClientFilename())->toBe('MyFile.jpg');
    expect($result['file1']->getClientMediaType())->toBe('text/plain');
    expect($result['file2']->getClientMediaType())->toBe(null);

    // não nomeado
    // file1 não tem name e file2 não tem type
    $files = [
        'uploads' => [
            'name'     => [null, 'MyFile.jpg'],
            'type'     => ['text/plain', null],
            'tmp_name' => ['/tmp/php/php1h4j1o', '/tmp/php/php6hst32'],
            'error'    => [UPLOAD_ERR_OK,        UPLOAD_ERR_OK],
            'size'     => [123,                  98174],
        ],
    ];

    $result = $factory->createUploadedFilesFromGlobal($files);

    expect($result)->toHaveCount(2);
    expect($result[0])->toBeInstanceOf(UploadedFileInterface::class);
    expect($result[1])->toBeInstanceOf(UploadedFileInterface::class);
    expect($result[0]->getSize())->toBe(123);
    expect($result[1]->getSize())->toBe(98174);
    expect($result[0]->getError())->toBe(0);
    expect($result[1]->getError())->toBe(0);
    expect($result[0]->getClientFilename())->toBe(null);
    expect($result[1]->getClientFilename())->toBe('MyFile.jpg');
    expect($result[0]->getClientMediaType())->toBe('text/plain');
    expect($result[1]->getClientMediaType())->toBe(null);
});

test('Deve estourar um erro caso não tenha os dados necessários', function () {
    $factory = new UploadedFileFactory();

    $files = [
        'file1' => [
            'name'     => 'MyFile.txt',
            'type'     => 'text/plain',
            'error'    => 0,
            'size'     => 123,
        ],
    ];

    expect(fn () => $factory->createUploadedFilesFromGlobal($files))->toThrow(InvalidArgumentException::class);
});
