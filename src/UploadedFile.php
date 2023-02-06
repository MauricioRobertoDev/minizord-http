<?php

namespace Minizord\Http;

use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use RuntimeException;

final class UploadedFile implements UploadedFileInterface
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

    /**
     * Tamanho do arquivo.
     */
    private int $size;

    /**
     * Código de erro.
     */
    private int $error;

    /**
     * Se foi movido ou não.
     */
    private bool $moved                 = false;

    /**
     * Filename do arquivo.
     */
    private string|null $file               = null;

    /**
     * Nome do arquivo.
     */
    private string|null $clientFilename     = null;

    /**
     * Tipo do arquivo.
     */
    private string|null $clientMediaType    = null;

    /**
     * Stream do arquivo.
     */
    private StreamInterface|null $stream = null;

    /**
     * Representa um arquivo carregado por meio de uma solicitação HTTP.
     *
     * @param StreamInterface|resource|string $streamOrFile
     */
    public function __construct(
        $streamOrFile,
        int $size,
        int $error,
        string|null $clientFilename = null,
        string|null $clientMediaType = null
    ) {
        if (! isset(self::ERRORS[$error])) {
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

        if ($streamOrFile instanceof StreamInterface) {
            $this->stream = $streamOrFile;

            return;
        }

        throw new InvalidArgumentException('Stream ou arquivo inválido');
    }

    /**
     * Retorna um stream que representa o arquivo carregado.
     */
    public function getStream(): StreamInterface
    {
        $this->hasMovedThrowException();

        if ($this->error !== UPLOAD_ERR_OK) {
            throw new RuntimeException(self::ERRORS[$this->error]);
        }

        if ($this->stream instanceof StreamInterface) {
            return $this->stream;
        }

        $resource = @fopen($this->file, 'r+');

        if ($resource === false) {
            throw new RuntimeException('O arquivo não pode ser aberto: ' . $this->file);
        }

        $this->stream = new Stream($resource);

        return $this->stream;
    }

    /**
     * Mova o arquivo carregado para um novo local.
     *
     * @param string $targetPath
     */
    public function moveTo($targetPath): void
    {
        $this->hasMovedThrowException();

        if ($this->error !== UPLOAD_ERR_OK) {
            throw new RuntimeException(self::ERRORS[$this->error]);
        }

        if (! is_string($targetPath) || ! trim($targetPath)) {
            throw new InvalidArgumentException('O caminho para mover o arquivo deve ser passado em forma de string');
        }

        $targetDirectory = dirname($targetPath);

        if (! is_dir($targetDirectory) || ! is_writable($targetDirectory)) {
            throw new RuntimeException('O diretório escolhido para mover o arquivo não existe ou não é gravável: ' . $targetDirectory);
        }

        if ($this->file) {
            $this->moved = PHP_SAPI === 'cli' ? rename($this->file, $targetPath) : move_uploaded_file($this->file, $targetPath);

            if (! $this->moved) {
                throw new RuntimeException('O arquivo enviado não pôde ser movido para: ' . $targetPath);
            }

            return;
        }

        $resource = @fopen($targetPath, 'r+');

        if (! $resource) {
            throw new RuntimeException('O arquivo ' . $targetPath . ' não pôde ser aberto');
        }

        $this->stream->rewind();

        while (! $this->stream->eof()) {
            fwrite($resource, $this->stream->read(1048576));
        }

        fclose($resource);

        $this->moved = true;
    }

    /**
     * Retorna o tamanho do arquivo.
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * Retorna o erro associado ao arquivo carregado.
     */
    public function getError(): int
    {
        return $this->error;
    }

    /**
     * Retorna a mensagem do erro ssociado ao arquivo carregado.
     */
    public function getErrorMessage(): string
    {
        return self::ERRORS[$this->error] ?? '';
    }

    /**
     * Retorna o nome do arquivo enviado pelo cliente.
     */
    public function getClientFilename(): string|null
    {
        return $this->clientFilename;
    }

    /**
     * Retorna o tipo de mídia enviado pelo cliente.
     */
    public function getClientMediaType(): string|null
    {
        return $this->clientMediaType;
    }

    /**
     * Retorna se o arquivo carregado já foi movido.
     */
    public function hasBeenMoved(): bool
    {
        return $this->moved;
    }

    /**
     * Estoura um erro caso o arquivo já tenha sido movido.
     */
    private function hasMovedThrowException(): void
    {
        if ($this->moved) {
            throw new RuntimeException('O arquivo já foi movido');
        }
    }
}
