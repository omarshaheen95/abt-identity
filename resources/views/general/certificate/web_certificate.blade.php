{{--Dev Omar Shaheen
    Devomar095@gmail.com
    WhatsApp +972592554320
    --}}
@php
    if (app()->getLocale()=='ar'){
      $title = 'شهادةُ تقدير للطلبةِ ذوي التحصيلِ العالي';
      $content_1 = 'تشهدُ شركةُ اي بي تي للاختباراتِ المعياريّةِ الدوليّة بأنّ';
      $content_2 = 'بالصف';
      $content_3 = 'قد حصلَ على تقديرٍ متميّزٍ في اختبارِ اي بي تي للاختباراتِ المعياريّةِ الدوليّة في اختبارِ الهُويّةِ الوطنيّة';
      $content_4 = ' بنسبةِ نجاحٍ بلغت';
      $end = 'راجينَ لهُ دوامَ التوفيقِ والتقدُّمِ في مسيرتِهِ العلميّة.';
      $signature_1 = 'مع تحياتِ: شركة اي بي تي للاختباراتِ المعياريّةِ الدوليّة';
      $signature_2 = 'المديرُ التنفيذيُّ:';
      $signature_3 = 'أ. محمد جمال';
      $date = 'التاريخ:';
      $student = 'الطالب/ة : ';
      $dir='rtl';
      $local='ar';

    }else{
      $title = 'Certificate of Appreciation For High-Achieving Students';
      $content_1 = 'This is to certify that ABT Assessments / The International Benchmark Test for Arabic Subjects acknowledges that the';
      $content_2 = 'In grade';
      $content_3 = 'Has achieved outstanding performance in the ABT International Benchmark Test for National Identity';
      $content_4 = 'with a success rate of ';
      $end = 'We wish them continued success and progress in their academic journey.';
      $signature_1 = 'Sincerely,ABT Assessments / The International Benchmark Test for Arabic Subjects';
      $signature_2 = 'Executive Director';
      $signature_3 = 'Mr. Mohamed Gamal';
      $date = 'Date:';
      $student = 'Student : ';
      $dir = 'ltr';
      $local='en';
    }
@endphp
<!DOCTYPE html>
<html lang="{{$local}}" dir="{{$dir}}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate Awarded to {{$name}}</title>
    <link rel="shortcut icon" href="{{asset('logo_min.svg')}}?v={{time()}}" type="image/x-icon">
    <link rel="stylesheet" href="{{asset('assets_v1/certificate/style.css')}}?v={{time()}}">
</head>
<body>
<div class="page">
    <img class="page-border" src="{{asset('assets_v1/certificate/img/border.svg')}}?v={{time()}}" alt="Certificate Border">
    <div class="certificate-content">
        <div class="header-logos">
            <img src="{{asset('assets_v1/certificate/img/achievement_'.$local.'.svg')}}?v={{time()}}" alt="Injaz Logo">
            <img src="{{asset('logo.svg')}}?v={{time()}}" alt="Company Logo">
        </div>

        <h1 class="certificate-title">{{$title}}</h1>

        <div class="certificate-body">
            <div class="student-info">
                <div class="student-info-intro">
                    {{$content_1}}
                </div>
                <div class="student-details">
                    {{$student}}
                    <span class="highlight">{{$name}}</span>
                    {{$content_2}}
                    <span class="highlight">{{$grade}}</span>
                </div>
            </div>

            <p class="achievement-text">
                {{$content_3}}<br>{{$content_4}}
                <span class="highlight">( {{$mark}}% )</span>
            </p>

            <p class="achievement-text">{{$end}}</p>

            <p class="achievement-text">{{$signature_1}}</p>

            <div class="signature-section">
                <div class="signature-block">
                    <span class="signature-title">{{$signature_2}}</span>
                    <span class="signature-name">{{$signature_3}}</span>
                    <img src="{{asset('assets_v1/certificate/img/seo_signature.png')}}?v={{time()}}" style="height: 50px;" alt="Signature">
                </div>
                <div class="signature-block">
                    <img src="{{asset('assets_v1/certificate/img/signature.png')}}?v={{time()}}" style="height: 90px;" alt="Signature">
                </div>
                <div class="signature-block">
                    <span class="signature-title">{{$date}}</span>
                    <span class="signature-name">{{date('d-m-Y')}}</span>
                </div>
            </div>

            <img class="logos" src="{{asset('assets_v1/certificate/img/logos_group.svg')}}?v={{time()}}" alt="Logos">
        </div>
    </div>
</div>
</body>
</html>
