@extends('school.layout.auth')
@section('title')
    {{t('Login')}}
@endsection
@section('content')
    <section>
        <div class="pic">
            <img src="{{asset('web_assets/img/bg-auth.svg')}}" alt="">
        </div>
        <div class="form">
            <div class="form-card">
                <form id="login-form" action="{{ url('/school/password/reset') }} " method="POST" class="needs-validation" novalidate>
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <div class="header">
                        @if(app()->getLocale() == "ar")
                            <a href="{{ route('switch-language', 'en') }}" class="lang">
                                <img style="border-radius: 50%;" src="{{asset('assets_v1/media/flags/united-states.svg')}}" width="20px" alt="arabic">
                            </a>
                        @else
                            <a href="{{ route('switch-language', 'ar') }}" class="lang">
                                <img style="border-radius: 50%;" src="{{asset('assets_v1/media/flags/united-arab-emirates.svg')}}" width="20px" alt="arabic">
                            </a>
                        @endif
                        <a href="/" class="logo">
                                <img class="img-fluid" alt="Logo" src="{{!settingCache('logo')? asset('logo.svg'):asset(settingCache('logo'))}}" />
                        </a>
                        <a href="/" class="back">
                            <img src="{{asset('web_assets/img/close.svg')}}" class="img-fluid" alt="close">
                        </a>
                    </div>
                    <div class="info">
                        <h1 class="title">{{t('Reset Password')}} </h1>
                    </div>
                    <div class="body">
                        <div class="form-group">
                            <label for="" class="form-label">{{t('Email')}} </label>
                            <div class="form-control-icon">
                                <div class="icon">
                                    <svg id="Icon" xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32">
                                        <rect id="Bounding_Box" data-name="Bounding Box" width="32" height="32" fill="none"/>
                                        <path id="Icon-2" data-name="Icon" d="M7.834,0A7.827,7.827,0,0,0,0,7.794v8.412A7.814,7.814,0,0,0,7.834,24H19.166A7.838,7.838,0,0,0,27,16.206V14.258a.953.953,0,0,0-.955-.95l-.013.023a.954.954,0,0,0-.955.95v1.925a5.957,5.957,0,0,1-5.911,5.882H7.834a5.957,5.957,0,0,1-5.911-5.882V7.794A5.956,5.956,0,0,1,7.834,1.913H19.166a5.956,5.956,0,0,1,5.911,5.881.968.968,0,0,0,1.922,0A7.839,7.839,0,0,0,19.166,0Zm3.033,10.694L5.3,15.125a.959.959,0,0,0-.143,1.342A.947.947,0,0,0,6.5,16.61l5.612-4.42a1.943,1.943,0,0,1,2.389,0l5.553,4.42h.012a.97.97,0,0,0,1.349-.143.946.946,0,0,0-.155-1.342L15.7,10.694a3.871,3.871,0,0,0-4.837,0Z" transform="translate(29 28) rotate(180)" fill="#30b8af"/>
                                    </svg>
                                </div>
                                <input type="email" name="email" class="form-control" placeholder="ex: example@domain.com" value="{{ $email or old('email') }}" required>
                                @if ($errors->has('email'))
                                    <p class="text-danger" style="font-size: 12px">{{ $errors->first('email') }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="" class="form-label"> {{t('Password')}} </label>
                            <div class="form-control-icon">
                                <div class="icon">
                                    <svg id="Icon" xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32">
                                        <rect id="Bounding_Box" data-name="Bounding Box" width="32" height="32" fill="none"/>
                                        <g id="Icon-2" data-name="Icon" transform="translate(4 3)">
                                            <path id="Path" d="M1.175,1.168A4,4,0,1,0,4,0,4,4,0,0,0,1.175,1.168Z" transform="translate(16 19) rotate(180)" fill="#172239"/>
                                            <path id="Path-2" data-name="Path" d="M8,4V2.667A2.667,2.667,0,0,0,5.333,0H2.667A2.667,2.667,0,0,0,0,2.667V4" transform="translate(8)" fill="none" stroke="#172239" stroke-miterlimit="10" stroke-width="1.867"/>
                                            <path id="Path-3" data-name="Path" d="M.849,10.693A.9.9,0,0,1,0,9.743H0V7.794C.012,3.495,3.122.013,6.963,0H17.037C20.882,0,24,3.49,24,7.794v8.412c-.006,4.3-3.12,7.788-6.963,7.794H6.963C3.122,23.987.012,20.505,0,16.206a.9.9,0,0,1,.854-.856.9.9,0,0,1,.854.856c.029,3.235,2.364,5.849,5.254,5.881H17.037c2.89-.032,5.226-2.647,5.254-5.881V7.794c-.029-3.235-2.364-5.849-5.254-5.881H6.963c-2.89.032-5.226,2.647-5.254,5.881V9.719a1.011,1.011,0,0,1-.249.672.806.806,0,0,1-.6.278h0Z" transform="translate(0 3)" fill="#172239"/>
                                        </g>
                                    </svg>
                                </div>
                                <input type="password" name="password" class="form-control text-dark" placeholder=" *********** " required>
                                @if ($errors->has('password'))
                                    <p class="text-danger" style="font-size: 12px">{{ $errors->first('password') }}</p>
                                @endif


                            </div>
                        </div>

                        <div class="form-group">
                            <label for="" class="form-label"> {{t('Password Confirmation')}} </label>
                            <div class="form-control-icon">
                                <div class="icon">
                                    <svg id="Icon" xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32">
                                        <rect id="Bounding_Box" data-name="Bounding Box" width="32" height="32" fill="none"/>
                                        <g id="Icon-2" data-name="Icon" transform="translate(4 3)">
                                            <path id="Path" d="M1.175,1.168A4,4,0,1,0,4,0,4,4,0,0,0,1.175,1.168Z" transform="translate(16 19) rotate(180)" fill="#172239"/>
                                            <path id="Path-2" data-name="Path" d="M8,4V2.667A2.667,2.667,0,0,0,5.333,0H2.667A2.667,2.667,0,0,0,0,2.667V4" transform="translate(8)" fill="none" stroke="#172239" stroke-miterlimit="10" stroke-width="1.867"/>
                                            <path id="Path-3" data-name="Path" d="M.849,10.693A.9.9,0,0,1,0,9.743H0V7.794C.012,3.495,3.122.013,6.963,0H17.037C20.882,0,24,3.49,24,7.794v8.412c-.006,4.3-3.12,7.788-6.963,7.794H6.963C3.122,23.987.012,20.505,0,16.206a.9.9,0,0,1,.854-.856.9.9,0,0,1,.854.856c.029,3.235,2.364,5.849,5.254,5.881H17.037c2.89-.032,5.226-2.647,5.254-5.881V7.794c-.029-3.235-2.364-5.849-5.254-5.881H6.963c-2.89.032-5.226,2.647-5.254,5.881V9.719a1.011,1.011,0,0,1-.249.672.806.806,0,0,1-.6.278h0Z" transform="translate(0 3)" fill="#172239"/>
                                        </g>
                                    </svg>
                                </div>
                                <input type="password_confirmation" name="password_confirmation" class="form-control text-dark" placeholder=" *********** " required>
                                @if ($errors->has('password_confirmation'))
                                    <p class="text-danger" style="font-size: 12px">{{ $errors->first('password_confirmation') }}</p>
                                @endif


                            </div>
                        </div>


                        <div class="form-group">
                            <button type="submit" class="btn btn-submit">
                                <span class="spinner-border spinner-border-sm me-2 d-none"></span>
                                <span class="text"> {{t('Login')}} </span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
