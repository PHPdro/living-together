<?php

namespace App\Enums;

enum ExpenseType: string
{
    case Fixed = 'fixed';
    case Variable = 'variable';

    /**
     * Get the human-readable label for the enum value.
     */
    public function label(): string
    {
        return match ($this) {
            self::Fixed => 'Fixa',
            self::Variable => 'Variável',
        };
    }

    /**
     * Get the Filament color associated with the enum value.
     */
    public function color(): string
    {
        return match ($this) {
            self::Fixed => 'info',
            self::Variable => 'warning',
        };
    }
}
