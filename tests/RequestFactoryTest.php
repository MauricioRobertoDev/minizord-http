<?php

use Minizord\Http\Factory\RequestFactory;
use Psr\Http\Message\RequestInterface;

test('Deve criar uma Request', function () {
    $factory = new RequestFactory();

    $request = $factory->createRequest('GET', 'https://example.com');

    expect($request)->toBeInstanceOf(RequestInterface::class);
});
