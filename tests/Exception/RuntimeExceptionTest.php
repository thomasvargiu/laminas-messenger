<?php

declare(strict_types=1);

namespace TMV\Messenger;

use PHPUnit\Framework\TestCase;
use TMV\Messenger\Exception\ExceptionInterface;
use TMV\Messenger\Exception\RuntimeException;

class RuntimeExceptionTest extends TestCase
{
    public function testInstance(): void
    {
        $this->assertInstanceOf(ExceptionInterface::class, new RuntimeException());
        $this->assertInstanceOf(\RuntimeException::class, new RuntimeException());
    }
}
