<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('case_applications', function (Blueprint $table) {
            $table->id();

            $table->foreignId('legal_case_id')
                ->constrained('legal_cases')
                ->cascadeOnDelete();

            $table->foreignId('lawyer_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

            $table->text('note')->nullable(); // optional: pesan lawyer saat apply / note admin

            $table->timestamps();

            $table->unique(['legal_case_id', 'lawyer_id']); // 1 lawyer apply 1x per case
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('case_applications');
    }
};
