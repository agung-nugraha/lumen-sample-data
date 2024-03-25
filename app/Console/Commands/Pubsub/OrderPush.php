<?php

namespace App\Console\Commands\Pubsub;

use App\Service\Pubsub\Order;
use Exception;
use Illuminate\Console\Command;

class OrderPush extends Command
{
    protected $signature = 'pubsub:push-order';

    protected $description = 'Push Order to Pubsub';

    public function __construct(private Order $order)
    {
        parent::__construct();
    }

    public function handle()
    {
        try {
            $this->order->push();
            $this->info('🚀 Hooray... the order alread pushed.');
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
