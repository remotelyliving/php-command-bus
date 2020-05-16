<?php

declare(strict_types=1);

namespace RemotelyLiving\PHPCommandBus;

use Psr\EventDispatcher;
use RemotelyLiving\PHPCommandBus\Interfaces;

final class CommandBus implements Interfaces\CommandBus
{
    private Interfaces\Resolver $resolver;

    private EventDispatcher\EventDispatcherInterface $eventDispatcher;

    /**
     * @var callable
     */
    private $callStack;

    public function __construct(
        Interfaces\Resolver $resolver,
        EventDispatcher\EventDispatcherInterface $eventDispatcher = null
    ) {
        $this->resolver = $resolver;
        $this->eventDispatcher = $eventDispatcher ?? $this->createDummyDispatcher();
        $this->callStack = $this->seedCallStack();
    }

    public static function create(
        Interfaces\Resolver $resolver,
        EventDispatcher\EventDispatcherInterface $eventDispatcher = null
    ): self {
        return new static($resolver, $eventDispatcher);
    }

    public function pushMiddleware(callable $middleware): Interfaces\CommandBus
    {
        $next = $this->callStack;

        $this->callStack = function (object $command) use ($middleware, $next): void {
            $middleware($command, $next);
        };

        return $this;
    }

    public function handle(object $command): void
    {
        ($this->callStack)($command);
    }

    private function seedCallStack(): callable
    {
        return function (object $command): void {
            $possibleEvents = $this->resolver->resolve($command)->handle($command, $this);
            if (!$possibleEvents || !is_iterable($possibleEvents)) {
                return;
            }

            foreach ($possibleEvents as $event) {
                $this->eventDispatcher->dispatch($event);
            }
        };
    }

    private function createDummyDispatcher(): EventDispatcher\EventDispatcherInterface
    {
        return  (new class implements EventDispatcher\EventDispatcherInterface {
            public function dispatch(object $event)
            {
                return $event;
            }
        });
    }
}
