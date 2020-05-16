<?php

declare(strict_types=1);

namespace RemotelyLiving\PHPCommandBus\Interfaces;

interface Handler
{
    /**
     * @return null|iterable
     */
    public function handle(object $command, CommandBus $bus);
}
