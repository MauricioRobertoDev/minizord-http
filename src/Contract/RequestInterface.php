<?php

namespace Minizord\Http\Contract;

use Psr\Http\Message\RequestInterface as PsrRequestInterface;

interface RequestInterface extends PsrRequestInterface, MessageInterface
{
}
