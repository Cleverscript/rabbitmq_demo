<?php

require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/config.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use Predis\Client;

$redis = new Client([
    'scheme' => 'tcp',
    'host'   => 'rabbitmq_redis',
    'port'   => 6379,
]);

$exchange = 'router';
$queue = 'product_exchange';
$consumerTag = 'consumer';

$connection = new AMQPStreamConnection(
    Config::RABBIT_MQ_HOST,
    Config::RABBIT_MQ_PORT,
    Config::RABBIT_MQ_USER,
    Config::RABBIT_MQ_PASS,
    Config::RABBIT_MQ_VHOST
);

$channel = $connection->channel();

echo "Ожидание сообщений. Нажмите Ctrl+C для выхода.\n";

$callback = function ($message) use ($channel, $exchange, $queue, $redis) {
    echo "Получено сообщение: \n";

    echo "$message->body \n";

    $data = json_decode($message->body, true);

    $redis->hset($data['key'], 'price', $data['price']);
    $redis->hset($data['key'], 'quantity', $data['quantity']);
};

$channel->basic_consume($queue, $consumerTag, false, true, false, false, $callback);

// Цикл прослушивания
while ($channel->is_consuming()) {
    $channel->wait();
}
