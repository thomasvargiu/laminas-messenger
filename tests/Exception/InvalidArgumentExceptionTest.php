<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger;

use PHPUnit\Framework\TestCase;
use TMV\Laminas\Messenger\Exception\ExceptionInterface;
use TMV\Laminas\Messenger\Exception\InvalidArgumentException;

class InvalidArgumentExceptionTest extends TestCase
{
    public function testInstance(): void
    {
        $this->assertInstanceOf(ExceptionInterface::class, new InvalidArgumentException());
        $this->assertInstanceOf(\InvalidArgumentException::class, new InvalidArgumentException());
    }
}
