<?php

declare(strict_types=1);

namespace RemotelyLiving\PHPCommandBus\Tests\Integration;

use RemotelyLiving\PHPCommandBus\Interfaces;
use RemotelyLiving\PHPCommandBus\Tests\Stubs;

class MiddlewareTest extends AbstractTestCase
{
    private Interfaces\CommandBus $commandBus;

    protected function setUp(): void
    {
        $this->commandBus = $this->createConfiguredCommandBus();
    }

    public function testLoggerMiddlewareLogsOnlyLoggableCommands(): void
    {
        $this->commandBus->handle(new Stubs\Commands\PublishDraft());
        $this->commandBus->handle(new Stubs\Commands\ReserveRoom());
        $this->assertEquals([
            'level' => 'info',
            'message' => 'Trying to publish a draft',
            'context' => ['draftData' => (new Stubs\Commands\PublishDraft())->getDraftData()]
        ], $this->testLogger->records[0]);

        $this->assertCount(1, $this->testLogger->records);
    }
}
