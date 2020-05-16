<?php

declare(strict_types=1);

namespace RemotelyLiving\PHPCommandBus\Interfaces;

interface CommandBus
{
    public function handle(object $command): void;

    public function pushMiddleware(callable $middleware): self;
}
