<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequestFundSource extends Model
{
    use HasFactory;

    protected $table = 'purchase_request_fund_sources';

    // Budget checker workflow.
    public const STATUS_PENDING = 'pending';
    public const STATUS_VERIFIED = 'verified';
    public const STATUS_RETURNED = 'returned';

    protected $fillable = [
        'purchase_request_id',
        'user_id',
        'campus_id',
        'office_id',
        'transaction_no',
        'financing_source',
        'fund_cluster',
        'fund_category',
        'fund_auth',
        'specific_fund',
        'reasons',
        'allotment',
        'mooe_amount',
        'co_amount',
        'account_code',
        'amount',
        'status',
        'verified_by',
        'verified_at',
        'remarks',
    ];

    protected $casts = [
        'mooe_amount' => 'decimal:2',
        'co_amount' => 'decimal:2',
        'amount' => 'decimal:2',
        'verified_at' => 'datetime',
    ];

    public function purchaseRequest()
    {
        return $this->belongsTo(PurchaseRequest::class, 'purchase_request_id');
    }

    public function encoder()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function campus()
    {
        return $this->belongsTo(Campus::class, 'campus_id');
    }

    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id');
    }
}
