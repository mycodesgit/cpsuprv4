<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FundCategory extends Model
{
    use HasFactory;

    protected $table = 'fund_categories';

    protected $fillable = [
        'user_id',
        'code',
        'name',
        'description',
        'status',
    ];

    protected $casts = [
        'status' => 'integer',
    ];

    protected static function booted(): void
    {
        // Always store the 3-char suffix uppercase (used in pr_no).
        static::saving(function (FundCategory $fund) {
            $fund->code = strtoupper((string) $fund->code);
        });
    }

    public function purchaseRequests()
    {
        return $this->hasMany(PurchaseRequest::class, 'fund_category_id');
    }
}
