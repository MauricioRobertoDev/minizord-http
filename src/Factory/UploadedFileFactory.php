<?php

namespace Minizord\Http\Factory;

use InvalidArgumentException;
use Minizord\Http\Contract\UploadedFileInterface;
use Minizord\Http\Stream;
use Minizord\Http\UploadedFile;
use Psr\Http\Message\{
    StreamInterface as PsrStreamInterface,
    UploadedFileFactoryInterface as PsrUploadedFileFactoryInterface
};

class UploadedFileFactory implements PsrUploadedFileFactoryInterface
{
    /**
     * Cria uma UploadedFile.
     *
     * @param  PsrStreamInterface    $stream
     * @param  int|null              $size
     * @param  int                   $error
     * @param  string|null           $clientFilename
     * @param  string|null           $clientMediaType
     * @return UploadedFileInterface
     */
    public function createUploadedFile(
        PsrStreamInterface $stream,
        int $size = null,
        int $error = \UPLOAD_ERR_OK,
        string $clientFilename = null,
        string $clientMediaType = null
    ) : UploadedFileInterface {
        return new UploadedFile($stream, $size, $error, $clientFilename, $clientMediaType);
    }

    /**
     * Cria um array de UploadedFiles através do array passado ou do $_FILES.
     *
     * @param  array|null $files
     * @return array
     */
    public function createUploadedFilesFromGlobal(array $files = null) : array
    {
        $files         = $this->simplifyFiles($files ?? $_FILES);
        $uploadedFiles = [];

        foreach ($files as $key => $file) {
            $this->validateFileData($file);
            $resource = @fopen($file['tmp_name'], 'rw+');

            $uploadedFiles[$key] = $this->createUploadedFile(
                new Stream($resource ? $resource : ''),
                $file['size'],
                $file['error'],
                $file['name'] ?? null,
                $file['type'] ?? null,
            );
        }

        return $uploadedFiles;
    }

    private function simplifyFiles(array $files) : array
    {
        $simplifiedFiles = [];

        foreach ($files as $input => $infoArr) {
            foreach ($infoArr as $key => $valueArr) {
                if (is_array($valueArr)) { // file input "multiple"
                    foreach ($valueArr as $i => $value) {
                        $simplifiedFiles[$i][$key] = $value;
                    }
                } else { // -> string, normal file input
                    $simplifiedFiles[] = $infoArr;

                    break;
                }
            }
        }

        return $simplifiedFiles;
    }

    private function validateFileData(array $fileData) : void
    {
        if (
            !isset($fileData['tmp_name']) || !isset($fileData['size']) || !isset($fileData['error'])
        ) {
            throw new InvalidArgumentException('Os dados necessários não estão presente: "tmp_name", "size" e "error"');
        }
    }
}
