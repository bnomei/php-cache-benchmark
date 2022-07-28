<?php

namespace Bnomei\Driver;

class Apcu implements Driver {

    public function get(string $key, $default = null)
    {
        $value = apcu_fetch($key);
        return $value !== false ? $value : $default;
    }

    public function beginTransaction() {}

    public function endTransaction() {}

    public function set(string $key, $data, int $expire = 0): bool
    {
        return apcu_store($key, $data, $expire);
    }

    public function remove(string $key): bool
    {
        return apcu_delete($key);
    }

    public function flush(): bool
    {
        return apcu_clear_cache();
    }
}
