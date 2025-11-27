<?php

namespace App\Versions\D_NextLesson\Config;

use App\Versions\D_NextLesson\Enums\RoundingModes;

class PricingOptions
{
    public function __construct(
        public ?float $maxDiscount = null,
        public ?float $taxRate = null,
        public RoundingModes $roundingMode = RoundingModes::ROUND,
    )
    {

    }

    public static function withDefaults(): self
    {
        return new self();
    }

    public function getRoundingMode(): RoundingModes
    {
        return $this->roundingMode;
    }

    public function setRoundingMode(RoundingModes $roundingMode): PricingOptions
    {
        $this->roundingMode = $roundingMode;

        return $this;
    }

    public function getTaxRate(): ?float
    {
        return $this->taxRate;
    }

    public function setTaxRate(?float $taxRate): PricingOptions
    {
        $this->taxRate = $taxRate;

        return $this;
    }

    public function getMaxDiscount(): ?float
    {
        return $this->maxDiscount;
    }

    public function setMaxDiscount(?float $maxDiscount): PricingOptions
    {
        $this->maxDiscount = $maxDiscount;

        return $this;
    }


}