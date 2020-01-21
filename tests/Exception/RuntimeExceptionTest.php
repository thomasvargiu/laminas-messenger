<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger;

use PHPUnit\Framework\TestCase;
use TMV\Laminas\Messenger\Exception\ExceptionInterface;
use TMV\Laminas\Messenger\Exception\RuntimeException;

class RuntimeExceptionTest extends TestCase
{
    public function testInstance(): void
    {
        $this->assertInstanceOf(ExceptionInterface::class, new RuntimeException());
        $this->assertInstanceOf(\RuntimeException::class, new RuntimeException());
    }
}
