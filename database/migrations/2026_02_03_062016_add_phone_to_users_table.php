<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 32)->nullable()->unique()->after('email');
            $table->timestamp('phone_verified_at')->nullable()->after('phone');
            $table->boolean('is_active')->default(false)->after('phone_verified_at');

            $table->string('phone_otp_hash', 255)->nullable()->after('phone_verified_at');
            $table->timestamp('phone_otp_expires_at')->nullable()->after('phone_otp_hash');
            $table->unsignedSmallInteger('phone_otp_attempts')->default(0)->after('phone_otp_expires_at');
            $table->timestamp('phone_otp_sent_at')->nullable()->after('phone_otp_attempts');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['phone']);
            $table->dropColumn([
                'phone',
                'phone_verified_at',
                'phone_otp_hash',
                'phone_otp_expires_at',
                'phone_otp_attempts',
                'phone_otp_sent_at',
            ]);
        });
    }
};
