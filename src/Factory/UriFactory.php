<?php

namespace Minizord\Http\Factory;

use Minizord\Http\Uri;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;

class UriFactory implements UriFactoryInterface
{
    /**
     * Cria uma Uri.
     *
     * @param  string       $uri
     * @return UriInterface
     */
    public function createUri(string $uri = '') : UriInterface
    {
        return new Uri($uri);
    }
}
