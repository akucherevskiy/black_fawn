<?php

declare(strict_types=1);

namespace App;

class CommissionService
{
    private const EUR = 'EUR';
    private const COMMISSION = [
        'EU' => 0.01,
        'NON_EU' => 0.02
    ];

    /**
     * @var BinListProvider
     */
    private $binListProvider;

    /**
     * @var RatesProvider
     */
    private $ratesProvider;

    public function __construct(BinListProvider $binListProvider, RatesProvider $ratesProvider)
    {
        $this->binListProvider = $binListProvider;
        $this->ratesProvider = $ratesProvider;
    }

    public function handle(string $filePath): string
    {
        $fileHandle = fopen($filePath, "r");
        if ($fileHandle === false) {
            return 'Could not get file handle for: ' . $filePath . PHP_EOL;
        }
        $returnData = '';
        while (!feof($fileHandle)) {
            $line = fgets($fileHandle);

            if ($line) {
                $returnData .= ($this->calculateCommission($line) ??
                        "Error occurs while calculating commission ") . PHP_EOL;
            }
        }

        fclose($fileHandle);

        return $returnData;
    }

    public function calculateCommission(string $line): ?float
    {
        $transaction = json_decode($line);
        if (TransactionHelper::invalidTransaction($transaction)) {
            return null;
        }

        $currency = $transaction->currency;
        $binData = $this->binListProvider->getBinData($transaction->bin);
        $finalAmount = $this->getFinalAmount(
            $currency,
            $this->ratesProvider->getRateByCurrency($currency),
            (float)$transaction->amount
        );

        return !$binData ? null : round($finalAmount * $this->getBinCommission($binData), 2);
    }

    private function getBinCommission(\stdClass $binData): ?float
    {
        return CountryHelper::isEu($binData->country->alpha2) ? self::COMMISSION['EU'] : self::COMMISSION['NON_EU'];
    }

    private function getFinalAmount(string $currency, ?float $rate, float $transactionAmount): float
    {
        if ($currency == self::EUR || $rate == 0 || null == $rate) {
            return $transactionAmount;
        }

        if ($currency != self::EUR || $rate > 0) {
            return $transactionAmount / $rate;
        }

        return 0;
    }
}