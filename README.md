[![Build Status](https://travis-ci.com/remotelyliving/php-command-bus.svg?branch=master)](https://travis-ci.org/remotelyliving/php-command-bus)
[![Total Downloads](https://poser.pugx.org/remotelyliving/php-command-bus/downloads)](https://packagist.org/packages/remotelyliving/php-command-bus)
[![Coverage Status](https://coveralls.io/repos/github/remotelyliving/php-command-bus/badge.svg?branch=master)](https://coveralls.io/github/remotelyliving/php-command-bus?branch=master) 
[![License](https://poser.pugx.org/remotelyliving/php-command-bus/license)](https://packagist.org/packages/remotelyliving/php-command-bus)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/remotelyliving/php-command-bus/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/remotelyliving/php-command-bus/?branch=master)

# php-command-bus: 🚍 A Command Bus Implementation For PHP 🚍

### Use Cases

If you want a light weight compliment to your Query Bus for CQRS, hopefully this library helps out.

### Installation

```sh
composer require remotelyliving/php-command-bus
```

### Usage

#### Create the Command Resolver 

The Resolver can have Handlers added manually or locate them in a PSR-11 Service Container
Commands are mapped 1:1 with a handler and are mapped by the Command class name as the lookup key.
```php
$resolver = Resolver::create($serviceContainer) // can locate in service container
    ->pushHandler(Commands\ReserveRoom::class, new Handlers\ReserveRoom()) // can locate in a local map {command => handler}
    ->pushHandlerDeferred(Commands\Checkout::class, fn() => new Handlers\Checkout()); // can locate deferred to save un unnecessary object instantiation

```

#### Create the Command Bus

The Command Bus takes in a Command Resolver and optional PSR-14 Event Dispatcher and pushes whatever Middleware you want on the stack.

```php
$resolver = Resolver::create($container);
$commandBus = CommandBus::create($resolver, $psr14EventDispatcher | null)
    ->pushMiddleware($myMiddleware1);

$command = new Commands\ReserveRoom(123);
$commandBus->handle($command);
```

Middleware is any callable. Some base middleware is included: [src/Middleware](https://github.com/remotelyliving/php-command-bus/tree/master/src/Middleware)

That's really all there is to it!

### Command

The Command for this library is left intentionally unimplemented. It's just an object.
My suggestion for Command objects is to keep them as a DTO of what you need to perform a command. 

An example command might look like this:

```php
class ReserveRoom
{
    private User $user;

    private Room $room;

    public function __construct(User $user, Room $room)
    {
        $this->user = $user;
        $this->room = $room;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getRoom(): Room
    {
        return $this->room;
    }
}
```

As you can see, it's just a few getters

### Handler

The Handlers are where the magic happens. 

- Inject what ever Domain Services you need to perform your command in the contstuctor.
- Perform the logic for the command.
- Yield Domain Events that will be automatically dispatched by the PSR14 Dispatcher you pass in to the Bus
  (When calling a subsequent command from within the handler, make sure your events don't get out of order)
- Execute other commands from within the handler

Going with our ReserveRoom example, a Handler could look like:

```php
class ReserveRoom implements Interfaces\Handler
{
    public function handle(object $command, Interfaces\CommandBus $bus)
    {
        $bus->handle(new Commands\MarkRoomAsReserved($command->getRoom()));
        $bus->handle(new Commands\CompleteInvoiceForRoom($command->getUser(), $command->getRoom()));
         
        yield new Events\RoomWasReserved();
    }
}
```

### Middleware

[Middleware](https://github.com/remotelyliving/php-command-bus/tree/master/src/Middleware) that this library ships with.
The default execution order is LIFO and the signature very simple.

```public function __invoke(Interfaces\Command $command, callable $next);```

A Middleware must pass the command to the $next callable and execute it to continue the execution of the Command.

#### [CommandLogger](https://github.com/remotelyliving/php-command-bus/blob/master/src/Middleware/QueryLogger.php)
Helpful for debugging, but best left for dev and stage environments.
