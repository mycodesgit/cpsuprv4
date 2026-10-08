<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * PR header (replaces v3 `purpose` table).
     *
     * Number formats (generated in App\Models\PurchaseRequest):
     *   transaction_no: {OFFICE_ABBR}-{6-char unique}-{YEAR}  e.g. MISO-X7K2Q9-2026
     *   pr_no:          {YEAR}-{4-digit seq}-{FUND_CODE}      e.g. 2026-0001-GAA
     *   pr_sequence is the per-year + per-fund increment backing pr_no.
     *
     * Workflow (see logic.txt):
     *   pending -> mis_review (ICT only) -> procurement_review
     *     -> budget_review -> approved -> completed
     *   Any stage can go to `returned` / `cancelled`.
     */
    public function up(): void
    {
        Schema::create('purchase_requests', function (Blueprint $table) {
            $table->id();

            // Who / where (v3: user_id, camp_id, office_id).
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('campus_id')->nullable()->constrained('campuses')->nullOnDelete();
            $table->foreignId('office_id')->nullable()->constrained('offices')->nullOnDelete();

            // Fund driving the pr_no suffix (v3 had no link; funding_source
            // duplicated user/camp/office/transaction instead of an FK).
            $table->foreignId('fund_category_id')->nullable()->constrained('fund_categories')->nullOnDelete();

            // System-generated identifiers.
            $table->string('transaction_no', 50)->unique();
            $table->string('pr_no', 50)->unique()->nullable();
            $table->unsignedInteger('pr_sequence')->nullable();
            $table->char('fiscal_year', 4);

            // Request content.
            $table->text('purpose');
            $table->boolean('has_ict')->default(false)->comment('true = must pass MIS checker stage');
            $table->decimal('total_amount', 14, 2)->default(0);

            // Multi-role status + per-checker audit trail
            // (v3: single pstatus enum + ~15 string date columns).
            $table->string('status', 30)->default('pending');
            $table->foreignId('mis_checker_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('procurement_checker_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('budget_checker_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('mis_reviewed_at')->nullable();
            $table->timestamp('procurement_reviewed_at')->nullable();
            $table->timestamp('budget_reviewed_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('returned_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('remarks')->nullable();
            $table->text('cancel_reason')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Efficient lookups for dashboard chart, year filter, role queues.
            $table->unique(['fiscal_year', 'fund_category_id', 'pr_sequence'], 'pr_year_fund_seq_unique');
            $table->index(['status', 'fiscal_year']);
            $table->index(['office_id', 'status']);
            $table->index(['campus_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->index(['fund_category_id', 'fiscal_year']);
            $table->index('has_ict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_requests');
    }
};
