<?php
declare(strict_types=1);

namespace Test;

use PHPUnit\Framework\TestCase as PhpUnitTestCase;

/**
 * test de TestCase
 */
class TestCase extends PhpUnitTestCase
{
    /**
     * @param array<int|string, mixed> $keysNeedle
     * @param mixed $actual
     */
    public static function assertArrayHasKeys(array $keysNeedle, $actual): void
    {
        self::assertIsArray($actual, 'la valeur testée doit être un tableau');
        foreach ($keysNeedle as $key) {
            self::assertArrayHasKey($key, $actual);
        }
    }

    /**
     * @param array<string,string> $expected
     * @param mixed $actual
     */
    public static function assertInfoEqual(array $expected, $actual): void
    {
        self::assertArrayHasKeys(array_keys($expected), $actual);
        foreach ($expected as $key => $val) {
            self::assertEquals($val, $actual[$key]);
        }
    }
}
