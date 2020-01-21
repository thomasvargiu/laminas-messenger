<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Test\Exception;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use TMV\Laminas\Messenger\Exception\ExceptionInterface;
use TMV\Laminas\Messenger\Exception\NotFoundException;

class NotFoundExceptionTest extends TestCase
{
    public function testInstance(): void
    {
        $this->assertInstanceOf(ExceptionInterface::class, new NotFoundException());
        $this->assertInstanceOf(InvalidArgumentException::class, new NotFoundException());
    }
}
