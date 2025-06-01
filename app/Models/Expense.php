<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Expense extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'description',
        'expense_date',
        'user_id',
    ];
    
    protected $casts = [
        'expense_date' => 'date',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function items()
    {
        return $this->hasMany(ExpenseItem::class);
    }
    
    public function getTotalAmountAttribute()
    {
        return $this->items->sum('price');
    }
}
