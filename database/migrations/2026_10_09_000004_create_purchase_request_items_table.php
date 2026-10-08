<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Line items under one PR (replaces v3 `item_request` table).
     *
     * Efficiency fixes vs v3:
     * - Real FKs (purchase_request / category / item / unit) instead of
     *   all-varchar columns with no constraints.
     * - NO redundant user_id / off_id / campid per line; join the parent
     *   purchase_requests row instead (v3 duplicated them on every line).
     * - NO redundant transaction_no string per line for the relation;
     *   kept as a nullable snapshot only for legacy traceability.
     * - Numeric qty/cost columns (v3 stored them as strings) + indexes
     *   for category-filtered item selection.
     */
    public function up(): void
    {
        Schema::create('purchase_request_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('purchase_request_id')->constrained('purchase_requests')->cascadeOnDelete();

            // Item picker: user selects a category first, then an item
            // belonging to it; unit is snapshotted from the catalog item.
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignId('item_id')->nullable()->constrained('items')->nullOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete();

            // Snapshot at request time (catalog prices may change later).
            $table->text('item_description');
            $table->decimal('item_cost', 12, 2)->default(0);
            $table->decimal('qty', 12, 2)->default(1);
            $table->decimal('total_cost', 14, 2)->default(0);

            $table->string('status', 30)->default('pending');
            $table->timestamp('approved_at')->nullable();
            $table->text('remarks')->nullable();

            // Legacy traceability only (nullable, NOT the relation key).
            $table->string('transaction_no', 50)->nullable();

            $table->timestamps();

            $table->index('purchase_request_id');
            $table->index(['category_id', 'item_id']);
            $table->index('item_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_request_items');
    }
};
