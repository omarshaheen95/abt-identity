@extends('manager.layout.container')
@section('title')
    {{t('Text Translation')}}
@endsection
@push('breadcrumb')
    <li class="breadcrumb-item text-muted">
        {{t('Text Translation')}}
    </li>
@endpush
@section('content')
    <input type="text" class="form-control mb-2" id="searchInput" onkeyup="searchDiv()"
           placeholder="{{t('Search')}}">
    <ul class="nav nav-tabs nav-fill" role="tablist">
        @foreach($folders as $key => $folder)
            <li class="nav-item">
                <a class="nav-link @if($loop->first) active @endif" data-bs-toggle="tab"
                   href="#kt_tabs_{{$key}}">{{$key}}</a>
            </li>
        @endforeach
    </ul>

    <div class="tab-content" id="tabContent">
        @foreach($folders as $l_key => $folder)
            <div class="tab-pane @if($loop->first) active @endif" id="kt_tabs_{{$l_key}}" role="tabpanel">
                <ul class="nav nav-tabs nav-fill" role="tablist">
                    @foreach($folder['files'] as $n_key => $file)
                        @if(str_replace('.php', '', basename($file)) !== 'validation')
                            <li class="nav-item">
                                <a class="nav-link @if($loop->first) active @endif" data-bs-toggle="tab"
                                   href="#kt_tabs_{{$l_key}}_{{$n_key}}">{{basename($file)}}</a>
                            </li>
                        @endif
                    @endforeach
                </ul>
                <div class="tab-content">
                    @foreach($folder['files'] as $n_key => $file)
                        @if(str_replace('.php', '', basename($file)) !== 'validation')
                            @php
                                $translations = \Illuminate\Support\Facades\File::getRequire($file);
                            @endphp
                            <div class="tab-pane @if($loop->first) active @endif"
                                 id="kt_tabs_{{$l_key}}_{{$n_key}}" role="tabpanel">
                                <form class="kt-form kt-form--label-right"
                                      action="{{route('manager.text_translation.update', [$l_key, basename($file)])}}"
                                      method="post">
                                    @csrf
                                    <div class="kt-portlet__body">
                                        <div class="form-group row">
                                            @if(is_array($translations))
                                                @foreach($translations as $t_key => $v_key)
                                                    <div class="col-lg-4 mt-4 trans-div">
                                                        <label>{{$t_key}}:</label>
                                                        <textarea name="{{$t_key}}"
                                                                  class="form-control">{{$v_key}}</textarea>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                    <div class="row my-5">
                                        <div class="separator separator-content my-4"></div>
                                        <div class="col-12 d-flex justify-content-end">
                                            <button type="submit" class="btn btn-primary mr-2">{{t('Submit')}}</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

@endsection

@section('script')
    <script>
        function searchDiv() {
            var input, filter, tabs, transDivs, transDiv, i, txtValue;
            input = document.getElementById("searchInput");
            filter = input.value;
            tabs = document.getElementById("tabContent");
            transDivs = tabs.getElementsByClassName("trans-div");
            for (i = 0; i < transDivs.length; i++) {
                transDiv = transDivs[i].getElementsByTagName("textarea")[0];
                if (transDiv) {
                    txtValue = transDiv.textContent || transDiv.innerText;
                    if (txtValue.indexOf(filter) > -1) {
                        transDivs[i].style.display = "";
                    } else {
                        transDivs[i].style.display = "none";
                    }
                }
            }
        }
    </script>
@endsection
