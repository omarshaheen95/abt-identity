<div class="d-flex">
    <span @if(isset($delete_option))class="mb-2"@endif>{{t($title)}} :</span>
    @if($url)
        <div class="ms-auto d-flex flex-row align-items-center gap-1 pb-1">
            <a @if(isset($delete_option)) data-option-id="{{$id}}" @else data-id={{$id}}@endif data-type="@isset($type) {{$type}} @endisset" class="btn btn-icon btn-danger {{isset($delete_option)?'delete-option-image':'delete-file'}}"
               style="height: 20px; width: 20px">
                <i class="la la-close la-2"></i>
            </a>
            <a href="{{$url}}" target="_blank" class="btn btn-icon btn-success ml-2" style="height: 20px; width: 20px">
                <i class="la la-eye la-2"></i>
            </a>
        </div>
    @endif

</div>
<input type="file" name="{{$name}}" class="form-control">
