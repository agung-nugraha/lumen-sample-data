<?php

namespace App\Models;

use App\Models\Order\Item;
use Database\Factories\OrderFactory as OrderFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $table = 'oms_order';
    public $timestamps = false;

    public function items(): HasMany
    {
        return $this->hasMany(Item::class, 'oms_order_id');
    }
}
