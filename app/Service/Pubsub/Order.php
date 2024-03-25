<?php

namespace App\Service\Pubsub;

class Order extends Pubsub
{
    public function push(): void
    {
        $topic = $this->getTopic('CNX_ORDER_FETCHED_32');
        $topic->publishBatch([]);
    }

    public function fetch()
    {
        // TODO: Implement fetch() method.
    }
}
