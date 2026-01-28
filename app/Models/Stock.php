<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stock extends Model
{
    use HasFactory;

    protected $table = 'stock';

    protected $fillable = [
        'productID',
        'storeID',
        'quantity',
        'minimum',
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
