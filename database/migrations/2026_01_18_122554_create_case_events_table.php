<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('case_events', function (Blueprint $table) {
            $table->id();

            $table->foreignId('legal_case_id')
                ->constrained('legal_cases')
                ->cascadeOnDelete();

            $table->foreignId('actor_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('actor_role')->nullable(); // client/admin/lawyer (string aja dulu)
            $table->string('event'); // case_created, case_updated, files_added, file_deleted, status_changed, etc

            $table->string('status_from')->nullable();
            $table->string('status_to')->nullable();

            $table->text('note')->nullable(); // free text / summary of change

            $table->json('meta')->nullable(); // extra: filenames, counts, etc

            $table->timestamps();

            $table->index(['legal_case_id', 'created_at']);
            $table->index(['event']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('case_events');
    }
};
