<?php

declare(strict_types=1);

namespace RemotelyLiving\PHPCommandBus\Tests\Unit;

use Psr\EventDispatcher;
use RemotelyLiving\PHPCommandBus\Interfaces;
use RemotelyLiving\PHPCommandBus\CommandBus;

class CommandBusTest extends AbstractTestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\RemotelyLiving\PHPCommandBus\Interfaces\Command
     */
    private $command;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\RemotelyLiving\PHPCommandBus\Interfaces\Handler
     */
    private $handler;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\RemotelyLiving\PHPCommandBus\Interfaces\Resolver
     */
    private $resolver;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Psr\EventDispatcher\EventDispatcherInterface
     */
    private $dispatcher;

    private CommandBus $bus;

    protected function setUp(): void
    {
        $this->command = $this->createMock(\stdClass::class);
        $this->handler = $this->createMock(Interfaces\Handler::class);
        $this->resolver = $this->createMock(Interfaces\Resolver::class);
        $this->dispatcher = $this->createMock(EventDispatcher\EventDispatcherInterface::class);
        $this->resolver->method('resolve')
            ->with($this->command)
            ->willReturn($this->handler);

        $this->bus = CommandBus::create($this->resolver, $this->dispatcher);
    }

    public function testHandleCommand(): void
    {
        $this->handler->expects($this->once())
            ->method('handle')
            ->with($this->command);

        $this->bus->handle($this->command);
    }

    public function testPushesMiddlewareLIFO(): void
    {
        $calledMiddleware = [];

        $middleware1 = function (object $command, callable $next) use (&$calledMiddleware) {
            $calledMiddleware[] = 'middleware1';
            $next($command);
        };

        $middleware2 = function (object $command, callable $next) use (&$calledMiddleware) {
            $calledMiddleware[] = 'middleware2';
            $next($command);
        };

        $middleware3 = function (object $command, callable $next) use (&$calledMiddleware) {
            $calledMiddleware[] = 'middleware3';
            $next($command);
        };

        $this->bus->pushMiddleware($middleware2)
            ->pushMiddleware($middleware3)
            ->pushMiddleware($middleware1);

        $this->bus->handle($this->command);

        $this->assertSame(['middleware1', 'middleware3', 'middleware2'], $calledMiddleware);
    }


    public function testNotDispatchEventsIfNoneReturnedFromHandler(): void
    {
        $this->dispatcher->expects($this->never())
            ->method('dispatch');

        $this->bus->handle($this->command);
    }

    public function testDispatchesEventsIfReturnedFromHandler(): void
    {
        $event1 = new class {
        };
        $event2 = new class {
        };

        $this->handler->method('handle')
            ->with($this->command)
            ->willReturn([$event1, $event2]);

        $this->dispatcher->expects($this->exactly(2))
            ->method('dispatch')
            ->withConsecutive([$event1], [$event2]);

        $this->bus->handle($this->command);
    }
}
