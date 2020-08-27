<?php

namespace Zotapay\Helper;

/**
 * @internal
 */
final class HelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Data Array
     * @return array
     */
    public function getData()
    {
        return [
            [
                // data
                [
                    'merchantID'    => 'api_test_merchant_id',
                    'dateType'      => 'created',
                    'endpointIds'   => '503364,503365',
                    'fromDate'      => '2020-06-01',
                    'statuses'      => 'APPROVED,DECLINED',
                    'toDate'        => '2020-06-30',
                    'types'         => 'SALE,PAYOUT',
                ],

                // ref
                'merchantID=api_test_merchant_id&dateType=created&endpointIds=503364%2C503365&fromDate=2020-06-01&statuses=APPROVED%2CDECLINED&toDate=2020-06-30&types=SALE%2CPAYOUT'
            ],
        ];
    }


    /**
     * Parameters to query string
     *
     * @dataProvider getData
     */
    public function testParametersToQueryString($data, $ref)
    {
        $parametersToQueryString = \Zotapay\Helper\Helper::parametersToQueryString($data);

        static::assertSame($ref, $parametersToQueryString);
    }


    /**
     * Generate uuid
     */
    public function testGenerateUuid()
    {
        $generateUuid = \Zotapay\Helper\Helper::generateUuid();

        $regex = preg_match('/\b[0-9a-f]{8}\b-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-\b[0-9a-f]{12}\b/', $generateUuid);

        static::assertTrue($regex === 1);
    }
}
