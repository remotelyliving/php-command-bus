<?php

declare(strict_types=1);

namespace RemotelyLiving\PHPCommandBus\Tests\Stubs\Commands;

use RemotelyLiving\PHPCommandBus\Enums;
use RemotelyLiving\PHPCommandBus\Interfaces;

class PublishDraft implements Interfaces\LoggableCommand
{
    public function getDraftData(): array
    {
        return ['some draft data'];
    }

    public function getLogContext(): array
    {
        return ['draftData' => $this->getDraftData()];
    }

    public function getLogMessage(): string
    {
        return 'Trying to publish a draft';
    }

    public function getLogLevel(): ?Enums\LogLevel
    {
        return null;
    }
}
