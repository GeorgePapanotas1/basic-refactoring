<?php

namespace App\Versions\B_NoMagicValues;

use App\Contracts\IPriceCalculator;

class PricingCalculator implements IPriceCalculator
{

    private const VIP_RATE = 0.20;
    private const PARTNER_FLAT = 15.0;
    private const DEFAULT_TAX = 0.24;

    private const ROUND_FLOOR = 'floor';
    private const ROUND_CEIL  = 'ceil';
    private const ROUND_STD   = 'round';

    public function priceFor(string $customerType, float $amount, array $options = []): float
    {

        if ($amount === 0.0) {
            return $amount;
        }

        // choose discount
        if ($customerType === 'vip') {
            $discounted = $amount - ($amount * self::VIP_RATE);
        } elseif ($customerType === 'partner') {
            $discounted = max(0.0, $amount - self::PARTNER_FLAT);
        } else {
            $discounted = $amount;
        }

        // cap discount
        $maxDiscount = $options['maxDiscount'] ?? null;
        if ($maxDiscount !== null) {
            $discounted = max($amount - $maxDiscount, $discounted);
        }

        $taxRate = $options['taxRate'] ?? self::DEFAULT_TAX;
        $withTax = $discounted * (1 + $taxRate);

        $mode = $options['rounding'] ?? self::ROUND_STD;

        // Bonus - We can now use a match statement here
        return match ($mode) {
            self::ROUND_FLOOR => floor($withTax),
            self::ROUND_CEIL  => ceil($withTax),
            default           => round($withTax, 2),
        };

    }
}