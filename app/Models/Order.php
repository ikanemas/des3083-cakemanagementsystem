<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'order_number',
        'user_id',
        'menu_item_id',
        'cake_name',
        'cake_price',
        'delivery_date',
        'frosting',
        'toppings',
        'phone',
        'address',
        'remark',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'cake_price' => 'decimal:2',
            'delivery_date' => 'date',
            'toppings' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class);
    }
}
