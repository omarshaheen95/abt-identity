@extends('school.layout.container')

@section('title')
    {{$title}}
@endsection
@push('breadcrumb')
    <li class="breadcrumb-item text-muted">
        {{$title}}
    </li>
@endpush
@section('content')
    <form id="sc_edit_student_form" class="form" method="post"
          action="{{isset($student) ? route('school.student.update', $student->id):route('school.student.store')}}">
        @csrf
        @isset($student)
            <input type="hidden" name="_method" value="PATCH"/>
        @endisset
        <div class="row">

            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label mb-1">{{t('Student ID Number')}}:</label>
                    <input class="form-control id_number" name="id_number" type="text" value="{{ isset($student) ? $student->id_number : old('id_number') }}">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label mb-1">{{t('Student Name')}}:</label>
                    <input class="form-control name remove_spaces" name="name" type="text" value="{{ isset($student) ? $student->name : old('name') }}">
                </div>
            </div>

            <div class="col-md-4">
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
            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label mb-1">{{t('Grade Name')}}:</label>
                    <input class="form-control " name="grade_name" type="text" value="">

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

            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label mb-1">{{t('Nationality')}}:</label>
                    <input class="form-control" name="nationality" type="text" value="{{ isset($student) ? $student->nationality_name : old('nationality_name') }}">
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label mb-1">{{t('Date of Birth')}}:</label>
                    <input class="form-control" name="dob" placeholder="{{t('Date of Birth')}}" id="datepicker" value="{{ isset($student->dob) ? $student->dob : old('dob') }}"/>
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
            </div>

        </div>

        <div class="row my-5">
            <div class="separator separator-content my-4"></div>
            <div class="col-12 d-flex justify-content-end">
                <button type="submit" class="btn btn-primary mr-2">{{isset($student)?t('Update Student'):t('Add Student')}}</button>
            </div>
        </div>
    </form>
@endsection

@section('script')
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}?v=1"></script>
    {!! JsValidator::formRequest(\App\Http\Requests\School\StudentRequest::class, '#sc_edit_student_form'); !!}

    <script>
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
    </script>
    <script src="{{asset('assets_v1/js/school/general.js')}}"></script>

@endsection
