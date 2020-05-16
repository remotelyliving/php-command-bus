<?php

declare(strict_types=1);

namespace RemotelyLiving\PHPCommandBus\Tests\Stubs\Handlers;

use RemotelyLiving\PHPCommandBus\Interfaces;
use RemotelyLiving\PHPCommandBus\Tests\Stubs\Commands;
use RemotelyLiving\PHPCommandBus\Tests\Stubs\Events;

class ReserveRoom implements Interfaces\Handler
{
    public function handle(object $command, Interfaces\CommandBus $bus)
    {
        \assert(($command instanceof Commands\ReserveRoom), 'Command is instance of ReserveRoom');

        yield new Events\RoomWasReserved();
    }
}
