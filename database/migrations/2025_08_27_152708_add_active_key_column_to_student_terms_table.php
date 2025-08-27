<?php
/*
Dev Omar Shaheen
Devomar095@gmail.com
WhatsApp +972592554320
*/

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1) إضافة العمود
        Schema::table('student_terms', function (Blueprint $table) {
            $table->unsignedBigInteger('active_key')->nullable()->after('updated_at');
        });

        // 2) تعبئة القيم الحالية: 0 للفعّال، و id للمحذوف ناعماً
        DB::statement("
            UPDATE `student_terms`
            SET `active_key` = IF(`deleted_at` IS NULL, 0, `id`)
        ");

        // (اختياري للتأكد قبل إنشاء الفهرس) — اتركها مُعلّقة للاستخدام اليدوي لو لزم
        // DB::statement(\"SELECT student_id, term_id, COUNT(*) c
        // FROM student_terms
        // WHERE deleted_at IS NULL
        // GROUP BY 1,2
        // HAVING c > 1\");

        // 3) إنشاء فهرس فريد يمنع وجود أكثر من سجل فعّال لنفس (student_id, term_id)
        DB::statement("
            CREATE UNIQUE INDEX `uniq_student_term_active`
            ON `student_terms` (`student_id`, `term_id`, `active_key`)
        ");

        // 4) التريغرز لتحديث active_key تلقائياً

        // احذف إن وُجدت سابقاً
        DB::unprepared("DROP TRIGGER IF EXISTS `bi_student_terms_set_active_key`;");
        DB::unprepared("DROP TRIGGER IF EXISTS `bu_student_terms_set_active_key`;");
        DB::unprepared("DROP TRIGGER IF EXISTS `ai_student_terms_fix_active_key`;");

        // قبل الإدراج: خليه 0 (السجل عادةً يكون فعّال)
        DB::unprepared("
            CREATE TRIGGER `bi_student_terms_set_active_key`
            BEFORE INSERT ON `student_terms`
            FOR EACH ROW
            BEGIN
              SET NEW.active_key = 0;
            END
        ");

        // قبل التحديث: 0 لو فعّال، وإلا id
        DB::unprepared("
            CREATE TRIGGER `bu_student_terms_set_active_key`
            BEFORE UPDATE ON `student_terms`
            FOR EACH ROW
            BEGIN
              SET NEW.active_key = IF(NEW.deleted_at IS NULL, 0, NEW.id);
            END
        ");

        // بعد الإدراج: لو INSERT وفيه deleted_at مش NULL (نادر)، صحّحها إلى id
        DB::unprepared("
            CREATE TRIGGER `ai_student_terms_fix_active_key`
            AFTER INSERT ON `student_terms`
            FOR EACH ROW
            BEGIN
              IF NEW.deleted_at IS NOT NULL THEN
                UPDATE `student_terms`
                SET `active_key` = NEW.id
                WHERE `id` = NEW.id;
              END IF;
            END
        ");
    }

    public function down(): void
    {
        // حذف التريغرز
        DB::unprepared("DROP TRIGGER IF EXISTS `bi_student_terms_set_active_key`;");
        DB::unprepared("DROP TRIGGER IF EXISTS `bu_student_terms_set_active_key`;");
        DB::unprepared("DROP TRIGGER IF EXISTS `ai_student_terms_fix_active_key`;");

        // حذف الفهرس الفريد
        DB::statement("DROP INDEX `uniq_student_term_active` ON `student_terms`;");

        // حذف العمود
        Schema::table('student_terms', function (Blueprint $table) {
            $table->dropColumn('active_key');
        });
    }
};
