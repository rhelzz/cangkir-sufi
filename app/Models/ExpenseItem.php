<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExpenseItem extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'expense_id',
        'item_name',
        'price',
    ];
    
    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }
}
