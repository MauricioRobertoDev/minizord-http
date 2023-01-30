<?php

namespace Minizord\Http\Contract;

interface FWraperInterface
{
    public function fread($stream, int $length) : string|false;

    public function fwrite($stream, string $data, ?int $length = null) : int|false;

    public function fopen(string $filename, string $mode, bool $use_include_path = false, $context = null);

    public function fstat($stream) : array|false;

    public function fseek($stream, int $offset, int $whence = SEEK_SET) : int;

    public function feof($stream) : bool;

    public function ftell($stream) : int|false;

    public function fclose($stream) : bool;

    public function stream_get_meta_data($stream) : array;

    public function stream_get_contents($stream, ?int $length = null, int $offset = -1) : string|false;
}
