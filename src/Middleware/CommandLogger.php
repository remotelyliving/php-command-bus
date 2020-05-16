<?php

declare(strict_types=1);

namespace RemotelyLiving\PHPCommandBus\Middleware;

use Psr\Log;
use RemotelyLiving\PHPCommandBus\Enums;
use RemotelyLiving\PHPCommandBus\Interfaces;
use RemotelyLiving\PHPCommandBus\Traits;

final class CommandLogger implements Log\LoggerAwareInterface
{
    use Traits\Logger;

    public function __invoke(object $command, callable $next): void
    {
        if ($command instanceof Interfaces\LoggableCommand) {
            $this->getLogger()->log(
                (string) ($command->getLogLevel() ?? Enums\LogLevel::INFO()),
                $command->getLogMessage(),
                $command->getLogContext()
            );
        }

        $next($command);
    }
}
