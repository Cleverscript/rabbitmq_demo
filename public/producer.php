<?php

require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/config.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;

try {

    $exchange = 'router';
    $queue = 'product_exchange';

    $connection = new AMQPStreamConnection(
        Config::RABBIT_MQ_HOST,
        Config::RABBIT_MQ_PORT,
        Config::RABBIT_MQ_USER,
        Config::RABBIT_MQ_PASS,
        Config::RABBIT_MQ_VHOST
    );

    $channel = $connection->channel();

    $channel->queue_declare($queue, false, true, false, false);

    $channel->exchange_declare($exchange, AMQPExchangeType::DIRECT, false, true, false);

    $channel->queue_bind($queue, $exchange);

    echo "<h1>Добавляем актуальные данные для обновления товаров в очередь RabbitMQ</h1>";

    $products = [
        [
            'name'     => 'Наушники JBL',
            'price'    => rand(999, 5000),
            'quantity' => rand(1, 1000)
        ],
        [
            'name'     => 'Ноутбук Lenovo',
            'price'    => rand(35000, 100000),
            'quantity' => rand(1, 1000)
        ],
        [
            'name'     => 'Смартфон Xiaomi',
            'price'    => rand(5000, 50000),
            'quantity' => rand(1, 1000)
        ]
    ];

    foreach ($products as $product) {
        $product['key'] = 'product:' . md5($product['name']);

        $message = new AMQPMessage(
            json_encode($product, JSON_UNESCAPED_UNICODE),
            ['content_type' => 'application/json', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]
        );

        $channel->basic_publish($message, $exchange);

        dump($product);
    }

    $channel->close();
    $connection->close();

} catch (\Throwable $e) {
    print $e->getMessage();
}