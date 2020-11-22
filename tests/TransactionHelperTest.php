<?php

declare(strict_types=1);

namespace App\Tests;

use App\TransactionHelper;
use PHPUnit\Framework\TestCase;

final class TransactionHelperTest extends TestCase
{
    public function testInvalidTransaction(): void
    {
        $this->assertEquals(
            true,
            TransactionHelper::invalidTransaction(json_decode('{"bin":"45717360","amount":"100.00","ncy":"EUR"}'))
        );
    }

    public function testValidTransaction(): void
    {
        $this->assertEquals(
            false,
            TransactionHelper::invalidTransaction(json_decode('{"bin":"45717360","amount":"100.00","currency":"EUR"}'))
        );
    }
}