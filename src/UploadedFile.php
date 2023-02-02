<?php

namespace Minizord\Http;

use InvalidArgumentException;
use Minizord\Http\Contract\UploadedFileInterface;
use Psr\Http\Message\StreamInterface as PsrStreamInterface;
use RuntimeException;

class UploadedFile implements UploadedFileInterface
{
    public const ERRORS = [
        UPLOAD_ERR_OK         => 'Upado com sucesso.',
        UPLOAD_ERR_INI_SIZE   => 'O arquivo carregado excede a diretiva upload_max_filesize em php.ini.',
        UPLOAD_ERR_FORM_SIZE  => 'O arquivo carregado excede a diretiva MAX_FILE_SIZE especificada no formulário HTML.',
        UPLOAD_ERR_PARTIAL    => 'O arquivo carregado foi carregado apenas parcialmente.',
        UPLOAD_ERR_NO_FILE    => 'Nenhum arquivo foi carregado.',
        UPLOAD_ERR_NO_TMP_DIR => 'Faltando uma pasta temporária.',
        UPLOAD_ERR_CANT_WRITE => 'Falha ao gravar o arquivo no disco.',
        UPLOAD_ERR_EXTENSION  => 'Uma extensão PHP interrompeu o upload do arquivo.',
    ];

    private int $size;
    private int $error;
    private bool $moved                 = false;
    private ?string $file               = null;
    private ?string $clientFilename     = null;
    private ?string $clientMediaType    = null;
    private ?PsrStreamInterface $stream = null;

    public function __construct(
        $streamOrFile,
        int $size,
        int $error,
        string $clientFilename = null,
        string $clientMediaType = null
    ) {
        if (!isset(self::ERRORS[$error])) {
            throw new InvalidArgumentException('O erro deve ser um "UPLOAD_ERR_" válido');
        }

        $this->error           = $error;
        $this->size            = $size;
        $this->clientFilename  = $clientFilename;
        $this->clientMediaType = $clientMediaType;

        if ($this->error !== UPLOAD_ERR_OK) {
            return;
        }

        if (is_string($streamOrFile) && trim($streamOrFile)) {
            $this->file = $streamOrFile;

            return;
        }

        if (is_resource($streamOrFile)) {
            $this->stream = new Stream($streamOrFile);

            return;
        }

        if ($streamOrFile instanceof PsrStreamInterface) {
            $this->stream = $streamOrFile;

            return;
        }

        throw new InvalidArgumentException('Stream ou arquivo inválido');
    }

    public function getStream()
    {
        $this->hasMovedThrowException();

        if ($this->error !== UPLOAD_ERR_OK) {
            throw new RuntimeException(self::ERRORS[$this->error]);
        }

        if ($this->stream instanceof PsrStreamInterface) {
            return $this->stream;
        }

        $resource = @fopen($this->file, 'r+');

        if ($resource === false) {
            throw new RuntimeException('O arquivo não pode ser aberto: ' . $this->file);
        }

        $this->stream = new Stream($resource);

        return $this->stream;
    }

    public function moveTo($targetPath)
    {
        $this->hasMovedThrowException();

        if ($this->error !== UPLOAD_ERR_OK) {
            throw new RuntimeException(self::ERRORS[$this->error]);
        }

        if (!is_string($targetPath) || !trim($targetPath)) {
            throw new InvalidArgumentException('O caminho para mover o arquivo deve ser passado em forma de string');
        }

        $targetDirectory = dirname($targetPath);

        if (!is_dir($targetDirectory) || !is_writable($targetDirectory)) {
            throw new RuntimeException('O diretório escolhido para mover o arquivo não existe ou não é gravável: ' . $targetDirectory);
        }

        if ($this->file) {
            $this->moved = PHP_SAPI === 'cli' ? rename($this->file, $targetPath) : move_uploaded_file($this->file, $targetPath);

            if (!$this->moved) {
                throw new RuntimeException('O arquivo enviado não pôde ser movido para: ' . $targetPath);
            }

            return;
        }

        $resource = @fopen($targetPath, 'r+');

        if (!$resource) {
            throw new RuntimeException('O arquivo ' . $targetPath . ' não pôde ser aberto');
        }

        $this->stream->rewind();

        while (!$this->stream->eof()) {
            fwrite($resource, $this->stream->read(1048576));
        }

        fclose($resource);

        $this->moved = true;
    }

    public function getSize() : int
    {
        return $this->size;
    }

    public function getError() : int
    {
        return $this->error;
    }

    public function getErrorMessage() : string
    {
        return self::ERRORS[$this->error] ?? '';
    }

    public function getClientFilename() : ?string
    {
        return $this->clientFilename;
    }

    public function getClientMediaType() : ?string
    {
        return $this->clientMediaType;
    }

    public function hasBeenMoved() : bool
    {
        return $this->moved;
    }

    private function hasMovedThrowException() : void
    {
        if ($this->moved) {
            throw new RuntimeException('O arquivo já foi movido');
        }
    }
}
