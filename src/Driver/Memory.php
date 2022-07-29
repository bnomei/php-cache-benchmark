<?php

namespace Bnomei\Driver;

class Memory implements Driver {

    protected $cache;

    public function beginTransaction() {}

    public function endTransaction() {}

    public function get(string $key, $default = null)
    {
        $value = $this->cache[$key];
        return $value !== false ? $value : $default;
    }

    public function set(string $key, $data, int $expire = 0): bool
    {
        $this->cache[$key] = $data; // ignore expire
        return true;
    }

    public function remove(string $key): bool
    {
        unset($this->cache[$key]);
        return true;
    }

    public function flush(): bool
    {
        $this->cache = [];
        return true;
    }
}
