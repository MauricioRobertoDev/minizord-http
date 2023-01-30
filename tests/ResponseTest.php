<?php

use Minizord\Http\Response;
use Minizord\Http\Stream;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

test('Deve criar uma nova response', function () {
    $response = new Response();
    expect($response)->toBeInstanceOf(PsrResponseInterface::class);
    expect($response->getBody())->toBeInstanceOf(Stream::class);

    $response = new Response(body: 'batata');
    expect($response)->toBeInstanceOf(PsrResponseInterface::class);
    expect($response->getBody())->toBeInstanceOf(Stream::class);
    expect($response->getBody()->getContents())->toBe('batata');

    $response = new Response(status: 100);
    expect($response)->toBeInstanceOf(PsrResponseInterface::class);
    expect($response->getStatusCode())->toBe(100);

    $response = new Response(headers: ['any_name' => 'any_value']);
    expect($response)->toBeInstanceOf(PsrResponseInterface::class);
    expect($response->getHeaders())->toBe(['any_name' => ['any_value']]);

    $response = new Response(reason: 'Batata');
    expect($response)->toBeInstanceOf(PsrResponseInterface::class);
    expect($response->getReasonPhrase())->toBe('Batata');
});

test('Deve criar uma nova instância com o status passado', function () {
    $response = new Response(status: 100);

    expect($response->withStatus(200))->not()->toBe($response);
    expect(fn () => $response->withStatus(null))->toThrow(InvalidArgumentException::class);
    expect(fn () => $response->withStatus(99))->toThrow(InvalidArgumentException::class);
    expect(fn () => $response->withStatus(600))->toThrow(InvalidArgumentException::class);
});
