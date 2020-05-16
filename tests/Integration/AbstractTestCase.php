<?php

declare(strict_types=1);

namespace RemotelyLiving\PHPCommandBus\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Psr\Log\Test\TestLogger;
use RemotelyLiving\PHPCommandBus\Middleware;
use RemotelyLiving\PHPCommandBus\CommandBus;
use RemotelyLiving\PHPCommandBus\Resolver;
use RemotelyLiving\PHPCommandBus\Interfaces;
use RemotelyLiving\PHPCommandBus\Tests\Stubs;

abstract class AbstractTestCase extends TestCase
{
    protected ?TestLogger $testLogger = null;

    public function getTestLogger(): TestLogger
    {
        if ($this->testLogger === null) {
            $this->testLogger = new TestLogger();
        }

        return $this->testLogger;
    }

    public function createConfiguredCommandBus(): Interfaces\CommandBus
    {
        $commandLogger = new Middleware\CommandLogger();
        $commandLogger->setLogger($this->getTestLogger());

        $container = new Stubs\Container([
            Stubs\Commands\PublishDraft::class => new Stubs\Handlers\PublishDraft(),
        ]);

        $resolver = Resolver::create($container);
        $lazyFn = function (): Interfaces\Handler {
            return new Stubs\Handlers\ReserveRoom();
        };

        $resolver->pushHandlerDeferred(Stubs\Commands\ReserveRoom::class, $lazyFn);

        return CommandBus::create($resolver)
            ->pushMiddleware($commandLogger);
    }
}
