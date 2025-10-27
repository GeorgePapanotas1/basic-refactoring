<?php

namespace App\Contracts;

interface IPriceCalculator
{
    public function priceFor(string $customerType, float $amount, array $options = []): float;

}