<?php

declare(strict_types=1);

namespace App\Tests;

use App\Example;
use PHPUnit\Framework\TestCase;

final class ExampleTest extends TestCase
{
    public function testAdd(): void
    {
        $example = new Example();
        $this->assertSame(5, $example->add(2, 3));
        $this->assertSame(0, $example->add(-2, 2));
    }
}
