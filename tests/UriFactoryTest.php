<?php

use Minizord\Http\Factory\UriFactory;
use Psr\Http\Message\UriInterface;

test('Deve criar uma Uri', function () {
    $factory = new UriFactory();
    $uri     = $factory->createUri('https://example.com');

    expect($uri)->toBeInstanceOf(UriInterface::class);
});
