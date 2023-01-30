<?php

namespace Minizord\Http\Helper;

use Minizord\Http\Contract\FWraperInterface;

// ###########################
// EU realmente não gostaria de ter essa classe porem eu tentei de todas as formas
// simular um erro nas funções nativas do php e sem sucesso. Isso para cobertura de código,
// pois em várias situais não achei formas de forçar um erro.
// ###########################

/**
 * Essa classe não faz nada alem do que os métodos padrões usados aqui.
 */
class FWraper implements FWraperInterface
{
    public function fread($stream, int $length) : string|false
    {
        return fread($stream, $length);
    }

    public function fwrite($stream, string $data, ?int $length = null) : int|false
    {
        return fwrite($stream, $data, $length);
    }

    public function fopen(string $filename, string $mode, bool $use_include_path = false, $context =  null)
    {
        return fopen($filename, $mode, $use_include_path, $context);
    }

    public function fstat($stream) : array|false
    {
        return fstat($stream);
    }

    public function fseek($stream, int $offset, int $whence = SEEK_SET) : int
    {
        return fseek($stream, $offset, $whence);
    }

    public function feof($stream) : bool
    {
        return feof($stream);
    }

    public function ftell($stream) : int|false
    {
        return ftell($stream);
    }

    public function fclose($stream) : bool
    {
        return fclose($stream);
    }

    public function stream_get_meta_data($stream) : array
    {
        return stream_get_meta_data($stream);
    }

    public function stream_get_contents($stream, ?int $length = null, int $offset = -1) : string|false
    {
        return stream_get_contents($stream, $length, $offset);
    }
}
