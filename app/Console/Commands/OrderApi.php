<?php

namespace App\Console\Commands;

use Faker\Factory as Faker;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class OrderApi extends Command
{
    protected $signature = 'order:import';

    protected $description = 'Insert bulk order with api to swiftoms.';

    public function handle()
    {
        $start = microtime(true);

        $this->runImport();

        $timeCalc = floor((microtime(true) - $start) * 1000);

        $this->info('🚀 Hooray... all orders already pushed.');

        $this->info(sprintf('⏰ Execute time in %s ms', $timeCalc));
    }

    private function runImport(?int $maxMessages = null)
    {
        $disk = storage_path('order/');
        $orderData = json_decode(file_get_contents($disk . 'order-import.json'), true);

        $fake = Faker::create('id_ID');

        $orders = [];
        for ($i = 0; $i < 1000; $i++) {
            if (!isset($orderData['order'])) {
                $this->info('Invalid sample data, please verify again.');
                return 1;
            }

            $order = $orderData['order'];
            $referenceId = '#' . time() + $i;

            $firstname = $fake->firstName();
            $lastname = $fake->lastName();

            $order['id'] = $referenceId;
            $order['channel_code'] = 'SWI';
            $order['increment_id'] = $referenceId;
            $order['ordered_at'] = Carbon::now()->format('Y-m-d H:i:s');
            $order['firstname'] = $firstname;
            $order['lastname'] = $lastname;
            $order['email'] = sprintf(
                '%s.%s@%s',
                strtolower($firstname),
                strtolower($lastname),
                $fake->freeEmailDomain()
            );

            $grandTotal = 0;
            foreach ($order['item_lines'] as $key => $value) {
                $order['item_lines'][$key]['sku'] = $fake->randomElement(['24-MB01']);
                $grandTotal += $value['sell_price'];
            }

            $order['grand_total'] = $grandTotal + $order['shipping']['shipping_cost'];
            $orders[] = $order;
        }

        $successOrders = [];
        // start import
        foreach ($orders as $key => $order) {
            $res = Http::withToken(env('SWIFTOMS_KEY'))
                ->withoutVerifying()
                ->contentType('application/json')
                ->post(env('SWIFTOMS_URL') . '/rest/V1/magentochannel/order', [
                    'order' => $order
                ]);

            $response = json_decode(json_decode($res->body()));
            if ($response?->message != 'failed') {
                $successOrders[] = $key;
            }
        }

        $this->info(sprintf("%d orders has been success.", count($successOrders)));

        // $numberOfFailedMessages = count($failedOrders);
        // if ($numberOfFailedMessages > 0) {
        //     return $this->runImport($numberOfFailedMessages);
        // }
    }
}
