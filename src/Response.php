<?php

namespace Minizord\Http;

use Minizord\Http\Contract\ResponseInterface;

class Response implements ResponseInterface
{
    use MessageTrait;

    public function getStatusCode()
    {
    }

    public function withStatus($code, $reasonPhrase = '')
    {
    }

    public function getReasonPhrase()
    {
    }
}
