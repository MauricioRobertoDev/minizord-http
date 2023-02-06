<?php

namespace Minizord\Http\Factory;

use InvalidArgumentException;
use Minizord\Http\Stream;
use Psr\Http\Message\StreamFactoryInterface;

final class StreamFactory implements StreamFactoryInterface
{
    /**
     * Cria uma Stream.
     */
    public function createStream(string $content = ''): Stream
    {
        return new Stream($content);
    }

    /**
     * Cria uma Stream passando o caminho de um arquivo.
     */
    public function createStreamFromFile(string $filename, string $mode = 'r'): Stream
    {
        $file = @fopen($filename, $mode);

        if (! $file) {
            throw new InvalidArgumentException('Não foi possível abrir o arquivo');
        }

        return new Stream($file);
    }

    /**
     * Cria uma Stream passando um resource.
     *
     * @param resource $resource
     */
    public function createStreamFromResource($resource): Stream
    {
        if (! is_resource($resource)) {
            throw new InvalidArgumentException('O dado deve ser um resource');
        }

        return new Stream($resource);
    }
}
