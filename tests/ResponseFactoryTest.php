<?php

use Minizord\Http\Factory\ResponseFactory;
use Psr\Http\Message\ResponseInterface;

test('Deve criar uma Response', function () {
    $factory  = new ResponseFactory();
    $response = $factory->createResponse(200, 'OK');

    expect($response)->toBeInstanceOf(ResponseInterface::class);
});
