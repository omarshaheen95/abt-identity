# ترقية المشروع إلى Laravel 13 و PHP 8.4

بتاريخ 22-10-2026 · الفرع `upgrade/laravel-13` · نقطة البداية: الـ tag `before-l13`

نُفِّذت الترقية بحسب "خطة تحديث مشاريع ABT": هيكل Laravel 13 جديد داخل نفس الـ repo، مع إبقاء كود المشروع كما هو، ثم مقارنة سلوك النسخة الجديدة بالقديمة على نفس قاعدة البيانات.

| | قبل | بعد |
| --- | --- | --- |
| Laravel | 6.20.45 | 13.38.0 |
| PHP | 7.2 (composer) / 7.4 محلياً | 8.4 |
| PHPUnit | 8 | 12 |
| `composer audit` | 43 ثغرة معروفة | لا شيء |

## ما الذي تغيّر

### الهيكل
- `bootstrap/app.php` يحل محل `app/Http/Kernel.php` و`app/Console/Kernel.php` و`RouteServiceProvider`. الـ middleware بنفس الأسماء (`admin` و`school.guest` و`force.password` ...) ونفس الترتيب، ومجموعات الـ routes بنفس الـ prefix والـ middleware.
- `bootstrap/providers.php` فيه `AppServiceProvider` و`SettingServiceProvider` فقط. الـ `UserEventSubscriber` صار يُسجَّل في `AppServiceProvider`، واكتشاف الـ listeners التلقائي مطفأ حتى لا تُسجَّل أحداث الدخول مرتين.
- الـ Exception Handler القديم باقٍ كما هو (بتوقيع `Throwable`) ومربوط في `bootstrap/app.php`.
- `database/seeds` صار `database/seeders` بالـ namespace الجديد، و`UserFactory` بصيغة الـ class.
- كل الـ routes بصيغة `[Controller::class, 'method']`، ولم يعد هناك namespace للـ controllers في `bootstrap/app.php` ولا في مجموعات الـ routes. حُوّلت آلياً (200 سطر)، و`routes-dump.php` بعد التحويل مطابق لما قبله حرفياً.

### الحزم
| الحزمة | القرار |
| --- | --- |
| spatie/laravel-permission 5 | 8.3 (نفس نسخة ABT-Platforms). الـ middleware انتقل إلى `Spatie\Permission\Middleware` |
| spatie/laravel-activitylog 3 | 4.12. كل model صار فيه `getActivitylogOptions()` بنفس الحقول تماماً، و migration جديدة تضيف عمودي `event` و`batch_uuid` |
| spatie/laravel-translatable 4 | 6.15 |
| yajra/laravel-datatables-oracle 9 | 13.3، مع config جديد |
| dyrynda/laravel-cascade-soft-deletes 3 | 4.6 (الـ namespace صار `Dyrynda\...`) |
| arcanedev/log-viewer | حُذفت (لا نسخة مستقرة لـ 13) وحلّت محلها opcodesio/log-viewer. **كانت بدون أي حماية** (حتى حذف الملفات)، والآن خلف `admin` و`auth:admin` |
| hesto/multi-auth | حُذفت، والـ trait الوحيد المستخدم نُقل إلى `app/Traits/LogsoutGuard.php` |
| laravel/helpers | حُذفت بعد تحويل `array_first`/`array_last` إلى `Arr::first`/`Arr::last` (PHP 8.5 فيها دوال بنفس الاسم) |
| fideloper/proxy, facade/ignition, fzaninotto/faker | حُذفت (أصبحت جزءاً من Laravel أو استُبدلت بـ fakerphp/faker) |
| laravel/ui 4 | أُضيفت، لأن `AuthenticatesUsers` وأخواتها خرجت من الإطار منذ Laravel 7 |
| maatwebsite/excel, jsvalidation, simple-qrcode, guzzle | إلى أحدث نسخة من نفس الإصدار الرئيسي. أُعيد نشر ملف jsvalidation.js |

`composer.lock` صار محفوظاً في git (كان في `.gitignore`)، حتى ينزل على السيرفر نفس ما اختبرناه بالضبط.

### سلوك أبقيناه كما كان في Laravel 6 عن قصد
هذه أشياء غيّرها Laravel بين 7 و13، ولو تُركت لتغيّر ما يراه المستخدم:

- **التواريخ في JSON** (كل الـ datatables): Laravel 7+ يرسلها بصيغة ISO وبتوقيت UTC، أي أن كل تاريخ كان سيظهر متأخراً 4 ساعات. الـ trait `SerializesDatesAsStored` في كل الـ models يعيد الصيغة القديمة `Y-m-d H:i:s` بتوقيت دبي.
- **`getOriginal()`** صار يمر على الـ accessors. الاستدعاءات التي كانت تريد القيمة الخام (أيقونة الاختبار، صورة المدرسة، مرفق الفقرة...) صارت `getRawOriginal()`. بدونها كانت الجداول تعرض صورة فارغة بدل "-".
- **Carbon 3**: دوال `diffIn*` صارت ترجع كسراً وبإشارة. كل الاستدعاءات (13) صارت `(int) ...diffInX(..., true)` لتبقى عدداً صحيحاً موجباً كما كانت.
- **أسماء الـ routes المكررة**: Laravel 13 يأخذ أول route بالاسم، وLaravel 6 كان يأخذ آخرها، و`route:cache` يرفض التكرار أصلاً. أعطينا الـ routes المكررة أسماء مميزة، فبقي `route('admin.year.destroy')` وأمثاله يشير إلى نفس الرابط القديم، ولم يُحذف أي رابط.
- **ملفات `.env` الحالية تعمل كما هي**: الإعدادات تقرأ الأسماء الجديدة ثم القديمة (`CACHE_DRIVER` و`MAIL_DRIVER` و`FILESYSTEM_DRIVER`)، والقيم الافتراضية هي قيم Laravel 6 (file/sync/smtp).
- **لا يخرج المستخدمون عند النشر**: Laravel 13 يجعل `session.serialization` بقيمة `json`، وملفات الجلسات التي كتبها Laravel 6 مكتوبة بـ `serialize()`. جُرّبت بملف جلسة كتبه Laravel 6 (`harness/session-check.php`): بـ `json` يضيع تسجيل الدخول وتوكن CSRF، وبـ `php` تُقرأ كاملة، فصارت `php`. والجلسة التي يكتبها 13 يقرؤها 6 أيضاً، فالرجوع لا يُخرج أحداً. اسم الكوكي `laravel_session` كما هو، والكوكي المشفر والقيم المشفرة (مثل QR التقارير المطبوعة) تُفك بين النسختين في الاتجاهين (`harness/cookie-check.php`).
  - هذا الإعداد أُصلح بعد الـ commit الأول للترقية، بعد أن ظهر في ترقية socialuae. أدوات المقارنة لم تكشفه لأن كل اختبار يبدأ بجلسة جديدة.
- **الكاش**: Laravel 13 يجعل `cache.serializable_classes` بقيمة `false`، فيرجع أي object من الكاش كـ `__PHP_Incomplete_Class`. يُخزَّن هنا model `Setting` وCollection منه (مفتاح `settings`) و`stdClass` (عدّادات صفحة كل الاختبارات). بـ `false` كانت الزيارة الثانية لصفحة `admin/all_student_terms` (حين تأتي العدّادات من الكاش) ترجع 500. صارت `true` كما كان، ويُقرأ أيضاً ما كتبه Laravel 6 في الكاش (`harness/cache-check.php`). أُصلح مع الجلسة، ولم تكشفه الاختبارات لأنها تستخدم الكاش `array` الذي لا يعمل serialize.
- **prefix الكاش** بقي `laravel_cache` (Laravel 13 يجعله `laravel-cache-`). لا أثر له مع الكاش `file`، لكنه يحفظ المفاتيح الموجودة لو كان السيرفر على redis أو database.
- **مسار الـ local disk** بقي `storage/app` (Laravel 13 يجعله `storage/app/private`).
- **لا CORS على `/api`**، و**لا إعادة hash لكلمة المرور عند الدخول** (كانت ستُفهم كتغيير كلمة مرور فتُخرج الجلسات الأخرى)، وbcrypt بقي على 10 rounds.
- الـ pagination على Bootstrap 4.

### PHP 8
- إعلان الخصائص التي كانت تُنشأ ديناميكياً (9 classes)، وجعل الـ nullable صريحاً، واستبدال `utf8_decode`.
- تحميل كل الـ 303 class في `app/` على PHP 8.4 ينجح بدون أي تحذير.

## كيف تأكدنا أن السلوك لم يتغير
كل المقارنات شُغّلت على نفس قاعدة البيانات المحلية، مرة على نسخة Laravel 6 (git worktree من `before-l13`) ومرة على Laravel 13، وكل كتابة داخل transaction تُلغى في النهاية. الأدوات في `docs/upgrade/harness`.

| الفحص | النتيجة |
| --- | --- |
| قائمة الـ routes (`routes-before.json` / `routes-after.json`) | 377 route: نفس الرابط والـ action والـ middleware وترتيبه. 16 منها أخذت اسماً مميزاً فقط (التكرارات) |
| 104 صفحة بدون parameters، كصفحة وكطلب datatables، لكل guard | المحتوى مطابق بعد استبعاد ما يتغير في كل طلب (CSRF، أرقام عشوائية، أوقات) |
| 64 صفحة بـ parameters: تعديل، تقارير الطالب، الشهادات، كل widgets الـ dashboards، صفحات الاختبار | مطابق |
| الدخول والخروج والقفل بعد المحاولات الخاطئة لكل guard، وسجل `login_sessions` | مطابق |
| 12 تصدير Excel | مطابق خلية بخلية |
| استيراد ملف طلاب (صفوف صحيحة وخاطئة) | نفس الطلاب ونفس الأخطاء |
| تقديم اختبار بمراحله الثلاث ثم التصحيح الآلي | نفس الإجابات ونفس الدرجات (المجموع، المراحل الست، 90 standard) |
| إضافة وتعديل المدارس والطلاب والمستويات والسنوات والمشرفين والمفتشين، والنماذج الفارغة | نفس البيانات المحفوظة ونفس رسائل التحقق ونفس سجل activity log |
| رسائل استعادة كلمة المرور للـ guards الثلاثة | تُرسل وفيها الرابط |
| جلسة وكاش وكوكي تكتبها نسخة وتقرؤها الأخرى (`session-check` و`cache-check` و`cookie-check`) | تُقرأ كاملة في الاتجاهين، بعد إصلاح `session.serialization` و`cache.serializable_classes` |
| `tests/Feature/SmokeRoutesTest.php` (موجود في المشروع) | ينجح على النسختين |

## النشر

1. PHP **8.4.27 أو أحدث** (8.4.7 الموجودة محلياً مصابة بثغرة php2shell). الـ extensions: `pdo_mysql` و`mbstring` و`gd` و`zip` و`intl` و`bcmath` و`fileinfo` و`openssl`.
2. ملف `.env` الحالي يعمل كما هو.
   - البريد يُرسل بإعدادات `smtp_mail_*` من جدول `settings` لا من `.env` (انظر "مصدر إعدادات البريد" في المتابعات)، فتُراجع قيمها على السيرفر قبل النشر.
   - إن كان الموقع خلف proxy أو Cloudflare، يُضاف إلى `TrustProxies` قبل النشر، وإلا يُرفض `api/import_students` و`api/update_settings` من السيرفرات الموثوقة (انظر إصلاح فحص الـ IP).
3. في مجلد جديد بجانب القديم:
   ```
   composer install --no-dev --optimize-autoloader
   php artisan migrate --force
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```
   الـ migration الوحيدة الجديدة تضيف عمودين nullable إلى `activity_log` (بدون `after()` حتى لا يُعاد بناء الجدول على MySQL 5.7)، و`down()` فيها يحذفهما.
4. ملفات jsvalidation وlog-viewer المنشورة في `public/vendor` محفوظة في git، ولا تحتاج نشراً.
5. الرجوع: الـ symlink إلى المجلد القديم، ثم `php artisan migrate:rollback --step=1` إن لزم.

## مشاكل كانت موجودة قبل الترقية (لم نلمسها)
- **`api/import_students` و`api/update_settings` كان يمكن تجاوز حمايتهما. أُصلح**: لا يوجد عليهما تسجيل دخول، والحماية الوحيدة `isTrustIpAddress()`، وكانت تأخذ الـ IP من `getRealIpAddress()` التي تقرأ `X-Forwarded-For` وأخواتها أولاً، وهذه يكتبها المرسل بنفسه. و`127.0.0.1` ضمن `trusted_ips`، فكان يكفي `X-Forwarded-For: 127.0.0.1` لتغيير أي إعداد (منها خادم البريد وكلمة مروره، فيستلم المهاجم روابط استعادة كلمات المرور) أو استيراد طلاب. جُرّب فعلياً عبر HTTP قبل الإصلاح.
  - الآن تقارن `$request->ip()` (عنوان الاتصال نفسه، عبر `TrustProxies`) بـ `config('app.trusted_ips')` مقارنة صارمة، وحُذفت `getRealIpAddress()` (لا استخدام آخر لها). نفس إصلاح readingbenchmark.
  - `tests/Feature/TrustedIpApiTest.php` (منقول من readingbenchmark) يزوّر كل رأس من الخمسة، في الطلب وفي `$_SERVER`: كل الحالات العشر كانت تنجح في التجاوز بالكود القديم، والآن ترجع 401 ولا يُكتب شيء. والسيرفر الموثوق المتصل مباشرة ما زال يحدّث الإعدادات. وعبر HTTP: كل الرؤوس المزوّرة ترجع 401، والطلب من `127.0.0.1` مباشرة يمر.
  - **عند النشر**: إن كان الموقع خلف proxy أو load balancer أو Cloudflare، يجب إضافته إلى `TrustProxies` في `bootstrap/app.php`، وإلا تُرفض طلبات السيرفرات الموثوقة.
- `admin/proctor-images` يرجع 500: `ProctorImage` يشير إلى `App\StudentTerm` غير الموجود (الصحيح `App\Models\StudentTerm`).
- 18 route تشير إلى methods غير موجودة في الـ controller، فترجع 500 إذا فُتحت. 17 منها من `Route::resource` (يُنشئ السبعة كلها والـ controller لا يحتاج إلا بعضها)، والأخيرة `studentCardsQR`. القائمة نفسها في Laravel 6، وتُستخرج بمرور على ناتج `routes-dump.php` مع `method_exists`:

  | الـ route | الـ action |
  | --- | --- |
  | `GET admin/admin/{admin}` | `Admin\AdminController@show` |
  | `GET admin/corrector/{corrector}` | `Admin\CorrectorController@show` |
  | `GET admin/inspection/{inspection}` | `Admin\InspectionController@show` |
  | `GET admin/level/{level}` | `Admin\LevelController@show` |
  | `GET admin/school/{school}` | `Admin\SchoolController@show` |
  | `GET admin/term/{term}` | `Admin\TermController@show` |
  | `GET admin/year/{year}` | `Admin\YearController@show` |
  | `GET admin/login_sessions/create` | `Admin\LoginSessionController@create` |
  | `POST admin/login_sessions` | `Admin\LoginSessionController@store` |
  | `GET admin/login_sessions/{login_session}` | `Admin\LoginSessionController@show` |
  | `GET admin/login_sessions/{login_session}/edit` | `Admin\LoginSessionController@edit` |
  | `PUT/PATCH admin/login_sessions/{login_session}` | `Admin\LoginSessionController@update` |
  | `DELETE admin/login_sessions/{login_session}` | `Admin\LoginSessionController@destroy` |
  | `GET admin/students-files/{students_file}/edit` | `Admin\StudentImportController@edit` |
  | `PUT/PATCH admin/students-files/{students_file}` | `Admin\StudentImportController@update` |
  | `GET school/marking_request/{marking_request}` | `School\MarkingRequestController@show` |
  | `GET school/student/{student}` | `School\StudentController@show` |
  | `GET school/studentCardsQR` | `School\StudentController@studentCardsQR` |

  الإصلاح المقترح: `->only([...])` أو `->except([...])` على كل `Route::resource`، بعد التأكد أن أسماءها غير مستخدمة في الـ views. لم نفعله هنا حتى تبقى قائمة الـ routes مطابقة لما قبل الترقية.
- كود ميت يشير إلى classes غير موجودة: Firebase وFCM وPusher في `helpers.php`، و`Delegate` و`App\Term` في `Inspection\CombinedReportController` و`ComparisonReportController`.
- سجل activity log لـ `School` و`Inspection` كان يحفظ hash كلمة المرور ضمن الحقول المسجّلة. **أُصلح**: حُذف `password` من الحقول المسجّلة كما في `Admin` و`Student`، فتغيير كلمة المرور وحده لم يعد يُنشئ سجلاً. السجلات القديمة ما زالت تحوي الـ hash.
- `student-report` و`student-report-card` بدون token كانت ترجع 500، والآن ترجع صفحة فارغة 200.

## متابعات مقترحة
- التأكد من نسخة MySQL على السيرفر، فقد صُمّم Laravel 13 لـ MySQL 8.
- الانتقال من Laravel Mix إلى Vite في خطوة منفصلة.

### من مراجعة ترقيتي socialuae وreadingbenchmark (لم تُطبَّق هنا بعد)
- **مصدر إعدادات البريد تغيّر، وتقرر إبقاء الجديد**: `SettingServiceProvider` يضع إعدادات SMTP من جدول `settings` في `mail.mailers.smtp.*` منذ ما قبل الترقية. Laravel 6 لا يقرأ هذه المفاتيح (يقرأ `mail.host` وأخواتها)، فكان يرسل فعلياً بإعدادات `.env`. Laravel 13 يقرؤها، فصار يرسل بإعدادات قاعدة البيانات. محلياً: Laravel 6 يرسل إلى `smtp.mailtrap.io` (من `.env`)، و13 إلى `smtp.office365.com` (من `settings`). اكتُشف بمقارنة `config()` كاملاً بين النسختين.
  - **القرار (22-10-2026، من صاحب المشروع)**: يبقى البريد على إعدادات جدول `settings`، وهو ما قصده الكود أصلاً، ولم يُعَد سلوك Laravel 6. هذا التغيير الوحيد في السلوك الذي أُبقي عن قصد.
  - **قبل النشر**: التأكد أن قيم `smtp_mail_*` في صفحة الإعدادات على السيرفر صحيحة (المضيف والمنفذ والتشفير والحساب وعنوان المرسل)، ثم إرسال رسالة استعادة كلمة مرور حقيقية بعد النشر. إعدادات `MAIL_*` في `.env` تُستخدم فقط إذا لم يوجد في الجدول `smtp_mail_driver` أو `smtp_mail_host` (أو كانت قيمته `null`).
  - `api/update_settings` يستطيع تغيير هذه القيم، فحماية فحص الـ IP (أعلاه) صارت أهم.
- **صفحات الأخطاء وقوالب البريد**: لا يوجد في المشروع `resources/views/errors` ولا `vendor/mail`، فصفحات 404 و419 و500 وقالب رسالة استعادة كلمة المرور من الإطار نفسه، وشكلها تغيّر بين 6 و13. في socialuae وreadingbenchmark نُسخت views النسخة القديمة.
- **عند النشر**: `php artisan permission:cache-reset` بعد `migrate` (كاش الصلاحيات كتبته النسخة 5 من الحزمة)، ونسخ `resources/lang` من السيرفر الحالي لأنها ليست في git.
- **`user/{id}/report` و`user/{id}/certificate`** في `routes/web.php` بدون دخول. في readingbenchmark يعرضان تقرير أي طالب برقمه لأي زائر؛ يستحق الفحص هنا.
- مقارنة `config()` كاملاً بين النسختين (`config-dump.php` من readingbenchmark) لم تُظهر فرقاً آخر غير المذكور أعلاه وفي "سلوك أبقيناه".

## دروس للمشروع التالي
ما واجهناه هنا وسيتكرر غالباً في باقي مشاريع الخطة.

### قبل البدء
- `git fetch` ومقارنة الفرع بـ `origin/development` أولاً. هنا كان الفرع يحمل commits سابقة للترقية غير موجودة في `development`، فصارت ضمن نفس الـ push.
- التأكد من رابط الـ repo: `origin` هنا ما زال `isbt` بينما الاسم على GitHub صار `ABT-Islamic` (يعمل عبر إعادة التوجيه).
- tag لنقطة البداية، وتسجيل `routes-dump.php` و`composer audit` و`SmokeRoutesTest` قبل أي تعديل.
- PHP 8.4 بجانب القديمة في Laragon، واستدعاؤها بالمسار الكامل في كل أمر، لأن `php` على الـ PATH تبقى القديمة.

### التعديلات الآلية
- `preg_replace` يرجع `null` عند خطأ في الـ regex أو الترميز، ولو كُتب الناتج مباشرة يُفرَغ الملف. أفرغ هذا 14 ملفاً هنا قبل استرجاعها من git. كل سكربت يجب أن يتحقق من الناتج ولا يكتب إلا إذا تغيّر الملف كما هو متوقع.
- الأفضل كتابة السكربت في ملف بأداة الكتابة، لا تمريره عبر `php -r` أو heredoc في الـ shell (الـ escaping يفسده بصمت).
- الإدراج بحساب المواقع (offsets) يخطئ إذا تغيّر الملف أثناء المرور عليه. أخطأ هنا في `Admin.php`.
- `sed` على اسم حقل مثل `'password'` يصيب كل سطر فيه نفس النص، ومنها `$fillable`. التعديل يكون على سياق فريد، ومراجعة `git diff` قبل كل commit.
- لتحويل الـ routes: `token_get_all` أدق من الـ regex لأنه يتجاهل التعليقات ويتتبع الـ namespace الخاص بكل group.

### Laravel 13
- الـ Exception Handler المخصص يُربط بعد `->create()` في `bootstrap/app.php`. ربطه بـ `withSingletons` يأتي متأخراً فلا يُستخدم.
- اكتشاف الـ listeners التلقائي يسجل الـ subscriber مرتين إذا كان مسجلاً يدوياً: `withEvents(discover: false)`.
- `route:cache` يرفض الأسماء المكررة، فيجب البحث عنها قبل النشر لا بعده.
- `Route::resource` مع route يدوي بنفس الاسم (مثل الحذف الجماعي) هو أشهر مصدر للتكرار.
- ملفات config: البدء من نسخة Laravel 13 ثم إعادة تخصيصات المشروع، لا العكس. الـ config القديم لـ arcanedev كان يشير إلى classes محذوفة.
- الحزم التي تغيّر namespace بدون إعلان واضح (مثل `Iatstuti` ← `Dyrynda`) لا تظهر إلا عند تحميل كل الـ classes. تحميل كل ما في `app/` على PHP 8.4 يكشفها مبكراً.

### التحقق
- المقارنة بين النسختين على نفس قاعدة البيانات كشفت مشاكل التواريخ و`getOriginal()` والأسماء المكررة، وكلها لم تظهر في الاختبارات العادية.
- الـ worktree القديم يحتاج `core.longpaths` على Windows، ونسخ `.env` و`resources/lang` إليه.
- الصفحات البطيئة جداً (مثل تقرير المدرسة الكامل) تُستبعد من المقارنة الآلية وتُختبر يدوياً.
- أدوات المقارنة تحتاج تعديلاً لكل مشروع: الـ guards، ومستخدم أدمن للتصحيح الآلي، وكلمة مرور تحقق قواعد المشروع.
- المقارنة بالطلبات وحدها لا تكشف ما يُكتب في طلب ويُقرأ في نشر لاحق: الجلسات (كل اختبار يبدأ بجلسة جديدة) والكاش (الاختبارات على `array` بدون serialize). لهذا فاتت `session.serialization` و`cache.serializable_classes` هنا، وظهرتا في socialuae. افحصها بملفات تكتبها نسخة وتقرؤها الأخرى، وقارن `config()` كاملاً بين النسختين لا الملفات المعدّلة فقط.

## قائمة الاختبار اليدوي
بحسب الخطة: الدخول والخروج لكل guard، الصلاحيات، الجداول (بحث وترتيب وتقسيم صفحات)، استيراد وتصدير Excel، التقارير والشهادات وQR، رفع الصور والتسجيلات الصوتية في الاختبار، الترجمة واتجاه الصفحة، الـ scheduler (`php artisan schedule:list`)، رسالة استعادة كلمة مرور حقيقية تصل عبر SMTP المضبوط في صفحة الإعدادات، ومراقبة `storage/logs` يوماً كاملاً على نسخة بيانات الإنتاج.
