<?php

declare(strict_types=1);

namespace RemotelyLiving\PHPCommandBus\Interfaces;

use RemotelyLiving\PHPCommandBus\Enums;

interface LoggableCommand
{
    public function getLogContext(): array;

    public function getLogMessage(): string;

    public function getLogLevel(): ?Enums\LogLevel;
}
