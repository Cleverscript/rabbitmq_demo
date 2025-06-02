<?php

require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/config.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;
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

$channel->queue_declare($queue, false, true, false, false);

$channel->exchange_declare($exchange, AMQPExchangeType::DIRECT, false, true, false);

$channel->queue_bind($queue, $exchange);

echo "<h1>Актуализируем товары данными из очереди RabbitMQ</h1>";

for ($i=0; $i <= Config::RABBIT_MQ_CONSUMER_READ_LIMIT; $i++) {

    $message = $channel->basic_get($queue);

    if ($message instanceof AMQPMessage) {

        $message->ack();
        dump($message->body);

        $data = json_decode($message->body, true);

        $redis->hset($data['key'], 'price', $data['price']);
        $redis->hset($data['key'], 'quantity', $data['quantity']);
    }
}

$channel->close();
$connection->close();