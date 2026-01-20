<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('legal_cases', function (Blueprint $table) {
            $table->id();

            $table->foreignId('client_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('assigned_lawyer_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('case_no')->unique();
            $table->string('title');
            $table->string('category')->nullable(); // nanti bisa jadi practice_area_id
            $table->enum('type', ['consultation','non_litigation','litigation'])->default('consultation');

            $table->enum('status', [
                'draft',
                'submitted',
                'need_info',
                'qualified',
                'assigned',
                'active',
                'waiting_client',
                'waiting_external',
                'resolved',
                'closed',
                'rejected',
                'cancelled',
            ])->default('submitted');

            $table->text('summary'); // cerita client

            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index(['client_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('legal_cases');
    }
};
