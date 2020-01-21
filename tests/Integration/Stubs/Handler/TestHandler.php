<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Test\Integration\Stubs\Handler;

class TestHandler
{
    private $handledMessages = [];

    public function getHandledMessages(): array
    {
        return $this->handledMessages;
    }

    public function resetHandledMessages(): void
    {
        $this->handledMessages = [];
    }

    public function __invoke($message): void
    {
        $this->handledMessages[] = $message;
    }
}
