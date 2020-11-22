<?php

declare(strict_types=1);

namespace App\Tests;

use App\BinListProvider;
use App\CommissionService;
use App\RatesProvider;
use PHPUnit\Framework\TestCase;

final class CommissionServiceTest extends TestCase
{
    private $binListData;
    private $ratesData;

    protected function setUp(): void
    {
        $this->binListData = [
            "45717360" => '{"number":{"length":16,"luhn":true},"scheme":"visa","type":"debit","brand":"Traditional","prepaid":false,"country":{"numeric":"208","alpha2":"DK","name":"Denmark","emoji":"🇩🇰","currency":"DKK","latitude":56,"longitude":10},"bank":null}',
            "516793" => '{"number":{},"scheme":"mastercard","type":"debit","brand":"Debit","country":{"numeric":"440","alpha2":"LT","name":"Lithuania","emoji":"🇱🇹","currency":"EUR","latitude":56,"longitude":24},"bank":{}}',
            "45417360" => '{"number":{"length":16,"luhn":true},"scheme":"visa","type":"credit","brand":"Traditional","prepaid":false,"country":{"numeric":"392","alpha2":"JP","name":"Japan","emoji":"🇯🇵","currency":"JPY","latitude":36,"longitude":138},"bank":{"name":"CREDIT SAISON CO., LTD.","url":"corporate.saisoncard.co.jp","phone":"(03)3988-2111"}}',
            "41417360" => '{"number":{},"scheme":"visa","country":{"numeric":"840","alpha2":"US","name":"United States of America","emoji":"🇺🇸","currency":"USD","latitude":38,"longitude":-97},"bank":{"name":"VERMONT NATIONAL BANK","url":"www.communitynationalbank.com","phone":"(802) 744-2287"}}',
            "4745030" => '{"number":{"length":16,"luhn":true},"scheme":"visa","type":"debit","brand":"Traditional","prepaid":null,"country":{"numeric":"826","alpha2":"GB","name":"United Kingdom of Great Britain and Northern Ireland","emoji":"🇬🇧","currency":"GBP","latitude":54,"longitude":-2},"bank":{}}'
        ];
        $this->ratesData = '{"rates":{"CAD":1.5484,"HKD":9.1972,"ISK":161.3,"PHP":57.206,"DKK":7.4489,"HUF":359.5,"CZK":26.34,"AUD":1.6227,"RON":4.8735,"SEK":10.2168,"IDR":16840.24,"INR":87.941,"BRL":6.3347,"RUB":90.2622,"HRK":7.5665,"JPY":123.18,"THB":35.922,"CHF":1.0811,"SGD":1.5934,"PLN":4.4639,"BGN":1.9558,"TRY":9.047,"CNY":7.7916,"NOK":10.6613,"NZD":1.7086,"ZAR":18.2192,"USD":1.1863,"MXN":23.8656,"ILS":3.9608,"GBP":0.89393,"KRW":1323.26,"MYR":4.8549},"base":"EUR","date":"2020-11-20"}';
    }

    /** @dataProvider provider
     * @param $input
     * @param $result
     */
    public function testMainAction($input, $result): void
    {
        $data = json_decode($input);

        $binListProvider = $this->getMockBuilder(BinListProvider::class)
            ->setMethods(['getBinData'])
            ->getMock();

        $binListProvider->expects($this->once())
            ->method('getBinData')
            ->with($this->equalTo($data->bin))
            ->willReturn(json_decode($this->binListData[$data->bin]));

        $ratesProvider = $this->getMockBuilder(RatesProvider::class)
            ->setMethods(['getRateByCurrency'])
            ->getMock();

        $returnData = json_decode($this->ratesData, true);

        $ratesProvider->expects($this->once())
            ->method('getRateByCurrency')
            ->with($this->equalTo($data->currency))
            ->willReturn($returnData['rates'][$data->currency] ?? null);

        $commissionService = new CommissionService($binListProvider, $ratesProvider);

        $this->assertEquals(
            $commissionService->calculateCommission($input),
            $result
        );
    }

    public function provider(): array
    {
        return [
            ['{"bin":"45717360","amount":"100.00","currency":"EUR"}', 1],
            ['{"bin":"516793","amount":"50.00","currency":"USD"}', 0.42],
            ['{"bin":"45417360","amount":"10000.00","currency":"JPY"}', 1.62],
            ['{"bin":"41417360","amount":"130.00","currency":"USD"}', 2.19],
            ['{"bin":"4745030","amount":"2000.00","currency":"GBP"}', 44.75]
        ];
    }
}