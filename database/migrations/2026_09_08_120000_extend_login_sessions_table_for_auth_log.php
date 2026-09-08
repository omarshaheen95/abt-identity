<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ExtendLoginSessionsTableForAuthLog extends Migration
{
    public function up(): void
    {
        // Failed attempts may belong to no known account, and the columns were
        // created NOT NULL by the original morphs() call. doctrine/dbal is not
        // installed, so ->change() is unavailable and raw ALTER is used instead.
        DB::statement('ALTER TABLE `login_sessions` MODIFY `model_type` VARCHAR(255) NULL');
        DB::statement('ALTER TABLE `login_sessions` MODIFY `model_id` BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE `login_sessions` MODIFY `data` TEXT NULL');

        Schema::table('login_sessions', function (Blueprint $table) {
            $table->string('status', 20)->default('success')->after('model_id');
            $table->string('guard', 30)->nullable()->after('status');
            $table->string('identifier')->nullable()->after('guard');
            $table->string('reason', 50)->nullable()->after('identifier');
            $table->string('ip', 45)->nullable()->after('reason');
            $table->text('user_agent')->nullable()->after('ip');

            $table->index(['status', 'created_at'], 'idx_login_sessions_status_created');
            $table->index(['ip', 'created_at'], 'idx_login_sessions_ip_created');
            $table->index('identifier', 'idx_login_sessions_identifier');
        });
    }

    public function down(): void
    {
        Schema::table('login_sessions', function (Blueprint $table) {
            $table->dropIndex('idx_login_sessions_status_created');
            $table->dropIndex('idx_login_sessions_ip_created');
            $table->dropIndex('idx_login_sessions_identifier');

            $table->dropColumn(['status', 'guard', 'identifier', 'reason', 'ip', 'user_agent']);
        });

        // Keep these nullable: failed attempts cannot be converted back to
        // mandatory account references without deleting historical records.
    }
}
