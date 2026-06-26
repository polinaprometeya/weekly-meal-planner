<?php

namespace App\Services;

use App\Enums\DefaultUnit;

class UnitConversionService
{
    public function convert(float|string $amount, string $fromUnit, DefaultUnit $toUnit): float
    {
        $amount = (float) $amount;
        $fromUnit = strtolower($fromUnit);
        $target = $toUnit->value;

        if ($fromUnit === $target) {
            return $amount;
        }

        $normalized = match ($fromUnit) {
            'kg' => $amount * 1000,
            'g' => $amount,
            'l' => $amount * 1000,
            'ml' => $amount,
            'dl' => $amount * 100,
            'tsk' => $amount,
            'stk' => $amount,
            default => $amount,
        };

        return match ($target) {
            'g' => match ($fromUnit) {
                'kg', 'g' => $normalized,
                default => $amount,
            },
            'ml' => match ($fromUnit) {
                'l', 'ml', 'dl' => $normalized,
                default => $amount,
            },
            'stk', 'tsk', 'dl' => $amount,
            default => $amount,
        };
    }
}
