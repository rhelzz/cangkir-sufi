<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'order_number',
        'total_amount',
        'tax',
        'discount',
        'final_amount',
        'payment_method',
        'user_id',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
    
    public static function generateOrderNumber()
    {
        $latest = self::latest()->first();
        
        if (!$latest) {
            return 'ORD-' . date('Ymd') . '0001';
        }
        
        $string = preg_replace('/[^0-9]/', '', $latest->order_number);
        $lastNumber = intval($string);
        $newNumber = $lastNumber + 1;
        
        return 'ORD-' . date('Ymd') . sprintf('%04d', $newNumber % 10000);
    }
}
