<?php

namespace App\Models\Order;

use App\Models\Order;
use Database\Factories\Order\ItemFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Item extends Model
{
    use HasFactory;

    protected $table = 'oms_order_item';
    public $timestamps = false;

    protected static function newFactory(): Factory
    {
        return ItemFactory::new();
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'oms_order_id');
    }
}
