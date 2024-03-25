<?php

namespace App\Console\Commands\RabbitMq;

use App\Service\RabbitMq\ConsumerConfig;
use App\Service\RabbitMq\Order as OrderAmqp;
use Exception;
use Illuminate\Console\Command;
use Swoole\Coroutine\WaitGroup;

use function Swoole\Coroutine\run;

class OrderPull extends Command
{
    protected $signature = 'rabbitmq:order-pull';
    protected $description = 'Push order to rabbitmq.';

    public function __construct(protected OrderAmqp $order)
    {
        parent::__construct();
    }

    public function handle()
    {
        try {
            run(function () {
                $wg = new WaitGroup(count($this->config()));
                for ($i = 0; $i < count($this->config()); $i++) {
                    go(function () use ($wg, $i) {
                        $wg->add($i);

                        $config = $this->config();
                        $this->order->pull(new ConsumerConfig(
                            $config[$i]['queue'],
                            $config[$i]['endpoint']
                        ));

                        $wg->done();
                    });
                }
                $wg->wait();
            });

            $this->info('🚀 Hooray... the order already pull.');
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    public function config(): array
    {
        return [
            ['queue' => 'swiftoms.order-queue.new', 'endpoint' => 'rest/V1/order/create'],
            ['queue' => 'swiftoms.invoice.new', 'endpoint' => 'rest/V1/invoice/create'],
            ['queue' => 'swiftoms.shipment.new', 'endpoint' => 'rest/V1/shipment/create']
        ];
    }
}
