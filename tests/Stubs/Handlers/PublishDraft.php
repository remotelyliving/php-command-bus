<?php

declare(strict_types=1);

namespace RemotelyLiving\PHPCommandBus\Tests\Stubs\Handlers;

use RemotelyLiving\PHPCommandBus\Interfaces;
use RemotelyLiving\PHPCommandBus\Tests\Stubs\Commands;

class PublishDraft implements Interfaces\Handler
{
    public function handle(object $command, Interfaces\CommandBus $bus)
    {
        \assert(($command instanceof Commands\PublishDraft), 'Command is instance of ReserveRoom');
    }
}
