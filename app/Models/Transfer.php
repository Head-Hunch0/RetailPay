<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'productID',
        'fromStoreID',
        'toStoreID',
        'quantity',
        'requestedBy',
        'approvedBy',
        'status',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'productID');
    }

    public function fromStore(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'fromStoreID');
    }

    public function toStore(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'toStoreID');
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requestedBy');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approvedBy');
    }
    
}
