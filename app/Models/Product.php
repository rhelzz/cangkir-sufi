<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'description',
        'selling_price',
        'cost_price',
        'stock',
        'image',
        'category_id',
    ];
    
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    
    public function ingredients()
    {
        return $this->hasMany(Ingredient::class);
    }
    
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
    
    public function calculateProfit()
    {
        return $this->selling_price - $this->cost_price;
    }
}
