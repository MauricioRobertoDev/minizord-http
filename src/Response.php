<?php

namespace Minizord\Http;

use Psr\Http\Message\ResponseInterface;

final class Response extends AbstractResponse implements ResponseInterface
{
    /**
     * Representação de uma resposta de saída do lado do servidor.
     *
     * @param string $body
     */
    public function __construct(
        int $status = 200,
        array $headers = [],
        $body = null,
        string $version = '1.1',
        string $reason = ''
    ) {
        $this->init($status, $headers, $body, $version, $reason);
    }
}
