<?php

declare(strict_types=1);

namespace RemotelyLiving\PHPCommandBus\Enums;

use MyCLabs\Enum\Enum;

/**
 * @psalm-immutable
 *
 * @method static \RemotelyLiving\PHPCommandBus\Enums\LogLevel EMERGENCY()
 * @method static \RemotelyLiving\PHPCommandBus\Enums\LogLevel ALERT()
 * @method static \RemotelyLiving\PHPCommandBus\Enums\LogLevel CRITICAL()
 * @method static \RemotelyLiving\PHPCommandBus\Enums\LogLevel WARNING()
 * @method static \RemotelyLiving\PHPCommandBus\Enums\LogLevel NOTICE()
 * @method static \RemotelyLiving\PHPCommandBus\Enums\LogLevel INFO()
 * @method static \RemotelyLiving\PHPCommandBus\Enums\LogLevel DEBUG()
 */
final class LogLevel extends Enum
{
    private const EMERGENCY = 'emergency';
    private const ALERT = 'alert';
    private const CRITICAL = 'critical';
    private const WARNING = 'warning';
    private const NOTICE = 'notice';
    private const INFO = 'info';
    private const DEBUG = 'debug';
}
