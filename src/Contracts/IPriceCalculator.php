<?php

namespace App\Contracts;

use App\Versions\D_NextLesson\Config\PricingOptions;

interface IPriceCalculator
{
    public function priceFor(string $customerType, float $amount, array $options = []): float;

}