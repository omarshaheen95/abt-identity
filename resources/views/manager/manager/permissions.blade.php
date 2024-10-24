@extends('manager.layout.container')
@section('title')
    {{t('Edit Permissions')}}
@endsection
@push('breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{route('manager.manager.index')}}" class="text-muted">
            {{t('Managers')}}
        </a>
    </li>
    <li class="breadcrumb-item text-muted">
        {{t('Edit Permissions')}}
    </li>
@endpush

@section('actions')
        <a onclick="selectAll()" class="btn btn-primary font-weight-bolder">
            <i class="la la-check-circle"></i>{{t('Select All Permissions')}}</a>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <!--begin::Card-->
            <div class="card m-0 p-0">
                <!--begin::Form-->
                <form class="form" id="form_data"
                      action="{{route('manager.manager.update-permissions')}}"
                      method="post">
                    @csrf
                    <input name="manager_id" type="hidden" value="{{$manager_id}}">
                    <div class="card-body m-0 p-0">
                        <div class="form-group row">
                            @foreach($permissions as $key=>$values)
                                <div class="col-12 d-flex flex-column">
                                    @php
                                        $permissions_ids = $values->pluck('id')->toArray();
                                        $manager_permissions_ids = collect($manager_permissions)->pluck('permission_id')->toArray();
                                        $c_status = false;
                                        foreach ($permissions_ids as $value) {
                                           if (in_array($value, $manager_permissions_ids)) {
                                               $c_status= true;
                                           } else {
                                               $c_status= false;
                                               break;
                                           }
                                       }
                                    @endphp
                                    <div class="d-flex my-5 align-items-center">
                                        <h2 class="m-0">{{t(camelCaseText($key))}}</h2>
                                        <div class="form-check form-check-custom form-check-solid form-check-sm ms-2">
                                            <input id="{{$key}}_checkbox" class="form-check-input" type="checkbox" value=""  {{$c_status?'checked':''}}
                                            onclick="checkAllPermissions('{{$key}}_checkbox','{{'class_'.$key}}')"  {{$c_status?'checked':''}}/>
                                        </div>
                                    </div>
                                    <div class="card bg-gray-100">
                                        <div class="card-body">
                                            <div class="row">
                                                @foreach($values as $permission)
                                                    @php
                                                        $status =
                                                        collect($manager_permissions)->where('permission_id',$permission->id)->first();
                                                    @endphp
                                                    <div class="col-3 form-check form-check-custom form-check-solid form-check-sm p-2">
                                                        <input class="form-check-input {{'class_'.$key}}" type="checkbox" name="permissions[]" value="{{$permission->name}}" id="{{$key.$loop->index}}" {{$status?'checked':''}}/>
                                                        <label class="form-check-label text-dark" for="{{$key.$loop->index}}">
                                                            {{t(camelCaseText($permission->name))}}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            @endforeach

                        </div>
                    </div>
                    <div class="card-footer mt-4">
                        <div class="row">
                            <div class="col-lg-12 d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary mr-2">{{t('Submit')}}</button>
                            </div>
                        </div>
                    </div>
                </form>
                <!--end::Form-->
            </div>
            <!--end::Card-->
        </div>
    </div>

@endsection
@section('script')
    <script>
        function checkAllPermissions(input_id,m_class) {
            let is_checked = $('#'+input_id).is(':checked')
            $('.'+m_class).each((index,item)=>{
                if (is_checked){ //when checked
                    $(item).prop('checked',true)

                }else {//when not checked
                    $(item).prop('checked',false)
                }
            })
        }
        function selectAll(){
            $('#form_data input:checkbox').prop('checked',true);
        }
    </script>
@endsection
