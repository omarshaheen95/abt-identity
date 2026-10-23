# ترقية abt-identity إلى Laravel 13 و PHP 8.4 (لم تبدأ بعد)

هذا المجلد منقول من مشاريع islamic وsocialuae وreadingbenchmark بعد إنهاء ترقياتها، ليكون نقطة البداية هنا. لم يُعدَّل أي شيء في كود abt-identity حتى الآن.

| الملف | ما هو |
| --- | --- |
| `خطة تحديث مشاريع ABT.md` (في جذر المشروع) | الخطة العامة. هذا المشروع من **الموجة 2**: ست منصات اختبارات على القالب الموحد (english ثم fluency وabt-identity وarabicplus وscience وmath) |
| `readingbenchmark-reference.md` | آخر ترقية لمنصة اختبارات وأكملها توثيقاً: ثغرة الـ IP، مقارنة `config()`، تعطيل Telegram، و"دروس للمشروع التالي". **ابدأ بها** |
| `socialuae-reference.md` | منصة اختبارات من نفس القالب: `session.serialization` و`cache.serializable_classes` وصفحات الأخطاء وقوالب البريد ودروسها |
| `islamic-reference.md` | الترقية الأولى (من Laravel 6)، ودروسها |
| `harness/` | أدوات المقارنة بنسخة readingbenchmark، ومعها `cache-check.php` من islamic و`TrustedIpApiTest.php` من `tests/Feature` في readingbenchmark. مكتوبة على نفس الـ guards ونفس نوع الصفحات، فتحتاج تكييفاً أقل بكثير من المشاريع الأخرى: أسماء الـ models والـ routes والبيانات تُراجع فقط |

## abt-identity مقارنةً بالمشاريع السابقة
- **Laravel 8.83.29** (المثبت في vendor)، وPHP `^7.3`. `composer.lock` موجود في git. الفرع الحالي `master`.
- **القالب الموحد**: نفس guards منصات الاختبار (`manager` و`school` و`inspection` و`student` و`web`)، ونفس models تقريباً (`Student` و`Term` و`StudentTerm` و`Question` وأنواع الأسئلة `MatchQuestion` و`OptionQuestion` و`SortQuestion` و`TFQuestion` و`FillBlankQuestion`). لا يوجد class اسمه `Match`.
- **قاعدة البيانات المحلية** `abt-identity`: **القاعدة المحلية فارغة** (لا جداول)، فيلزم استيراد نسخة من القاعدة قبل تسجيل الـ baselines.
- **الـ routes** محوّلة أصلاً إلى `[Controller::class, 'method']` تقريباً (بقيت 1 إلى 3 بصيغة النص)، فلا يلزم التحويل الكبير الذي جرى في islamic.
- **الحزم** معظمها بـ `"*"` في `composer.json`، والخطة تطلب تحديد نسخ صريحة لها. لا توجد `config/log-viewer.php`، أي arcanedev بإعداداته الافتراضية بلا حماية. `fruitcake/laravel-cors` يُحذف (CORS مدمج). `hesto/multi-auth` متروك، لكن controllers الدخول تستخدم منه trait `LogsoutGuard` وقت التشغيل، فيُنقل الـ trait إلى المشروع قبل حذف الحزمة. `spatie/laravel-translatable` موجود كما في islamic.
- لا يوجد `resources/views/errors`، و`resources/views/vendor` فيها `jsvalidation` فقط (قوالب البريد غير منشورة، كما في socialuae). `resources/lang` ليست في git (ما عدا `auth_log.php`)، وتُنسخ عند النشر.
- `composer.json` مطابق لـ fluency (لا يحمّل `app/Helpers/reports.php`).
- الخطة ذكرت تعديلات غير محفوظة هنا، لكن `git status` كان نظيفاً وقت النسخ.

## ما رُصد قبل البدء ويجب عرضه على صاحب المشروع
- لا توجد routes artisan عامة في `routes/web.php` (بخلاف english وfluency وscience وarabicplus)، لكن في `routes/manager.php` route `seed` يشغّل `PermissionsTableSeeder` خلف دخول المدير. لا يُستدعى في الاختبارات.
- **ثغرة الـ IP نفسها**: `getRealIpAddress()` في `app/Helpers/helpers.php` تقرأ `X-Forwarded-For` أولاً، و`isTrustIpAddress()` تحمي `POST api/update_settings` (`Manager\SettingController@updateSettingsApi`). أُصلحت في readingbenchmark وislamic بـ `$request->ip()` مع `TrustProxies`، ومعها `TrustedIpApiTest`.
- **hash كلمة المرور في activity log**: `Manager` و`School` و`Inspection` تسجّل `['name', 'email', 'password']`. أُوقف في المشاريع الثلاثة السابقة، وتُركت السجلات القديمة كما هي.
- **Telegram مضبوط في جدول `settings` المحلي** (token وchannel id)، ويُرسل إليه من `LogMessageJob` و`TelegramLoggerHandler` و`Handler`. يُعطَّل قبل أول تشغيل (تفريغ القيمة محلياً أو `Bus::fake` في كل اختبار).
- **البريد من جدول `settings`** عبر `SettingServiceProvider`. Laravel 8 يقرأ `mail.mailers.smtp.*` أصلاً، فيُتوقع ألا يتغير شيء، ويُتأكد بمقارنة `config()` كاملاً، وتُمنع أي رسالة حقيقية أثناء الاختبار.
- **`dd(` في 6 ملفات تحت `app/Http`**. أي route GET ينتهي بـ `dd()` يوقف phpunit بصمت، فتُعرف قبل تشغيل `SmokeRoutesTest`.
- ما ثبت في المشاريع السابقة ويُفحص هنا: `session.serialization` و`cache.serializable_classes` (وما يُحفظ في الكاش من objects)، prefix الكاش، Carbon 3، أسماء `.env` القديمة، `rehash_on_login`، مسار الـ local disk، ترميز CSV في Laravel Excel، الـ routes التي تشير إلى methods غير موجودة، ترتيب `sortBy` المستقر في PHP 8، صفحات الأخطاء وقوالب البريد، و`user/{id}/report` العام.

## أول الخطوات
1. `git fetch`، ثم فرع `upgrade/laravel-13` وtag لنقطة البداية.
2. تعطيل كل ما يرسل إلى الخارج (Telegram، البريد) قبل أول تشغيل.
3. تسجيل الـ baselines قبل أي تعديل: `routes-dump.php`، `config-dump.php`، `composer audit`، `composer show`، نتيجة الاختبارات، و`SmokeRoutesTest` بعد تكييفه ونسخه إلى `tests/Feature` وتشغيله بـ `SMOKE_RECORD=1`، مع استثناء routes الـ artisan و`seed` حتى لا تُشغَّل.
4. قراءة أقسام "دروس للمشروع التالي" في المراجع، من الأحدث إلى الأقدم.
5. إن كانت ترقية منصة أخرى من الموجة 2 قد انتهت (english أولاً)، فـ `docs/upgrade/README.md` فيها هو أقرب مرجع على الإطلاق، لأن الكود شبه متطابق.
