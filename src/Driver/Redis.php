<?php

namespace Bnomei\Driver;

class Redis implements Driver {

    protected $connection;
    protected $method;

    public function __construct(array $options = [])
    {
        $options = $options + [
            'host' => '127.0.0.1',
            'port' => 6379,
        ];

        $this->connection = new \Predis\Client($options);
        $this->method = $this->connection;
    }

    public function beginTransaction()
    {
        $this->method = $this->connection->transaction();
    }

    public function endTransaction()
    {
        try {
            $this->method->execute();
        } catch (\Exception $ex) {
            // TODO: ignore errors for now
            // https://redis.io/topics/transactions
            // It's important to note that even when a command fails,
            // all the other commands in the queue are processed –
            // Redis will not stop the processing of commands.
        }
        $this->method = $this->connection;
    }

    public function get(string $key, $default = null)
    {
        $value = $this->method->get($key);
        return $value !== false ? $value : $default;
    }

    public function set(string $key, $data, int $expire = 0): bool
    {
        $status = $this->method->set($key, $data);
        if ($expire) {
            $status = $this->method->expireat(
                $key,
                $expire * 60
            );
        }

        return $status == 'OK' || $status == 'QUEUED';
    }

    public function remove(string $key): bool
    {
        $status = $this->method->del($key);
        if (is_int($status)) {
            return $status > 0;
        }
        if (is_string($status)) {
            return $status === 'QUEUED';
        }
        return false;
    }

    public function flush(): bool
    {
        return $this->connection->flushdb() == 'OK';
    }
}
