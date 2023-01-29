<?php

namespace Minizord\Http;

use InvalidArgumentException;
use Minizord\Http\Contract\StreamInterface;

class Stream implements StreamInterface
{
    private $stream;
    private ?int $size;
    private bool $seekable;
    private bool $writable;
    private bool $readable;

    public function __construct(stream|string $body = '')
    {
        if (!is_string($body) && !is_resource($body) && $body !== null) {
            throw new InvalidArgumentException('Argumento 1 deve ser uma string, resource ou null');
        }

        if (is_string($body)) {
            $resource = fopen('php://temp', 'w+b');
            fwrite($resource, $body);
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
    }

    public function getMetadata($key = null)
    {
        if (!isset($this->stream)) {
            return $key ? null : [];
        }

        $meta = stream_get_meta_data($this->stream);
        if ($key === null) {
            return $meta;
        }

        return $meta[$key] ?? null;
    }

    public function getContents()
    {
    }

    public function read($length)
    {
    }

    public function isReadable()
    {
    }

    public function write($string)
    {
    }

    public function isWritable()
    {
    }

    public function rewind()
    {
    }

    public function seek($offset, $whence = SEEK_SET)
    {
    }

    public function isSeekable()
    {
    }

    public function eof()
    {
    }

    public function tell()
    {
    }

    public function getSize()
    {
    }

    public function detach()
    {
    }

    public function close()
    {
    }

    public function __toString()
    {
    }
}
