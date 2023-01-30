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
    /**
     * Rsource da stream, tipicamente pego por fopen().
     *
     * @var resource|null
     */
    private $stream;

    /**
     * Tamanho da stream;.
     *
     * @var int|null
     */
    private ?int $size;

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
     * Apenas um wraper para as funçãos nativas do php que manipulam arquivos como fopen() fclose().
     *
     * @var FWraperInterface
     */
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

        $meta = $this->fwraper->stream_get_meta_data($this->stream);

        if ($key === null) {
            return $meta;
        }

        return $meta[$key] ?? null;
    }

    /**
     * Retorna o conteúdo da stream.
     *
     * @return string
     */
    public function getContents() : string
    {
        $this->hasStreamOrError();

        return $this->fwraper->stream_get_contents($this->stream);
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

        $stats      = $this->fwraper->fstat($this->stream);
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

        $result = $this->fwraper->fread($this->stream, $length);

        if ($result === false) {
            throw new RuntimeException('Não é possível ler a stream');
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

        $write = $this->fwraper->fwrite($this->stream, $string);

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
            throw new RuntimeException('A stream não é buscavél');
        }

        $result = $this->fwraper->fseek($this->stream, $offset, $whence);

        if ($result === -1) {
            throw new RuntimeException('Não foi possível buscar a posição ' . $offset);
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
        return !isset($this->stream) || $this->fwraper->feof($this->stream);
    }

    /**
     * Retorna a posição atual do ponteiro da stream.
     *
     * @return int
     */
    public function tell() : int
    {
        $this->hasStreamOrError();

        $position = $this->fwraper->ftell($this->stream);

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
            $this->fwraper->fclose($this->stream);
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

    // private
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
