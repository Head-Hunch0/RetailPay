<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'productID',
        'storeID',
        'quantitySold',
        'totalPrice',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'productID');
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'storeID');
    }
}
