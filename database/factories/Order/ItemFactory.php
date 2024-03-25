<?php

namespace Database\Factories\Order;

use App\Models\Order\Item;
use Faker\Factory as Faker;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition()
    {
        return [
            'sku' => '24-MB01',
            'qty' => 1,
            'base_price' => 140000,
            'sell_price' => 140000,
            'discount_amount' => 0,
            'is_indent' => 0,
            'custom_item_attributes' => serialize([]),
            'product_type' => 'simple',
            'vendor_sku' => null
        ];
    }
}
