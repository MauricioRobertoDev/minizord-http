<?php

use Minizord\Http\Factory\ServerRequestFactory;
use Psr\Http\Message\ServerRequestInterface;

test('Deve criar uma ServerRequest', function () {
    $factory       = new ServerRequestFactory();
    $serverRequest = $factory->createServerRequest('POST', 'https://example.com', []);

    expect($serverRequest)->toBeInstanceOf(ServerRequestInterface::class);
});
