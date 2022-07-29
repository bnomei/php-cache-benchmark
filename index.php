<pre>
<?php
require __DIR__ . '/vendor/autoload.php';

\Bnomei\CacheBenchmark::$ITERATIONS = 10000; // default

// echo json_encode(\Bnomei\CacheBenchmark::all(), JSON_PRETTY_PRINT);
// echo json_encode(\Bnomei\CacheBenchmark::apcu(), JSON_PRETTY_PRINT);
// echo json_encode(\Bnomei\CacheBenchmark::memcached(), JSON_PRETTY_PRINT);
echo json_encode(\Bnomei\CacheBenchmark::memory(), JSON_PRETTY_PRINT);
// echo json_encode(\Bnomei\CacheBenchmark::redis(), JSON_PRETTY_PRINT);
?>
</pre>
