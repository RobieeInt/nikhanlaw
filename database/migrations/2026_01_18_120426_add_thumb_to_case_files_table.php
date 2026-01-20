<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('case_files', function (Blueprint $table) {
            $table->string('thumb_path')->nullable()->after('path');
            $table->boolean('is_image')->default(false)->after('mime_type');
        });
    }

    public function down(): void
    {
        Schema::table('case_files', function (Blueprint $table) {
            $table->dropColumn(['thumb_path', 'is_image']);
        });
    }
};
