<?php

namespace Minizord\Http\Contract;

use Psr\Http\Message\ServerRequestInterface as PsrServerRequestInterface;

interface ServerRequestInterface extends PsrServerRequestInterface, MessageInterface
{
}
