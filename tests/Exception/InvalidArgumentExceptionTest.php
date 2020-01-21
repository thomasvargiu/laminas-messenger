<?php

declare(strict_types=1);

namespace TMV\Messenger;

use PHPUnit\Framework\TestCase;
use TMV\Messenger\Exception\ExceptionInterface;
use TMV\Messenger\Exception\InvalidArgumentException;

class InvalidArgumentExceptionTest extends TestCase
{
    public function testInstance(): void
    {
        $this->assertInstanceOf(ExceptionInterface::class, new InvalidArgumentException());
        $this->assertInstanceOf(\InvalidArgumentException::class, new InvalidArgumentException());
    }
}
