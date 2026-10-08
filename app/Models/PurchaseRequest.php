<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PurchaseRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'purchase_requests';

    // Multi-role workflow (see logic.txt).
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PENDING = 'pending';
    public const STATUS_MIS_REVIEW = 'mis_review';
    public const STATUS_PROCUREMENT_REVIEW = 'procurement_review';
    public const STATUS_BUDGET_REVIEW = 'budget_review';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_RETURNED = 'returned';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'user_id',
        'campus_id',
        'office_id',
        'fund_category_id',
        'transaction_no',
        'pr_no',
        'pr_sequence',
        'fiscal_year',
        'purpose',
        'has_ict',
        'total_amount',
        'status',
        'mis_checker_id',
        'procurement_checker_id',
        'budget_checker_id',
        'submitted_at',
        'mis_reviewed_at',
        'procurement_reviewed_at',
        'budget_reviewed_at',
        'approved_at',
        'returned_at',
        'cancelled_at',
        'remarks',
        'cancel_reason',
    ];

    protected $casts = [
        'has_ict' => 'boolean',
        'total_amount' => 'decimal:2',
        'submitted_at' => 'datetime',
        'mis_reviewed_at' => 'datetime',
        'procurement_reviewed_at' => 'datetime',
        'budget_reviewed_at' => 'datetime',
        'approved_at' => 'datetime',
        'returned_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    // --- Relations ---

    public function requester()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function campus()
    {
        return $this->belongsTo(Campus::class, 'campus_id');
    }

    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id');
    }

    public function fundCategory()
    {
        return $this->belongsTo(FundCategory::class, 'fund_category_id');
    }

    public function items()
    {
        return $this->hasMany(PurchaseRequestItem::class, 'purchase_request_id');
    }

    public function fundSource()
    {
        return $this->hasOne(PurchaseRequestFundSource::class, 'purchase_request_id');
    }

    // --- Number generators ---

    /**
     * transaction_no: {OFFICE_ABBR}-{6-char unique}-{YEAR}
     * e.g. MISO-X7K2Q9-2026
     */
    public static function generateTransactionNo(string $officeAbbr, string $year): string
    {
        $abbr = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $officeAbbr) ?: 'GEN');

        do {
            $candidate = sprintf('%s-%s-%s', $abbr, Str::upper(Str::random(6)), $year);
        } while (static::where('transaction_no', $candidate)->exists());

        return $candidate;
    }

    /**
     * pr_no: {YEAR}-{4-digit seq}-{FUND_CODE}  e.g. 2026-0001-GAA
     * Sequence restarts per (year, fund). Call inside a DB transaction
     * with ->lockForUpdate() on the caller side for strict safety; the
     * unique (fiscal_year, fund_category_id, pr_sequence) index is the
     * final guard against races.
     */
    public static function generatePrNo(string $year, FundCategory $fund): array
    {
        $max = (int) static::where('fiscal_year', $year)
            ->where('fund_category_id', $fund->id)
            ->max('pr_sequence');

        $sequence = $max + 1;

        return [
            'pr_no' => sprintf('%s-%04d-%s', $year, $sequence, strtoupper($fund->code)),
            'pr_sequence' => $sequence,
        ];
    }

    /**
     * Recompute the cached header total from line items.
     */
    public function recalculateTotal(): void
    {
        $this->total_amount = (float) $this->items()->sum('total_cost');
        $this->save();
    }
}
