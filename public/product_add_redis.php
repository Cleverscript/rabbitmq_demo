<?php

require 'vendor/autoload.php';

use Predis\Client;

try {
    $redis = new Client([
        'scheme' => 'tcp',
        'host'   => 'rabbitmq_redis',
        'port'   => 6379,
    ]);


    echo "<h1>Добавляем товары в БД redis</h1>";

    $products = [
        [
            'name'     => 'Наушники JBL',
            'price'    => 4990,
            'quantity' => 12
        ],
        [
            'name'     => 'Ноутбук Lenovo',
            'price'    => 55690,
            'quantity' => 10
        ],
        [
            'name'     => 'Смартфон Xiaomi',
            'price'    => 25800,
            'quantity' => 100
        ]
    ];

    foreach ($products as $product) {
        // Ключ в Redis
        $productKey = 'product:' . md5($product['name']);

        // Если товар с таким ключем уже есть то не добавляем
        if ($redis->exists($productKey)) continue;

        // Сохраняем данные как хеш
        $redis->hmset($productKey, $product);

        echo "Товар сохранён в Redis под ключом: $productKey \n\n";
    }

} catch (\Throwable $e) {
    print $e->getMessage();
}