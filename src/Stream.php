<?php

namespace Minizord\Http;

use InvalidArgumentException;
use Minizord\Http\Contract\StreamInterface;
use RuntimeException;
use Throwable;

class Stream implements StreamInterface
{
    /**
     * Rsource tipicamente fopen().
     *
     * @var resource|null
     */
    private $stream;

    /**
     * Tamanho da stream.
     *
     * @var int|null
     */
    private ?int $size = null;

    /**
     * Se o ponteiro é manipulável.
     *
     * @var bool
     */
    private bool $seekable;

    /**
     * Se é possível escrever.
     *
     * @var bool
     */
    private bool $writable;

    /**
     * Se é possível ler.
     *
     * @var bool
     */
    private bool $readable;

    /**
     * Um wrapper para um fluxo de dados (stream php).
     *
     * @param resource|string $body
     */
    public function __construct($body = '')
    {
        if (!is_string($body) && !is_resource($body)) {
            throw new InvalidArgumentException('O body deve ser uma string ou resource');
        }

        if (is_string($body)) {
            $resource = fopen('php://temp', 'rw+b');
            fwrite($resource, $body);
            $body = $resource;
        }

        $this->stream   = $body;
        $this->seekable = $this->getMetadata('seekable') ?? false;
        $mode           = $this->getMetadata('mode');
        $this->writable = preg_match('/[xwca+]/', $mode);
        $this->readable = preg_match('/[r+]/', $mode);
        $this->rewind();
    }

    /**
     * Retorna a metadata da stream.
     *
     * @param  string|null       $key
     * @return array|string|null
     */
    public function getMetadata($key = null) : array|string|null
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

    /**
     * Retorna o conteúdo restante em uma string.
     *
     * @return string
     */
    public function getContents() : string
    {
        $this->hasStreamOrError();

        return stream_get_contents($this->stream);
    }

    /**
     * Retorna o tamanho da stream.
     *
     * @return int|null
     */
    public function getSize() : int|null
    {
        if (!isset($this->stream)) {
            return null;
        }

        $stats      = fstat($this->stream);
        $this->size = $stats['size'] ?? null;

        return $this->size;
    }

    /**
     * Lê determinada quantidade da stream.
     *
     * @param  int    $length
     * @return string
     */
    public function read($length) : string
    {
        $this->hasStreamOrError();

        if (!$this->isReadable()) {
            throw new RuntimeException('A stream não é legível');
        }

        $result = @fread($this->stream, $length);

        if ($result === false) {
            throw new RuntimeException('Não foi possível ler a stream');
        }

        return $result;
    }

    /**
     * Retorna se é possível ler a stream.
     *
     * @return bool
     */
    public function isReadable() : bool
    {
        return $this->readable;
    }

    /**
     * Escreve o dado na stream.
     *
     * @param  string $string
     * @return int
     */
    public function write($string) : int
    {
        $this->hasStreamOrError();

        if (!$this->isWritable()) {
            throw new RuntimeException('A stream não é gravável');
        }

        $write = @fwrite($this->stream, $string);

        if ($write === false) {
            throw new RuntimeException('Não foi possível escrever na stream');
        }

        return $write;
    }

    /**
     * Retorna se é possível gravar na stream.
     *
     * @return bool
     */
    public function isWritable()
    {
        return $this->writable;
    }

    /**
     * Seta o ponteiro da stream no 0.
     *
     * @return void
     */
    public function rewind() : void
    {
        $this->seek(0);
    }

    /**
     * Coloca o ponteiro da stream em determinado local.
     *
     * @param  int  $offset
     * @param  int  $whence
     * @return void
     */
    public function seek($offset, $whence = SEEK_SET) : void
    {
        $this->hasStreamOrError();

        if (!$this->isSeekable()) {
            throw new RuntimeException('O ponteiro da stream é manipulável');
        }

        $result = fseek($this->stream, $offset, $whence);

        if ($result === -1) {
            throw new RuntimeException('Não foi possível alterar o pointeiro da stream para posição ' . $offset);
        }
    }

    /**
     * Retorna se o ponteiro da stream é manipulável.
     *
     * @return bool
     */
    public function isSeekable()
    {
        return $this->seekable;
    }

    /**
     * Retorna se já está no final da stream.
     *
     * @return bool
     */
    public function eof() : bool
    {
        if (!isset($this->stream)) {
            return true;
        }

        return feof($this->stream);
    }

    /**
     * Retorna a posição atual do ponteiro da stream.
     *
     * @return int
     */
    public function tell() : int
    {
        $this->hasStreamOrError();

        $position = @ftell($this->stream);

        if ($position === false) {
            throw new RuntimeException('Não é possível setar a posição atual');
        }

        return $position;
    }

    /**
     * Remove o resource da stream e o retorna.
     *
     * @return mixed
     */
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

    /**
     * Fecha a conexão com o resource da stream e o remove.
     *
     * @return void
     */
    public function close() : void
    {
        if (is_resource($this->stream)) {
            fclose($this->stream);
        }

        $this->detach();
    }

    /**
     * Retorna se existe um resource na stream.
     *
     * @return bool
     */
    public function hasStream() : bool
    {
        return isset($this->stream);
    }

    /**
     * Retorna todo o conteúdo da stream.
     *
     * @return string
     */
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

    /**
     * Caso não tenha nada na stream estora um erro.
     *
     * @return void
     */
    private function hasStreamOrError() : void
    {
        if (!isset($this->stream)) {
            throw new RuntimeException('Não há nenhuma stream');
        }
    }
}
