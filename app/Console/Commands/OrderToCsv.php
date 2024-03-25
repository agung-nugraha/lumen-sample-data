<?php

namespace App\Console\Commands;

use App\Models\Order\Item;
use Illuminate\Console\Command;
use App\Models\Order as ModelsOrder;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Support\Collection as SupportCollection;

class OrderToCsv extends Command
{
    protected $signature = 'order:create-csv';

    protected $description = 'Insert bulk order swiftoms.';

    public function handle()
    {
        $lastId = 113950;
        $lastItemId = 120633;

        // insert direct to database.
        $orders = ModelsOrder::factory()
            ->count(10000)
            ->sequence(fn (Sequence $sequence) => [
                'id' => $lastId + $sequence->index,
                'channel_order_increment_id' => 'SWIFT' . time() + $sequence->index,
                'channel_code' => 'SWI'
            ])->afterMaking(function (ModelsOrder $order) use (&$lastItemId) {
                $lastItemId += 1;
                $order->setRelation(
                    'items',
                    Item::factory()->count(1)->sequence(fn (Sequence $sequence) => [
                        'id' => $lastItemId,
                        'oms_order_id' => $order->id
                    ])->make()
                );
            })->make();

        $this->generateCsv($orders, 'order');
    }

    protected function generateCsv($orders, string $name)
    {
        $orderDir = storage_path('order/');
        $orderPath = $orderDir . $name . '.csv';

        $orderItems = new SupportCollection();
        try {
            $handle = fopen($orderPath, 'w');

            $header = array_keys($orders->first()->toArray());
            if ($name == 'order') {
                array_pop($header);
            }

            fputcsv($handle, $header);
            $orders->map(function ($order) use ($handle, $orderItems, $name) {
                $orderArr = array_values($order->toArray());
                if ($name == 'order') {
                    array_pop($orderArr);
                }

                fputcsv($handle, $orderArr);
                if (method_exists($order, 'items') && !$order->items->isEmpty()) {
                    $orderItems->push($order->items->first());
                }
            });

            fclose($handle);
        } catch (\Exception $e) {
            File::delete($orderPath);

            $this->error($e->getMessage());
        }

        if ($orderItems->isNotEmpty()) {
            $this->generateCsv($orderItems, 'items');
        }
    }
}
