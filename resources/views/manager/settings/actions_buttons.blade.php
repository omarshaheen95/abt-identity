
@isset($action_data['edit_url'])
<a href="{{$action_data['edit_url']}}" class="btn btn-icon btn-outline-primary">
    <i class="flaticon2-pen"></i>
</a>
@endisset



@isset($action_data['login_url'])
<a href="{{$action_data['login_url']}}"  class="btn btn-icon btn-outline-primary">
    <i class="flaticon2-left-arrow"></i>
</a>
@endisset

@isset($action_data['edit_permissions_url'])
<a href="{{$action_data['edit_permissions_url']}}"  class="btn btn-icon btn-outline-primary">
    <i class="flaticon2-check-mark"></i>
</a>
@endisset

@isset($action_data['questions_structure'])
    <a href="{{$action_data['questions_structure']}}"  class="btn btn-icon btn-outline-primary">
        <i class="flaticon2-add"></i>
    </a>
@endisset

@isset($action_data['questions'])
    <a href="{{$action_data['questions']}}"  class="btn btn-icon btn-outline-primary">
        <i class="flaticon2-left-arrow"></i>
    </a>
@endisset

{{--@isset($action_data['edit_term'])--}}
{{--    <a href="{{$action_data['edit_term']}}"  class="btn btn-icon btn-outline-primary">--}}
{{--        <i class="flaticon2-pen"></i>--}}
{{--    </a>--}}
{{--@endisset--}}

@isset($action_data['terms_scheduling'])
    <a href="{{$action_data['terms_scheduling']}}"  class="btn btn-icon btn-outline-primary">
        <i class="la la-table"></i>
    </a>
@endisset

@isset($action_data['student_login'])
    <a href="{{$action_data['student_login']}}"  class="btn btn-icon btn-outline-primary">
        <i class="flaticon2-left-arrow"></i>
    </a>
@endisset




@if(isset($action_data['delete']) && $action_data['delete'])
    <button class="btn btn-icon btn-outline-primary delete_row" data-id="{{$row->id}}">
        <i class="flaticon2-trash"></i>
    </button>
@endif
