<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Lookup table for campuses.
     * Needed so purchase_requests.campus_id can be a real FK
     * (v4 users.campus_id is still a plain string).
     */
    public function up(): void
    {
        Schema::create('campuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('campus_name')->unique();
            $table->string('campus_abbr', 20)->unique()->nullable();
            $table->enum('status', [1, 2])->default(1)->comment('1=Enabled, 2=Disabled');
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campuses');
    }
};
