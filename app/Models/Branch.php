<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'managerID',
    ];

    public function manager()
    {
        return $this->belongsTo(User::class, 'managerID');
    }

    public function stores()
    {
        return $this->hasMany(Store::class, 'branchID');
    }
}
