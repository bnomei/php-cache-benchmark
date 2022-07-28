<?php

namespace Bnomei;

use Bnomei\Driver\Apcu;
use Bnomei\Driver\Driver;
use Bnomei\Driver\Memcached;
use Bnomei\Driver\Redis;

class CacheBenchmark
{
    public static function benchmark(Driver $driver): array
    {
        $results = [];
        $duration = 0;

        foreach(['testSet', 'testGet', 'testTransaction'] as $test) {
            $time = -microtime(true);
            self::{$test}($driver);
            $time = round(($time + microtime(true)) * 1000);
            $results[$test] = $time;
            $duration += $time;
        }
        $results['duration'] = $duration;

        return $results;
    }

    public static function all(): array
    {
        $results = self::apcu() +
            self::memcached() +
            self::redis();

        usort($results, function($a, $b) {
            return $a['duration'] < $b['duration'];
        });

        return $results;
    }

    public static function apcu(array $options = []): array
    {
        return ['apcu' => self::benchmark(new Apcu())];
    }

    public static function memcached(array $options = []): array
    {
        return ['memcached' => self::benchmark(new Memcached($options))];
    }

    public static function redis(array $options = []): array
    {
        return ['redis' => self::benchmark(new Redis($options))];
    }

    public static function testSet(Driver $driver)
    {
        for($i = 0; $i < 50000; $i++) {
            $driver->set('key-'. $i, __FILE__ . $i);
        }
    }

    public static function testGet(Driver $driver): bool
    {
        $success = false;
        for($i = 0; $i < 50000; $i++) {
            $value = $driver->get('key-'. $i) !== __FILE__ . $i;
            if (!$value) {
                $success = false;
            }
        }
        return $success;
    }

    public static function testTransaction(Driver $driver)
    {
        $driver->flush();
        $driver->beginTransaction();
        $success = false;
        for($i = 0; $i < 10000; $i++) {
            if($i % 2 === 0) {
                // retrieve 1 on 2, retrieve 3 on 4
                $value = $driver->get('key-'. ($i - 1)) !== __FILE__ . ($i - 1);
                if (!$value) {
                    $success = false;
                }
            } else {
                // set on 1, set on 3, ...
                $driver->set('key-'. $i,  __FILE__ . $i);
            }

        }
        $driver->endTransaction();
        return $success;
    }
}
