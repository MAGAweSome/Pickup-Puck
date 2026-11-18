<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'account_recovery_token')) {
                $table->string('account_recovery_token', 100)->nullable()->after('remember_token');
            }
            if (!Schema::hasColumn('users', 'account_recovery_expires_at')) {
                $table->timestamp('account_recovery_expires_at')->nullable()->after('account_recovery_token');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'account_recovery_expires_at')) {
                $table->dropColumn('account_recovery_expires_at');
            }
            if (Schema::hasColumn('users', 'account_recovery_token')) {
                $table->dropColumn('account_recovery_token');
            }
        });
    }
};
