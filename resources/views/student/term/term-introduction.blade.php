@extends('student.layout.container')
@section('style')
    <style>
        .i_card{
            background-color:#FFFFFF;
            height: 40px;
            border-color:#67d2c8;
            border-style: solid;
            border-width: 1px;
        }
    </style>
@endsection
@section('content')

    <section class="assessment assessment-view">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="assessment-view-header">
                        <div class="logo">
                            <img src="{{asset('assets/media/logos/logo.svg')}}" class="img-fluid logo-introduction" alt="">
                        </div>
                        <h1 class="title">{{$term->getTranslation('name',app()->getLocale())}}  </h1>
                        <ul class="nav nav-assessment-info" dir="ltr">
                            <li class="nav-item">
                                <a href="#!" class="nav-link">
                                    Student ID: <span class="text-theme">{{$student->id}}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#!" class="nav-link">
                                    Student Name: <span class="text-theme">{{$student->name}}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#!" class="nav-link">
                                    Grade: <span class="text-theme">{{$student->grade_name}}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#!" class="nav-link">
                                    Username: <span class="text-theme">{{$student->email}}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#!" class="nav-link">
                                    Nationality: <span class="text-theme">{{$student->nationality}}</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="mb-4 bg-white row p-2"  style="border-radius: 10px;border-color:#67d2c8; border-style: solid; border-width: 2px">
                <h2> إرشادات الاختبار - Assessment Instructions </h2>

            </div>
            <div class="row p-3"  style="background-color: #ffffff;border-radius: 10px">
                <div class="col-sm-12  col-md-6 p-1">
                    <div class="rounded d-flex align-items-center px-2 i_card" >
                        يتكون الاختبار من 20 سؤالًا.
                    </div>
                </div>

                <div class="col-sm-12  col-md-6 p-1" dir="ltr">
                    <div class="rounded d-flex align-items-center px-2 i_card" >
                        The assessment consists of 20 questions.
                    </div>
                </div>

                <div class="col-sm-12  col-md-6 p-1">
                    <div class="rounded d-flex align-items-center px-2 i_card" >
                        زمن الاختبار هو 60 دقيقة.
                    </div>
                </div>

                <div class="col-sm-12  col-md-6 p-1" dir="ltr">
                    <div class="rounded d-flex align-items-center px-2 i_card" >
                        The assessment time is 60 minutes.
                    </div>
                </div>


                <div class="col-sm-12  col-md-6 p-1">
                    <div class="rounded d-flex align-items-center px-2 i_card" >
                        يجب الإجابة عن جميع الأسئلة.
                    </div>
                </div>

                <div class="col-sm-12  col-md-6 p-1" dir="ltr">
                    <div class="rounded d-flex align-items-center px-2 i_card" >
                        All questions must be answered.
                    </div>
                </div>

                <div class="col-sm-12  col-md-6 p-1">
                    <div class="rounded d-flex align-items-center px-2 i_card" >
                        الدرجة النهائية هي 100 درجة.
                    </div>
                </div>

                <div class="col-sm-12  col-md-6 p-1" dir="ltr">
                    <div class="rounded d-flex align-items-center px-2 i_card" >
                        The assessment out of 100.
                    </div>
                </div>

                <div class="col-sm-12  col-md-6 p-1">
                    <div class="rounded d-flex align-items-center px-2 i_card" >
                        يمكنك التنقل بين الأسئلة ومراجعة إجاباتك في أي وقت.
                    </div>
                </div>

                <div class="col-sm-12  col-md-6 p-1" dir="ltr">
                    <div class="rounded d-flex align-items-center px-2 i_card" >
                        You can scroll through the questions and review your answers at any time.
                    </div>
                </div>

                <div class="col-sm-12  col-md-6 p-1">
                    <div class="rounded d-flex align-items-center px-2 i_card" >
                        يجب عليك حفظ الاختبار عند الانتهاء.
                    </div>
                </div>

                <div class="col-sm-12  col-md-6 p-1" dir="ltr">
                    <div class="rounded d-flex align-items-center px-2 i_card" >
                        You must save the assessment when finished.
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-center py-3">
                <a href="{{route('student.term',['id'=>request()['id']])}}" class="btn btn-primary" style="background-color: #0BB7AF;font-size: 25px;border-radius: 20px"><span class="txt"> ابدأ -  Start </span></a>{{--            <div class="row">--}}
            </div>
{
        </div>
    </section>

@endsection


