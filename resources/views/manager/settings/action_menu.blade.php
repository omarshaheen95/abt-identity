<div class="dropdown">
    <button class="btn btn-light btn-active-light-primary btn-flex btn-center btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        {{t('Actions')}}
        <i class="ki-duotone ki-down fs-5 ms-1"></i>
    </button>
    <ul class="dropdown-menu">
        @if(isset($actions))
            @foreach($actions as $action)
              @if($action)
                    @if($action['key'] == 'delete')
                        @if(isset($action['permission']))
                            @can($action['permission'])
                                <li><button type="button" class="dropdown-item text-danger delete_row" data-id="{{$action['route']}}" >{{$action['name']}}</button></li>
                            @endcan

                        @else
                            <li><button type="button" class="dropdown-item text-danger delete_row" data-id="{{$action['route']}}" >{{$action['name']}}</button></li>
                        @endif
                    @else
                        @if(isset($action['permission']))
                            @can($action['permission'])
                                <li><a class="dropdown-item" target="@if($action['key']=='login'||$action['key']=='blank') _blank @endif" href="{{$action['route']}}">{{$action['name']}}</a></li>
                            @endcan

                        @else
                            <li><a class="dropdown-item" target="@if($action['key']=='login'||$action['key']=='blank') _blank @endif" href="{{$action['route']}}">{{$action['name']}}</a></li>
                        @endif
                    @endif
              @endif
            @endforeach
        @endif
    </ul>
</div>


