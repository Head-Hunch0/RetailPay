<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'SKU',
    ];

    public function stockItems(): HasMany
    {
        return $this->hasMany(Stock::class, 'productID');
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class, 'productID');
    }

    public function transfers(): HasMany
    {
        return $this->hasMany(Transfer::class, 'productID');
    }
}
