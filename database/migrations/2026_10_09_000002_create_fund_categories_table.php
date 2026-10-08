<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Fund categories that drive the PR number suffix.
     * pr_no format: {YEAR}-{SEQUENCE}-{CODE}  e.g. 2026-0001-GAA
     * where CODE is fund_categories.code (exactly 3 chars).
     */
    public function up(): void
    {
        Schema::create('fund_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->char('code', 3)->unique()->comment('3-char suffix used in pr_no, e.g. GAA');
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->enum('status', [1, 2])->default(1)->comment('1=Enabled, 2=Disabled');
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fund_categories');
    }
};
