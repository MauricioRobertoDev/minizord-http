<?php

namespace Minizord\Http;

use InvalidArgumentException;
use Minizord\Http\Contract\FWraperInterface;
use Minizord\Http\Contract\StreamInterface;
use Minizord\Http\Helper\FWraper;
use RuntimeException;
use Throwable;

class Stream implements StreamInterface
{
    private $stream;
    private ?int $size;
    private bool $seekable;
    private bool $writable;
    private bool $readable;
    private FWraperInterface $fwraper;

    public function __construct($body = '', $wraper = new FWraper())
    {
        $this->fwraper = $wraper;

        if (!is_string($body) && !is_resource($body) && $body !== null) {
            throw new InvalidArgumentException('Argumento 1 deve ser uma string, resource ou null');
        }

        if (is_string($body)) {
            $resource = $this->fwraper->fopen('php://temp', 'rw+');
            $this->fwraper->fwrite($resource, $body);
            $body = $resource;
        }

        if (!is_resource($body)) {
            throw new InvalidArgumentException('Body deve ser uma string ou resource.');
        }

        $this->stream   = $body;
        $this->seekable = $this->getMetadata('seekable') ?? false;
        $mode           = $this->getMetadata('mode');
        $this->writable = preg_match('/[xwca+]/', $mode);
        $this->readable = preg_match('/[r+]/', $mode);
        $this->rewind();
    }

    public function getMetadata($key = null) : array|string|null
    {
        if (!isset($this->stream)) {
            return $key ? null : [];
        }

        $meta = $this->fwraper->stream_get_meta_data($this->stream);

        if ($key === null) {
            return $meta;
        }

        return $meta[$key] ?? null;
    }

    public function getContents() : string
    {
        $this->hasStreamOrError();

        return $this->fwraper->stream_get_contents($this->stream);
    }

    public function getSize() : int|null
    {
        if (!isset($this->stream)) {
            return null;
        }

        $stats      = $this->fwraper->fstat($this->stream);
        $this->size = $stats['size'] ?? null;

        return $this->size;
    }

    public function read($length) : string
    {
        $this->hasStreamOrError();

        if (!$this->isReadable()) {
            throw new RuntimeException('A stream não é legível');
        }

        $result = $this->fwraper->fread($this->stream, $length);

        if ($result === false) {
            throw new RuntimeException('Não é possível ler a stream');
        }

        return $result;
    }

    public function isReadable() : bool
    {
        return $this->readable;
    }

    public function write($string) : int
    {
        $this->hasStreamOrError();

        if (!$this->isWritable()) {
            throw new RuntimeException('A stream não é gravável');
        }

        $write = $this->fwraper->fwrite($this->stream, $string);

        if ($write === false) {
            throw new RuntimeException('Não foi possível escrever na stream');
        }

        return $write;
    }

    public function isWritable()
    {
        return $this->writable;
    }

    public function rewind() : void
    {
        $this->seek(0);
    }

    public function seek($offset, $whence = SEEK_SET) : void
    {
        $this->hasStreamOrError();

        if (!$this->isSeekable()) {
            throw new RuntimeException('A stream não é buscavél');
        }

        $result = $this->fwraper->fseek($this->stream, $offset, $whence);

        if ($result === -1) {
            throw new RuntimeException('Não foi possível buscar a posição ' . $offset);
        }
    }

    public function isSeekable()
    {
        return $this->seekable;
    }

    public function eof() : bool
    {
        return !isset($this->stream) || $this->fwraper->feof($this->stream);
    }

    public function tell() : int
    {
        $this->hasStreamOrError();

        $position = $this->fwraper->ftell($this->stream);

        if ($position === false) {
            throw new RuntimeException('Não é possível setar a posição atual');
        }

        return $position;
    }

    public function detach() : mixed
    {
        if (!isset($this->stream)) {
            return null;
        }

        $resource = $this->stream;
        unset($this->stream);
        $this->size     = null;
        $this->readable = $this->writable = $this->seekable = false;

        return $resource;
    }

    public function close() : void
    {
        if (is_resource($this->stream)) {
            $this->fwraper->fclose($this->stream);
        }
        $this->detach();
    }

    public function hasStream() : bool
    {
        return isset($this->stream);
    }

    public function __toString() : string
    {
        try {
            if ($this->isSeekable()) {
                $this->rewind();
            }

            return $this->getContents();
        } catch (Throwable $exception) {
            return '';
        }
    }

    // private
    private function hasStreamOrError() : void
    {
        if (!isset($this->stream)) {
            throw new RuntimeException('Não há nenhuma stream');
        }
    }
}
