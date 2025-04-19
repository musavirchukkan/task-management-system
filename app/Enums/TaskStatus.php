<?php

namespace App\Enums;

enum TaskStatus: string
{
    case PENDING = 'pending';
    case COMPLETED = 'completed';
    case EXPIRED = 'expired';

    /**
     * Get all the enum values as an array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get the default status
     */
    public static function default(): self
    {
        return self::PENDING;
    }
}
