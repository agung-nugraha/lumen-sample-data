<?php

namespace App\Console\Commands\Pubsub;

use App\Service\Pubsub\Product;
use Exception;
use Illuminate\Console\Command;

class ProductPush extends Command
{
    protected $signature = 'pubsub:push-product';

    protected $description = 'Push Product to Pubsub';

    public function __construct(private Product $product)
    {
        parent::__construct();
    }

    public function handle()
    {
        try {
            $this->product->push();
            $this->info('🚀 Hooray... the product alread pushed.');
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
