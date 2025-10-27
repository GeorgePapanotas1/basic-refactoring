<?php

namespace App\Versions\D_NoConfigArrays;

use App\Contracts\IPriceCalculator;
use App\Versions\D_NoConfigArrays\Config\PricingOptions;

class PricingCalculator implements IPriceCalculator
{

    private const VIP_RATE = 0.20;
    private const PARTNER_FLAT = 15.0;
    private const DEFAULT_TAX = 0.24;

    private const ROUND_FLOOR = 'floor';
    private const ROUND_CEIL  = 'ceil';
    private const ROUND_STD   = 'round';

    public function priceFor(string $customerType, float $amount, PricingOptions $options): float
    {

        if ($amount === 0.0) {
            return $amount;
        }
        $discounted = $this->applyBaseDiscount($customerType, $amount);

        $discounted = $this->applyCap($amount, $discounted, $options['maxDiscount'] ?? null);

        $withTax = $this->applyTax($discounted, $options['taxRate'] ?? null);

        return $this->applyRounding($withTax, $options['rounding'] ?? null);
    }

    private function applyBaseDiscount(string $customerType, float $amount): float
    {
        return match ($customerType) {
            'vip'     => $amount - ($amount * self::VIP_RATE),
            'partner' => max(0.0, $amount - self::PARTNER_FLAT),
            default   => $amount,
        };
    }

    private function applyCap(float $amount, float $discounted, ?float $maxDiscount): float
    {
        return $maxDiscount !== null ? max($amount - $maxDiscount, $discounted) : $discounted;
    }

    private function applyTax(float $price, ?float $taxRate): float
    {
        $rate = $taxRate ?? self::DEFAULT_TAX;
        return $price * (1 + $rate);
    }

    private function applyRounding(float $price, ?string $mode): float
    {
        $m = $mode ?? self::ROUND_STD;
        return match ($m) {
            self::ROUND_FLOOR => floor($price),
            self::ROUND_CEIL  => ceil($price),
            default           => round($price, 2),
        };
    }
}