<?php

namespace Minizord\Http\Contract;

use Psr\Http\Message\StreamInterface as PsrStreamInterface;

interface StreamInterface extends PsrStreamInterface
{
    public function hasStream() : bool;
}
