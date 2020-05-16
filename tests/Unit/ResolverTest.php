<?php

declare(strict_types=1);

namespace RemotelyLiving\PHPCommandBus\Tests\Unit;

use RemotelyLiving\PHPCommandBus\Exceptions;
use RemotelyLiving\PHPCommandBus\Interfaces;
use RemotelyLiving\PHPCommandBus\Resolver;
use RemotelyLiving\PHPCommandBus\Tests\Stubs;

class ResolverTest extends AbstractTestCase
{
    private Stubs\Container $emptyContainer;

    private Stubs\Container $containerHandlerSet;

    private Interfaces\Resolver $resolver;

    protected function setUp(): void
    {
        $this->emptyContainer = new Stubs\Container([]);
        $this->containerHandlerSet = new Stubs\Container([
            Stubs\Commands\ReserveRoom::class => new Stubs\Handlers\ReserveRoom(),
        ]);

        $this->resolver = Resolver::create();
    }

    public function testResolvesCommandsWhenFound(): void
    {
        $reserveRoomHandler = new Stubs\Handlers\ReserveRoom();
        $publishDraftHandler = new Stubs\Handlers\PublishDraft();

        $this->resolver
            ->pushHandler(Stubs\Commands\ReserveRoom::class, $reserveRoomHandler)
            ->pushHandler(Stubs\Commands\PublishDraft::class, $publishDraftHandler);

        $this->assertInstanceOf(
            Stubs\Handlers\ReserveRoom::class,
            $this->resolver->resolve(new Stubs\Commands\ReserveRoom())
        );
    }

    public function testResolvesCommandsWhenFoundInContainer(): void
    {
        $this->resolver = Resolver::create($this->containerHandlerSet);
        $this->assertInstanceOf(
            Stubs\Handlers\ReserveRoom::class,
            $this->resolver->resolve(new Stubs\Commands\ReserveRoom())
        );
    }

    public function testResolvesCommandsWhenHandlerDeferred(): void
    {
        $this->resolver->pushHandlerDeferred(
            Stubs\Commands\ReserveRoom::class,
            fn() => new Stubs\Handlers\ReserveRoom()
        );

        $this->assertInstanceOf(
            Stubs\Handlers\ReserveRoom::class,
            $this->resolver->resolve(new Stubs\Commands\ReserveRoom())
        );
    }

    public function testThrowsOutOfBoundsWhenNoHandlerFound(): void
    {
        $this->expectException(Exceptions\OutOfBounds::class);
        $this->resolver->resolve(new Stubs\Commands\PublishDraft());
    }

    public function testThrowsOutOfBoundsWhenNoHandlerFoundInContainer(): void
    {
        $this->resolver = Resolver::create($this->emptyContainer);
        $this->expectException(Exceptions\OutOfBounds::class);
        $this->resolver->resolve(new Stubs\Commands\PublishDraft());
    }
}
