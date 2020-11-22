<?php

declare(strict_types=1);

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use App\CountryHelper;

final class CountryHelperTest extends TestCase
{
    public function testIsEu(): void
    {
        $this->assertEquals(
            true,
            CountryHelper::isEu('MT')
        );
    }

    public function testNonIsEu(): void
    {
        $this->assertEquals(
            false,
            CountryHelper::isEu('UA')
        );
    }
}