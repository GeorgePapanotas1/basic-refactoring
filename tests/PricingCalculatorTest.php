<?php

declare(strict_types=1);

namespace App\Tests;

use App\Contracts\IPriceCalculator;
use App\Services\PricingCalculator;
use App\Versions\A_EarlyReturns\PricingCalculator as EarlyReturnsPricingCalculator;
use App\Versions\B_NoMagicValues\PricingCalculator as NoMagicValuesPricingCalculator;
use App\Versions\C_Functions\PricingCalculator as FunctionsPricingCalculator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class PricingCalculatorTest extends TestCase
{
    private IPriceCalculator $sut;

    protected function setUp(): void
    {
//        $this->sut = new PricingCalculator();
//        $this->sut = new EarlyReturnsPricingCalculator();
        $this->sut = new NoMagicValuesPricingCalculator();
//        $this->sut = new FunctionsPricingCalculator();
    }

    public static function provideBasicDiscounts(): array
    {
        return [
            // vip: 20% off, then +24% tax
            'vip 100' => ['vip', 100.0, 99.20], // 100 -> 80; *1.24 = 99.2
            // partner: flat 15 off (not below 0), then +24% tax
            'partner 100' => ['partner', 100.0, 105.40], // 100 -> 85; *1.24 = 105.4
            // other: no discount, +24% tax
            'other 100' => ['other', 100.0, 124.00],
        ];
    }

    #[DataProvider('provideBasicDiscounts')]
    public function test_basic_discounts_with_default_tax_and_rounding(string $customerType, float $amount, float $expected): void
    {
        $this->assertEqualsWithDelta($expected, $this->sut->priceFor($customerType, $amount), 0.00001);
    }

    public function test_partner_discount_cannot_go_below_zero(): void
    {
        // partner 10 -> max(0, 10-15) = 0; tax -> 0
        $this->assertEqualsWithDelta(0.0, $this->sut->priceFor('partner', 10.0), 0.00001);
    }

    public function test_max_discount_cap_applies(): void
    {
        // vip 200 -> 20% off = 160 (discount 40), but cap maxDiscount=30 => min final discount is 30
        // code uses max(amount - maxDiscount, discounted) => max(170, 160) = 170
        // tax 24% => 170 * 1.24 = 210.8
        $price = $this->sut->priceFor('vip', 200.0, ['maxDiscount' => 30.0]);
        $this->assertEqualsWithDelta(210.80, $price, 0.00001);
    }

    public function test_custom_tax_rate_overrides_default(): void
    {
        // other 100, tax 10% => 110.00
        $price = $this->sut->priceFor('other', 100.0, ['taxRate' => 0.10]);
        $this->assertEqualsWithDelta(110.00, $price, 0.00001);
    }

//    public function test_another_custom_tax_rate_overrides_default(): void
//    {
//        // other 100, tax 12% => 112.00
//        $price = $this->sut->priceFor('other', 100.0, ['tax_rate' => 0.12]);
//        $this->assertEqualsWithDelta(112.0, $price, 0.00001);
//    }

    public function test_rounding_floor_mode(): void
    {
        // other 99 -> 99 * 1.24 = 122.76 -> floor => 122
        $price = $this->sut->priceFor('other', 99.0, ['rounding' => 'floor']);
        $this->assertSame(122.0, $price);
    }

    public function test_rounding_ceil_mode(): void
    {
        // other 99 -> 99 * 1.24 = 122.76 -> ceil => 123
        $price = $this->sut->priceFor('other', 99.0, ['rounding' => 'ceil']);
        $this->assertSame(123.0, $price);
    }

    public function test_default_rounding_is_two_decimals(): void
    {
        // choose a case that produces more than 2 decimals before rounding
        // amount 19.99, other: no discount; 19.99 * 1.24 = 24.7876 -> 24.79
        $price = $this->sut->priceFor('other', 19.99);
        $this->assertEqualsWithDelta(24.79, $price, 0.00001);
    }

    #[DataProvider('provideCustomerTypes')]
    public function test_zero_amount_returns_zero_for_all_customer_types(string $customerType): void
    {
        $this->assertSame(0.0, $this->sut->priceFor($customerType, 0.0));
    }

    public static function provideCustomerTypes(): array
    {
        return [
            ['vip'],
            ['partner'],
            ['other'],
            ['unknown'],
        ];
    }

    public function test_zero_amount_ignores_rounding_options(): void
    {
        // Even with rounding modes, zero should short-circuit and remain zero
        $this->assertSame(0.0, $this->sut->priceFor('other', 0.0, ['rounding' => 'floor']));
        $this->assertSame(0.0, $this->sut->priceFor('vip', 0.0, ['rounding' => 'ceil']));
    }

    public function test_zero_amount_ignores_tax_and_max_discount_options(): void
    {
        // Changing taxRate or maxDiscount must not affect zero amounts
        $this->assertSame(0.0, $this->sut->priceFor('partner', 0.0, [
            'taxRate' => 0.50,
            'maxDiscount' => 999.0,
        ]));
    }

    public function test_tiny_non_zero_amount_goes_through_pipeline_with_ceil(): void
    {
        // For a tiny amount, the early return must NOT trigger (since amount !== 0.0),
        // and with rounding=ceil we expect 1.0 after tax.
        // amount = 0.0001, no discount, tax 24% => 0.000124; ceil => 1.0
        $price = $this->sut->priceFor('other', 0.0001, ['rounding' => 'ceil']);
        $this->assertSame(1.0, $price);
    }
}
