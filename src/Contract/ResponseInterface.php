<?php

namespace Minizord\Http\Contract;

use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

interface ResponseInterface extends PsrResponseInterface, MessageInterface
{
}
