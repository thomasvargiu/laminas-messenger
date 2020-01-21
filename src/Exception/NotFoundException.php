<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Exception;

use Psr\Container\NotFoundExceptionInterface;

class NotFoundException extends InvalidArgumentException implements NotFoundExceptionInterface
{
}
