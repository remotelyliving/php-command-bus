<?php

declare(strict_types=1);

namespace RemotelyLiving\PHPCommandBus;

use Psr\Container;
use RemotelyLiving\PHPCommandBus\Exceptions;
use RemotelyLiving\PHPCommandBus\Interfaces;

final class Resolver implements Interfaces\Resolver
{
    private ?Container\ContainerInterface $container;

    /**
     * @var \RemotelyLiving\PHPCommandBus\Interfaces\Handler[]
     */
    private array $map = [];

    /**
     * @var callable[]
     */
    private array $deferred = [];

    public function __construct(Container\ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public static function create(Container\ContainerInterface $container = null): Interfaces\Resolver
    {
        return new static($container);
    }

    public function resolve(object $command): Interfaces\Handler
    {
        $commandClass = get_class($command);

        if ($this->container && $this->container->has($commandClass)) {
            return $this->container->get($commandClass);
        }

        if (isset($this->map[$commandClass])) {
            return $this->map[$commandClass];
        }

        if (isset($this->deferred[$commandClass])) {
            $this->map[$commandClass] = $this->deferred[$commandClass]();
            unset($this->deferred[$commandClass]);
            return $this->map[$commandClass];
        }

        throw new Exceptions\OutOfBounds("Command Handler for {$commandClass} not found");
    }

    public function pushHandler(string $commandClass, Interfaces\Handler $handler): Interfaces\Resolver
    {
        $this->map[$commandClass] = $handler;

        return $this;
    }

    public function pushHandlerDeferred(string $class, callable $handlerFn): Interfaces\Resolver
    {
        $this->deferred[$class] = $handlerFn;

        return $this;
    }
}
