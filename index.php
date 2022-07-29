<pre>
<?php
require __DIR__ . '/vendor/autoload.php';

\Bnomei\Benchmark::$ITERATIONS = 10000; // default

echo json_encode(\Bnomei\Benchmark::all(), JSON_PRETTY_PRINT);
// echo json_encode(\Bnomei\Benchmark::apcu(), JSON_PRETTY_PRINT);
// echo json_encode(\Bnomei\Benchmark::memcached(), JSON_PRETTY_PRINT);
// echo json_encode(\Bnomei\Benchmark::memory(), JSON_PRETTY_PRINT);
// echo json_encode(\Bnomei\Benchmark::redis(), JSON_PRETTY_PRINT);
?>
</pre>
