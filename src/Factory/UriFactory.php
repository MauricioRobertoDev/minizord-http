<?php

namespace Minizord\Http\Factory;

use Minizord\Http\Uri;
use Psr\Http\Message\UriFactoryInterface;

class UriFactory implements UriFactoryInterface
{
    /**
     * Cria uma Uri.
     */
    public function createUri(string $uri = ''): Uri
    {
        return new Uri($uri);
    }
}
