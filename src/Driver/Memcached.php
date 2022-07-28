<?php

namespace Bnomei\Driver;

class Memcached implements Driver {

    protected $connection;

    public function __construct(array $options = [])
    {
        $options = $options + [
            'host' => '127.0.0.1',
            'port' => 11211,
        ];

        $this->connection = new \Memcached();
        $this->connection->addServer($options['host'], $options['port']);
    }

    public function beginTransaction() {}

    public function endTransaction() {}

    public function get(string $key, $default = null)
    {
        $value = $this->connection->get($key);
        return $value !== false ? $value : $default;
    }

    public function set(string $key, $data, int $expire = 0): bool
    {
        return $this->connection->set($key, $data, $expire * 60);
    }

    public function remove(string $key): bool
    {
        return $this->connection->delete($key);
    }

    public function flush(): bool
    {
        return $this->connection->flush();
    }
}
