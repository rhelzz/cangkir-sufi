<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ingredient extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'price',
        'product_id',
    ];
    
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
