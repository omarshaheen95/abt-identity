<div class="action-content">
    @if(isset($tab_index)&& $tab_index!=0)
        <a class="btn btn-prev" data-tab-index="{{$tab_index}}">{{$term->level->arab?'السابق':'Previous'}}</a>
    @endif

    @if(isset($tab_index)&& $tab_index!=3)
        <a class="btn btn-next" data-tab-index="{{$tab_index}}">{{$term->level->arab?'التالي':'Next'}}</a>
    @else
        <div class="btn btn-submit">
            <span class="spinner-border spinner-border-sm me-2 d-none"></span>
            {{$term->level->arab?'إنهاء':'Finish'}}
        </div>
    @endif

</div>


