<?php

namespace Minizord\Http\Factory;

use Minizord\Http\Response;
use Psr\Http\Message\ResponseFactoryInterface;

final class ResponseFactory implements ResponseFactoryInterface
{
    /**
     * Cria uma Response.
     */
    public function createResponse(int $code = 200, string $reasonPhrase = ''): Response
    {
        return new Response(status: $code, reason: $reasonPhrase);
    }
}
