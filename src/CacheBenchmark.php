<?php

namespace Bnomei;

use Bnomei\Driver\Apcu;
use Bnomei\Driver\Driver;
use Bnomei\Driver\Memcached;
use Bnomei\Driver\Memory;
use Bnomei\Driver\Redis;

class CacheBenchmark
{
    public static $ITERATIONS = 10000;

    public static function benchmark(Driver $driver, array $options = []): array
    {
        $results = [];
        $duration = 0;

        $driver->flush();
        foreach(['testSet', 'testGet', 'testTransaction'] as $test) {
            $time = -microtime(true);
            $success = self::{$test}($driver);
            $time = round(($time + microtime(true)) * 1000);
            $results[$test] = [
                'duration' => $time,
                'iterations' => self::$ITERATIONS,
                'success' => $success,
            ];
            $duration += $time;
        }
        $results['total_duration'] = $duration;
        $results['driver_options'] = $options;

        return $results;
    }

    public static function all(): array
    {
        $results =
            self::apcu() +
            self::memcached() +
            self::memory() +
            self::redis();

        usort($results, function($a, $b) {
            return $a['duration'] < $b['duration'];
        });

        return $results;
    }

    public static function apcu(array $options = [], string $label = null): array
    {
        return [ ($label ?? 'apcu') => self::benchmark(new Apcu(), $options)];
    }

    public static function memcached(array $options = [], string $label = null): array
    {
        $options = $options + [
            'host' => '127.0.0.1',
            'port' => 11211,
        ];

        return [ ($label ?? 'memcached') => self::benchmark(new Memcached($options), $options)];
    }

    public static function memory(array $options = [], string $label = null): array
    {
        return [ ($label ?? 'memory') => self::benchmark(new Memory($options), $options)];
    }

    public static function redis(array $options = [], string $label = null): array
    {
        $options = $options + [
            'host' => '127.0.0.1',
            'port' => 6379,
        ];

        return [ ($label ?? 'redis') => self::benchmark(new Redis($options), $options)];
    }

    public static function testSet(Driver $driver): bool
    {
        for($i = 0; $i < self::$ITERATIONS; $i++) {
            $success = $driver->set('key-'. $i, __FILE__ . $i);
            if (! $success) {
                return false;
            }
        }
        return true;
    }

    public static function testGet(Driver $driver): bool
    {
        for($i = 0; $i < self::$ITERATIONS; $i++) {
            $driver->set('key-'. $i, __FILE__ . $i);
            $success = $driver->get('key-'. $i) === __FILE__ . $i;
            if (! $success) {
                return false;
            }
        }
        return true;
    }

    public static function testTransaction(Driver $driver): bool
    {
        $driver->beginTransaction();
        for($i = 1; $i <= self::$ITERATIONS; $i++) {
            if($i % 2 === 0) {
                // retrieve 1 on 2, retrieve 3 on 4, ...
                $success = $driver->get('tkey-'. ($i - 1)) === __FILE__ . ($i - 1);
            } else {
                // set on 1, set on 3, ...
                $success = $driver->set('tkey-'. $i,  __FILE__ . $i);
            }
            if (! $success) {
                $driver->endTransaction();
                return false;
            }

        }
        $driver->endTransaction();
        return true;
    }
}
