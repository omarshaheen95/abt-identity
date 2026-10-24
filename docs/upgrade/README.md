# ترقية abt-identity إلى Laravel 13 و PHP 8.4

بتاريخ 23-10-2026 · الفرع `upgrade/laravel-13` · نقطة البداية: الـ tag `before-l13` (الـ commit `4849741`، مطابق لـ `origin/master`) · **لم يُرفع شيء إلى GitHub**

نُفِّذت الترقية بحسب "خطة تحديث مشاريع ABT" (في جذر المشروع): هيكل Laravel 13 جديد داخل نفس الـ repo، مع إبقاء كود المشروع كما هو، ثم مقارنة سلوك النسخة الجديدة بالقديمة على نفس قاعدة البيانات المحلية (`abt-identity`). abt-identity أول منصة من منصات الموجة 2 الست تُرقّى (english وfluency وmath وscience وarabicplus لم تبدأ). نقطة البداية كانت readingbenchmark (نفس القالب، `readingbenchmark-reference.md`)، ومعها ترقيات nonarab وarabic-arabs وss platform من **Laravel 8** نفسه (قُرئت في مجلداتها ولم يُعدَّل فيها شيء)، وكل حل منها فُحص هنا من جديد بالمقارنة الفعلية.

| | قبل | بعد |
| --- | --- | --- |
| Laravel | 8.83.29 | 13.38.0 |
| PHP | `^7.3` (composer) / 7.4 محلياً | `^8.4` |
| PHPUnit | 9.6 | 12.5 |
| Carbon | 2.72 | 3.15 |
| `composer audit` | 68 ثغرة معروفة في 15 حزمة | لا شيء |
| اختبارات المشروع | 9 (و5 smoke) | 9 (و5 smoke) و12 جديدة لثغرة الـ IP |

| الـ commit | ما فيه |
| --- | --- |
| `748fdce` | الخطة ومراجع الترقيات السابقة وأدواتها |
| `cb5b8f6` | الـ baselines قبل أي تعديل: `routes-before.json` و`config-before.json` و`composer-audit-before.txt` و`composer-show-before.txt` و`tests-before.txt` و`tests/smoke-baseline.json`، و`tests/TestCase.php` يعطّل `LogMessageJob` |
| `0dfb669` | أدوات المقارنة بعد تكييفها لهذا المشروع (`harness/`) |
| `4ac18fb` | الترقية نفسها، ومعها آخر route بصيغة النص |
| `1950295` | إصلاح مستوى الطالب المستورد حين يكون عمود Arab بـ Yes/No (خطأ قديم كشفه PHP 8، انظر أدناه) |
| `4527f92` | أداة مقارنة لدخول المشرف كنفس الحساب مرتين |
| `88d6ee9` | حذف routes الـ GET `seed` و`copy_structure` و`update_questions_standards/{year}`، وتعليقات routes الـ artisan |
| `1a051be` | إصلاح فحص الـ IP في `api/update_settings` و`api/import_students`، و`TrustedIpApiTest` |
| `a71d99c` | إيقاف حفظ hash كلمة المرور في activity log |
| `bcbc85f` | إخفاء كلمات المرور والتوكنات في رسائل Telegram |
| (آخر commit) | هذا التوثيق، ونتائج المقارنة النهائية |

## ما الذي تغيّر

### الهيكل
- `bootstrap/app.php` يحل محل `app/Http/Kernel.php` و`app/Console/Kernel.php` و`RouteServiceProvider`. مجموعات الـ routes بنفس الترتيب (api، web، manager، school، inspection، student) ونفس الـ prefix والـ middleware، ومجموعتا `web` و`api` مكتوبتان كاملتين كما كانتا في Laravel 8، و`throttle:api` (60 في الدقيقة لكل مستخدم أو IP) صار في `AppServiceProvider`. الـ aliases بنفس الأسماء (`manager` و`school.guest` و`generalLocal` و`checkIfActive` و`setRequestData` و`desktop.only` و`force.password` و`session.password` ...). `auth` ما زال `App\Http\Middleware\Authenticate`.
- **CORS**: كانت fruitcake/laravel-cors مثبتة لكن `HandleCors` لم يكن في الـ middleware العام، فلم تحمل أي استجابة ترويسات CORS. Laravel 13 يضيفه افتراضياً، فأُزيل في `bootstrap/app.php` حتى يبقى السلوك نفسه. `config/cors.php` بقي كما هو (غير مستخدم، كما كان).
- `bootstrap/providers.php` فيه `AppServiceProvider` و`SettingServiceProvider` بنفس ترتيبهما. ما كان في `EventServiceProvider` (`School::observe` و`UserEventSubscriber`) صار في `AppServiceProvider`، واكتشاف الـ listeners التلقائي مطفأ. `AuthServiceProvider` كان فارغاً، و`BroadcastServiceProvider` معطّلاً (و`routes/channels.php` لا يُحمَّل، كما كان).
- الـ Exception Handler القديم باقٍ (يرسل إلى Telegram في production، ويشكّل أخطاء JSON والتوجيه لكل guard)، ومربوط بعد `->create()`. أُعلنت فيه `$statusCode`، و`array_get` صار `Arr::get`.
- حُذفت نسخ التطبيق من middleware الإطار (`EncryptCookies` و`VerifyCsrfToken` و`TrimStrings` و`TrustProxies` و`TrustHosts` و`PreventRequestsDuringMaintenance` و`RedirectIfAuthenticated` و`ValidateSignature`)، لأنها لم تكن معدّلة. التعديل الوحيد كان `old_password` في `TrimStrings` (لا يُقصّ قبل فحصه)، وصار في `bootstrap/app.php`. `RouteServiceProvider::HOME` في Auth controllers صار `'/home'`، و`redirectUsersTo('/home')` للـ `guest`.
- الـ scheduler (ثلاث مهام) انتقل إلى `withSchedule()` كما هو، والأوامر في `app/Console/Commands` تُكتشف تلقائياً.
- `app/Helpers/helpers.php` كان في `autoload-dev` فقط، أي أن `composer install --no-dev` كان سيُسقط الموقع كله؛ صار في `autoload`. وdebugbar انتقلت إلى `require-dev`.
- آخر route بصيغة النص (`student-cards` و`user/{id}/certificate`) صار `[Controller::class, 'method']`. `routes-dump.php` بعد الترقية مطابق لما قبلها في 345 من 346 route (الرابط والاسم والـ action والـ middleware وترتيبه)، والفرق الوحيد أن Sanctum 4 يعطي route الـ csrf-cookie الخاص به اسماً (`sanctum.csrf-cookie`). و`route:cache` يعمل.
- لا يوجد model اسمه `Match` هنا (بخلاف readingbenchmark وnonarab)، ولا `$dates`.

### الحزم
| الحزمة | القرار |
| --- | --- |
| spatie/laravel-permission 5.11 | 8.3 (نفس المشاريع السابقة). الـ middleware انتقل إلى `Spatie\Permission\Middleware`، والـ config هو الافتراضي للنسخة 8 (القديم كان الافتراضي للنسخة 5 حرفياً). migration الجداول تقرأ أسماء أعمدة الـ pivot من الـ config بدل خصائص `PermissionRegistrar` الثابتة (نفس إصلاح readingbenchmark). **كاش الصلاحيات بين النسختين غير متوافق**: `permission:cache-reset` عند النشر وعند الرجوع |
| spatie/laravel-activitylog 3.17 | 4.12. كل model من الـ 12 صار فيه `getActivitylogOptions()` بنفس الحقول (`logOnly`) و`logOnlyDirty` و`dontSubmitEmptyLogs`، و`$recordEvents` بقي static كما هو. migration جديدة تضيف `event` و`batch_uuid` |
| spatie/laravel-translatable 4.6 | 6.15. الـ fallback نفسه (`app.fallback_locale`) |
| yajra/laravel-datatables-oracle 9 | 13.3 بالـ config الافتراضي (كان الافتراضي في 9 أيضاً) |
| maatwebsite/excel 3.1.56 | 3.1.70، والـ config من النسخة الجديدة مع التخصيص الوحيد القديم: استيراد CSV بترميز `UTF-8` (الافتراضي يخمّن الترميز) |
| laravel/sanctum 2.15 | 4.2 بالـ config الافتراضي. لا يستخدمه الكود إلا route `api/user` و`sanctum/csrf-cookie`، وأُبقي حتى تبقى قائمة الـ routes نفسها |
| arcanedev/log-viewer 8.3 | حُذفت (لا نسخة لـ 13) وحلّت محلها opcodesio/log-viewer، وملفات الواجهة منشورة في `public/vendor/log-viewer`. **للمشرف فقط من البداية** (انظر "ثغرات أُصلحت") |
| hesto/multi-auth | حُذفت، والـ trait الوحيد المستخدم نُقل كما هو إلى `app/Traits/LogsoutGuard.php` |
| laravel/helpers | حُذفت بعد تحويل الاستدعاءات (`array_get` و`array_first` و`array_last` و`snake_case`) إلى `Arr::` و`Str::` |
| propaganistas/laravel-phone، psr/simple-cache | حُذفتا من `composer.json`: الأولى لا يستخدمها أي كود، والثانية يجلبها الإطار نفسه |
| fruitcake/laravel-cors، facade/ignition، laravel/sail | حُذفت (CORS صار جزءاً من Laravel ومعطّل كما كان، والباقي أدوات تطوير) |
| laravel/ui 3 | 4.6 (`AuthenticatesUsers` وأخواتها منها) |
| barryvdh/laravel-debugbar `*` | 4.4 في `require-dev` |
| guzzle 7، jsvalidation، simple-qrcode، dyrynda cascade soft deletes، tinker | أحدث نسخة من نفس الإصدار الرئيسي (tinker 3). ملف `public/vendor/jsvalidation/js` المنشور بقي كما هو، و`jsvalidation.view` بقي `bootstrap4` |

كل الحزم بنسخ صريحة بدل `"*"`، و`minimum-stability` صار `stable`.

### سلوك أبقيناه كما كان في Laravel 8 عن قصد
هذه أشياء غيّرها Laravel أو الحزم بين 8 و13، ولو تُركت لتغيّر ما يراه المستخدم:

- **لا يخرج مستخدمو اللوحات بعد الدخول**: منذ Laravel 11 يكتب `SessionGuard::login()` HMAC لـ hash كلمة المرور تحت `password_hash_<guard>`، وهو نفس المفتاح الذي يكتب فيه `ValidateSessionPassword` (و`UpdatesPassword`) الـ hash نفسه ويقارنه. صار الـ middleware يقبل الشكلين (إصلاح nonarab). هنا لا يظهر الخلل في الدخول العادي ولا في الدخول الثاني بعد خروج، لأن الـ middleware يحفظ رقم صاحب الـ hash (`password_hash_<guard>_id`) والخروج يمسح الجلسة. يظهر حين **يدخل المشرف كنفس المدرسة أو نفس المفتش مرة ثانية في نفس الجلسة**: مفتاح المالك باقٍ و`login()` كتب HMAC، فيُطرد في الصفحة التالية برسالة "تم تغيير كلمة المرور، الرجاء تسجيل الدخول من جديد". جُرّب بإلغاء الإصلاح مؤقتاً (`ImpersonateTwiceSnapshotTest`): بدونه طُرد في المدرسة والمفتش، ومعه مطابق لـ Laravel 8. و`AuthFlowSnapshotTest` (ثلاث صفحات بعد كل دخول، ودخول ثانٍ في نفس الجلسة) مطابق في الحالتين. مجموعة `web` بلا `AuthenticateSession` كما كانت، فلا يلزم subclass كما في nonarab.
- **لا يخرج المستخدمون عند النشر ولا عند الرجوع**: `session.serialization` بقيت `php` (بـ `json` يضيع الدخول وتوكن CSRF من ملف جلسة كتبه 8، جُرّب بـ `harness/session-check.php`)، والجلسة التي يكتبها 13 يقرؤها 8. اسم الكوكي `laravel_session` كما هو، والكوكي المشفر والقيم المشفرة (توكن تقرير QR) تُفك في الاتجاهين (`harness/cookie-check.php`).
- **الكاش**: `cache.serializable_classes` بقيت `true`: `AppServiceProvider` و`updateSettingsApi` يخزّنان Collection من `Setting` تحت `settings`، و`StudentReport` يخزّن الطالب بمدرسته ومستواه ومحاولاته، وتقرير المقارنة يخزّن model المدرسة. جُرّب بملفات كاش كتبتها نسخة وقرأتها الأخرى (`harness/cache-check.php`): كلها مطابقة في الاتجاهين، وبـ `false` يرجع كل شيء ما عدا المصفوفة `__PHP_Incomplete_Class`. الـ prefix `laravel_cache_` كما كان (Laravel 13 يجعله `laravel-cache-`).
- **صفحات الأخطاء ورسائل البريد**: لم تكن منشورة في المشروع، فنُسخ من Laravel 8 ما تغيّر في 13: `errors/minimal` و`errors/layout`، وقوالب `vendor/mail` (html وtext) و`vendor/notifications/email`. رسائل استعادة كلمة المرور الثلاث مطابقة حرفياً، وHTML مطابق ما عدا مسافتين داخل سطر `@media` في الـ CSS (فرق في مترجم Blade).
- **Carbon 3**: دوال `diffIn*` صارت ترجع كسراً وبإشارة. الاستدعاءات الثمانية في إحصائيات المشرف والمدرسة (`> 1` شهر/يوم) صارت `(int) ...diffInX(..., true)`، و`passwordExpiresInDays()` (كانت بـ `false`) بقيت بإشارتها مع `(int)`.
- **ملفات `.env` الحالية تعمل كما هي**: الإعدادات تقرأ الأسماء الجديدة ثم القديمة (`CACHE_DRIVER` و`MAIL_DRIVER` و`FILESYSTEM_DRIVER` و`DATABASE_URL`)، والقيم الافتراضية هي قيم Laravel 8 (file وsync وsmtp). إعدادات SMTP من جدول `settings` (`SettingServiceProvider`) تصل إلى نفس المفاتيح، و`mail.mailers.smtp.local_domain` بقي null (Laravel 13 يجعله اسم `APP_URL`، أي يغيّر EHLO)، ومسار sendmail وقيم argon كما كانت.
- **كلمات مرور غير bcrypt**: `hashing.bcrypt.verify` بقيت `false` كما كان (13 يرمي exception بدل فشل الدخول). محلياً كل كلمات المرور bcrypt، فهو احتياط للإنتاج.
- مسار الـ local disk بقي `storage/app`، ولا إعادة hash لكلمة المرور عند الدخول (كانت ستغيّر الـ hash الذي يقارنه `ValidateSessionPassword`)، وbcrypt على 10 rounds، والـ pagination بالقالب الافتراضي كما كان.
- استيراد CSV بترميز `UTF-8` (أعلاه في الحزم).

### PHP 8.4
- إعلان الخصائص التي كانت تُنشأ ديناميكياً (`$statusCode` في الـ Handler، و`$last_cell`/`$last_row` في تصديرين، و`$log_errors` في `OldStudentImport`)، وجعل الـ nullable صريحاً في 4 دوال.
- تحميل كل الـ 209 class في `app/` على PHP 8.4 بدون أي تحذير (`harness/load-all.php`)، و`php -l` بدون خطأ لكل ملف PHP في المشروع.

## كيف تأكدنا أن السلوك لم يتغير
كل المقارنات شُغّلت على نفس قاعدة البيانات المحلية، مرة على نسخة Laravel 8 (git worktree من commit الـ baselines `cb5b8f6`، وكود التطبيق فيه مطابق لـ `before-l13`، في `../abt-identity-l8-ref`) ومرة على Laravel 13، وكل كتابة داخل transaction تُلغى في النهاية، والوقت مثبّت. الأدوات في `harness/` (انظر `harness/README.md`). شُغّلت الأدوات مرتين على Laravel 8 نفسه لمعرفة ما يتغير بين تشغيلين بدون أي ترقية: 390 ملفاً مطابقاً و6 مختلفة (ترتيب الطلاب المتساوين في تقرير الطالب، وأرقام صفوف تضيفها الأداة، وترتيب أسئلة معاينة الاختبار العشوائي).

| الفحص | النتيجة |
| --- | --- |
| قائمة الـ routes (`routes-before.json` / `routes-after.json`) | 346 route للتطبيق قبل الإصلاحات: نفس الرابط والاسم والـ action والـ middleware وترتيبه، ما عدا اسم route الـ csrf-cookie من Sanctum 4. بعد الإصلاحات حُذفت 3 routes فقط (`seed` و`copy_structure` و`update_questions_standards/{year}`). الفرق الآخر routes الـ log viewer (البديل) والـ debugbar |
| الإعدادات الفعلية بعد التشغيل (`config-before.json` / `config-after.json`) | نفس القيم، ما عدا مفاتيح جديدة في Laravel 13 والحزم، ومفاتيح حزم حُذفت (ignition وflare وcors وmailgun)، وإعدادات debugbar (dev فقط)، و`session.lifetime` صار رقماً بدل نص |
| 109 صفحات GET بدون parameters للـ guards الأربعة والزائر، كل منها كصفحة وكطلب datatables | مطابقة، ما عدا ترتيب الأسئلة العشوائي في معاينة الاختبار، وأرقام صفوف `inspection_schools` التي تضيفها الأداة (يختلفان بين تشغيلين على 8 أيضاً) |
| 74 صفحة بـ parameters أو فلاتر (التعديل، تقرير الطالب وبطاقته، الشهادات، تقارير attainment وprogress وstudent mark وyear to year وcomparison، بطاقات الطلاب العامة وتقرير QR، الدخول كحساب آخر، تغيير اللغة)، كصفحة وكطلب datatables | مطابقة، ما عدا اسم "أعلى درجة" في تقرير attainment: 16 طالباً متساوون بدرجة 100، وPHP 8 يرتّب ترتيباً مستقراً فيظهر أولهم في الاستعلام، بينما كان PHP 7 يختار أحدهم بترتيب غير ثابت. نفس الأرقام والإحصاءات |
| الدخول ثم ثلاث صفحات، والدخول الثاني في نفس الجلسة، والخطأ والقفل وفرض تغيير كلمة المرور لكل guard، ودخول المشرف كمدرسة ومفتش وطالب، ودخول الطالب، وسجل `login_sessions` | مطابق |
| دخول المشرف كنفس المدرسة ونفس المفتش مرتين | مطابق (بدون إصلاح `ValidateSessionPassword`: يُطرد في الدخول الثاني) |
| 21 تصدير Excel من اللوحات الثلاث | مطابقة خلية بخلية |
| استيراد طلاب (xlsx، ثم تحديث بـ csv عربي، ثم إنشاء من csv بأسماء دخول من رقم الطالب) | نفس الصفوف والأخطاء والسجلات وعدد الصفوف. الفرق الوحيد مقصود: طلاب Yes في مستويات العرب (`1950295`) |
| تقديم اختبارين (صح وخطأ واختيار وتوصيل، واختبار الجولة المفتوحة بـ 40 standard) ثم التصحيح الآلي وشاشة التصحيح، وإعادة تصحيح محاولة قديمة بـ standards | نفس الإجابات المحفوظة والمجموع ودرجات المواد ودرجات كل standard وحالة التصحيح |
| 50 حالة نموذج (إضافة وتعديل للمشرف والمدرسة، صحيحة وفارغة وخاطئة، طلب التصحيح ورسالته، تغيير كلمة المرور والملف الشخصي لكل لوحة، فحص JsValidator عن بُعد) | نفس البيانات المحفوظة ورسائل التحقق والبريد. الفرقان: اختفاء hash كلمة المرور من activity log (مقصود، `a71d99c`)، والنصوص العربية في أعمدة translatable من نوع TEXT تُحفظ الآن كنص مباشر بدل `\uXXXX` (نفس القيمة المقروءة، كما في nonarab) |
| رسائل استعادة كلمة المرور للـ guards الثلاثة | نفس الرسالة والرابط والصفحة، وHTML مطابق ما عدا مسافتين في CSS |
| من يفتح log viewer | المشرف فقط (كان يفتح للجميع) |
| الجلسات والكوكيز والكاش بين النسختين | تُقرأ في الاتجاهين |
| `schedule:list` | نفس المهام الثلاث بنفس المواعيد |
| اختبارات المشروع (9) و`SmokeRoutesTest` (5) | تنجح على النسختين (`tests-before.txt` / `tests-after.txt`)، والـ smoke بنفس الصفحات الست التي ترجع 500 أصلاً. و`TrustedIpApiTest` (12) يفشل في 10 حالات على الكود القديم وينجح كله بعد الإصلاح |

الجولة الأخيرة على Laravel 13 بعد كل الإصلاحات مقابل Laravel 8: 382 ملفاً مطابقاً من 397. الـ 15 الباقية هي المقصودة أو العشوائية فقط: `log-viewer.txt`، و`forms.txt` (الـ hash والترميز)، و`import.txt` (المستويات)، وثماني صفحات لتقرير attainment (ترتيب المتساوين)، وتقرير الطالب (ترتيب المتساوين، يختلف بين تشغيلين على 8)، ومعاينة الاختبار وأرقام `inspection` (عشوائية)، وصفحتا `student-import-error` بلا رقم (404 في النسختين، تغيّر نص الخطأ فقط).

فروق أخرى ليست تغييراً فيما يراه المستخدم:
- رسالة الـ `ValidationException` الداخلية صارت أول خطأ بدل "The given data was invalid".
- `PreventRequestForgery` في 13 يقبل طلب POST من نفس الموقع (`Sec-Fetch-Site: same-origin`) حتى لو انتهى توكن CSRF؛ حماية CSRF من المواقع الأخرى كما هي.
- `ValidatePathEncoding` (جديد) يرفض رابطاً فيه UTF-8 غير صالح بـ 400.

## متابعات مقترحة
- **14 طالباً محلياً قيمة `arab` عندهم لا تطابق مستواهم** (الخطأ القديم في الاستيراد). يُفحص الإنتاج بنفس الاستعلام (`students.arab <> levels.arab`) ويُقرَّر تصحيحهم يدوياً.
- إضافة عناوين الـ proxy إلى `TrustProxies` إن وُجد (بند النشر 2).
- حماية دخول الطلاب و`student-cards` (رابط موقّع أو كلمة مرور) بعد تجربة خدمة PDF على سيرفر التجربة.
- إصلاح الصفحات التي ترجع 500 أصلاً (`->only()` على `Route::resource`)، و`with_abt_id` في الاستيراد.
- حذف hash كلمات المرور من سجلات activity log القديمة.
- التأكد من نسخة MySQL على السيرفر، فقد صُمّم Laravel 13 لـ MySQL 8 (المحلي 8.0.30).
- الانتقال من Laravel Mix إلى Vite في خطوة منفصلة.
- **قبل التشغيل الحقيقي محلياً**: إعادة جدول `settings` من النسخة الاحتياطية (Telegram وSMTP معطّلان الآن في القاعدة المحلية).

## الملفات في هذا المجلد
| الملف | ما هو |
| --- | --- |
| `routes-before.json` / `routes-after.json` | ناتج `harness/routes-dump.php` قبل الترقية، وبعدها مع الإصلاحات |
| `config-before.json` / `config-after.json` | ناتج `harness/config-dump.php` (كلمات المرور والمفاتيح مخفية) |
| `composer-audit-before.txt` / `composer-audit-after.txt` | ناتج `composer audit` |
| `composer-show-before.txt` / `composer-show-after.txt` | نسخ الحزم المثبتة |
| `tests-before.txt` / `tests-after.txt` | اختبارات المشروع والـ smoke ونتيجتها على Laravel 8 ثم 13 |
| `readingbenchmark-reference.md` / `socialuae-reference.md` / `islamic-reference.md` | توثيق الترقيات السابقة، والأرقام فيها تخص مشاريعها |
| `harness/` | أدوات المقارنة (انظر `harness/README.md`) |

## قرارات صاحب المشروع في المشاكل الأمنية
عُرضت قبل أي إصلاح. كلها موجودة على Laravel 8، وليست من الترقية. كل إصلاح في commit مستقل يمكن التراجع عنه وحده.

| المشكلة | القرار | التنفيذ |
| --- | --- | --- |
| routes الـ artisan في `routes/web.php` (`cache` و`migrate` و`migrate-fresh` و`rollback` و`seed` و`view`) | **معلّقة أصلاً** (كود ميت، لا تعمل). حُذفت التعليقات | `88d6ee9` |
| `GET manager/seed`: يشغّل `PermissionsTableSeeder` ثم يعطي المشرف الحالي كل صلاحيات المشرفين. أي مشرف يرفع صلاحياته بفتح رابط، بلا CSRF | حُذف | `88d6ee9` |
| `GET manager/copy_structure`: يحذف أسئلة اختبارات غير العرب في السنة 2 وينسخها من اختبارات العرب | حُذف | `88d6ee9` |
| `GET manager/transfer_student_term`: ينقل محاولات طلاب مدرسة ثابتة في الكود (100) إلى اختبارات أخرى | **أُبقي** بقرار صاحب المشروع | - |
| `GET manager/update_questions_standards/{year}`: يعيد بناء standards أسئلة سنة كاملة (وُجد أثناء المقارنة وعُرض بعد القائمة الأولى) | حُذف الـ route والـ method، وبقي `QuestionStandardService` (لا يستخدمه غيره، ويمكن تشغيله من أمر artisan إن لزم) | `88d6ee9` |
| `getRealIpAddress()` تثق بـ `X-Forwarded-For` وأربع ترويسات مثلها، فأي شخص يغيّر جدول `settings` (ومنه SMTP وtoken الـ Telegram) عبر `POST api/update_settings`، أو يستورد طلاباً لأي مدرسة عبر `POST api/import_students` | يُصلح | `1a051be` (انظر "ثغرات أُصلحت") |
| hash كلمة المرور في activity log لـ `Manager` و`School` و`Inspection` (58 صفاً محلياً: 6 مشرفين و49 مدرسة و3 مفتشين) | يُوقف التسجيل فقط، والسجلات القديمة تبقى | `a71d99c` |
| log viewer (arcanedev) بلا أي middleware: أي زائر يقرأ السجلات وينزّلها ويحذفها | opcodesio خلف دخول المشرف | ضمن commit الترقية (الحزمة القديمة لا تعمل على 13، فالبديل محمي من أول يوم) |
| `TelegramLoggerHandler` يرسل الطلب والجلسة كاملين مع كل خطأ: كلمة المرور المكتوبة، توكن CSRF، و`password_hash_<guard>` | يُخفى كل مفتاح فيه pass أو token أو secret | `bcbc85f` |
| صفحات عامة بلا دخول: `student-cards?school_id=N` و`user/{id}/report` و`user/{id}/certificate` | **تُترك وتُوثَّق** (انظر "مشاكل لم نلمسها"، وفيها سبب يجعل الأولى أخطر مما تبدو) | - |

## خطأ قديم كشفه PHP 8: مستوى الطالب المستورد (`1950295`)
ظهر في مقارنة الاستيراد: على Laravel 13 رُفضت صفوف قُبلت على 8 برسالة "Assessment not found, check grade and arab status". `StudentImport::findLevel()` كانت تقارن خلية Arab كما هي (`Yes` و`no` و`1` ...) بعمود `arab` في المستويات (0/1) مقارنة غير صارمة:
- **في PHP 7** `0 == 'Yes'` صحيحة، فالطالب المكتوب أمامه `Yes` كان يُوضع بصمت في **مستوى غير العرب** من صفه، ويُحفظ بـ `arab = 1` (في المقارنة: UPG-1001 في المستوى 63 بدل 51). **محلياً 14 طالباً قيمة `arab` عندهم لا تطابق مستواهم**، وغالباً هذا سببهم.
- **في PHP 8** المقارنة خاطئة، فكان كل صف بـ Yes/No سيُرفض.

صارت الخلية تمر على `prepareYesOrNoValue()` (نفس التحويل الذي يُحفظ به الطالب). ملفات 1/0 تُستورد كما كانت تماماً، وملفات Yes/No تأخذ المستوى الصحيح. بعد الإصلاح: نفس الصفوف والأخطاء والسجلات وعدد الصفوف كما على 8، والفرق الوحيد أن طلاب Yes في مستويات العرب. **البيانات القديمة لم تُلمس** (انظر "متابعات مقترحة").

## ثغرات ومشاكل أُصلحت بعد الترقية
كلها موجودة على Laravel 8، وعُرضت على صاحب المشروع قبل الإصلاح، وكل واحدة في commit.

- **فحص الـ IP في `api/update_settings` و`api/import_students`** (`1a051be`): بلا دخول، ومحميان فقط بقائمة `config('app.trusted_ips')`، و`getRealIpAddress()` كانت تأخذ العنوان من `X-Forwarded-For` وأخواتها قبل `REMOTE_ADDR`. صار `isTrustIpAddress()` يقارن `$request->ip()` مقارنة صارمة، وحُذفت `getRealIpAddress()`. `tests/Feature/TrustedIpApiTest.php` يزوّر كل ترويسة من الخمس في الطلب وفي `$_SERVER`: **قبل الإصلاح نجحت حالات التزوير العشر كلها** (200 وتغيّر الإعداد، و422 في الاستيراد أي أن الطلب تجاوز فحص الـ IP)، والآن 401، والسيرفر الموثوق ما زال يحدّث الإعدادات. (انظر بند TrustProxies في النشر.)
- **hash كلمة المرور في activity log** (`a71d99c`): حُذف `password` من الحقول المسجّلة لـ `Manager` و`School` و`Inspection`. في مقارنة النماذج اختفت قيم الـ hash فقط، وتغيير كلمة المرور وحده لم يعد يُنشئ سجلاً. السجلات القديمة (58) ما زالت تحوي الـ hash.
- **كلمات المرور في رسائل Telegram** (`bcbc85f`): كل مفتاح فيه pass أو token أو secret، على أي عمق، صار `***`. فُحص بالتقاط الـ job بـ `Bus::fake`: الأسرار الأربعة (كلمة المرور المكتوبة، القديمة، `password_hash_manager`، توكن CSRF) كانت في الرسالة وصارت مخفية، وباقي الرسالة كما هو.
- **routes GET تعدّل البيانات** (`88d6ee9`): حُذفت `manager/seed` و`manager/copy_structure` و`manager/update_questions_standards/{year}` (ومعه method `updateQuestionStandards()`)، وتعليقات routes الـ artisan في `routes/web.php`. لا يستخدم أي view أو كود أسماءها، و`route:cache` يعمل. `manager/transfer_student_term` أُبقي.
- **log viewer**: للمشرف فقط (`manager` و`auth:manager` للصفحات والـ API، و`LogViewer::auth()` يسمح للمشرف في production). الزائر والمدرسة والمفتش والطالب يوجَّهون إلى دخول المشرف (`LogViewerAccessSnapshotTest`).

## مشاكل كانت موجودة قبل الترقية (لم نلمسها)
- **الطالب يدخل باسم المستخدم وحده، بلا كلمة مرور** (`StudentAuth\LoginController@studentLogin`)، و`GET student-cards?school_id=N` يعرض لأي زائر أسماء مستخدمي كل طلاب أي مدرسة مع رموز QR للدخول. النتيجة: **أي زائر يستطيع الدخول كأي طالب** (فتح الاختبارات وتسليمها باسمه). الصفحة تفتحها خدمة PDF الخارجية (`PdfZipService`) بلا جلسة، فإصلاحها يحتاج روابط موقّعة (كما في nonarab) وتجربة الخدمة على سيرفر التجربة. نفس الشيء في math.
- `user/{id}/report` يعرض تقرير أي طالب برقمه لأي زائر، و`user/{id}/certificate` يعرض الاسم والدرجة لأي محاولة تتجاوز علامة الشهادة.
- `GET manager/transfer_student_term` يكتب في القاعدة خلف دخول المشرف بلا صلاحية خاصة ولا CSRF، وأُبقي بقرار صاحب المشروع.
- `manager/cer` (متغير غير معرّف في الـ view) و`create` لـ `login_sessions` و`student_term` و`school/scheduling` و`inspection/school` و`inspection/student` ترجع 500 على النسختين (`Route::resource` بلا `->only()`، والـ methods غير موجودة)، وتظهر في `tests/smoke-baseline.json`.
- الاستيراد من شاشة المشرف بـ `process_type=update` بدون `with_abt_id` يرجع 500 (`$data['with_abt_id']` غير موجود بعد `validated()`). الشاشة ترسله دائماً، فلا يظهر للمستخدم.
- `ActivityLogController@show` يستخدم `\GuzzleHttp\json_decode`، وستُحذف في Guzzle 8.
- الـ Exception Handler يعيد نص الـ exception في استجابات JSON لأخطاء 500.
- الـ helper `t()` يكتب كل مفتاح ترجمة غير موجود في ملفات `resources/lang` أثناء عرض الصفحة.
- `encryptStudentId()`/`decryptStudentId()` فيهما مفتاح ثابت في الكود بعد `return` (كود ميت)، والتشفير الفعلي بـ `APP_KEY`.
- إعدادات composer العامة على هذا الجهاز: `disable-tls = true` و`secure-http = false`، أي أن الحزم تُنزَّل بدون التحقق من الشهادات (كما لوحظ في readingbenchmark).

## النشر

1. PHP **8.4.27 أو أحدث** (8.4.7 الموجودة محلياً مصابة بثغرة php2shell). الـ extensions: `pdo_mysql` و`mbstring` و`gd` و`zip` و`intl` و`bcmath` و`fileinfo` و`openssl` و`curl`.
2. ملف `.env` الحالي يعمل كما هو. **إن كان الموقع خلف proxy أو load balancer أو Cloudflare** تُضاف عناوينه إلى `TrustProxies` (`$middleware->trustProxies(at: [...])` في `bootstrap/app.php`) قبل النشر، وإلا يُرفض `api/update_settings` و`api/import_students` من السيرفرات الموثوقة. لا يُضاف `'*'`، فهو يعيد الثغرة.
3. ملفات `resources/lang` ليست في git (ما عدا `auth_log.php`)، فتُنسخ من النسخة الحالية على السيرفر إلى المجلد الجديد، ومعها `public/uploads` (كل ما يرفعه `uploadFile()`: ملفات الاستيراد وصور المراقبة والإعدادات والأسئلة) و`storage/app`.
4. في مجلد جديد بجانب القديم:
   ```
   composer install --no-dev --optimize-autoloader
   php artisan migrate --force
   php artisan permission:cache-reset
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```
   الـ migration الوحيدة الجديدة تضيف عمودين nullable إلى `activity_log` (بدون `after()`)، و`down()` فيها يحذفهما. جُرّبت `up` ثم `down` ثم `up` محلياً، وهي مطبّقة الآن على القاعدة المحلية (نسخة Laravel 8 تعمل معها لأن العمودين nullable)، و`migrate --pretend` لم يُظهر غيرها. **`permission:cache-reset` ليس اختيارياً**: كاش الصلاحيات الذي كتبته النسخة 5 لا تقرؤه 8 (ثبت في nonarab، ولم يُعَد تجريبه هنا).
5. ملفات jsvalidation وlog-viewer المنشورة في `public/vendor` محفوظة في git، ولا تحتاج نشراً.
6. الـ cron الحالي (`schedule:run`) يعمل كما هو، والـ queue كما هي (`QUEUE_CONNECTION` من `.env`؛ `LogMessageJob` على queue اسمها `log`).
7. إن كان السيرفر يشغّل `composer install` بدون `--no-dev` و`APP_DEBUG=true`، فـ debugbar 4 تظهر بقيم افتراضية مختلفة قليلاً؛ في production يجب أن يكون `APP_DEBUG=false` على أي حال.
8. الرجوع: الـ symlink إلى المجلد القديم، ثم `php artisan migrate:rollback --step=1` إن لزم، و**`php artisan permission:cache-reset` من المجلد القديم**. الجلسات والكوكيز والكاش تُقرأ في الاتجاهين.

## قائمة الاختبار اليدوي
بحسب الخطة: الدخول والخروج لكل guard **مع فتح أكثر من صفحة بعد الدخول والدخول مرتين في نفس المتصفح**، ودخول المشرف كمدرسة أو مفتش أو طالب، الصلاحيات، الجداول (بحث وترتيب وتقسيم صفحات)، استيراد ملف طلاب xlsx وcsv بأسماء عربية (إنشاء وتحديث وحذف)، وتصدير Excel، التقارير (attainment وprogress وyear to year وcomparison وtrends) والشهادات وبطاقات الطلاب وQR، ملفات PDF المضغوطة (`pdfservice`)، الاختبار من جهاز مكتبي ومن هاتف لمدرسة فيها `desktop_only`، صور المراقبة (selfie وscreenshot) و`proctor:clean-images`، تصحيح الاختبارات (الآلي وشاشة التصحيح و`student-terms:auto-correct`) وأداة ترقية المحاولات، الترجمة واتجاه الصفحة، log viewer بحساب مشرف، الـ scheduler (`php artisan schedule:list`)، رسالة استعادة كلمة مرور وطلب تصحيح حقيقيان عبر SMTP، و`api/update_settings` و`api/import_students` من السيرفرات الموثوقة، ومراقبة `storage/logs` يوماً كاملاً على نسخة بيانات الإنتاج.

## دروس للمشروع التالي (للمنصات الخمس الأخرى: english وfluency وscience وarabicplus، وmath إن احتاج)
تضاف إلى ما في `readingbenchmark-reference.md` و`socialuae-reference.md` و`islamic-reference.md`، وإلى أقسام الدروس في `../nonarab` و`../arabic-arabs` و`../ss platform` (ترقيات من Laravel 8 نفسه، أقرب من readingbenchmark في كل ما يخص الإطار).

### قبل البدء
- **README البداية قد يكون قديماً**: كان يقول إن القاعدة المحلية فارغة، ووجدتها مستوردة (49 جدولاً، 182 ألف طالب). تُفحص القاعدة نفسها (`information_schema.tables`) قبل سؤال صاحب المشروع.
- **منصة شقيقة قد تكون تُرقّى في نفس الوقت**: math بدأت في جلسة أخرى بالتوازي ولم يكن لها README وقت البدء. يُعاد فحص مجلدات المنصات الخمس أثناء العمل لا في البداية فقط. وعملية `php` غريبة في قائمة العمليات قد تكون تلك الجلسة (`Win32_Process.CommandLine` يكشف مجلدها)؛ قاعدتها مختلفة فلا تتعارض.
- الأسرار في جدول `settings` (token وchannel الـ Telegram، وخادم SMTP وكلمة مروره الحقيقية). أسرع تعطيل: نسخة من الجدول خارج المشروع (`mysqldump ... settings`)، ثم تفريغ قيم Telegram وكلمة مرور SMTP وتحويل الخادم إلى `127.0.0.1:1`، **مع** wrapper الـ proxy و`MAIL_MAILER=log` (SMTP لا يمر عبر الـ proxy).
- القالب الموحد فيه routes GET تكتب في القاعدة غير routes الـ artisan: `copy_structure` و`transfer_student_term` و`seed`، و`update_questions_standards/{year}` (له parameter، فلا تراه قائمة الصفحات بلا parameters). يُبحث عنها في كل `routes/*.php` بقراءة الـ closures والـ methods، لا بالأسماء فقط.
- الطالب يدخل باسم المستخدم وحده (`username`)، فأي اختبار دخول للطالب يرسل `username` لا `email`، و`student-cards` العامة أخطر مما تبدو.

### Laravel 13 والحزم
- `HandleCors` لم يكن في Kernel القديم رغم وجود fruitcake: يُزال من الـ middleware العامة وإلا ظهرت ترويسات CORS جديدة على `/api`.
- مشكلة `password_hash_<guard>` (nonarab) موجودة هنا بشكل مختلف: لا تظهر في الصفحة الثانية لأن `ValidateSessionPassword` في القالب يحفظ رقم صاحب الـ hash (`_id`)، ولا بعد خروج ودخول (الخروج يمسح الجلسة)، وتظهر فقط حين **يدخل المشرف كنفس الحساب مرتين في نفس الجلسة** (`ImpersonateTwiceSnapshotTest`). افترضتُ أولاً أن الدخول الثاني بعد خروج يكفي لإظهارها، وإلغاء الإصلاح مؤقتاً أثبت العكس: كل ادعاء من هذا النوع يُثبت بإلغاء الإصلاح وإعادة الاختبار، لا بقراءة الكود.
- opcodesio/log-viewer يُحمى من أول commit: ترك الـ route مفتوحاً "مثل القديم" مؤقتاً رفضه فحص الأمان في هذه البيئة، وهو منطقي لأن الحزمة القديمة تُستبدل على أي حال.
- `array_first`/`array_last` موجودة في PHP 8.5 كدوال أصلية، لكن ليس في 8.4: بعد حذف laravel/helpers يجب تحويلها، و`php -l` لا يكشفها (تظهر فقط عند التنفيذ أو بالبحث).
- قوالب البريد غير المنشورة: Laravel 13 يدمج `mail.markdown.paths` من config الإطار (فيه `resources/views/vendor/mail`) حتى لو حذف `config/mail.php` القسم، فنسخ قوالب Laravel 8 إلى ذلك المجلد يكفي.

### التشغيل والتحقق
- **إيقاف `php` على Windows لا يغلق اتصاله بـ MySQL**: أوقفتُ تشغيلاً طويلاً، فبقي الاتصال نائماً بـ transaction مفتوحة تقفل صف المدرسة، وفشل التشغيل التالي بـ `Lock wait timeout`. بعد أي إيقاف: `SELECT * FROM information_schema.innodb_trx` و`KILL <thread>` لاتصال قاعدة المشروع.
- `run.sh` يُشغَّل من نسخة مجمّدة للأدوات (في scratchpad) حتى يمكن تعديل الأدوات أثناء التشغيل الطويل، ويعيد `resources/lang` ويحذف الاختبارات المنسوخة في النهاية؛ لكن إن أُوقف بالقوة لا يعيدها، فتُعاد يدوياً.
- فلاتر التصدير تُقرأ من `scopeSearch`/`scopeFilter` لا تُخمَّن: `guard` بدل `login_guard` جعل تصدير `login_sessions` يكتب 106 آلاف صف ويتوقف التشغيل عشرين دقيقة. وتُفلتر تصديرات الطلاب على صف واحد من مدرسة واحدة.
- توكن تقرير QR (`encryptStudentId`) مشفّر بـ IV عشوائي في كل تشغيل، فيُستبدل في أسماء الملفات والروابط قبل المقارنة.
- تأكد أن كل حالة في أدوات المقارنة تعمل فعلاً على النسخة القديمة قبل المقارنة: طلب التصحيح كان يُرفض بـ "No students found" (يحتاج محاولات غير مصححة)، وشاشة التصحيح ترجع 500 بدون `subject_id`. كلاهما كان سيظهر "مطابقاً" دون أن يفحص شيئاً.

### التعديلات الآلية
- كما في المشاريع السابقة: أداة Bash تفسد `\\` في heredoc (أفسد ملف JSON هنا فرفضته أداة الاستبدال بـ "bad spec" قبل أي كتابة). كل spec فيه backslash يُكتب بأداة الكتابة، وأداة الاستبدال تتحقق من العدد قبل الكتابة وتحفظ نهايات الأسطر.
- في سكربت PHP: `"    {$eol}"` يُقرأ كـ interpolation فيأكل القوس `{`. والـ placeholder بلا سطر جديد يجعل السطر التالي لا يطابق `^` فلا يُحذف. أُعيدت الملفات من git، والسكربت صار يتحقق أن الخصائص القديمة اختفت فعلاً.


