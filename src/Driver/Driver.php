<?php

namespace Bnomei\Driver;

interface Driver
{
    public function get(string $key, $default = null);
    public function set(string $key, $data, int $expire = 0): bool;
    public function remove(string $key): bool;
    public function flush(): bool;
    public function beginTransaction();
    public function endTransaction();
}
