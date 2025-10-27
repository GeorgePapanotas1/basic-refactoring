<?php

namespace App\Versions\A_EarlyReturns;

use App\Contracts\IPriceCalculator;

class PricingCalculator implements IPriceCalculator
{

    public function priceFor(string $customerType, float $amount, array $options = []): float
    {

        if ($amount === 0.0) {
            return $amount;
        }

        // choose discount
        if ($customerType === 'vip') {
            $discounted = $amount - ($amount * 0.20); // 20%
        } elseif ($customerType === 'partner') {
            $discounted = max(0, $amount - 15); // flat 15
        } else {
            $discounted = $amount; // no discount
        }

        // cap discount
        $maxDiscount = $options['maxDiscount'] ?? null;
        if ($maxDiscount !== null) {
            $discounted = max($amount - $maxDiscount, $discounted);
        }

        // tax and rounding
        $taxRate = $options['taxRate'] ?? 0.24;
        $withTax = $discounted * (1 + $taxRate);

        $mode = $options['rounding'] ?? 'round';
        if ($mode === 'floor') {
            return floor($withTax);
        } elseif ($mode === 'ceil') {
            return ceil($withTax);
        }

        return round($withTax, 2);

    }
}