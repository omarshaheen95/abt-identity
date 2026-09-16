@extends('manager.layout.container')
@section('title')
    {{$title}}
@endsection
@push('breadcrumb')
    <li class="breadcrumb-item text-muted">
        {{$title}}
    </li>
@endpush
@section('content')
    @if(!empty($forced))
        <div class="alert alert-warning d-flex align-items-center mb-6">
            <i class="la la-exclamation-triangle fs-2 me-3"></i>
            <div>
                <h4 class="mb-1">{{t('Password change required')}}</h4>
                <span>
                    @if(($reason ?? null) === 'expired')
                        {{t('Your password has expired, please choose a new one to continue.')}}
                    @else
                        {{t('You must change your password before you can continue.')}}
                    @endif
                </span>
            </div>
        </div>
    @endif
    <div class="row">
        <form class="form" id="form_data" action="{{route('manager.update-password')}}" method="post">
            @csrf
            <div class="form-group row">

                <div class="col-lg-4 mb-2">
                    <label class="form-label mb-1">{{t('Old Password')}} <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input name="old_password" type="password" placeholder="{{t('Old Password')}}"
                               autocomplete="current-password" class="form-control"/>
                        <button class="btn btn-icon btn-light border" type="button" data-password-toggle
                                title="{{t('Show / Hide Password')}}">
                            <i class="la la-eye"></i>
                        </button>
                    </div>
                </div>

                @include('components.password-fields')

            </div>

            <div class="row my-5">
                <div class="separator separator-content my-4"></div>
                <div class="col-12 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary mr-2">{{t('Submit')}}</button>
                </div>
            </div>
        </form>
    </div>
@endsection
@section('script')
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}?v=2"></script>
    {!! JsValidator::formRequest(\App\Http\Requests\Manager\ManagerPasswordRequest::class, '#form_data'); !!}
@endsection
