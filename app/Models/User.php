<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
        ];
    }
    
    /**
     * Check if user is owner
     */
    public function isOwner()
    {
        return $this->role === 'owner';
    }
    
    /**
     * Check if user is cashier
     */
    public function isCashier()
    {
        return $this->role === 'cashier';
    }
    
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    
    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }
}
