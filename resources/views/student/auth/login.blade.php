@extends('general.auth')
@section('title')
    {{t('SignIn')}}
@endsection
@section('content')
    <main class="login-page login-student">
        <div class="login-form">
            <div class="logo">
                <img src="{{!settingCache('logo')? asset('logo.svg'):asset(settingCache('logo'))}}" alt="" width="100%">
            </div>
            <div class="header">
                <div class="pic">
                    <img src="{{asset('web_assets/img/login-school.svg')}}" alt="">
                </div>
                <h1 class="title"> دخول الطالب | Student Log in</h1>
                @if (count($errors) > 0)
                    <div class="alert alert-danger w-50 m-auto" role="alert" dir="ltr">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif
                <div class="alert alert-warning w-100 m-auto d-none" role="alert" id="chrome_settings">
                    <h5 dir="ltr">To be able to start your ABT assessment, please use Google Chrome as a browser.
                    </h5>
                    <h5 dir="rtl">لتكون قادرا على بدء اختبار اي بي تي المعياري ، يرجى استخدام متصفح جوجل كروم.</h5>
                </div>
            </div>
            <div class="form">
                <form action="/student/login" method="post" class="needs-validation " novalidate>
                    {{csrf_field()}}
                    <div class="form-group">
                        <label for="email" class="form-label text-start">اسم المستخدم - Username</label>
                        <div class="form-icon">
                                <span class="icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="27" height="24" viewBox="0 0 27 24">
                                        <path id="Icon"
                                              d="M7.834,0A7.827,7.827,0,0,0,0,7.794v8.412A7.814,7.814,0,0,0,7.834,24H19.166A7.838,7.838,0,0,0,27,16.206V14.258a.953.953,0,0,0-.955-.95l-.013.023a.954.954,0,0,0-.955.95v1.925a5.957,5.957,0,0,1-5.911,5.882H7.834a5.957,5.957,0,0,1-5.911-5.882V7.794A5.956,5.956,0,0,1,7.834,1.913H19.166a5.956,5.956,0,0,1,5.911,5.881.968.968,0,0,0,1.922,0A7.839,7.839,0,0,0,19.166,0Zm3.033,10.694L5.3,15.125a.959.959,0,0,0-.143,1.342A.947.947,0,0,0,6.5,16.61l5.612-4.42a1.943,1.943,0,0,1,2.389,0l5.553,4.42h.012a.97.97,0,0,0,1.349-.143.946.946,0,0,0-.155-1.342L15.7,10.694a3.871,3.871,0,0,0-4.837,0Z"
                                              transform="translate(27 24) rotate(180)" fill="#172239"/>
                                    </svg>
                                </span>
                            <input type="text" value="{{request()->get('username', null)}}" name="username" id="email" class="form-control"
                                   placeholder="اسم المستخدم - Username" required>
                        </div>
                        <input type="hidden" id="browserInfo" name="browserInfo">
                    </div>
                    <div class="form-group ">
                        <button type="submit" class="btn btn-theme btn-submit w-100">
                            <span class="spinner-border spinner-border-sm d-none"></span>
                            <span class="text"> تسجيل الدخول - Log in </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
@endsection












