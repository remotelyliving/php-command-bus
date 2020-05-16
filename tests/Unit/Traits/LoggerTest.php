<?php

declare(strict_types=1);

namespace RemotelyLiving\PHPCommandBus\Tests\Unit\Traits;

use Psr\Log;
use RemotelyLiving\PHPCommandBus\Tests;
use RemotelyLiving\PHPCommandBus\Traits;

class LoggerTest extends Tests\Unit\AbstractTestCase
{
    private Log\Test\TestLogger $testLogger;

    private Log\LoggerAwareInterface $loggableClass;

    protected function setUp(): void
    {
        $this->testLogger = $this->createTestLogger();
        $this->loggableClass = new class implements Log\LoggerAwareInterface {
            use Traits\Logger;

            public function getTraitLogger(): Log\LoggerInterface
            {
                return $this->getLogger();
            }
        };
    }

    public function testDefaultLoggerIsNullLogger(): void
    {
        $this->assertInstanceOf(Log\NullLogger::class, $this->loggableClass->getTraitLogger());
    }

    public function testSetsLogger(): void
    {
        $this->loggableClass->setLogger($this->testLogger);
        $this->assertInstanceOf(Log\Test\TestLogger::class, $this->loggableClass->getTraitLogger());
    }

    public function testLogs(): void
    {
        $this->loggableClass->setLogger($this->testLogger);
        $this->loggableClass->getTraitLogger()->log('critical', 'the message', ['the' => 'context']);

        $this->assertSame([
            'level' => 'critical',
            'message' => 'the message',
            'context' => ['the' => 'context'],
        ], $this->testLogger->records[0]);
    }
}
