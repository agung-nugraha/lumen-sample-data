<?php

namespace App\Service\RabbitMq;

use Exception;
use PhpAmqpLib\Connection\AMQPStreamConnection;

abstract class AbstractQueue
{
    private ?AMQPStreamConnection $connection = null;

    /**
     * Channel.
     *
     * @throws Exception
     */
    public function getChannel()
    {
        if (!$this->connection) {
            $this->connection = $this->createConnection();
        }
        return $this->connection->channel();
    }

    /**
     * Create connection.
     *
     * @throws Exception
     */
    protected function createConnection(): AMQPStreamConnection
    {
        return new AMQPStreamConnection(
            env('AMQP_HOST'),
            env('AMQP_PORT'),
            env('AMQP_USER'),
            env('AMQP_PASSWORD'),
            env('AMQP_VHOST')
        );
    }
}
