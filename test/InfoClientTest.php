<?php
declare(strict_types=1);

namespace Test;

use InfoClient\InfoClient;

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
        self::assertEquals($SRV['HTTP_X_FORWARDED_FOR'], $client->getIp());
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
        self::assertEquals($SRV['HTTP_X_FORWARDED_FOR'], $client->getIp(true));
    }

    /** test de getIp */
    public function testGetIpWithSpecificCase(): void
    {
        $SRV = ['remote_addr' => '93.123.0.1'];
        $client = new InfoClient($SRV);
        self::assertEquals($SRV['remote_addr'], $client->getIp());
    }

    /** test de getAgent */
    public function testGetAgent():void
    {
        $SRV = ['HTTP_USER_AGENT' => 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4400.8 Safari/537.36'];
        $client = new InfoClient($SRV);
        self::assertEquals($SRV['HTTP_USER_AGENT'], $client->getAgent());
    }

    /** test de getAgent */
    public function testGetInfos():void
    {
        $SRV = ['HTTP_USER_AGENT' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/97.0.4692.71 Safari/537.36'];
        $client = new InfoClient($SRV);
        self::assertInfoEqual([
            'browser'=>'Google Chrome',
            'version'=>'97.0.4692.71',
            'platform'=>'windows'
        ], $client->getInfos());

        $SRV = ['HTTP_USER_AGENT' => 'Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML like Gecko) Chrome/44.0.2403.155 Safari/537.36'];
        $client = new InfoClient($SRV);
        self::assertInfoEqual([
            'browser'=>'Google Chrome',
            'version'=>'44.0.2403.155',
            'platform'=>'windows'
        ], $client->getInfos());

        $SRV = ['HTTP_USER_AGENT' => 'Mozilla/5.0 (X11; Ubuntu; Linux i686 on x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2820.59 Safari/537.36'];
        $client = new InfoClient($SRV);
        self::assertInfoEqual([
            'browser'=>'Google Chrome',
            'version'=>'53.0.2820.59',
            'platform'=>'linux'
        ], $client->getInfos());

        $SRV = ['HTTP_USER_AGENT' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.102 Safari/537.36 Edge/18.19577'];
        $client = new InfoClient($SRV);
        self::assertInfoEqual([
            'browser'=>'Edge',
            'version'=>'18.19577',
            'platform'=>'windows'
        ], $client->getInfos());


        $SRV = ['HTTP_USER_AGENT' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2656.18 Safari/537.36'];
        $client = new InfoClient($SRV);
        self::assertInfoEqual([
            'browser'=>'Google Chrome',
            'version'=>'49.0.2656.18',
            'platform'=>'mac'
        ], $client->getInfos());
    }
}
