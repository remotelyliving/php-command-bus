<?php

declare(strict_types=1);

namespace RemotelyLiving\PHPCommandBus\Tests\Unit\Middleware;

use Psr\Log;
use RemotelyLiving\PHPCommandBus\Enums;
use RemotelyLiving\PHPCommandBus\Interfaces;
use RemotelyLiving\PHPCommandBus\Middleware;
use RemotelyLiving\PHPCommandBus\Tests;

class CommandLoggerTest extends Tests\Unit\AbstractTestCase
{
    private Log\Test\TestLogger $testLogger;

    private Middleware\CommandLogger $commandLogger;

    protected function setUp(): void
    {
        $this->testLogger = $this->createTestLogger();
        $this->commandLogger = new Middleware\CommandLogger();
        $this->commandLogger->setLogger($this->testLogger);
    }

    public function testSetsInfoAsDefaultIfLogLevelNotDefined(): void
    {
        $commandWithLogLevelNotDefined = new class implements Interfaces\LoggableCommand {

            public function getLogContext(): array
            {
                return ['foo' => 'bar'];
            }

            public function getLogMessage(): string
            {
                return 'The log message';
            }

            public function getLogLevel(): ?Enums\LogLevel
            {
                return null;
            }
        };

        ($this->commandLogger)($commandWithLogLevelNotDefined, fn() => null);

        $this->assertEquals(
            [
            'level' => 'info',
            'message' => 'The log message',
            'context' => ['foo' => 'bar'],
            ],
            $this->testLogger->records[0]
        );
    }

    public function testSetsInfoAsDefaultIfLogLevelIfDefined(): void
    {
        $commandWithLogLevelDefined = new class implements Interfaces\LoggableCommand {

            public function getLogContext(): array
            {
                return ['foo' => 'bar'];
            }

            public function getLogMessage(): string
            {
                return 'The log message';
            }

            public function getLogLevel(): ?Enums\LogLevel
            {
                return Enums\LogLevel::DEBUG();
            }
        };

        ($this->commandLogger)($commandWithLogLevelDefined, fn() => null);

        $this->assertEquals(
            [
                                'level' => 'debug',
                                'message' => 'The log message',
                                'context' => ['foo' => 'bar'],
                            ],
            $this->testLogger->records[0]
        );
    }
}
