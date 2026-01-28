<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'branchID',
        'managerID',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branchID');
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'managerID');
    }

    public function stockItems(): HasMany
    {
        return $this->hasMany(Stock::class, 'storeID');
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class, 'storeID');
    }

    public function outgoingTransfers(): HasMany
    {
        return $this->hasMany(Transfer::class, 'fromStoreID');
    }

    public function incomingTransfers(): HasMany
    {
        return $this->hasMany(Transfer::class, 'toStoreID');
    }
}
