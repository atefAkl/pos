<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'type',
        'balance'
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'type' => 'string'
    ];

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}
