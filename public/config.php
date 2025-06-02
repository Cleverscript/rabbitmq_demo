<?php

class Config
{
    const RABBIT_MQ_HOST = 'rabbitmq';
    const RABBIT_MQ_PORT = 5672;
    const RABBIT_MQ_USER = 'guest';
    const RABBIT_MQ_PASS = 'guest';
    const RABBIT_MQ_VHOST = '/';
    const RABBIT_MQ_CONSUMER_READ_LIMIT = 10;
}