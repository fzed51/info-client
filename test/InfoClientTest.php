<?php
declare(strict_types=1);

namespace test;

use InfoClient\InfoClient;
use PHPUnit\Framework\TestCase;
use function PHPUnit\Framework\assertEquals;

/**
 * test de InfoClientTest
 */
class InfoClientTest extends TestCase
{

    /** test de getIp */
    public function testGetIp(): void
    {
        $SRV = ['HTTP_X_FORWARDED_FOR' => '93.123.0.1'];
        $client = new InfoClient($SRV);
        assertEquals($SRV['HTTP_X_FORWARDED_FOR'], $client->getIp());
    }

    /** test de getIp with private ip*/
    public function testDontGetIpPrivate(): void
    {
        $SRV = ['HTTP_X_FORWARDED_FOR' => '127.0.0.1'];
        $client = new InfoClient($SRV);
        self::assertNull($client->getIp());
    }

    /** test de getIp with private ip and allow it*/
    public function testGetIpPrivate(): void
    {
        $SRV = ['HTTP_X_FORWARDED_FOR' => '127.0.0.1'];
        $client = new InfoClient($SRV);
        assertEquals($SRV['HTTP_X_FORWARDED_FOR'], $client->getIp(true));
    }

    /** test de getIp */
    public function testGetIpWithSpecificCase(): void
    {
        $SRV = ['remote_addr' => '93.123.0.1'];
        $client = new InfoClient($SRV);
        assertEquals($SRV['remote_addr'], $client->getIp());
    }

    /** test de getAgent */
    public function testGetAgent():void
    {
        $SRV = ['HTTP_USER_AGENT' => 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4400.8 Safari/537.36'];
        $client = new InfoClient($SRV);
        assertEquals($SRV['HTTP_USER_AGENT'], $client->getAgent());
    }
}
