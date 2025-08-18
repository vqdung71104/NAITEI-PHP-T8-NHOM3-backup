<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'address_id', 
        'total_price',
        'status'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'total_price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Order status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_RETURN = 'return';

    /**
     * Get all available statuses
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_PROCESSING,
            self::STATUS_COMPLETED,
            self::STATUS_CANCELLED,
            self::STATUS_RETURN,
        ];
    }

    /**
     * Relationship with User model
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship with Address model
     */
    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    /**
     * Relationship with OrderItem model
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get products through order items
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_items')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }

    /**
     * Scope for filtering orders by status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for filtering orders by user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Calculate total price from order items
     */
    public function calculateTotalPrice()
    {
        return $this->orderItems()->with('product')->get()->sum(function ($item) {
            return $item->quantity * $item->product->price;
        });
    }

    /**
     * Check if order can be cancelled
     */
    public function canBeCancelled()
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_PROCESSING]);
    }

    /**
     * Check if order belongs to user
     */
    public function belongsToUser($userId)
    {
        return $this->user_id == $userId;
    }
}
