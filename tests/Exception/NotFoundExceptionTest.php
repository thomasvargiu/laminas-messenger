<?php

declare(strict_types=1);

namespace TMV\Messenger\Test\Exception;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use TMV\Messenger\Exception\ExceptionInterface;
use TMV\Messenger\Exception\NotFoundException;

class NotFoundExceptionTest extends TestCase
{
    public function testInstance(): void
    {
        $this->assertInstanceOf(ExceptionInterface::class, new NotFoundException());
        $this->assertInstanceOf(InvalidArgumentException::class, new NotFoundException());
    }
}
