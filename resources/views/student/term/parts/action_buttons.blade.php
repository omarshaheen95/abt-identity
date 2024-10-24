<div class="action-content">
    @if(isset($tab_index)&& $tab_index!=0)
        <a class="btn btn-prev" data-tab-index="{{$tab_index}}">{{t('Previous')}}</a>
    @endif

    @if(isset($tab_index)&& $tab_index!=3)
        <a class="btn btn-next" data-tab-index="{{$tab_index}}">{{t('Next')}}</a>
    @else
        <div class="btn btn-submit">
            <span class="spinner-border spinner-border-sm me-2 d-none"></span>
            {{t('Finish')}}
        </div>
    @endif

</div>


