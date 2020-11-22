<?php

declare(strict_types=1);

namespace App;

class BinListProvider
{
    private const BIN_URL = 'https://lookup.binlist.net/';

    private function validBin(\stdClass $binData): bool
    {
        return !empty($binData->country->alpha2);
    }

    public function getBinData(string $bin): ?\stdClass
    {
        $binData = json_decode(file_get_contents(self::BIN_URL . $bin));

        return $binData && $this->validBin($binData) ? $binData : null;
    }
}