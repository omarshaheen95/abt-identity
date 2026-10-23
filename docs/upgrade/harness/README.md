# أدوات مقارنة النسختين

هذه الملفات ليست جزءاً من التطبيق ولا تُشغَّل مع الاختبارات (ما عدا `SmokeRoutesTest.php`، ونسخته في `tests/Feature`). استُخدمت لمقارنة سلوك Laravel 8 بـ Laravel 13 على نفس قاعدة البيانات المحلية (`abt-identity`). أصلها نسخة readingbenchmark (نفس القالب ونفس الـ guards)، وأُعيدت كتابة التصديرات والاستيراد والنماذج ومسار الاختبار والصفحات ذات الـ parameters على routes هذا المشروع، وأُخذ منها ما تعلّمته ss platform وnonarab (الوقت المثبّت، `resetAuth()`، `compare.php` الأحدث، فتح أكثر من صفحة بعد الدخول).

**قبل أي تشغيل:**
- الـ Exception Handler يرسل كل خطأ إلى Telegram عبر `LogMessageJob`، وبيانات Telegram وSMTP الحقيقية في جدول `settings`. محلياً فُرّغت قيم Telegram وكلمة مرور SMTP وحُوّل الخادم إلى `127.0.0.1:1` (نسخة من الجدول الأصلي خارج المشروع). وفوق ذلك `tests/TestCase.php` وكل اختبار هنا يعطّل الـ job بـ `Bus::fake([LogMessageJob::class])`، وكل أوامر PHP شُغّلت عبر wrapper يضبط `HTTP_PROXY`/`HTTPS_PROXY`/`ALL_PROXY` (بالحروف الكبيرة والصغيرة) على `http://127.0.0.1:9` و`MAIL_MAILER=log`. تأكدنا بطلب فعلي من Guzzle وcurl أن الاتصال يفشل.
- **لا يُطلب أبداً** `manager/seed` ولا `manager/copy_structure` ولا `manager/transfer_student_term` ولا `manager/update_questions_standards/{year}` (كلها GET تكتب في القاعدة). `SmokeRoutesTest::SKIP` يستثني الثلاثة الأولى، والرابع له parameter فلا تفتحه أدوات الصفحات بلا parameters، و`SmokeRoutesTest` و`ResponseSnapshotTest` يرميان exception إن وصل أي رابط ينتهي بـ `seed` إلى قائمة الطلبات.
- تشغيل واحد فقط في كل مرة على القاعدة، وبدون تعديل كود التطبيق أثناءه.

`SmokeRoutesTest` فيه اختيار الحسابات المشترك لكل الاختبارات: مشرف مفعّل بلا تغيير كلمة مرور معلّق وبأكثر الصلاحيات، والمدرسة المفعّلة صاحبة أكثر محاولات مصححة (رقم 31 محلياً، يُرفع عنها تغيير كلمة المرور الإجباري داخل الـ transaction)، وأول مفتش (يُفعَّل ويُعطى مدرستين داخل الـ transaction)، وأول طالب في مدرسة مفعّلة تسمح بدخول الطلاب.

1. نسخة مرجعية من الكود القديم بجانب المشروع:
   ```
   git -c core.longpaths=true worktree add --detach ../abt-identity-l8-ref cb5b8f6
   ```
   (commit الـ baselines: كود التطبيق فيه مطابق لـ `before-l13`، وفيه `SmokeRoutesTest` و`TestCase` المعطّل لـ Telegram.) ثم نسخ `.env` و`vendor` (مطابق لـ `composer.lock` القديم) و`resources/lang/{ar,en}/*.php` (ليست في git) و`public/uploads` إليها.
2. تشغيل كل اختبارات المقارنة على نسخة واحدة:
   ```
   bash run.sh <wrapper php 7.4> ../abt-identity-l8-ref <snapshots القديمة>
   bash run.sh <wrapper php 8.4> . <snapshots الجديدة>
   ```
   ويمكن تمرير أسماء اختبارات بعينها (`Forms ExamFlow`). `run.sh` ينسخ الاختبارات إلى `tests/Feature` ثم يحذفها في النهاية، ويحفظ `resources/lang` قبل التشغيل ويعيدها بعده (الـ helper `t()` يكتب فيها كل مفتاح غير موجود). كل ما يُكتب في القاعدة يُلغى (`DatabaseTransactions`)، والوقت مثبّت على `2026-10-22 10:00:00`.
3. المقارنة:
   - `php compare.php <مجلد القديم> <مجلد الجديد> --show=50 --today=<تاريخ التشغيل>` لكل الملفات. يستبعد ما يتغير في كل طلب (CSRF، أوقات التشغيل، bcrypt hash، عمودا activitylog 4، صفحة الخطأ الافتراضية لـ 500).
   - `php routes-dump.php <مسار المشروع>` و`php routes-compare.php before.json after.json`.
   - `php config-dump.php <مسار المشروع>` (المفاتيح وكلمات المرور مخفية)، يُشغَّل بالـ proxy فقط حتى تظهر إعدادات البريد الحقيقية من `.env`.
   - `php load-all.php <مسار المشروع>` يحمّل كل class في `app/` ويطبع التحذيرات والخصائص الديناميكية.

قبل المقارنة بين النسختين يُشغَّل `run.sh` مرتين على النسخة القديمة ويُقارَن الناتجان، لمعرفة ما يتغير بدون أي ترقية.

| الملف | ما يغطيه |
| --- | --- |
| SmokeRoutesTest | حالة كل صفحة GET بدون parameters لكل guard وللزائر، مقابل `tests/smoke-baseline.json` |
| ResponseSnapshotTest | محتوى نفس الصفحات، كصفحة وكطلب datatables، مع الرسالة المؤقتة وأخطاء التحقق |
| ParamPagesSnapshotTest | صفحات بـ parameters وفلاتر: التعديل والعرض، تقارير الطالب وبطاقته، الشهادات، التقارير (attainment وprogress وstudent mark وyear to year وcomparison)، بطاقات الطلاب العامة وتقرير QR، الدخول كمدرسة أو مفتش أو طالب، تغيير اللغة |
| AuthFlowSnapshotTest | الدخول ثم ثلاث صفحات، والدخول مرة ثانية في نفس الجلسة، والخطأ والقفل وفرض تغيير كلمة المرور لكل guard، ودخول المشرف كمدرسة ومفتش وطالب ثم فتح صفحاتهم، ودخول الطالب، وسجل `login_sessions` |
| PasswordResetSnapshotTest | رسائل استعادة كلمة المرور (السطور والرابط وHTML كامل في `mail-reset-*.html`) وصفحة إعادة التعيين |
| LogViewerAccessSnapshotTest | من يستطيع فتح `/log-viewer` |
| ExportSnapshotTest | 21 تصدير Excel من اللوحات الثلاث، خلية بخلية، مفلترة على صف واحد من مدرسة واحدة |
| ImportSnapshotTest | استيراد طلاب xlsx (إنشاء بصفوف صحيحة وخاطئة)، ثم تحديث بملف csv عربي، ثم إنشاء من csv بأسماء دخول من رقم الطالب |
| ExamFlowSnapshotTest | طالب يفتح ويسلّم اختبارين (صح وخطأ واختيار وتوصيل، واختبار الجولة المفتوحة بـ standards)، ثم التصحيح الآلي وشاشة التصحيح، وإعادة تصحيح محاولة قديمة بـ standards |
| FormsSnapshotTest | نماذج الإضافة والتعديل (المشرف والمدرسة)، وطلب التصحيح ورسالته، وتغيير كلمة المرور والملف الشخصي لكل لوحة وفحص JsValidator عن بُعد، وسجل activity log |
| `session-check.php` | جلسة تكتبها نسخة وتقرأها الأخرى، بـ `php` و`json` |
| `cookie-check.php` | كوكي الجلسة المشفر والقيم المشفرة بين النسختين |
| `cache-check.php` | model وCollection وmodels بعلاقاتها و`stdClass` ومصفوفة تكتبها نسخة في الكاش وتقرؤها الأخرى (تُنسخ ملفات `storage/framework/cache/data` بينهما) |
| `mail-render.php` | HTML رسالة استعادة كلمة مرور المشرف |
| `desktop-only-check.php` | قرار `DesktopOnlyMiddleware` لـ 13 user agent |
| `TrustedIpApiTest.php` | يثبت أن `api/update_settings` و`api/import_students` لا يقبلان ترويسة IP مزوّرة (نسخته في `tests/Feature`) |
