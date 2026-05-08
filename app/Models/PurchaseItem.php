<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseItem extends Model
{
    protected $fillable = [
        'purchase_id',
        'desc',
        'qty',
        'price',
        'total'
    ];

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }
}
