<?php

namespace App\Service\RabbitMq;

class ConsumerConfig
{
    public function __construct(
        private string $queueName,
        private string $endpoint
    ) {
    }

    public function getQueueName(): string
    {
        return $this->queueName;
    }

    public function getEndpoint(): string
    {
        return $this->endpoint;
    }
}
