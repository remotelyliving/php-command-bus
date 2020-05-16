<?php

declare(strict_types=1);

namespace RemotelyLiving\PHPCommandBus\Interfaces;

interface Resolver
{
    /**
     * @throws \RemotelyLiving\PHPCommandBus\Exceptions\OutOfBounds
     */
    public function resolve(object $command): Handler;

    public function pushHandler(string $commandClass, Handler $handler): self;

    public function pushHandlerDeferred(string $handler, callable $handlerFn): self;
}
