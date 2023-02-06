<?php

namespace Minizord\Http\Factory;

use Minizord\Http\Uri;

final class RequestData
{
    /**
     * Pega os headers do $_SERVER.
     *
     * @param array<string, string> $server
     *
     * @return array<string, array<string>>
     */
    public function getHeaders(array $server): array
    {
        $headers = [];

        foreach ($server as $name => $value) {
            if (substr($name, 0, 5) === 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;

                continue;
            }

            if (substr($name, 0, 8) === 'CONTENT_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', $name))))] = $value;
            }
        }

        return $headers;
    }

    /**
     * Pega a versão do protocolo pelo $_SERVER.
     *
     * @param array<string, string> $server
     */
    public function getProtocolVersion(array $server): string
    {
        if (! isset($server['SERVER_PROTOCOL'])) {
            return '1.1';
        }

        return str_replace('HTTP/', '', $server['SERVER_PROTOCOL']);
    }

    /**
     * Pega o método http pelo $_SERVER.
     *
     * @param array<string, string> $server
     */
    public function getMethod(array $server): string
    {
        if (! isset($server['REQUEST_METHOD'])) {
            return 'GET';
        }

        return $server['REQUEST_METHOD'];
    }

    /**
     * Cria uma Uri pelo $_SERVER.
     *
     * @param array<string, string> $server
     */
    public function getUri(array $server): Uri
    {
        return (new Uri(''))
            ->withScheme(isset($server['HTTPS']) ? 'https' : 'http')
            ->withHost($server['HTTP_HOST'] ?? $server['SERVER_NAME'] ?? $server['SERVER_ADDR'] ?? '')
            ->withPort($server['SERVER_PORT'] ?? '')
            ->withPath(parse_url($server['REQUEST_URI'], PHP_URL_PATH) ?? '')
            ->withQuery($server['QUERY_STRING'] ?? '')
            ->withFragment(parse_url($server['REQUEST_URI'], PHP_URL_FRAGMENT) ?? '');
    }

    /**
     * Pega os arquivos upados pelo $_FILES.
     *
     * @param array<string, array<string, string>> $files
     *
     * @return array<UploadedFile>
     */
    public function getUploadedFiles(array $files): array
    {
        return (new UploadedFileFactory())->createUploadedFilesFromGlobal($files);
    }
}
