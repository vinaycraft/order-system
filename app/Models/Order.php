<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\OrderItem;
use App\Models\MenuItem;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'total_amount'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'status' => 'string'
    ];

    // Define valid status values
    const STATUS_PENDING = 'pending';
    const STATUS_PREPARING = 'preparing';
    const STATUS_READY = 'ready';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_CANCELLED = 'cancelled';

    public static function getValidStatuses()
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_PREPARING,
            self::STATUS_READY,
            self::STATUS_DELIVERED,
            self::STATUS_CANCELLED
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function menuItems()
    {
        return $this->belongsToMany(MenuItem::class, 'order_items')
            ->withPivot(['quantity', 'unit_price', 'subtotal', 'special_instructions']);
    }
}
