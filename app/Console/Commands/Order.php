<?php

namespace App\Console\Commands;

use App\Models\Order as ModelsOrder;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Factories\Sequence;

class Order extends Command
{
    protected $signature = 'order:create';

    protected $description = 'Insert bulk order swiftoms.';

    public function handle()
    {
        // insert direct to database.
        ModelsOrder::factory()->count(1000)
            ->hasItems(1)
            ->sequence(fn (Sequence $sequence) => [
                'channel_order_increment_id' => 'SWIFT' . time() + $sequence->index,
                'channel_code' => 'SWI'
            ])
            ->create();
    }
}
