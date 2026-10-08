<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequestItem extends Model
{
    use HasFactory;

    protected $table = 'purchase_request_items';

    protected $fillable = [
        'purchase_request_id',
        'category_id',
        'item_id',
        'unit_id',
        'item_description',
        'item_cost',
        'qty',
        'total_cost',
        'status',
        'approved_at',
        'remarks',
        'transaction_no',
    ];

    protected $casts = [
        'item_cost' => 'decimal:2',
        'qty' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        // Keep line total always in sync: qty × unit cost.
        static::saving(function (PurchaseRequestItem $line) {
            $line->total_cost = (float) $line->qty * (float) $line->item_cost;
        });
    }

    public function purchaseRequest()
    {
        return $this->belongsTo(PurchaseRequest::class, 'purchase_request_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }
}
