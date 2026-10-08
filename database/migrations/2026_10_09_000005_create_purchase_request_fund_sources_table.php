<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Fund source per PR (based on v3 `funding_source` table).
     *
     * v3 columns kept: user_id, camp_id->campus_id, office_id,
     * transaction_no, financing_source, fund_cluster, fund_category,
     * fund_auth, specific_fund, reasons, allotment, mooe_amount,
     * co_amount, account_code, amount.
     * (v3 `purpose_id` model field is now purchase_request_id,
     * pointing at purchase_requests.id.)
     *
     * Improvements vs v3:
     * - Real FKs (purchase_request / user / campus / office) instead of
     *   plain integer/string columns with no constraints.
     * - purchase_request_id is UNIQUE (one fund source per PR, matching
     *   the intended hasOne); v3 linked via the transaction_no string.
     * - transaction_no kept only as a nullable legacy snapshot + index,
     *   NOT the relation key.
     * - Money columns are decimal (v3 stored them as strings).
     * - Added status + verified_by/verified_at so the Budget Officer
     *   checker step has its own queue/audit (pending/verified/returned).
     * - Dropped rememberToken (meaningless on a non-auth table).
     */
    public function up(): void
    {
        Schema::create('purchase_request_fund_sources', function (Blueprint $table) {
            $table->id();

            $table->foreignId('purchase_request_id')->unique()->constrained('purchase_requests')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('campus_id')->nullable()->constrained('campuses')->nullOnDelete();
            $table->foreignId('office_id')->nullable()->constrained('offices')->nullOnDelete();

            $table->string('transaction_no', 50)->nullable();
            $table->string('financing_source')->nullable();
            $table->string('fund_cluster')->nullable();
            $table->string('fund_category')->nullable();
            $table->string('fund_auth')->nullable();
            $table->string('specific_fund')->nullable();
            $table->text('reasons')->nullable();
            $table->string('allotment')->nullable();
            $table->decimal('mooe_amount', 14, 2)->nullable();
            $table->decimal('co_amount', 14, 2)->nullable();
            $table->text('account_code')->nullable();
            $table->decimal('amount', 14, 2)->nullable();

            // Budget checker workflow.
            $table->string('status', 30)->default('pending');
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->text('remarks')->nullable();

            $table->timestamps();

            $table->index('transaction_no');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_request_fund_sources');
    }
};
