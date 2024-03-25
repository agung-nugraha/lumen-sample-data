<?php

namespace App\Service\RabbitMq;

use Exception;
use Illuminate\Support\Facades\Http;
use PhpAmqpLib\Message\AMQPMessage;

class Order extends AbstractQueue
{
    const QUEUE = 'swiftoms.invoice.new';
    const EXCHANGE = '';

    /**
     * Push order to rabbitmq.
     *
     * @throws Exception
     */
    public function push()
    {
        $filepath = storage_path('order/') . 'order-rabbitmq.json';
        $orders = json_decode(file_get_contents($filepath), true);

        foreach (array_chunk($orders, 100) as $order) {
            // prepare message to be push
            $message = new AMQPMessage(json_encode(['invoice' => $order]));
            $this->getChannel()->basic_publish(
                $message,
                static::EXCHANGE ?? '',
                static::QUEUE
            );
        }
    }

    public function pull(ConsumerConfig $config)
    {
        $channel = $this->getChannel();

        $channel->basic_qos(0, 100, false);
        $channel->basic_consume(
            $config->getQueueName(),
            '',
            false,
            false,
            false,
            false,
            function (AMQPMessage $message) use ($config) {
                $endpoint = 'https://swiftoms.test/' . $config->getEndpoint();
                $res = Http::withBody($message->getBody())
                    ->withToken('picmeifs7fwvlfp41ujamntsy6ip08hf')
                    ->withoutVerifying()
                    ->post($endpoint);

                if ($res->ok()) {
                    $message->ack();

                    echo "Success process data on: " . $endpoint . PHP_EOL;
                } else {
                    echo "Failed: " . $endpoint . PHP_EOL;
                }
            }
        );

        while ($channel->is_consuming()) {
            $channel->wait();
        }
    }
}
