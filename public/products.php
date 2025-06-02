<?php

require 'vendor/autoload.php';

use Predis\Client;

$redis = new Client([
    'scheme' => 'tcp',
    'host'   => 'rabbitmq_redis',
    'port'   => 6379,
]);

echo "<h1>Выводим товары из БД redis</h1>";

// Соберём все ключи, начинающиеся на "product:"
$pattern = 'product:*';
$cursor = 0;
$allKeys = [];

do {
    [$cursor, $keys] = $redis->scan($cursor, ['match' => $pattern, 'count' => 100]);

    if (!empty($keys)) {
        $allKeys = array_merge($allKeys, $keys);
    }

} while ($cursor != 0);

foreach ($allKeys as $key) {
    $value = $redis->hgetall($key);
    $value['key'] = $key;

    dump($value);
}

