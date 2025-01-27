<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;

//use LaravelFCM\Message\PayloadNotificationBuilder;
//use LaravelFCM\Message\PayloadDataBuilder;
//use LaravelFCM\Message\Topics;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

function grades()
{
    return [
        (object)['id' => 1, 'name' => t('Grade') . ' 1'],
        (object)['id' => 2, 'name' => t('Grade') . ' 2'],
        (object)['id' => 3, 'name' => t('Grade') . ' 3'],
        (object)['id' => 4, 'name' => t('Grade') . ' 4'],
        (object)['id' => 5, 'name' => t('Grade') . ' 5'],
        (object)['id' => 6, 'name' => t('Grade') . ' 6'],
        (object)['id' => 7, 'name' => t('Grade') . ' 7'],
        (object)['id' => 8, 'name' => t('Grade') . ' 8'],
        (object)['id' => 9, 'name' => t('Grade') . ' 9'],
        (object)['id' => 10, 'name' => t('Grade') . ' 10'],
        (object)['id' => 11, 'name' => t('Grade') . ' 11'],
        (object)['id' => 12, 'name' => t('Grade') . ' 12'],
    ];
}

function getGuard()
{
    $guard = 'web';
    if (request()->is('manager/*')) {
        $guard = 'manager';
    } elseif (request()->is('school/*')) {
        $guard = 'school';
    } elseif (request()->is('inspection/*')) {
        $guard = 'inspection';
    } elseif (request()->is('student/*')) {
        $guard = 'student';
    }
    return $guard;
}

function settingCache($key)
{
    //Cache::forget('settings');
    $cache = \Illuminate\Support\Facades\Cache::remember('settings', 60 * 48, function () {
        return \App\Models\Setting::get();
    });

    if ($cache) {
        return $cache->where('key', $key)->pluck('value')->first();
    }
    return null;
}

function uploadFile($file, $path = '')
{
    $fileName = $file->getClientOriginalName();
    $file_exe = $file->getClientOriginalExtension();
    $file_size = $file->getSize();
    $new_name = uniqid() . '.' . $file_exe;
    $directory = 'uploads' . '/' . $path; //.'/'.date("Y").'/'.date("m").'/'.date("d");
    $destination = public_path($directory);
    $file->move($destination, $new_name);
    $data['path'] = $directory . '/' . $new_name;
    $data['file_name'] = $fileName;
    $data['new_name'] = $new_name;
    $data['extension'] = $file_exe;
    //$data['size'] = formatBytes($file_size);
    return $data;
}

function uploadNewFile($file, $path, bool $new_name = true)
{
    $file_original_name = $file->getClientOriginalName();
    $file_new_name = Str::random(27) . '.' . $file->getClientOriginalExtension();
//    $path = $file->storeAs($path, $new_name?$file_new_name:$file_original_name, 'public');
    $directory = 'uploads' . '/' . $path . '/' . date("Y") . '/' . date("m") . '/' . date("d");
    $destination = public_path($directory);
    $file->move($destination, $file_new_name);
    return ['name' => $new_name ? $file_new_name : $file_original_name, 'path' => $directory . DIRECTORY_SEPARATOR . $file_new_name];
}

function deleteFile($path = '/questions', string $disk = 'public')
{
    return Storage::disk($disk)->delete($path);
}

function set_locale()
{
    $locale = isAPI() ? request()->header('Accept-Language') : (session('lang') ? session('lang') : 'en');
    if (!$locale || !in_array($locale, ['ar', 'en'])) $locale = 'ar';

    app()->setLocale($locale);

    return $locale;
}

function t($key, $placeholder = [], $locale = null)
{
    return translation('translation', $key, $placeholder = [], $locale);
}

function re($key, $placeholder = [], $locale = null)
{
    return translation('report', $key, $placeholder = [], $locale = null);
}

function translation($group, $key, $placeholder = [], $locale = null)
{
    if (is_null($locale)) {
        $locale = config('app.locale');
    }
    $key = trim($key);
    $word = $group . '.' . $key;
    if (Lang::has($word))
        return trans($word, $placeholder, $locale);

    $messages = [
        $word => $key,
    ];

    app('translator')->addLines($messages, $locale);
    $langs = Config::get('app.languages');
    foreach ($langs as $lang) {
        $translation_file = base_path() . '/resources/lang/' . $lang . '/' . $group . '.php';
        $fh = fopen($translation_file, 'r+');
        $key = str_replace("'", "\'", $key);
        $new_key = "\n \t'$key' => '$key',\n];\n";
        fseek($fh, -4, SEEK_END);
        fwrite($fh, $new_key);
        fclose($fh);
    }
    return trans($word, $placeholder, $locale);
    //return $key;
}

function sysDomain()
{
    $host = request()->getHost();
    if (strpos($host, 'www') !== false) {
        return $host;
    } else {
        return 'www.' . $host;
    }

}

function f($key, $placeholder = [], $locale = null)
{

    $group = 'frontend';
    if (is_null($locale))
        $locale = config('app.locale');
    $key = trim($key);
    $word = $group . '.' . $key;

    $messages = [
        $word => $key,
    ];

    app('translator')->addLines($messages, $locale);
    $langs = config('app.languages');
    foreach ($langs as $lang) {
        $translation_file = base_path() . '/lang/' . $lang . '/' . $group . '.php';
        $fh = fopen($translation_file, 'r+');
        $key = str_replace("'", "\'", $key);
        $new_key = "\n \t'$key' => '$key',\n];\n";
        fseek($fh, -4, SEEK_END);
        fwrite($fh, $new_key);
        fclose($fh);
    }
    return trans($word, $placeholder, $locale);
}

function w($key, $placeholder = [], $locale = null)
{

    $group = 'web';
    if (is_null($locale))
        $locale = config('app.locale');
    $key = trim($key);
    $word = $group . '.' . $key;
    if (\Illuminate\Support\Facades\Lang::has($word)) {
        return trans($word, $placeholder, $locale);
    } else {
        return $key;
    }

    $messages = [$word => $key];

    app('translator')->addLines($messages, $locale);
    $langs = config('translatable.locales');
    foreach ($langs as $lang) {
        $translation_file = base_path() . '/resources/lang/' . $lang . '/' . $group . '.php';
        $fh = fopen($translation_file, 'r+');
        $new_key = "\n \t'$key' => '$key',\n];\n";
        fseek($fh, -4, SEEK_END);
        fwrite($fh, $new_key);
        fclose($fh);
    }
    return trans($word, $placeholder, $locale);
    return $key;
}

function api($key, $placeholder = [], $locale = null)
{

    $group = 'api';
    if (is_null($locale))
        $locale = config('app.locale');
    $key = trim($key);
    $word = $group . '.' . $key;
    if (\Illuminate\Support\Facades\Lang::has($word)) {
        return trans($word, $placeholder, $locale);
    } else {
        return $key;
    }

    $messages = [
        $word => $key,
    ];

    app('translator')->addLines($messages, $locale);
    $langs = config('translatable.locales');

    $langs = ['ar', 'en'];

    foreach ($langs as $lang) {
        $translation_file = base_path() . '/resources/lang/' . $lang . '/' . $group . '.php';
        $fh = fopen($translation_file, 'r+');
        $new_key = "  \n  '$key' => '$key',\n];\n";
        fseek($fh, -4, SEEK_END);
        fwrite($fh, $new_key);
        fclose($fh);
    }
    return trans($word, $placeholder, $locale);
    return $key;
}

function documentTypes()
{
    return [
        'document',
        'audio',
        'video',
        'image',
        'archive',
    ];
}

function isRtl()
{
    return app()->getLocale() === 'ar';
}

function isRtlJS()
{
    return app()->getLocale() === 'ar' ? 'true' : 'false';
}

function direction($dot = '')
{
    return isRtl() ? 'rtl' . $dot : '';
}

function currentLanguage()
{
    return app()->getLocale();
}

function MimeFile($extension)
{
    /*
     Video Type     Extension       MIME Type
    Flash           .flv            video/x-flv
    MPEG-4          .mp4            video/mp4
    iPhone Index    .m3u8           application/x-mpegURL
    iPhone Segment  .ts             video/MP2T
    3GP Mobile      .3gp            video/3gpp
    QuickTime       .mov            video/quicktime
    A/V Interleave  .avi            video/x-msvideo
    Windows Media   .wmv            video/x-ms-wmv
    */
    $ext_photos = ['png', 'jpg', 'jpeg', 'gif'];
    return in_array($extension, $ext_photos) ? 'photo' : 'video';
}

function split_string($string, $count = 2)
{

    //Using the explode method
    $arr_ph = explode(" ", $string, $count);

    if (!isset($arr_ph[1]))
        $arr_ph[1] = '';
    return $arr_ph;
}

function assets($path = '', $relative = false)
{
    return $relative ? 'public/' . $path : url('public/' . $path);
}

function slug($string)
{
    return preg_replace('/\s+/u', '-', trim($string));
}

function generateRandomString($length = 20)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function defaultImage()
{
    return "public/assets/img/default.png";
}

function pic($src, $class = 'full')
{
    $html = "<img class='  " . $class . "' src='" . asset($src) . "'>";

    return $html;
}

function ext($filename, $style = false)
{

    //$ext = File::extension($filename);

    $ext = pathinfo($filename, PATHINFO_EXTENSION);

    if (!$style)
        return $ext;
    return $html = "<img class='' src='" . asset('public/assets/img/ext/' . $ext . '.png') . "'>";
}

function IsLang($lang = 'ar')
{
    return session('lang') == $lang;
}

function CurrentLang()
{
    return session('lang', 'en');
}

function isAPI()
{
    return request()->is('api/*');
}

function paginate($object)
{
    return [
        'current_page' => $object->currentPage(),
        //'items' => $object->items(),
        'first_page_url' => $object->url(1),
        'from' => $object->firstItem(),
        'last_page' => $object->lastPage(),
        'last_page_url' => $object->url($object->lastPage()),
        'next_page_url' => $object->nextPageUrl(),
        'per_page' => $object->perPage(),
        'prev_page_url' => $object->previousPageUrl(),
        'to' => $object->lastItem(),
        'total' => $object->total(),
    ];
}

function paginate_message($object)
{

    $items = [];
    foreach ($object->items() as $key => $item) {
        foreach ($item['data'] as $k => $val) {
            $items[$key][$k] = $val;

            // $items[$key] = ['id' => $item->id,'title' => $item->data['title'],'body' => $item->data['body'],'created_at' => $item->created_at ];
            /* if(isset($item->data['title']))
              $items[$key]['title'] = $item->data['title']; */
        }
        $items[$key]['notification_id'] = $item->id;
        $items[$key]['created_at'] = $item->created_at->format('Y-m-d H:i:s');
    }

    return [
        'current_page' => $object->currentPage(),
        'items' => $items,
        'first_page_url' => $object->url(1),
        'from' => $object->firstItem(),
        'last_page' => $object->lastPage(),
        'last_page_url' => $object->url($object->lastPage()),
        'next_page_url' => $object->nextPageUrl(),
        'per_page' => $object->perPage(),
        'prev_page_url' => $object->previousPageUrl(),
        'to' => $object->lastItem(),
        'total' => $object->total(),
    ];
}

function destroyFile($file)
{

    if (!empty($file) and File::exists(public_path($file)))
        File::delete(public_path($file));
}

function curl_get_contents($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
    $html = curl_exec($ch);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

function numhash($n)
{
    return (((0x0000FFFF & $n) << 16) + ((0xFFFF0000 & $n) >> 16));
}

function compress_image($source_url, $destination_url, $quality)
{

    // $info = getimagesize($source_url);
    //        $memoryNeeded = round(($info[0] * $info[1] * $info['bits']  / 8 + Pow(2, 16)) * 1.65);

    // if (function_exists('memory_get_usage') && memory_get_usage() + $memoryNeeded > (integer) ini_get('memory_limit') * pow(1024, 2)) {

    //     ini_set('memory_limit', (integer) ini_get('memory_limit') + ceil(((memory_get_usage() + $memoryNeeded) - (integer) ini_get('memory_limit') * pow(1024, 2)) / pow(1024, 2)) . 'M');

    // }

    ini_set('memory_limit', '265M');

    // $newHeight = ($height / $width) * $newWidth;
    // $tmp = imagecreatetruecolor($newWidth, $newHeight);
    // imagecopyresampled($tmp, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);


    // if ($info['mime'] == 'image/jpeg'){
    //       $image = imagecreatefromjpeg($source_url);
    // imagejpeg($image, $destination_url, $quality);
    // }
    // if ($info['mime'] == 'image/gif'){
    //       $image = imagecreatefromgif($source_url);
    // imagegif($image, $destination_url, 5);
    // }
    // elseif ($info['mime'] == 'image/png'){
    //       $image = imagecreatefrompng($source_url);
    // imagepng($image, $destination_url, 5);
    // }
    // else{
    // $image = imagecreatefromjpeg($source_url);
    // imagejpeg($image, $destination_url, $quality);
    // }


    // jpg, png, gif or bmp?
    $exploded = explode('.', $source_url);
    $ext = $exploded[count($exploded) - 1];

    if (preg_match('/jpg|jpeg/i', $ext))
        $imageTmp = imagecreatefromjpeg($source_url);
    else if (preg_match('/png/i', $ext))
        $imageTmp = imagecreatefrompng($source_url);
    else if (preg_match('/gif/i', $ext))
        $imageTmp = imagecreatefromgif($source_url);
    else if (preg_match('/bmp/i', $ext))
        $imageTmp = imagecreatefrombmp($source_url);
    else
        return 0;

    // quality is a value from 0 (worst) to 100 (best)
    imagejpeg($imageTmp, $destination_url, $quality);


    imagedestroy($imageTmp);
    return $destination_url;
}

function resize($newWidth, $originalFile)
{

    $info = getimagesize($originalFile);
    $mime = $info['mime'];

    switch ($mime) {
        case 'image/jpeg':
            $image_create_func = 'imagecreatefromjpeg';
            $image_save_func = 'imagejpeg';
            $new_image_ext = 'jpg';
            break;

        case 'image/png':
            $image_create_func = 'imagecreatefrompng';
            $image_save_func = 'imagepng';
            $new_image_ext = 'png';
            break;

        case 'image/gif':
            $image_create_func = 'imagecreatefromgif';
            $image_save_func = 'imagegif';
            $new_image_ext = 'gif';
            break;

        default:
            throw new Exception('Unknown image type.');
    }

    $img = $image_create_func($originalFile);
    list($width, $height) = getimagesize($originalFile);

    $newHeight = ($height / $width) * $newWidth;
    $tmp = imagecreatetruecolor($newWidth, $newHeight);
    imagecopyresampled($tmp, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

    // if (file_exists($targetFile)) {
    //         unlink($targetFile);
    // }

    $image_save_func($tmp, $originalFile);
}

function schoolsType()
{
    return [
        'MOE' => 'MOE',
        'British' => 'British',
        'American' => 'American',
        'Indian' => 'Indian',
        'Iranian' => 'Iranian',
        'Philippine' => 'Philippine',
        'International Baccalaureate' => 'International Baccalaureate',
        'Pakistan' => 'Pakistan',
        'French' => 'French',
        'Canadian' => 'Canadian',
        'German' => 'German',
        'Others' => 'Others',
    ];
}

function schoolsCountry()
{
    return [
        'UAE' => 'UAE',
        'KSA' => 'KSA',
        'KWT' => 'KWT',
        'OMAN' => 'OMAN',
        'QATAR' => 'QATAR',
        'BAHRAIN' => 'BAHRAIN',
        'EGYPT' => 'EGYPT',
        'Global' => 'Global',
    ];
}

function getRounds()
{
    return ['september', 'february', 'may'];
}

function camelCaseText($text, $replace = '_'): string
{
    return Str::title(str_replace($replace, ' ', $text));
}

function judgement($below, $inline, $above)
{
    $data = [];
    if ($below > 85) {
        $data['level'] = 'Very Weak';
        $data['color'] = '#DF8B03';
        $data['color'] = '#DF8B03';
        $data['bg_color'] = '#DF8B03';
    } elseif ($below > 25 && $below <= 85) {
        $data['level'] = 'Weak';
        $data['color'] = '#EF5428';
        $data['bg_color'] = '#EF5428';
    } elseif (($above > 0 && $above < 50) || ($inline >= 75 && $inline <= 100)) {
        $data['level'] = 'Acceptable';
        $data['color'] = '#7F7F7F';
        $data['bg_color'] = '#7F7F7F';
    } elseif ($above >= 50 && $above < 61) {
        $data['level'] = 'Good';
        $data['color'] = '#2FA6D6';
        $data['bg_color'] = '#2FA6D6';
    } elseif ($above >= 61 && $above < 75) {
        $data['level'] = 'Very Good';
        $data['color'] = '#FFC211';
        $data['bg_color'] = '#FFC211';
    } elseif ($above >= 75 && $above <= 100) {
        $data['level'] = 'Outstanding';
        $data['color'] = '#26C281';
        $data['bg_color'] = '#26C281';
    } else {
        $data['level'] = 'Weak';
        $data['color'] = '#F00';
        $data['bg_color'] = '#F00';
    }

    return $data;
}

function getSubjectAttainment($subject_data, $subjects, $responseTypeText = 1)
{
    $subject = $subjects->where('id', $subject_data->subject_id)->first();
    $below = (object)$subject->marks_range['below'];
    $inline = (object)$subject->marks_range['inline'];
    $above = (object)$subject->marks_range['above'];

    if ($subject_data->mark >= $below->from && $subject_data->mark <= $below->to) {
        return $responseTypeText ? 'Below' : 1;
    } elseif ($subject_data->mark >= $inline->from && $subject_data->mark <= $inline->to) {
        return $responseTypeText ? 'Inline' : 2;
    } elseif ($subject_data->mark >= $above->from && $subject_data->mark <= $above->to) {
        return $responseTypeText ? 'Above' : 3;
    } else {
        return $responseTypeText ? 'Unknown' : 0;
    }
}

function getProgressText($startPoint, $subTotalMarks)
{
    $progress = getProgress($startPoint, $subTotalMarks);
    if ($progress == 1) {
        return 'Better than expected progress';
    } elseif ($progress == 0) {
        return 'Expected progress';
    } else {
        return 'Below expected progress';
    }
}

function getProgress($startPoint, $subTotalMarks)
{
    if ($startPoint >= 80) {
        if ($subTotalMarks > -5) {
            return 1;
        } elseif ($subTotalMarks <= -5 && $subTotalMarks >= -10) {
            return 0;
        } else {
            return -1;
        }
    } elseif ($startPoint >= 60) {
        if ($subTotalMarks > 5) {
            return 1;
        } elseif ($subTotalMarks >= 0) {
            return 0;
        } else {
            return -1;
        }
    } else {
        if ($subTotalMarks > 10) {
            return 1;
        } elseif ($subTotalMarks >= 5) {
            return 0;
        } else {
            return -1;
        }
    }
}

function encryptStudentId($studentId)
{
    $encryptionKey = 'abt-assessment@,com';
    $method = 'AES-256-CBC';
    $ivLength = openssl_cipher_iv_length($method);
    $iv = openssl_random_pseudo_bytes($ivLength);
    $encrypted = openssl_encrypt($studentId, $method, $encryptionKey, 0, $iv);
    return base64_encode($iv . $encrypted);
}

function decryptStudentId($encryptedStudentId)
{
    $encryptionKey = 'abt-assessment@,com';
    $method = 'AES-256-CBC';
    $ivLength = openssl_cipher_iv_length($method);
    $encryptedStudentId = base64_decode($encryptedStudentId);
    $iv = substr($encryptedStudentId, 0, $ivLength);
    $encrypted = substr($encryptedStudentId, $ivLength);
    return openssl_decrypt($encrypted, $method, $encryptionKey, 0, $iv);
}


function getAllModels() {
    $modelsPath = app_path('Models');
    $modelFiles = \Illuminate\Support\Facades\File::allFiles($modelsPath);
    $modelNames = [];

    foreach ($modelFiles as $file) {
        $relativePath = $file->getRelativePathname();
        $className = 'App\\Models\\' . str_replace(['/', '.php'], ['\\', ''], $relativePath);
        if (class_exists($className)) {
            $modelNames[] = $className;
        }
    }

    return $modelNames;
}
