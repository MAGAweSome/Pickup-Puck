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
        // 1. Add notification preference toggle to users table
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'notify_team_assignment')) {
                $table->boolean('notify_team_assignment')->default(true)->after('completed_onboarding');
            }
        });

        // 2. Create push_subscriptions table
        if (!Schema::hasTable('push_subscriptions')) {
            Schema::create('push_subscriptions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->text('endpoint');
                $table->string('public_key')->nullable();
                $table->string('auth_token')->nullable();
                $table->string('content_encoding', 50)->default('aes128gcm');
                $table->timestamps();

                $table->index(['user_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('push_subscriptions');

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'notify_team_assignment')) {
                $table->dropColumn('notify_team_assignment');
            }
        });
    }
};
