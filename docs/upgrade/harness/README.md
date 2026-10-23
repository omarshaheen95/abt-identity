# أدوات مقارنة النسختين

هذه الملفات ليست جزءاً من التطبيق ولا تُشغَّل مع الاختبارات (ما عدا `SmokeRoutesTest.php`، ونسخته في `tests/Feature`). استُخدمت لمقارنة سلوك Laravel 7 بـ Laravel 13 على نفس قاعدة البيانات (`arbt`)، وعُدّلت عن نسخة socialuae لتناسب readingbenchmark: الحسابات، والمسارات، والنماذج، والتصديرات، والاستيراد، ومسار الاختبار (`start-term` ثم `save-term` ثم التصحيح من شاشة المشرف).

**قبل أي تشغيل:**
- الـ Exception Handler يرسل كل خطأ إلى Telegram عبر `LogMessageJob` (queue `sync`)، والـ token الحقيقي في جدول `settings`. `tests/TestCase.php` وكل اختبار هنا يعطّله بـ `Bus::fake([LogMessageJob::class])`. وللأوامر خارج phpunit (السكربتات هنا و`artisan`) شُغّل PHP عبر wrapper يضبط `HTTP_PROXY` و`HTTPS_PROXY` على `http://127.0.0.1:9`، فيفشل أي اتصال خارجي.
- تشغيل واحد فقط في كل مرة على القاعدة، وبدون تعديل كود التطبيق أثناءه (انظر "دروس للمشروع التالي" في `../README.md`).

`SmokeRoutesTest` فيه اختيار الحسابات المشترك لكل الاختبارات: مشرف بلا تغيير كلمة مرور معلّق، والمدرسة المفعّلة صاحبة أكثر محاولات مصححة (يُرفع عنها تغيير كلمة المرور الإجباري داخل الـ transaction)، وأول مفتش (يُفعَّل ويُعطى مدرستين داخل الـ transaction)، وأول طالب في مدرسة مفعّلة. وفيه قائمة routes الـ GET المستثناة (التي تكتب في القاعدة أو تنتهي بـ `dd()` أو تبني ملفات كبيرة).

1. نسخة مرجعية من الكود القديم بجانب المشروع:
   ```
   git -c core.longpaths=true worktree add --detach ../readingbenchmark-l7-ref 2ef7fa34
   ```
   (commit الـ baselines: كود التطبيق فيه مطابق لـ `before-l13`، وفيه `SmokeRoutesTest` و`TestCase` المعطّل لـ Telegram.) ثم نسخ `.env` و`vendor` (مطابق لـ `composer.lock` القديم) و`resources/lang/{ar,en}/*.php` (ليست في git) و`public/uploads` إليها.
2. تشغيل كل اختبارات المقارنة على نسخة واحدة (تُنسخ مع `SmokeRoutesTest` إلى `tests/Feature` ثم تُشغّل بالترتيب، والنتائج في مجلد الـ snapshots):
   ```
   bash run.sh <wrapper php 7.4> ../readingbenchmark-l7-ref <snapshots القديمة>
   bash run.sh <wrapper php 8.4> . <snapshots الجديدة>
   ```
   ويمكن تمرير أسماء اختبارات بعينها في النهاية (`Forms ParamPages`). كل ما يُكتب في قاعدة البيانات يُلغى (`DatabaseTransactions`). بعد الانتهاء تُحذف النسخ من `tests/Feature` (ما عدا `SmokeRoutesTest.php`)، وتُقارن ملفات `resources/lang` بما قبل التشغيل: الـ helper `t()` يضيف إليها كل مفتاح غير موجود.
3. المقارنة:
   - `php compare.php <مجلد القديم> <مجلد الجديد> --show=50` لكل الملفات. يستبعد ما يتغير في كل طلب (CSRF، أوقات وcache-busters، bcrypt hash، عمودا activitylog 4 الفارغان، صفحة الخطأ الافتراضية لـ 500).
   - `php routes-dump.php <مسار المشروع>` يطبع كل الـ routes مع الـ middleware بعد حلّها، و`php routes-compare.php before.json after.json` يقارن بعد مطابقة الـ middleware التي صارت من الإطار.
   - `php config-dump.php <مسار المشروع>` يطبع كل قيم `config()` بعد التشغيل (المفاتيح وكلمات المرور مخفية).
   - `php load-all.php <مسار المشروع>` يحمّل كل class في `app/` ويطبع التحذيرات والخصائص الديناميكية.

قبل المقارنة بين النسختين، يُشغَّل `run.sh` مرتين على النسخة القديمة ويُقارَن الناتجان، لمعرفة ما يتغير بدون أي ترقية.

| الملف | ما يغطيه |
| --- | --- |
| SmokeRoutesTest | حالة كل صفحة GET بدون parameters لكل guard وللزائر، مقابل `tests/smoke-baseline.json` |
| ResponseSnapshotTest | محتوى نفس الصفحات، كصفحة وكطلب datatables |
| ParamPagesSnapshotTest | صفحات بـ parameters وفلاتر: التعديل، تقارير الطالب والمدرسة والمقارنة، الشهادات، البطاقات، الدخول كمدرسة أو طالب، تغيير اللغة |
| AuthFlowSnapshotTest | الدخول والخروج والقفل وفرض تغيير كلمة المرور لكل guard، وسجل `login_sessions` |
| PasswordResetSnapshotTest | رسائل استعادة كلمة المرور ورابطها وصفحة إعادة التعيين للـ guards الأربعة |
| LogViewerAccessSnapshotTest | من يستطيع فتح `/log-viewer` |
| ExportSnapshotTest | 23 تصدير Excel من اللوحات الثلاث، خلية بخلية |
| ImportSnapshotTest | استيراد ملف طلاب (إنشاء بصفوف صحيحة وخاطئة، ثم تحديث) |
| ExamFlowSnapshotTest | تقديم اختبار (صح وخطأ، اختيار، توصيل) ثم التصحيح وإعادة التصحيح من شاشة المشرف، وإعادة تصحيح محاولة قديمة لها standards |
| FormsSnapshotTest | نماذج الإضافة والتعديل الرئيسية (فارغة وخاطئة أيضاً)، وسجل activity log، ورسالة طلب التصحيح |
| `session-check.php` | جلسة تكتبها نسخة وتقرأها الأخرى، بـ `php` و`json` |
| `cookie-check.php` | كوكي الجلسة المشفر والقيم المشفرة بين النسختين |
| `mail-render.php` | HTML رسالة استعادة كلمة المرور |
| `desktop-only-check.php` | قرار `DesktopOnlyMiddleware` لـ 13 user agent |
| `cache-check.php` | model وCollection و`stdClass` ومصفوفة تكتبها نسخة في الكاش وتقرؤها الأخرى، مع `serializable_classes` بـ `true` أو `false` (من islamic) |
| `TrustedIpApiTest.php` | يثبت أن `api/update_settings` لا يقبل `X-Forwarded-For` مزوراً (من readingbenchmark) |
