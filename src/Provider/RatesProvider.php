<?php

declare(strict_types=1);

namespace App;

class RatesProvider
{
    private const RATES_URL = 'https://api.exchangeratesapi.io/latest';

    public function getRateByCurrency(string $currency): ?float
    {
        $ratesData = json_decode(file_get_contents(self::RATES_URL), true);

        return $ratesData && in_array(
            $currency,
            array_keys($ratesData['rates'])
        ) ? $ratesData['rates'][$currency] : null;
    }
}