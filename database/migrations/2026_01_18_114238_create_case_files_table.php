<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('case_files', function (Blueprint $table) {
            $table->id();

            $table->foreignId('legal_case_id')
                ->constrained('legal_cases')
                ->cascadeOnDelete();

            $table->string('disk')->default('public');
            $table->string('path');                 // cases/{case_id}/filename.ext
            $table->string('original_name');        // nama asli dari user
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->nullable();

            $table->timestamps();

            $table->index(['legal_case_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('case_files');
    }
};
