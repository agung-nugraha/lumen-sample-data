<?php

namespace App\Console\Commands\RabbitMq;

use App\Service\RabbitMq\Order as OrderAmqp;
use Exception;
use Illuminate\Console\Command;

class OrderPush extends Command
{
    protected $signature = 'rabbitmq:order-push';
    protected $description = 'Push order to rabbitmq.';

    public function __construct(protected OrderAmqp $order)
    {
        parent::__construct();
    }

    public function handle()
    {
        try {
            $this->order->push();
            $this->info('🚀 Hooray... the order already pushed.');
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
