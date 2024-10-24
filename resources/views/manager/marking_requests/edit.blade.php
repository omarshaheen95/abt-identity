@extends('manager.layout.container')
@section('title')
    {{ isset($marking_request) ? t('Edit Marking Request' ) : t('Add Marking Request') }}
@endsection

@push('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('manager.marking_requests.index') }}">
            {{t('Marking Requests')}}
        </a>
    </li>

    <li class="breadcrumb-item text-muted">
        {{ isset($marking_request) ? t('Edit Marking Request' ) : t('Add Marking Request') }}
    </li>
@endpush

@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="kt-portlet">

                <form enctype="multipart/form-data" id="form_information" class="kt-form kt-form--label-right"
                      action="{{ isset($marking_request) ? route('manager.marking_requests.update', $marking_request->id): route('manager.marking_requests.store') }}"
                      method="post">
                    {{ csrf_field() }}
                    @if(isset($marking_request))
                        <input type="hidden" name="_method" value="patch">
                    @endif
                    <div class="row">
                        <div class="col-12 mb-2">
                            <div class="form-group">
                                <label class="form-label">{{t('School')}}</label>
                                <select class="form-select" data-control="select2" data-allow-clear="true" name="school_id" data-placeholder="{{t('Select School')}}">
                                    <option></option>
                                    @foreach($schools as $school)
                                        <option
                                            value="{{$school->id}}" {{isset($marking_request) && $marking_request->school_id == $school->id ? 'selected':''}}>{{$school->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12 mb-2">
                            <div class="form-group">
                                <label class="form-label">{{t('Year')}}</label>
                                <select class="form-select" data-control="select2" data-allow-clear="true" name="year_id" data-placeholder="{{t('Select Year')}}">
                                    <option></option>
                                    @foreach($years as $year)
                                        <option
                                            value="{{ $year->id }}" {{ isset($marking_request) && $marking_request->year_id == $year->id ? 'selected':'' }}>{{ $year->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12 mb-2">
                            <div class="form-group">
                                <label class="form-label">{{t('Round')}}</label>
                                <select class="form-select" data-control="select2" data-allow-clear="true" name="round" data-placeholder="{{t('Select Round')}}">
                                    @foreach(['september','february','may'] as $month)
                                        <option
                                            value="{{$month}}" {{isset($marking_request) && $month==$marking_request->round ? 'selected':''}}>{{$month}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12 mb-2">
                            <div class="form-group">
                                <label class="form-label">{{t('Grades')}}</label>
                                <select class="form-select" multiple data-control="select2" data-allow-clear="true" name="grades[]" data-placeholder="{{t('Select Grade')}}">
                                    @foreach($grades as $grade)
                                        <option
                                            value="{{$grade}}" {{isset($marking_request) && in_array($grade,$marking_request->grades) ? 'selected':''}}>Grade {{$grade}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12 mb-2">
                            <label class="form-label">{{t('Section')}} :</label>
                            <div class="d-flex border-secondary p-4" style="border: 1px solid;border-radius: 5px;">
                                <div class="form-check form-check-custom form-check-solid me-3">
                                    <input class="form-check-input" name="section" type="radio" value="0" id="section"
                                           @if(isset($marking_request) && $marking_request->section==0)checked
                                           @else checked @endif/>
                                    <label class="form-check-label" for="section">
                                        {{t('All')}}
                                    </label>
                                </div>
                                <div class="form-check form-check-custom form-check-solid me-3">
                                    <input class="form-check-input" name="section" type="radio" value="1" id="section"
                                           @if(isset($marking_request) && $marking_request->section==1)checked @endif/>
                                    <label class="form-check-label" for="section">
                                        {{t('Arab')}}
                                    </label>
                                </div>
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input" name="section" type="radio" value="2" id="section"
                                           @if(isset($marking_request) && $marking_request->section==2)checked @endif/>
                                    <label class="form-check-label" for="section">
                                        {{t('Non-Arabs')}}
                                    </label>
                                </div>

                            </div>
                        </div>

                        <div class="col-12 mb-2">
                            <div class="form-group">
                                <label class="form-label">{{t('Status')}}:</label>
                                <select class="form-select" data-control="select2" data-hide-search="true" data-allow-clear="true" name="status" data-placeholder="{{t('Select Section')}}">
                                    <option></option>
                                    <option value="Pending" {{isset($marking_request) && 'Pending' == $marking_request->status ? 'selected':''}}>{{t('Pending')}}</option>
                                    <option value="Accepted" {{isset($marking_request) && 'Accepted' == $marking_request->status ? 'selected':''}}>{{t('Accepted')}}</option>
                                    <option value="In Progress" {{isset($marking_request) && 'In Progress' == $marking_request->status ? 'selected':''}}>{{t('In Progress')}}</option>
                                    <option value="Completed" {{isset($marking_request) && 'Completed' == $marking_request->status ? 'selected':''}}>{{t('Completed')}}</option>
                                    <option value="Rejected"  {{isset($marking_request) && 'Rejected' == $marking_request->status ? 'selected':''}}>{{t('Rejected')}}</option>

                                </select>
                            </div>
                        </div>
                        <div class="col-12 mb-2">
                            <div class="form-group">
                                <label class="form-label">{{t('Contact Email')}}</label>
                                <input type="email" class="form-control" name="email" value="{{isset($marking_request) ? $marking_request->email:""}}">

                            </div>
                        </div>

                        <div class="col-12 mb-2">
                            <div class="form-group">
                                <label class="form-label">{{t('Notes')}}</label>
                                <textarea name="notes" class="form-control"  cols="30" rows="3">{{isset($marking_request) ? $marking_request->notes:''}}</textarea>
                            </div>
                        </div>

                    </div>

                    <div class="row my-5">
                        <div class="separator separator-content my-4"></div>
                        <div class="col-12 d-flex justify-content-end">
                            <button type="submit"
                                    class="btn btn-primary">{{ isset($marking_request) ? t('Update'):t('Create') }}</button>&nbsp;
                        </div>
                    </div>

                </form>

            </div>
        </div>
    </div>


@endsection

@section('script')
            <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}?v=1"></script>
            {!! JsValidator::formRequest(\App\Http\Requests\MarkingRequestRequest::class, '#form_information'); !!}
@endsection
