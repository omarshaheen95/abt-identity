@extends('manager.layout.container')

@section('title')
    {{$title}}
@endsection

@section('style')
    @if(app()->getLocale()=='ar')
        <link href="{{asset('assets_v1/js/multiselect/multiselect-rtl.css')}}?v={{time()}}" rel="stylesheet" type="text/css"/>
    @endif
@endsection

@push('breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{route('manager.student.index')}}" class="text-muted">
            {{t('Students')}}
        </a>
    </li>
    <li class="breadcrumb-item text-muted">
        {{$title}}
    </li>
@endpush
@section('content')
    <form class="form" id="form_data"
          action="{{isset($student) ? route('manager.student.update', $student->id):route('manager.student.store')}}"
          method="post">
        {{csrf_field()}}
        @isset($student)
            <input type="hidden" name="_method" value="PATCH"/>
        @endisset
        <div class="row">
             <div class="col-4 mb-2">
                <div class="form-group">
                    <label class="form-label mb-1">{{t('Student ID')}}:</label>
                    <input placeholder="{{t('Student ID')}}" class="form-control id_number" name="id_number" type="text" value="{{ isset($student) ? $student->id_number : old('id_number') }}">
                </div>
            </div>
             <div class="col-4 mb-2">
                <div class="form-group">
                    <label class="form-label mb-1">{{t('Student Name')}}:</label>
                    <input placeholder="{{t('Student Name')}}" class="form-control name remove_spaces" name="name" type="text" value="{{ isset($student) ? $student->name : old('name') }}">
                </div>
            </div>

             <div class="col-4 mb-2">
                <div class="form-group">
                    <label class="form-label mb-1">{{t('Username')}}:</label>
                    <div class="input-group mb-5">
                        <input name="email" type="text" placeholder="{{t('Username')}}" value="{{ isset($student) ? $student->email : old('email') }}"
                               class="form-control username" aria-describedby="basic-addon1"/>
                        <span class="input-group-text" id="basic-addon1">
                         <a class="p-0 cursor-pointer" id="generateUserName"><i class="fas fa-refresh"></i></a>
                     </span>
                    </div>
                </div>
            </div>


             <div class="col-4 mb-2">
                <div class="form-group">
                    <label class="form-label mb-1">{{t('School')}}:</label>
                    <select name="school_id" data-placeholder="{{t('Select School')}}" class="form-control form-select" data-control="select2" data-allow-clear="true">
                        <option></option>
                        @foreach($schools as $school)
                            <option value="{{ $school->id }}" {{isset($student) && $student->school_id == $school->id ? 'selected':''}}>{{ $school->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>


             <div class="col-4 mb-2">
                <div class="form-group">
                    <label class="form-label mb-1">{{t('Grade Name')}}:</label>
                    <input placeholder="{{t('Grade Name')}}" class="form-control " name="grade_name" type="text" value="{{ isset($student) ? $student->grade_name : old('grade_name') }}">
                </div>
            </div>
             <div class="col-4 mb-2">
                <div class="form-group">
                    <label class="form-label mb-1">{{t('Years')}}:</label>
                    <select id="year_id" name="year_id" data-placeholder="{{t('Select Year')}}" class="form-control form-select" data-control="select2" data-allow-clear="true">
                        <option></option>
                        @foreach($years as $year)
                            <option value="{{$year->id}}" {{isset($student) && $student->year_id == $year->id ? 'selected':''}}>{{$year->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
             <div class="col-4 mb-2">
                <div class="form-group">
                    <label class="form-label mb-1">{{t('Level')}}:</label>
                    <select id="levels_id" name="level_id" data-placeholder="{{t('Select Level')}}" class="form-control form-select" data-control="select2" data-allow-clear="true">
                        <option></option>
                        @isset($levels)
                            @foreach($levels as $level)
                                <option value="{{$level->id}}" {{isset($student) && $student->level_id == $level->id ? 'selected':''}}>{{$level->name}}</option>
                            @endforeach
                        @endisset
                    </select>
                </div>
            </div>
             <div class="col-4 mb-2">
                <div class="form-group">
                    <label class="form-label mb-1">{{t('Nationality')}}:</label>
                    <input placeholder="{{t('Nationality')}}" class="form-control" name="nationality" type="text" value="{{ isset($student) ? $student->nationality : old('nationality') }}">
                </div>
            </div>

             <div class="col-4 mb-2">
                <div class="form-group">
                    <label class="form-label mb-1">{{t('Date of Birth')}}:</label>
                    <input placeholder="{{t('Date of Birth')}}" class="form-control date" id="" name="dob" type="text" autocomplete="disabled"  value="{{ isset($student->dob) ? $student->dob : old('dob') }}">
                </div>
            </div>
            <div class="row col-12 mt-2">
                <div class="col-md-2 form-group">
                    <label class="form-label mb-1">{{t('Gender')}}:</label>
                    <div class="d-flex flex-column gap-2">
                        <div class="form-check form-check-custom form-check-solid form-check-sm">
                            <input class="form-check-input" type="radio" value="boy" name="gender" id="flexRadioLg" {{ isset($student) && $student->gender == "boy" ? 'checked':'' }}/>
                            <label class="form-check-label" for="flexRadioLg">
                                {{t('Boy')}}
                            </label>
                        </div>

                        <div class="form-check form-check-custom form-check-solid form-check-sm">
                            <input class="form-check-input" type="radio" value="girl" name="gender" id="flexRadioLg" {{ isset($student) && $student->gender == "girl" ? 'checked':'' }}/>
                            <label class="form-check-label" for="flexRadioLg">
                                {{t('Girl')}}
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-2 form-group">
                    <label class="form-label mb-1">{{t('Section')}}:</label>
                    <div class="d-flex flex-column gap-2">
                        <div class="form-check form-check-custom form-check-solid form-check-sm">
                            <input class="form-check-input" type="radio" value="1" name="arab" id="flexRadioLg" {{ isset($student) && $student->arab == 1 ? 'checked':'' }}/>
                            <label class="form-check-label" for="flexRadioLg">
                                {{t('Arabs Student')}}
                            </label>
                        </div>

                        <div class="form-check form-check-custom form-check-solid form-check-sm">
                            <input class="form-check-input" type="radio" value="0" name="arab" id="flexRadioLg" {{ isset($student) && $student->arab == 0 ? 'checked':'' }}/>
                            <label class="form-check-label" for="flexRadioLg">
                                {{t('Non Arabs Student')}}
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 form-group">
                    <label class="form-label mb-1">{{t('Inclusion')}}:</label>
                    <div class="d-flex flex-column gap-2">
                        <div class="form-check form-check-custom form-check-solid form-check-sm">
                            <input class="form-check-input" type="radio" value="1" name="sen" id="flexRadioLg" {{ isset($student) && $student->sen == 1 ? 'checked':'' }}/>
                            <label class="form-check-label" for="flexRadioLg">
                                {{t('SEN Student')}}
                            </label>
                        </div>

                        <div class="form-check form-check-custom form-check-solid form-check-sm">
                            <input class="form-check-input" type="radio" value="0" name="sen" id="flexRadioLg" {{ isset($student) && $student->sen == 0 ? 'checked':'' }}/>
                            <label class="form-check-label" for="flexRadioLg">
                                {{t('Normal Student')}}
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 form-group">
                    <label class="form-label mb-1">{{t('Citizen')}}:</label>
                    <div class="d-flex flex-column gap-2">
                        <div class="form-check form-check-custom form-check-solid form-check-sm">
                            <input class="form-check-input" type="radio" value="1" name="citizen" id="flexRadioLg" {{ isset($student) && $student->citizen == 1 ? 'checked':'' }}/>
                            <label class="form-check-label" for="flexRadioLg">
                                {{t('Citizen')}}
                            </label>
                        </div>

                        <div class="form-check form-check-custom form-check-solid form-check-sm">
                            <input class="form-check-input" type="radio" value="0" name="citizen" id="flexRadioLg" {{ isset($student) && $student->citizen == 0 ? 'checked':'' }}/>
                            <label class="form-check-label" for="flexRadioLg">
                                {{t('Not Citizen')}}
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 form-group">
                    <label class="form-label mb-1">{{t('G & T')}}:</label>
                    <div class="d-flex flex-column gap-2">
                        <div class="form-check form-check-custom form-check-solid form-check-sm">
                            <input class="form-check-input" type="radio" value="1" name="g_t"
                                   id="flexRadioLg" {{ isset($student) && $student->g_t == 1 ? 'checked':'' }}/>
                            <label class="form-check-label" for="flexRadioLg">
                                {{t('Yes')}}
                            </label>
                        </div>

                        <div class="form-check form-check-custom form-check-solid form-check-sm">
                            <input class="form-check-input" type="radio" value="0" name="g_t"
                                   id="flexRadioLg" {{ isset($student) && $student->g_t == 0 ? 'checked':'' }}/>
                            <label class="form-check-label" for="flexRadioLg">
                                {{t('No')}}
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-2 ">
                    <label class="form-label mb-1">{{t('Demo')}}:</label>
                    <div class="d-flex flex-column gap-2">
                        <div class="form-check form-check-custom form-check-solid form-check-sm">
                            <input class="form-check-input" type="radio" value="1" name="demo" id="flexRadioLg" {{ isset($student) && $student->demo == 1 ? 'checked':'' }}/>
                            <label class="form-check-label" for="flexRadioLg">
                                {{t('Demo')}}
                            </label>
                        </div>

                        <div class="form-check form-check-custom form-check-solid form-check-sm">
                            <input class="form-check-input" type="radio" value="0" name="demo" id="flexRadioLg" {{ isset($student) && $student->demo == 0 ? 'checked':'' }}/>
                            <label class="form-check-label" for="flexRadioLg">
                                {{t('Not Demo')}}
                            </label>
                        </div>
                    </div>
                </div>
            </div>


        </div>
        <div class="row d-none" id="demo_data">
            <div class="separator separator-dashed mt-8"></div>
            <h3 class="col-12 text-center my-5">{{t('Demo Information')}}</h3>
            <div class="separator separator-dashed mb-8"></div>

            <div class="col-12 row">
                <div class="col-6">
                    <label>{{t('Demo Year')}} :</label>
                    <select name="demo_data[year_id]" id="demo_year_id" class="form-select" data-control="select2" data-placeholder="{{t('Select Year')}}" data-allow-clear="true">
                        <option></option>
                        @foreach($years as $year)
                            <option value="{{ $year->id }}" @if(isset($student->demo_data->year_id) && $student->demo_data->year_id && $student->demo_data->year_id==$year->id) selected @endif>{{ $year->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6">
                    <label>{{t('Demo Rounds')}}</label>
                    <select name="demo_data[rounds][]" class="form-select" data-control="select2" data-placeholder="{{t('Select Round')}}" data-allow-clear="true" multiple="multiple">
                        <option></option>
                        @foreach(getRounds() as $round)
                            <option value="{{$round}}" @if(isset($student->demo_data->rounds) && $student->demo_data->rounds && in_array($round,$student->demo_data->rounds)) selected @endif>{{$round}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 d-flex justify-content-center gap-1 mt-2">
                    <div class="col-5 d-flex flex-column gap-2">
                        <label>{{t('Levels')}}</label>
                        <select name="from[]" id="multiselect" class="form-control h-100"  multiple="multiple">
                            @if(isset($demo_levels))
                                @foreach($demo_levels as $level)
                                    <option value="{{$level->id}}">{{$level->name}}</option>
                                @endforeach
                            @endif

                        </select>
                    </div>

                    <div class="col-2 d-flex flex-column justify-content-center align-items-center mt-5" >
                        <button type="button" id="multiselect_rightAll" class="btn btn-secondary btn-sm mb-1"><i class="fa fa-forward"></i></button>
                        <button type="button" id="multiselect_rightSelected" class="btn btn-secondary btn-sm mb-1"><i class="fa fa-chevron-right"></i></button>
                        <button type="button" id="multiselect_leftSelected" class="btn btn-secondary btn-sm mb-1"><i class="fa fa-chevron-left"></i></button>
                        <button type="button" id="multiselect_leftAll" class="btn btn-secondary btn-sm mb-1"><i class="fa fa-backward"></i></button>
                    </div>

                    <div class="col-5 d-flex flex-column gap-2">
                        <label>{{t('Selected Levels')}}</label>
                        <select name="demo_data[levels][]" id="multiselect_to" class="form-control h-100" size="8" multiple="multiple">

                            @if(isset($selected_demo_levels)&&count($selected_demo_levels)>0)
                                @foreach($selected_demo_levels as $level)
                                    <option value="{{$level->id}}">{{$level->name}}</option>
                                @endforeach
                            @endif

                        </select>
                    </div>
                </div>





            </div>
        </div>

        <div class="row my-5">
            <div class="separator separator-content my-4"></div>
            <div class="col-6 d-flex justify-content-start">
                @isset($student)
                    <a href="{{route('manager.student.student-login', $student->id)}}"
                       class="btn btn-info">{{ t('Login') }}</a>
                @endisset
            </div>
            <div class="col-6 d-flex justify-content-end">
                <button type="submit"
                        class="btn btn-primary">{{ isset($student) ? t('Update'):t('Create') }}</button>&nbsp;
            </div>

        </div>
    </form>


@endsection
@section('script')
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}?v=1"></script>
    <script type="text/javascript" src="{{ asset('assets_v1/js/multiselect/multiselect.min.js')}}"></script>

    {!! JsValidator::formRequest(\App\Http\Requests\Manager\StudentRequest::class, '#form_data'); !!}
    <script>
        if (parseInt($('input[name="demo"]:checked').val()) === 1) {
            $('#demo_data').removeClass('d-none');
        }

        function generateUserName() {
            var name = $(".name").val().toLowerCase().split(" ");
            var year = (new Date).getFullYear();
            var number = parseInt(Math.random() * 100);
            var username = name[0] + year + number + '@identity';
            $(".username").val(username);
        }

        $('.name').keyup(function () {
            generateUserName();
        });
        $('#generateUserName').click(function () {
            generateUserName();
        });

        //Demo -------------------------------------------------------------------

        //show and hide Demo data
        $('input[name="demo"]').change(function () {
            var demo = $('input[name="demo"]:checked').val();
            if (parseInt(demo) === 1) {
                $('#demo_data').removeClass('d-none');
            } else {
                $('#demo_data').addClass('d-none');
            }
        });

        //Init multiselecet For Levels
        $('#multiselect').multiselect({
            search: {
                left: '<input type="text" name="q" class="form-control" placeholder="Search..." />',
                right: '<input type="text" name="q" class="form-control" placeholder="Search..." />',
            },
            sort:false
        });
        $('select[name="demo_data[year_id]"]').on('change',function () {
            $.ajax({
                url:"{{route('manager.level.levelGrades')}}",
                type: "GET",
                data:{
                    'id':$(this).val()
                },
                success: function (data) {
                    $('#multiselect').html(null)
                    $('#multiselect_to').html(null)
                    $('#multiselect').html(data.html)

                },
                error: function (jqXHR, status, errorThrown) {
                    toastr.error(errorThrown.message)
                }
            })
        })
    </script>
    <script src="{{asset('assets_v1/js/manager/models/general.js')}}?v2"></script>
@endsection
