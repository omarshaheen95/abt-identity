# ترقية readingbenchmark إلى Laravel 13 و PHP 8.4

بتاريخ 22-10-2026 · الفرع `upgrade/laravel-13` · نقطة البداية: الـ tag `before-l13` (الـ commit `96519205`، مطابق لـ `origin/master`)

نُفِّذت الترقية بحسب "خطة تحديث مشاريع ABT": هيكل Laravel 13 جديد داخل نفس الـ repo، مع إبقاء كود المشروع كما هو، ثم مقارنة سلوك النسخة الجديدة بالقديمة على نفس قاعدة البيانات المحلية (`arbt`). ترقيتا socialuae (`socialuae-reference.md`) وislamic (`islamic-reference.md`) كانتا نقطة البداية، لكن كل حل منهما فُحص هنا من جديد على بيانات هذا المشروع، وبعض ما وُجد هنا لم يظهر هناك (انظر "دروس للمشروع التالي").

| | قبل | بعد |
| --- | --- | --- |
| Laravel | 7.30.6 | 13.38.0 |
| PHP | `^7.2.5` (composer) / 7.4 محلياً | `^8.4` |
| PHPUnit | 8.5 | 12.5 |
| `composer audit` | 71 ثغرة معروفة في 15 حزمة | لا شيء |

| الـ commit | ما فيه |
| --- | --- |
| `2ef7fa34` | الـ baselines قبل أي تعديل: `routes-before.json` و`config-before.json` و`composer-audit-before.txt` و`composer-show-before.txt` و`tests-before.txt` و`tests/smoke-baseline.json`، وتعطيل `LogMessageJob` في `tests/TestCase.php` |
| `8cb22fe3` | أدوات المقارنة بعد تعديلها لهذا المشروع (`harness/`) |
| `9901faef` | الترقية نفسها |
| `e4bfb36d` | تحويل الـ routes النصية إلى `[Controller::class, 'method']` |
| `239bed6a` | إيقاف حفظ hash كلمة المرور في activity log |
| `6c9eea87` | هذا التوثيق، ونتائج المقارنة النهائية، وتحسينات أدوات المقارنة |
| (آخر commit) | إصلاح فحص الـ IP في `api/update_settings` و`api/import_students` (انظر "ثغرة أُصلحت بعد الترقية") |

## ما الذي تغيّر

### الهيكل
- `bootstrap/app.php` يحل محل `app/Http/Kernel.php` و`app/Console/Kernel.php` و`RouteServiceProvider`. مجموعات الـ routes بنفس الترتيب (api، web، inspection، student، school، manager) ونفس الـ prefix والـ middleware، والـ aliases بنفس الأسماء (`manager` و`school.guest` و`local` و`checkIfActive` و`force.password` و`session.password` ...). `auth` ما زال `App\Http\Middleware\Authenticate` (فيه توجيه كل guard إلى صفحة دخوله).
- `bootstrap/providers.php` فيه `AppServiceProvider` و`SettingServiceProvider` فقط. `UserEventSubscriber` صار يُسجَّل في `AppServiceProvider`، واكتشاف الـ listeners التلقائي مطفأ حتى لا تُسجَّل أحداث الدخول مرتين.
- الـ Exception Handler القديم باقٍ (يرسل إلى Telegram، ويشكّل أخطاء JSON والتوجيه لكل guard)، ومربوط بعد `->create()` في `bootstrap/app.php`.
- حُذفت نسخ التطبيق من middleware الإطار (`EncryptCookies` و`VerifyCsrfToken` و`TrimStrings` و`TrustProxies` و`TrustHosts` و`CheckForMaintenanceMode` و`RedirectIfAuthenticated`)، لأنها لم تكن معدّلة. التعديل الوحيد كان `old_password` في `TrimStrings`، وصار في `bootstrap/app.php`.
- الـ scheduler (مهمتان) انتقل إلى `withSchedule()` كما هو.
- `database/seeds` صار `database/seeders` بالـ namespace الجديد، و`UserFactory` بصيغة الـ class.
- كل الـ routes بصيغة `[Controller::class, 'method']` (196 action، حُوّلت بالـ tokenizer)، ولم يعد هناك namespace للـ controllers. `routes-dump.php` بعد التحويل مطابق لما قبله حرفياً، و`route:cache` يعمل.
- **`App\Models\Match` صار `App\Models\QuestionMatch`** على نفس جدول `matches`: `match` كلمة محجوزة منذ PHP 8.0، فالـ class القديم لا يمكن تعريفه أصلاً. لا يُخزَّن اسم هذا الـ class في قاعدة البيانات (لا activity log ولا morph)، والعلاقات (`matches()` و`match()` في `MatchResult`) بنفس الأسماء والمفاتيح. فلتر "Subject" في صفحة activity log يعرض كل الـ models بأسمائها المترجمة، فأُبقي لهذا الـ model مفتاح الترجمة القديم `Match` («مطابقة»).

### الحزم
| الحزمة | القرار |
| --- | --- |
| spatie/laravel-permission 5.11 | 8.3 (نفس socialuae وislamic). الـ middleware انتقل إلى `Spatie\Permission\Middleware`. migration الجداول تقرأ أسماء أعمدة الـ pivot من الـ config بدل خصائص `PermissionRegistrar` الثابتة التي حُذفت في v6 |
| spatie/laravel-activitylog 3.17 | 4.12. كل model (12) صار فيه `getActivitylogOptions()` بنفس الحقول والخيارات تماماً (`logOnlyDirty` و`dontSubmitEmptyLogs`، و`$recordEvents` كما هو)، و migration جديدة تضيف عمودي `event` و`batch_uuid` |
| yajra/laravel-datatables-oracle 9 | 13.3، مع config جديد بنفس القيم الافتراضية القديمة |
| dyrynda/laravel-cascade-soft-deletes 3 | 4.6. الـ namespace في 7 models صار `Dyrynda\...` (النسخة 3 كانت ما تزال `Iatstuti\...`) |
| arcanedev/log-viewer 7 | حُذفت (لا نسخة لـ 13) وحلّت محلها opcodesio/log-viewer. **كانت بدون أي middleware**: أي زائر يقرأ السجلات ويحذفها. الآن خلف `manager` و`auth:manager`، مع callback يسمح للمشرف في production، وملفات الواجهة منشورة في `public/vendor/log-viewer` |
| hesto/multi-auth | حُذفت، والـ trait الوحيد المستخدم نُقل كما هو إلى `app/Traits/LogsoutGuard.php` |
| laravel/helpers | حُذفت. الاستدعاء الوحيد (`array_get` في الـ Handler) صار `Arr::get` |
| fideloper/proxy, fruitcake/laravel-cors, facade/ignition, fzaninotto/faker | حُذفت (أصبحت جزءاً من Laravel أو استُبدلت بـ fakerphp/faker). إعدادات `config/cors.php` كما هي، و`TrustProxies` بنفس السلوك (لا proxies موثوقة) |
| laravel/ui 2 | 4.6، لأن `AuthenticatesUsers` وأخواتها منها |
| guzzlehttp/guzzle 6 | 7.16 |
| barryvdh/laravel-debugbar `*` | 4.4، في `require-dev` (لا يستخدمها الكود) |
| maatwebsite/excel 3.1.55 | 3.1.70، مع `config/excel.php` منشور (انظر الترميز أدناه) |
| jsvalidation, simple-qrcode | أحدث نسخة من نفس الإصدار الرئيسي. ملف jsvalidation.js المنشور بقي كما هو |

### سلوك أبقيناه كما كان في Laravel 7 عن قصد
هذه أشياء غيّرها Laravel أو الحزم بين 7 و13، ولو تُركت لتغيّر ما يراه المستخدم:

- **لا يخرج المستخدمون عند النشر**: Laravel 13 يجعل `session.serialization` بقيمة `json`. جُرّبت هنا بملف جلسة كتبه Laravel 7 (`harness/session-check.php`): بـ `json` يضيع تسجيل الدخول وتوكن CSRF، وبـ `php` تُقرأ كاملة، فبقيت `php`. وفي الاتجاه المعاكس (جلسة كتبها 13 ويقرؤها 7) تُقرأ أيضاً. اسم الكوكي `laravel_session` كما هو، والكوكي المشفر والقيم المشفرة تُفك بين النسختين في الاتجاهين (`harness/cookie-check.php`).
- **الكاش**: `cache.serializable_classes` بقيت `true`، لأن مفتاح `settings` يحفظ أحياناً model `Setting` (الدالة `cached()`) أو Collection (`updateSettingsApi`)، وكانا سيرجعان `__PHP_Incomplete_Class`. الـ prefix `laravel_cache` كما كان.
- **استيراد CSV**: الطلاب يُستوردون من xlsx أو xls أو csv. Laravel Excel 3.1.55 كان يقرأ CSV دائماً كـ UTF-8، والنسخة الجديدة "تخمّن" الترميز، فيُقرأ ملف محفوظ بترميز آخر بشكل مختلف. أُعيدت `excel.imports.csv.input_encoding` إلى `UTF-8`. (اكتُشف من مقارنة `config()` كاملاً، ولم يُعالَج في socialuae.)
- **صفحات الأخطاء ورسائل البريد**: نُسخت من Laravel 7 الملفات التي تغيّرت فعلاً في 13 فقط: `errors/minimal` و`layout` و`503`، وقوالب `vendor/mail` و`vendor/notifications/email`. رسالة استعادة كلمة المرور مطابقة حرفياً (ما عدا مسافتين داخل سطر CSS).
- **Carbon 3**: دوال `diffIn*` صارت ترجع كسراً وبإشارة. الاستدعاءات الثمانية في إحصائيات المشرف والمدرسة (`> 1` شهر/يوم) صارت `(int) ...diffInX(..., true)`، والاستدعاء الذي كان بـ `false` (أيام انتهاء كلمة المرور) بقي بإشارته مع `(int)`.
- **ملفات `.env` الحالية تعمل كما هي**: الإعدادات تقرأ الأسماء الجديدة ثم القديمة (`CACHE_DRIVER` و`MAIL_DRIVER` ...)، والقيم الافتراضية هي قيم Laravel 7. إعدادات SMTP التي يضعها `SettingServiceProvider` من جدول `settings` (المنفذ والتشفير) تعطي نفس الاتصال: 587 بـ STARTTLS و465 بـ TLS مباشر.
- **مسار الـ local disk** بقي `storage/app`، و**لا إعادة hash لكلمة المرور عند الدخول** (كانت ستُفهم في `ValidateSessionPassword` كتغيير كلمة مرور)، وbcrypt بقي على 10 rounds.
- الـ pagination على Bootstrap 4 (صفحتا دفعات الترقية تستخدمانه).

ما لم يحتج تعديلاً هنا: **التواريخ في JSON** و**`getOriginal()`** (Laravel 7 كان قد غيّرهما أصلاً)، و**الأسماء المكررة للـ routes** (`route:cache` يعمل)، وmobiledetect (غير مستخدمة هنا: `DesktopOnlyMiddleware` يطابق كلمات في الـ user agent بنفسه، وقراره مطابق لـ 13 user agent في `harness/desktop-only-check.php`).

### PHP 8.4
- الـ model `Match` (أعلاه).
- إعلان الخصائص التي كانت تُنشأ ديناميكياً (`$statusCode` في الـ Handler، و`$last_cell`/`$last_row` في تصديرين، و`$log_errors` في `OldStudentImport`)، وجعل الـ nullable صريحاً في 6 دوال.
- تحميل كل الـ 179 class في `app/` على PHP 8.4 بدون أي تحذير (`harness/load-all.php`).
- `@dataProvider` في اختبار واحد صار attribute كما يطلب PHPUnit 12.

### activity log
- حُذف `password` من الحقول المسجّلة لـ `Manager` و`School` و`Inspection`. كان تغيير كلمة المرور يحفظ الـ hash القديم والجديد في `activity_log`، وهي صفحة يقرؤها المشرفون (ظهر ذلك فعلاً في مقارنة النماذج). باقي السجل لم يتغير، وتغيير كلمة المرور وحده لم يعد يُنشئ سجلاً. السجلات القديمة ما زالت تحوي الـ hash (محلياً 2 للمشرفين). هو commit مستقل (`239bed6a`) يمكن التراجع عنه وحده.

## كيف تأكدنا أن السلوك لم يتغير
كل المقارنات شُغّلت على نفس قاعدة البيانات المحلية، مرة على نسخة Laravel 7 (git worktree من commit الـ baselines، وكود التطبيق فيه مطابق لـ `before-l13`، في `../readingbenchmark-l7-ref`) ومرة على Laravel 13، وكل كتابة داخل transaction تُلغى في النهاية. الأدوات في `docs/upgrade/harness`. شُغّلت الأدوات مرتين على Laravel 7 نفسه لمعرفة ما يتغير بين تشغيلين بدون أي ترقية.

| الفحص | النتيجة |
| --- | --- |
| قائمة الـ routes (`routes-before.json` / `routes-after.json`) | 293 route للتطبيق: نفس الرابط والاسم والـ action والـ middleware وترتيبه. الفرق الوحيد routes الـ log viewer (البديل) والـ debugbar |
| الإعدادات الفعلية بعد التشغيل (`config-before.json` / `config-after.json`) | نفس القيم، ما عدا مفاتيح جديدة في Laravel 13، ومفاتيح حزم حُذفت، وقيم غير مستخدمة هنا (broadcasting وargon وsendmail وsqs) |
| 133 صفحة للـ guards الأربعة والزائر، كل منها كصفحة وكطلب datatables: كل صفحات GET بدون parameters (79)، و54 صفحة بـ parameters أو فلاتر (التعديل، تقارير الطالب والمدرسة والمقارنة، الشهادات، البطاقات، الدخول كمدرسة أو طالب، تغيير اللغة) | مطابقة، ما عدا ترتيب المدارس المتساوية في تقرير المقارنة (انظر أدناه) |
| الدخول والخروج والقفل بعد المحاولات الخاطئة وفرض تغيير كلمة المرور لكل guard، وسجل `login_sessions` | مطابق |
| 23 تصدير Excel من اللوحات الثلاث | 18 مطابقة خلية بخلية، و5 بنفس الصفوف والقيم وترتيب مختلف داخل الصف الدراسي الواحد (انظر أدناه) |
| استيراد ملف طلاب (إنشاء بصفوف صحيحة وخاطئة، ثم تحديث) | نفس الطلاب ونفس الأخطاء ونفس سجل الملف |
| تقديم اختبار (صح وخطأ، اختيار، توصيل) ثم التصحيح وإعادة التصحيح من شاشة المشرف، وإعادة تصحيح محاولة قديمة لها standards (الجولة المفتوحة حالياً، مايو للسنة 6، ليس لأسئلتها standards) | نفس الإجابات ونفس النتيجة (المجموع، المستوى الجديد، التوقعات، تقدم القراءة، تواريخ التصحيح) ونفس الدرجات لكل standard (21) |
| إضافة وتعديل المدارس والطلاب (من المشرف ومن المدرسة) والمستويات والاختبارات والسنوات والمشرفين والمفتشين وطلب التصحيح، والنماذج الفارغة والخاطئة | نفس البيانات المحفوظة ونفس رسائل التحقق ونفس رسالة طلب التصحيح. سجل activity log مطابق ما عدا حذف `password` المقصود |
| رسائل استعادة كلمة المرور للـ guards الأربعة | نفس الرسالة والرابط وصفحة إعادة التعيين، والقالب مطابق |
| من يفتح log viewer | المشرف فقط (كان يفتح للجميع) |
| الجلسات والكوكيز المشفرة بين النسختين | تُقرأ في الاتجاهين |
| `schedule:list` | نفس المهمتين بنفس المواعيد |
| اختبارات المشروع (80) و`tests/Feature/SmokeRoutesTest.php` | تنجح على النسختين (`tests-before.txt` / `tests-after.txt`)، والـ smoke بنفس الصفحات الثلاث التي ترجع 500 أصلاً |
| تشغيلان كاملان على Laravel 7 | متطابقان، أي أن كل الفروق أعلاه من الترقية لا من عشوائية الصفحات |

فروق ظهرت في المقارنة، وليست تغييراً فيما يحسبه التطبيق:
- **ترتيب الصفوف المتساوية**: تصدير محاولات الطلاب (المصححة وغير المصححة، من المشرف والمدرسة والمفتش) يرتّب بـ `sortBy(grade)` صفوفاً مرتبة أصلاً من الأحدث، وتقرير المقارنة يرتّب المدارس بـ `sortByDesc(total_up)`. الترتيب في PHP 7 لم يكن مستقراً، فكانت الصفوف المتساوية تظهر بترتيب عشوائي ثابت؛ في PHP 8 مستقر. النتيجة: نفس الـ 4228 صفاً بنفس القيم، وداخل الصف الدراسي الواحد صار الأحدث أولاً (فُحص: 0 صف خارج الترتيب بعد، مقابل 1849 قبل)، وفي تقرير المقارنة تتبادل المدارس المتساوية في المجموع أماكنها وكل مدرسة بأرقامها نفسها. تقليد الترتيب القديم غير ممكن عملياً ولا فائدة منه.
- رسالة الـ `ValidationException` الداخلية صارت أول خطأ بدل "The given data was invalid"، ولا تظهر للمستخدم.
- صفحة الـ 500 في وضع debug تغيّرت (`student/password/reset` و`inspection/combined_result` ترجعان 500 في النسختين).

## النشر

1. PHP **8.4.27 أو أحدث** (8.4.7 الموجودة محلياً مصابة بثغرة php2shell). الـ extensions: `pdo_mysql` و`mbstring` و`gd` و`zip` و`intl` و`bcmath` و`fileinfo` و`openssl`.
2. ملف `.env` الحالي يعمل كما هو. ملفات `resources/lang` ليست في git (ما عدا `auth_log.php`)، فتُنسخ من النسخة الحالية على السيرفر إلى المجلد الجديد، ومعها `public/uploads` و`public/student-import-files`.
3. في مجلد جديد بجانب القديم:
   ```
   composer install --no-dev --optimize-autoloader
   php artisan migrate --force
   php artisan permission:cache-reset
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```
   الـ migration الوحيدة الجديدة تضيف عمودين nullable إلى `activity_log` (بدون `after()`)، و`down()` فيها يحذفهما. جُرّبت `up` ثم `down` ثم `up` محلياً، وهي مطبّقة الآن على `arbt` (نسخة Laravel 7 تعمل معها لأن العمودين nullable)، و`migrate --pretend` لم يُظهر غيرها. `permission:cache-reset` يمسح كاش الصلاحيات الذي كتبته النسخة 5 من الحزمة.
4. ملفات jsvalidation وlog-viewer المنشورة في `public/vendor` محفوظة في git، ولا تحتاج نشراً.
5. الـ cron الحالي (`schedule:run`) يعمل كما هو.
6. الرجوع: الـ symlink إلى المجلد القديم، ثم `php artisan migrate:rollback --step=1` إن لزم، و`permission:cache-reset` من المجلد القديم. الجلسات تبقى في الاتجاهين.

## ثغرة أُصلحت بعد الترقية
`api/update_settings` و`api/import_students` بدون دخول، ومحميان فقط بقائمة `config('app.trusted_ips')`. الدالة `getRealIpAddress()` كانت تأخذ عنوان المتصل من ترويسات `X-Forwarded-For` و`X-Forwarded` و`X-Cluster-Client-IP` و`Forwarded-For` و`Forwarded` قبل `REMOTE_ADDR`، وهذه يكتبها العميل نفسه. أي شخص كان يستطيع إرسال `X-Forwarded-For: 127.0.0.1` فيغيّر جدول `settings` (ومنه بيانات SMTP وtoken الـ Telegram) أو يستورد طلاباً لأي مدرسة. الثغرة موجودة على Laravel 7 أيضاً، وليست من الترقية.

- `isTrustIpAddress()` صار يقارن `$request->ip()` بالقائمة (مقارنة صارمة). `$request->ip()` يمر على `TrustProxies`، ولا proxies موثوقة في هذا المشروع، فهو عنوان الاتصال الفعلي. حُذفت `getRealIpAddress()`، ولم يكن يستخدمها غير هذا الفحص.
- `tests/Feature/TrustedIpApiTest.php`: طلب من عنوان غير موثوق يدّعي في كل واحدة من الترويسات الخمس أنه من `127.0.0.1` (على الطلب وفي `$_SERVER` كما يضعها PHP-FPM) يُرفض بـ 401 في المسارين، ولا يتغير أي setting ولا يُنشأ ملف استيراد. وسيرفر موثوق يتصل مباشرة ما زال يحدّث الإعدادات. على الكود القديم فشلت حالات التزوير العشر كلها (200 للإعدادات مع تغيير القيمة، و422 للاستيراد أي أن الطلب تجاوز فحص الـ IP).
- **قبل النشر:** إذا كان السيرفر خلف proxy أو load balancer أو Cloudflare، فـ `$request->ip()` هو عنوان الـ proxy، وسيُرفض المتصلون الموثوقون إلى أن يُضاف الـ proxy إلى `TrustProxies` (`$middleware->trustProxies(at: [...])` في `bootstrap/app.php`). لا يُضاف `'*'`، فهو يعيد الثغرة نفسها.

## مشاكل كانت موجودة قبل الترقية (لم نلمسها)
- **`user/{id}/report`** يعرض تقرير أي طالب برقمه لأي زائر بدون دخول، و`user/{id}/certificate` يعرض الاسم والدرجة لأي محاولة تتجاوز علامة الشهادة.
- routes بطلب GET تغيّر البيانات، خلف دخول المشرف وبدون صلاحية خاصة: أدوات إصلاح محاولات الطلاب (`fix_student_term_school` و`fixMismatchedGradeStudents` و`reorder_student_term_school` و`resubmit_student_term_school`)، و`update_matches` و`update_students_school` و`restore-students` و`student_term/copyStandards` و`correctMayTerms` و`approveStandardSeptember` و`updateCorrectedTerms/{from}/{to}`. وبعضها، ومعها `mismatchedGradeStudents` و`unorder_student_term_school`، تنتهي بـ `dd()`.
- `manager/register` (الـ view غير موجود) و`student/password/reset` و`inspection/combined_result` بـ parameters ترجع 500.
- 15 route تشير إلى methods غير موجودة، فترجع 500 إذا فُتحت. كلها من `Route::resource`. القائمة نفسها على Laravel 7 (`method_exists` على ناتج `routes-dump.php`):

  | الـ route | الـ action |
  | --- | --- |
  | `DELETE manager/login_sessions/{login_session}` | `Manager\LoginSessionController@destroy` |
  | `GET manager/inspection/{inspection}` | `Manager\InspectionController@show` |
  | `GET manager/level/{level}` | `Manager\LevelController@show` |
  | `GET manager/login_sessions/create` | `Manager\LoginSessionController@create` |
  | `GET manager/login_sessions/{login_session}` | `Manager\LoginSessionController@show` |
  | `GET manager/login_sessions/{login_session}/edit` | `Manager\LoginSessionController@edit` |
  | `GET manager/manager/{manager}` | `Manager\ManagerController@show` |
  | `GET manager/school/{school}` | `Manager\SchoolController@show` |
  | `GET manager/student_import/{student_import}/edit` | `Manager\StudentImportController@edit` |
  | `GET manager/year/{year}` | `Manager\YearController@show` |
  | `GET school/marking_request/{marking_request}` | `School\MarkingRequestController@show` |
  | `GET school/student/{student}` | `School\StudentController@show` |
  | `POST manager/login_sessions` | `Manager\LoginSessionController@store` |
  | `PUT|PATCH manager/login_sessions/{login_session}` | `Manager\LoginSessionController@update` |
  | `PUT|PATCH manager/student_import/{student_import}` | `Manager\StudentImportController@update` |

  الإصلاح المقترح: `->only([...])` أو `->except([...])` على كل `Route::resource`، بعد التأكد أن أسماءها غير مستخدمة في الـ views. لم نفعله حتى تبقى قائمة الـ routes مطابقة.
- `ActivityLogController@show` يستخدم `\GuzzleHttp\json_decode`، وستُحذف في Guzzle 8.
- الـ Exception Handler يرسل كل خطأ إلى Telegram عبر `LogMessageJob` (queue `sync`)، والـ token في جدول `settings` المحلي حقيقي. كل اختبار يعطّل هذا الـ job (`tests/TestCase.php` وكل ملفات `harness/`)، وكل أوامر PHP في هذه الترقية شُغّلت مع `HTTPS_PROXY` على منفذ مغلق، فلم تُرسل أي رسالة. وتأكدنا قبل أول تشغيل أن `Bus::fake` يلتقط الـ job فعلاً.
- الـ helper `t()` يكتب كل مفتاح ترجمة غير موجود في ملفات `resources/lang` أثناء عرض الصفحة.
- إعدادات composer العامة على هذا الجهاز: `disable-tls = true` و`secure-http = false`، أي أن الحزم تُنزَّل بدون التحقق من الشهادات.

## متابعات مقترحة
- إذا كان السيرفر خلف proxy أو load balancer أو Cloudflare، تُضاف عناوينه إلى `TrustProxies` في `bootstrap/app.php`، وإلا يُرفض استدعاء `api/update_settings` و`api/import_students` من السيرفرات الموثوقة (انظر أدناه).
- إصلاح routes الـ GET التي تغيّر البيانات أو تنتهي بـ `dd()`.
- التأكد من نسخة MySQL على السيرفر، فقد صُمّم Laravel 13 لـ MySQL 8 (المحلي 8.0.30).
- الانتقال من Laravel Mix إلى Vite في خطوة منفصلة.
- **في socialuae**: `public/vendor/log-viewer` غير موجود في git، فصفحة log viewer ستفتح بدون ملفات الواجهة (`php artisan vendor:publish --tag=log-viewer-assets`). وفيه أيضاً يُستحسن فحص `excel.imports.csv.input_encoding` إن كان يستورد CSV.

## دروس للمشروع التالي
تضاف إلى ما في `socialuae-reference.md` و`islamic-reference.md`.

### قبل البدء
- البحث في قاعدة البيانات المحلية عن أسرار حقيقية قبل أي تشغيل: هنا token الـ Telegram وبيانات SMTP في جدول `settings` لا في `.env`. `Bus::fake` يغطي الاختبارات فقط؛ للأوامر والسكربتات خارج phpunit شُغّل PHP عبر wrapper يضبط `HTTP_PROXY`/`HTTPS_PROXY` على `127.0.0.1:9`، فيفشل أي اتصال من Guzzle أو curl. (`composer` يُشغَّل بدونه.)
- قبل تشغيل SmokeRoutesTest: البحث عن `dd(` و`exit` في الـ controllers. أي route GET ينتهي بـ `dd()` يوقف phpunit كله بصمت.
- حسابات الدخول المحلية قد تكون كلها غير صالحة (هنا كل المدارس عليها تغيير كلمة مرور إجباري، والمفتشان غير مفعّلين وبدون مدارس). تُرفع هذه القيود داخل الـ transaction في الاختبار، لا في القاعدة.

### التشغيل
- **تشغيلان على نفس القاعدة في نفس الوقت يفسدان بعضهما**: تتعارض أقفال الـ transactions وتفشل الاختبارات أو تبطؤ عشرين ضعفاً. ولا يُعدَّل كود التطبيق أثناء تشغيل `run.sh` (كل اختبار يبدأ عملية PHP جديدة تقرأ الكود من جديد).
- `cmd &` داخل أداة Bash يموت مع انتهاء الأمر أحياناً ويبقى أحياناً أخرى في الخلفية؛ للتشغيل الطويل `run_in_background` فقط، والتأكد بعد إيقافه من عدم بقاء عمليات `php`.
- `git add -A` بعد `run.sh` يلتقط نسخ الاختبارات في `tests/Feature`.

### Laravel 13 والحزم
- **`match` كلمة محجوزة منذ PHP 8.0**: model اسمه `Match` لا يُحمَّل أصلاً. البحث عن أسماء classes محجوزة (`Match`، `Enum`، `Readonly`...) قبل البدء.
- مقارنة `config()` كاملاً تكشف قيماً افتراضية تغيّرت في **الحزم** أيضاً، لا في Laravel فقط (ترميز CSV في Laravel Excel).
- `sortBy` في Laravel 7 مع PHP 7 لم يكن مستقراً: الصفوف المتساوية تظهر بترتيب عشوائي ثابت. على PHP 8 صار مستقراً. في المقارنة يظهر هذا كـ "same lines, different order"، ويُفحص بعدّ الصفوف المتساوية لا بالعين.
- opcodesio/log-viewer يحتاج callback لـ `LogViewer::auth()` وإلا يرفض الجميع في production، ويحتاج نشر ملفات الواجهة.
- الـ helper `t()` (في هذه المشاريع) يكتب المفاتيح الجديدة في ملفات اللغة أثناء الاختبارات؛ تُقارن ملفات `resources/lang` بعد كل تشغيل.

### التعديلات الآلية
- أداة Bash تحوّل `\\` إلى `\` وتلتهم `\t` في الـ heredoc وفي `python -c`. كل سكربت فيه backslash يُكتب في ملف بأداة الكتابة، وأداة الاستبدال يجب أن تحفظ نهايات الأسطر (CRLF في هذا الـ repo) وترفض الكتابة إذا لم تجد النص بالعدد المتوقع بالضبط.
- `sed -i` على Windows حوّل نهايات الأسطر إلى LF بصمت.

## قائمة الاختبار اليدوي
بحسب الخطة: الدخول والخروج لكل guard (ومنها دخول المشرف كمدرسة أو طالب)، الصلاحيات، الجداول (بحث وترتيب وتقسيم صفحات)، استيراد ملف طلاب xlsx وcsv بأسماء عربية، وتصدير Excel، التقارير والشهادات وبطاقات الطلاب وQR، ملفات PDF المضغوطة (`pdfservice`)، الاختبار من جهاز مكتبي ومن هاتف ولوح لمدرسة فيها `desktop_only`، صور المراقبة (selfie وscreenshot)، تصحيح الاختبارات وأداة ترقية المحاولات (preview وexecute وrollback)، الترجمة واتجاه الصفحة، log viewer بحساب مشرف، الـ scheduler (`php artisan schedule:list`)، رسالة استعادة كلمة مرور وطلب تصحيح (marking request) حقيقيان عبر SMTP، ومراقبة `storage/logs` يوماً كاملاً على نسخة بيانات الإنتاج.

## الملفات في هذا المجلد
| الملف | ما هو |
| --- | --- |
| `routes-before.json` / `routes-after.json` | ناتج `harness/routes-dump.php` قبل وبعد |
| `config-before.json` / `config-after.json` | ناتج `harness/config-dump.php` (كلمات المرور والمفاتيح مخفية) |
| `composer-audit-before.txt` / `composer-audit-after.txt` | ناتج `composer audit` |
| `composer-show-before.txt` / `composer-show-after.txt` | نسخ الحزم المثبتة |
| `tests-before.txt` / `tests-after.txt` | قائمة اختبارات المشروع ونتيجتها على Laravel 7 ثم 13 |
| `socialuae-reference.md` / `islamic-reference.md` | توثيق الترقيتين السابقتين، والأرقام فيهما تخص مشروعيهما |
| `harness/` | أدوات المقارنة (انظر `harness/README.md`) |
