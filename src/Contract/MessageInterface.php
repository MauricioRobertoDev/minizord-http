<?php

namespace Minizord\Http\Contract;

use Psr\Http\Message\MessageInterface as PsrMessageInterface;

interface MessageInterface extends PsrMessageInterface
{
    public function inHeader(string $name, string|array $values) : bool;

    public function inHeaderAny(string $name, array $values) : bool;
}
