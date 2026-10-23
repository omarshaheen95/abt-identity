# ترقية socialuae إلى Laravel 13 و PHP 8.4

بتاريخ 22-10-2026 · الفرع `upgrade/laravel-13` · نقطة البداية: الـ tag `before-l13` (الـ commit `a74dcef4`، مطابق لـ `origin/master`)

نُفِّذت الترقية بحسب "خطة تحديث مشاريع ABT": هيكل Laravel 13 جديد داخل نفس الـ repo، مع إبقاء كود المشروع كما هو، ثم مقارنة سلوك النسخة الجديدة بالقديمة على نفس قاعدة البيانات (`ssbt`). ترقية islamic موثقة في `islamic-reference.md`، وكانت نقطة البداية هنا، لكن كل حل منها فُحص من جديد لأن socialuae كان على Laravel 7 لا 6.

| | قبل | بعد |
| --- | --- | --- |
| Laravel | 7.30.6 | 13.38.0 |
| PHP | 7.3 (composer) / 7.4 محلياً | 8.4 |
| PHPUnit | 8.5 | 12.5 |
| `composer audit` | 73 ثغرة معروفة في 16 حزمة | لا شيء |

| الـ commit | ما فيه |
| --- | --- |
| `a009c541` | الـ baselines قبل أي تعديل: `routes-before.json` و`composer-audit-before.txt` و`composer-show-before.txt` (لا يوجد `composer.lock` في git) و`tests/smoke-baseline.json` |
| `98a5a953` | أدوات المقارنة بعد تعديلها لهذا المشروع (`harness/`) |
| `11b5756d` | الترقية نفسها |
| `9315527b` | تحويل الـ routes النصية إلى `[Controller::class, 'method']` |
| `9b31b28d` | إيقاف حفظ hash كلمة المرور في activity log |

## ما الذي تغيّر

### الهيكل
- `bootstrap/app.php` يحل محل `app/Http/Kernel.php` و`app/Console/Kernel.php` و`RouteServiceProvider`. مجموعات الـ routes بنفس الترتيب (api، web، inspection، adek، khda، student، school، manager) ونفس الـ prefix والـ middleware، والـ aliases بنفس الأسماء (`manager` و`school.guest` و`force.password` و`session.password` ...).
- `bootstrap/providers.php` فيه `AppServiceProvider` و`SettingServiceProvider` فقط. `UserEventSubscriber` صار يُسجَّل في `AppServiceProvider`، واكتشاف الـ listeners التلقائي مطفأ حتى لا تُسجَّل أحداث الدخول مرتين.
- الـ Exception Handler القديم باقٍ (بتوقيع `Throwable`) ومربوط بعد `->create()` في `bootstrap/app.php`.
- حُذفت نسخ التطبيق من middleware الإطار (`EncryptCookies` و`VerifyCsrfToken` و`TrimStrings` ...)، لأنها لم تكن معدّلة. التعديل الوحيد كان `old_password` في `TrimStrings`، وصار في `bootstrap/app.php`.
- الـ scheduler (4 مهام) انتقل إلى `withSchedule()` كما هو. `schedule:list` يعرض نفس المهام.
- `database/seeds` صار `database/seeders` بالـ namespace الجديد، و`UserFactory` بصيغة الـ class.
- كل الـ routes بصيغة `[Controller::class, 'method']` (197 action، حُوّلت بالـ tokenizer)، ولم يعد هناك namespace للـ controllers. `routes-dump.php` بعد التحويل مطابق لما قبله حرفياً.

### الحزم
| الحزمة | القرار |
| --- | --- |
| spatie/laravel-permission 5.10 | 8.3 (نفس islamic وABT-Platforms). الـ middleware انتقل إلى `Spatie\Permission\Middleware`. migration الجداول تقرأ أسماء الأعمدة من الـ config بدل `PermissionRegistrar` |
| spatie/laravel-activitylog 3.17 | 4.12. كل model (13) صار فيه `getActivitylogOptions()` بنفس الحقول والخيارات تماماً، و migration جديدة تضيف عمودي `event` و`batch_uuid` |
| yajra/laravel-datatables-oracle 9 | 13.3، مع config جديد |
| iatstuti/laravel-cascade-soft-deletes 3 | حُذفت وحلّت محلها dyrynda 4.6 (نفس الـ trait، namespace `Dyrynda\...`) |
| arcanedev/log-viewer 7 | حُذفت (لا نسخة لـ 13) وحلّت محلها opcodesio/log-viewer، خلف `manager` و`auth:manager` كما كانت. حُذفت الـ views المنشورة للقديمة |
| mobiledetect/mobiledetectlib | كانت `*` في composer.json **وغير مثبتة في vendor المحلي** (صفحات الاختبار كانت ترجع 500 محلياً). صارت `^4.8`، و`DesktopOnlyMiddleware` يضبط الـ user agent بطريقة النسخة 4 (الـ constructor تغيّر). نفس النتيجة لـ 13 user agent (`harness/mobile-detect-check.php`) |
| hesto/multi-auth | حُذفت، والـ trait الوحيد المستخدم نُقل إلى `app/Traits/LogsoutGuard.php` |
| laravel/helpers | حُذفت بعد تحويل `array_first`/`array_last` (22 استدعاء) إلى `Arr::first`/`Arr::last`. `str_contains` صارت دالة PHP نفسها |
| fideloper/proxy, fruitcake/laravel-cors, facade/ignition, fzaninotto/faker | حُذفت (أصبحت جزءاً من Laravel أو استُبدلت بـ fakerphp/faker). إعدادات `config/cors.php` كما هي |
| laravel/ui 2 | 4.6، لأن `AuthenticatesUsers` وأخواتها منها |
| guzzlehttp/guzzle 6 | 7.16 |
| barryvdh/laravel-debugbar 3.7 | 4.4، ونُقلت إلى `require-dev` (لا يستخدمها الكود) |
| maatwebsite/excel, jsvalidation, simple-qrcode | أحدث نسخة من نفس الإصدار الرئيسي. ملف jsvalidation.js المنشور بقي كما هو: كود الحزمة في PHP لم يتغير بين 4.7.6 و4.10.3 |

`composer.lock` صار محفوظاً في git (كان في `.gitignore`)، حتى ينزل على السيرفر نفس ما اختبرناه بالضبط.

### سلوك أبقيناه كما كان في Laravel 7 عن قصد
هذه أشياء غيّرها Laravel بين 7 و13، ولو تُركت لتغيّر ما يراه المستخدم:

- **لا يخرج المستخدمون عند النشر**: Laravel 13 يجعل `session.serialization` بقيمة `json`، وملفات الجلسات التي كتبها Laravel 7 مكتوبة بـ `serialize()`. جرّبناها فعلياً (`harness/session-check.php`): بـ `json` يضيع تسجيل الدخول وتوكن CSRF، وبـ `php` تُقرأ كاملة. أبقيناها `php`. اسم الكوكي `laravel_session` كما هو، والكوكي المشفر والقيم المشفرة (QR التقارير) تُفك بين النسختين في الاتجاهين، فالرجوع أيضاً لا يُخرج أحداً (`harness/cookie-check.php`).
- **الكاش**: Laravel 13 يجعل `cache.serializable_classes` بقيمة `false`، فيرجع أي object من الكاش كـ `__PHP_Incomplete_Class`. هنا يُخزَّن في الكاش `stdClass` (عدّادات `all_student_terms`) وmodel `School` (تقرير المقارنة) وبيانات التقارير. أبقيناها `true` كما كان.
- **صفحات الأخطاء ورسائل البريد**: لا يوجد في المشروع views للأخطاء، فكانت صفحات 404 و419 و500 وغيرها من الإطار نفسه، وقد تغيّر شكلها في Laravel 13، وكذلك قالب رسائل البريد (الألوان والشعار). نسخنا views نسخة Laravel 7 إلى `resources/views/errors` و`resources/views/vendor/mail` و`vendor/notifications`، فصارت صفحة 404 مطابقة، ورسالة استعادة كلمة المرور مطابقة حرفياً (ما عدا مسافتين داخل سطر CSS).
- **Carbon 3**: دوال `diffIn*` صارت ترجع كسراً وبإشارة. كل الاستدعاءات (14) صارت `(int) ...diffInX(..., true)`، والاستدعاء الوحيد الذي كان بـ `false` (عمر كلمة المرور) بقي بإشارته مع `(int)`.
- **ملفات `.env` الحالية تعمل كما هي**: الإعدادات تقرأ الأسماء الجديدة ثم القديمة (`CACHE_DRIVER` و`MAIL_DRIVER` و`FILESYSTEM_DRIVER` و`DATABASE_URL`)، والقيم الافتراضية هي قيم Laravel 7 (file، sync، smtp مع tls، `SESSION_LIFETIME` 120، `same_site` lax، سجل `single`).
- **مسار الـ local disk** بقي `storage/app` (Laravel 13 يجعله `storage/app/private`)، وprefix الكاش `laravel_cache`.
- **لا إعادة hash لكلمة المرور عند الدخول** (كانت ستُفهم في `ValidateSessionPassword` كتغيير كلمة مرور فتُخرج الجلسات الأخرى)، وbcrypt بقي على 10 rounds.
- الـ pagination على Bootstrap 4، وجدول `failed_jobs` بدون uuid.

ما لم يحتج تعديلاً هنا، بخلاف islamic: **التواريخ في JSON** (Laravel 7 يرسلها أصلاً بصيغة ISO، والـ datatables مطابقة بدون أي trait)، و**`getOriginal()`** (Laravel 7 كان يطبق الـ casts أصلاً)، و**الأسماء المكررة للـ routes** (لا يوجد غير أسماء الـ group الفارغة مثل `manager.`، و`route:cache` يعمل).

### PHP 8.4
- إعلان الخصائص التي كانت تُنشأ ديناميكياً (30 خاصية في 8 classes، منها `$statusCode` في الـ Handler)، وجعل الـ nullable صريحاً، وترتيب معاملات `schoolSections()`.
- تحميل كل الـ 300 class في `app/` على PHP 8.4 بدون أي تحذير (`harness/load-all.php`). الاستثناء الوحيد `App\Adek` (مشكلة قديمة، انظر أدناه).

### activity log
- حُذف `password` من الحقول المسجّلة لـ `Manager` و`School` و`Inspection`، فلم يعد تغيير كلمة المرور يحفظ الـ hash القديم والجديد في `activity_log` (كما في islamic). باقي السجل لم يتغير: المقارنة تُظهر فقط اختفاء سطور `password`. السجلات القديمة ما زالت تحوي الـ hash (محلياً 53 للمدارس و7 للمشرفين).

## كيف تأكدنا أن السلوك لم يتغير
كل المقارنات شُغّلت على نفس قاعدة البيانات المحلية، مرة على نسخة Laravel 7 (git worktree من `before-l13` في `../socialuae-l7-ref`) ومرة على Laravel 13، وكل كتابة داخل transaction تُلغى في النهاية. الأدوات في `docs/upgrade/harness`. قبل المقارنة شُغّلت الأدوات مرتين على Laravel 7 نفسه لمعرفة ما يتغير بين تشغيلين بدون أي ترقية.

| الفحص | النتيجة |
| --- | --- |
| قائمة الـ routes (`routes-before.json` / `routes-after.json`) | 360 route للتطبيق: نفس الرابط والاسم والـ action والـ middleware وترتيبه (بعد مطابقة أسماء الـ middleware التي صارت من الإطار) |
| الإعدادات الفعلية بعد التشغيل (`config-before.json` / `config-after.json`) | نفس القيم، ما عدا مفاتيح جديدة في Laravel 13 ومفاتيح حزم حُذفت |
| 171 صفحة للـ guards الأربعة والزائر، كل منها كصفحة وكطلب datatables: كل صفحات GET بدون parameters، ومنها 66 صفحة بـ parameters (تعديل، تقارير الطالب، الشهادات، كل widgets الـ dashboards، صفحات الاختبار) | مطابق، ما عدا ترتيب "Internal Ranking" لطالب واحد (انظر أدناه) |
| الدخول والخروج والقفل بعد المحاولات الخاطئة وفرض تغيير كلمة المرور لكل guard، وسجل `login_sessions` | مطابق |
| 14 تصدير Excel | مطابق خلية بخلية |
| استيراد ملف طلاب (صفوف صحيحة وخاطئة) | نفس الطلاب ونفس الأخطاء |
| تقديم اختبار (صح وخطأ، اختيار، توصيل) ثم التصحيح الآلي ومحاولة التقديم الثانية | نفس الإجابات ونفس الدرجات (المجموع، المراحل الأربع، 56 standard) |
| إضافة وتعديل المدارس والطلاب (من المشرف ومن المدرسة) والمستويات والسنوات والمشرفين والمفتشين، والنماذج الفارغة والخاطئة | نفس البيانات المحفوظة ونفس رسائل التحقق ونفس سجل activity log |
| رسائل استعادة كلمة المرور للـ guards الثلاثة | نفس الرسالة والرابط وصفحة إعادة التعيين، والقالب مطابق |
| من يفتح log viewer | المشرف فقط، كما كان |
| الجلسات والكوكيز المشفرة بين النسختين | تُقرأ في الاتجاهين |
| الأوامر المجدولة (`--dry-run`) | نفس النتيجة |
| `tests/Feature/SmokeRoutesTest.php` | ينجح على النسختين (نفس الصفحات الثلاث التي ترجع 500 أصلاً) |

فروق ظهرت في المقارنة وليست من الترقية:
- **Internal Ranking** في تقرير الطالب: الترتيب بـ `orderByDesc('total')` فقط، وعند تساوي المجموع لا يضمن MySQL أي ترتيب. للطالب الذي فُحص ستة طلاب بنفس المجموع (90)، فيظهر ترتيبه 4 أو 8، وهذا يحدث بين تشغيلين على Laravel 7 نفسه.
- widget "متأخرات التصحيح" يحسب الأعمار نسبة إلى الوقت الحالي، فيتغير بين تشغيلين.
- رسالة الـ `ValidationException` الداخلية صارت أول خطأ بدل "The given data was invalid". لا تظهر للمستخدم (الـ Handler يعرض رسالته الخاصة)، وتظهر فقط في سطر debug في السجل.

## النشر

1. PHP **8.4.27 أو أحدث** (8.4.7 الموجودة محلياً مصابة بثغرة php2shell). الـ extensions: `pdo_mysql` و`mbstring` و`gd` و`zip` و`intl` و`bcmath` و`fileinfo` و`openssl`.
2. ملف `.env` الحالي يعمل كما هو.
3. في مجلد جديد بجانب القديم:
   ```
   composer install --no-dev --optimize-autoloader
   php artisan migrate --force
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```
   الـ migration الوحيدة الجديدة تضيف عمودين nullable إلى `activity_log` (بدون `after()`)، و`down()` فيها يحذفهما. جُرّبت `up` ثم `down` ثم `up` محلياً. `migrate --pretend` لم يُظهر غيرها.
4. ملفات jsvalidation المنشورة في `public/vendor` محفوظة في git كما كانت، ولا تحتاج نشراً.
5. الـ cron الحالي (`schedule:run`) يعمل كما هو.
6. الرجوع: الـ symlink إلى المجلد القديم، ثم `php artisan migrate:rollback --step=1` إن لزم. الجلسات تبقى في الاتجاهين.

## مشاكل كانت موجودة قبل الترقية (لم نلمسها)
- `manager/proctor-images` يرجع 500: `ProctorImage` يشير إلى `App\StudentTerm` غير الموجود (الصحيح `App\Models\StudentTerm`).
- **`GET /migrate` و`GET /cache` بدون أي حماية** (`WebsiteController`): الأول يشغّل `migrate` (يرفض في production بدون `--force`)، والثاني يمسح config وview cache لأي زائر. وكذلك `manager/seed` و`manager/set_permissions` و`manager/trim_student` و`manager/update_passwords` و`manager/copyStandards` وأمثالها تغيّر البيانات بطلب GET (خلف دخول المشرف).
- `App\Adek` في `app/Models/Adek.php` بالـ namespace `App`، فلا يُحمَّل، وprovider الـ `adeks` يشير إليه. routes الـ adek والـ khda فارغة، فلا يظهر أثر.
- 22 route تشير إلى methods غير موجودة، فترجع 500 إذا فُتحت. أغلبها من `Route::resource`، و**`GET student/lang/{local}`** (تغيير لغة الطالب) منها. القائمة نفسها على Laravel 7، وتُستخرج بمرور على ناتج `routes-dump.php` مع `method_exists`:

  | الـ route | الـ action |
  | --- | --- |
  | `GET student/lang/{local}` | `Student\HomeController@lang` |
  | `GET manager/inspection/{inspection}` | `Manager\InspectionController@show` |
  | `GET manager/level/{level}` | `Manager\LevelController@show` |
  | `GET manager/manager/{manager}` | `Manager\ManagerController@show` |
  | `GET manager/school/{school}` | `Manager\SchoolController@show` |
  | `GET manager/student/{student}` | `Manager\StudentController@show` |
  | `GET manager/year/{year}` | `Manager\YearController@show` |
  | `GET manager/login_sessions/create` | `Manager\LoginSessionController@create` |
  | `POST manager/login_sessions` | `Manager\LoginSessionController@store` |
  | `GET manager/login_sessions/{login_session}` | `Manager\LoginSessionController@show` |
  | `GET manager/login_sessions/{login_session}/edit` | `Manager\LoginSessionController@edit` |
  | `PUT/PATCH manager/login_sessions/{login_session}` | `Manager\LoginSessionController@update` |
  | `DELETE manager/login_sessions/{login_session}` | `Manager\LoginSessionController@destroy` |
  | `GET manager/students-files/{students_file}/edit` | `Manager\StudentImportController@edit` |
  | `PUT/PATCH manager/students-files/{students_file}` | `Manager\StudentImportController@update` |
  | `GET school/marking_request/{marking_request}` | `School\MarkingRequestController@show` |
  | `GET school/student/{student}` | `School\StudentController@show` |
  | `GET school/scheduling/create` | `School\SchedulingController@create` |
  | `POST school/scheduling` | `School\SchedulingController@store` |
  | `GET school/scheduling/{scheduling}` | `School\SchedulingController@show` |
  | `GET school/scheduling/{scheduling}/edit` | `School\SchedulingController@edit` |
  | `DELETE school/scheduling/{scheduling}` | `School\SchedulingController@destroy` |

  الإصلاح المقترح: `->only([...])` أو `->except([...])` على كل `Route::resource`، بعد التأكد أن أسماءها غير مستخدمة في الـ views. لم نفعله هنا حتى تبقى قائمة الـ routes مطابقة لما قبل الترقية.
- ترتيب "Internal Ranking" غير ثابت عند تساوي المجموع (انظر أعلاه).
- `ActivityLogController@show` يستخدم `\GuzzleHttp\json_decode`، وستُحذف في Guzzle 8.
- الـ Exception Handler يرسل كل خطأ إلى Telegram عبر `LogMessageJob`، والـ queue هو `sync`، فأي تشغيل للاختبارات على نسخة فيها إعدادات Telegram يرسل رسائل حقيقية إلى القناة. كل اختبارات `harness/` و`SmokeRoutesTest` صارت تعطّل هذا الـ job. أول تشغيلات للاختبارات في هذه الترقية (قبل التعطيل) أرسلت على الأرجح نحو 30 رسالة خطأ إلى القناة يوم 22-10-2026 بين 18:48 و19:06.

## متابعات مقترحة
- التأكد من نسخة MySQL على السيرفر، فقد صُمّم Laravel 13 لـ MySQL 8 (المحلي 8.0.30).
- إصلاح routes الـ GET التي تغيّر البيانات أو تشغّل أوامر (`/migrate` و`/cache` خصوصاً).
- الانتقال من Laravel Mix إلى Vite في خطوة منفصلة.
- **في islamic**: إعداداته الحالية `session.serialization = json` و`cache.serializable_classes = false`. الأولى ستُخرج كل المستخدمين عند نشره (جُرّب هنا)، والثانية تُفسد أي object في الكاش. يستحق الفحص قبل نشره.

## دروس للمشروع التالي
تضاف إلى ما في `islamic-reference.md`.

### قبل البدء
- `vendor` المحلي قد لا يطابق `composer.json`: هنا كانت mobiledetect مطلوبة وغير مثبتة، ولا يوجد `composer.lock`. حفظ `composer show` كـ baseline، ولنسخة المرجع يُنسخ `vendor` نفسه، ولإضافة حزمة واحدة إليه بدون تحديث الباقي يُبنى `composer.lock` مؤقت من `vendor/composer/installed.json`.
- `php` على الـ PATH قد يتغير أثناء العمل (تغيّر هنا من 7.4 إلى 8.4). كل أمر يُستدعى بمسار PHP الكامل.
- أدوات الاختبار تكتب في قاعدة البيانات وقد تتصل بالخارج: البحث عن أي job أو handler يرسل (Telegram، بريد، webhooks) وتعطيله **قبل** أول تشغيل.

### التعديلات الآلية
- أداة Bash في هذه البيئة تحوّل `\\` إلى `\`، فتفسد السكربتات المكتوبة inline (php -r، heredoc، python). كل سكربت فيه backslash يُكتب في ملف بأداة الكتابة.
- `\s*` في بداية regex يبتلع الأسطر الفارغة قبله؛ للمسافة البادئة `[ \t]*`.
- regex يبحث عن `use ... LogsActivity;` يطابق سطر الـ import قبل سطر الـ trait داخل الـ class.
- تعديل سكربت bash أثناء تشغيله يُفسد التشغيل (bash يقرأ الملف تدريجياً).
- `diff <(a) <(b)` يشغّل الأمرين معاً، فيتعارضان على الأقفال.

### Laravel 13
- القيم الافتراضية الجديدة التي تكسر بيانات موجودة لا تظهر كأخطاء: `session.serialization` و`cache.serializable_classes`. مقارنة `config()` كاملاً بعد التشغيل (`harness/config-dump.php`) تكشفها، ثم تُجرَّب على بيانات فعلية.
- صفحات الأخطاء وقوالب البريد من الإطار تتغير شكلاً بين النسخ، فتُنسخ views النسخة القديمة إذا لم يكن للمشروع views خاصة.
- حزم تغيّر الـ constructor بدون أن يظهر خطأ عند التحميل (mobiledetect 4) تُختبر بنفس المدخلات على النسختين.

### التحقق
- تشغيل أدوات المقارنة مرتين على النسخة القديمة أولاً يُظهر ما يتغير بدون ترقية (ترتيب غير ثابت، أوقات، ids، bcrypt salt)، فلا يُخلط بفروق الترقية.
- أسماء ملفات الـ snapshots يجب أن تبقى فريدة: استبدال الأرقام بـ `N` جعل شهادتين تكتبان نفس الملف.
- `Bus::fake([LogMessageJob::class])` في كل اختبار يكتب أو يرجع 500.

## قائمة الاختبار اليدوي
بحسب الخطة: الدخول والخروج لكل guard، الصلاحيات، الجداول (بحث وترتيب وتقسيم صفحات)، استيراد وتصدير Excel، التقارير والشهادات وQR، ملفات PDF المضغوطة (`pdfservice`)، الاختبار من جهاز مكتبي ومن هاتف ولوح لمدرسة فيها `desktop_only`، صور المراقبة (selfie وscreenshot)، الترجمة واتجاه الصفحة، الـ scheduler (`php artisan schedule:list`)، رسالة استعادة كلمة مرور حقيقية عبر SMTP، ومراقبة `storage/logs` يوماً كاملاً على نسخة بيانات الإنتاج.

## الملفات في هذا المجلد
| الملف | ما هو |
| --- | --- |
| `routes-before.json` / `routes-after.json` | ناتج `harness/routes-dump.php` قبل وبعد |
| `config-before.json` / `config-after.json` | ناتج `harness/config-dump.php` (كلمات المرور والمفاتيح مخفية) |
| `composer-audit-before.txt` / `composer-audit-after.txt` | ناتج `composer audit` |
| `composer-show-before.txt` / `composer-show-after.txt` | نسخ الحزم المثبتة |
| `islamic-reference.md` | توثيق ترقية islamic كاملاً، والأرقام فيه تخص islamic |
| `harness/` | أدوات المقارنة (انظر `harness/README.md`) |
