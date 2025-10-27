<?php

namespace App\Services;

use App\Contracts\IPriceCalculator;

class PricingCalculator implements IPriceCalculator
{

    public function priceFor(string $customerType, float $amount, array $options = []): float
    {

        if ($amount !== 0.0) {
            // choose discount
            if ($customerType === 'vip') {
                $discounted = $amount - ($amount * 0.20); // 20%
            } elseif ($customerType === 'partner') {
                if (($amount - 15) >= 0) {
                    $discounted = $amount - 15;
                } else {
                    $discounted = 0;
                }
            } else {
                $discounted = $amount; // no discount
            }

            // cap discount
            $maxDiscount = $options['maxDiscount'] ?? null;
            if ($maxDiscount !== null) {
                if (($amount - $maxDiscount) >= $discounted) {
                    $discounted = $amount - $maxDiscount;
                }
            }

            // tax and rounding
            if ( isset($options['taxRate'])) {
                $taxRate = $options['taxRate'];
            } else {
                $taxRate = 0.24;
            }

            $withTax = $discounted * (1 + $taxRate);

            $mode = $options['rounding'] ?? 'round';
            if ($mode === 'floor') {
                return floor($withTax);
            } elseif ($mode === 'ceil') {
                return ceil($withTax);
            }

            return round($withTax, 2);
        } else {
            return $amount;
        }

    }
}