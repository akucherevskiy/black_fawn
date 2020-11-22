<?php

declare(strict_types=1);

namespace App;

class TransactionHelper
{
    public static function invalidTransaction(\stdClass $transaction): bool
    {
        return !isset($transaction->bin) || !isset($transaction->amount) || !isset($transaction->currency);
    }
}