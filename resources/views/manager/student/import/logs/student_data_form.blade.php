<div class="row col-12">
    <input type="hidden" name="student[{{$row->id}}][row_num]" value="{{$row->row_num}}">
    @foreach ($inputs_with_values as $input)
        @if($input['key'] == 'Arab' || $input['key'] == 'Citizen' || $input['key'] == 'SEN' || $input['key'] == 'G&T')
            <div class="col-2">
                <label class="text-info">{{$input['key']}}</label>
                <select name="student[{{$row->id}}][{{str_replace(' ', '_', strtolower($input['key']))}}]"
                        data-name="student[{{$row->id}}][{{str_replace(' ', '_', strtolower($input['key']))}}]"
                        class="form-control from-input-data form-select"
                        data-control="select2"
                        data-placeholder="{{t('Select '.$input['key'])}}">
                    <option value="" disabled selected>{{t('Select Year')}}</option>
                    <option value="1" {{$input['value'] == 1 ? 'selected':''}}>{{t('Yes')}}</option>
                    <option value="0" {{$input['value'] == 0 && $input['value'] != null ? 'selected':''}}>{{t('No')}}</option>
                </select>
            </div>
        @elseif($input['key'] == 'Gender')
            <div class="col-3">
                <label class="text-info">{{$input['key']}}</label>
                <select name="student[{{$row->id}}][{{str_replace(' ', '_', strtolower($input['key']))}}]"
                        data-name="student[{{$row->id}}][{{str_replace(' ', '_', strtolower($input['key']))}}]"
                        class="form-control from-input-data form-select"
                        data-control="select2"
                        data-placeholder="{{t('Select '.$input['key'])}}">
                    <option value="" disabled selected>{{t('Select '.$input['key'])}}</option>
                    <option value="1" {{$input['value'] == 1 ? 'selected':''}}>{{t('Boy')}}</option>
                    <option value="2" {{$input['value'] == 2 ? 'selected':''}}>{{t('Girl')}}</option>
                </select>
            </div>
        @elseif($input['key'] == 'Grade')
            <div class="col-3">
                <label class="text-info">{{$input['key']}}</label>
                <select name="student[{{$row->id}}][{{str_replace(' ', '_', strtolower($input['key']))}}]"
                        data-name="student[{{$row->id}}][{{str_replace(' ', '_', strtolower($input['key']))}}]"
                        class="form-control from-input-data form-select"
                        data-control="select2"
                        data-placeholder="{{t('Select '.$input['key'])}}">
                    <option value="" disabled selected>{{t('Select '.$input['key'])}}</option>
                    @foreach(range(1, 12) as $grade)
                        <option value="{{$grade}}" {{$input['value'] == $grade ? 'selected':''}}>{{t('Grade').' '.$grade}}</option>
                    @endforeach
                </select>
            </div>
        @elseif($input['key'] == 'Assessment')
            <div class="col-3">
                <label class="text-info">{{t('Levels')}}</label>
                <select name="student[{{$row->id}}][{{str_replace(' ', '_', strtolower($input['key']))}}]"
                        data-name="student[{{$row->id}}][{{str_replace(' ', '_', strtolower($input['key']))}}]"
                        class="form-control from-input-data form-select"
                        data-control="select2"
                        data-placeholder="{{t('Select '.$input['key'])}}">
                    <option value="" disabled selected>{{t('Select '.$input['key'])}}</option>
                    @if($levels)
                        @foreach($levels as $level)
                            <option value="{{$level->id}}" {{$input['value']&& $input['value']==$level->id ? 'selected':''}}>{{$level->name}}</option>
                        @endforeach
                    @endif
                </select>
            </div>
        @else
            <div class="col-3">
                <label class="text-info">{{$input['key']}}</label>
                <input required name="student[{{$row->id}}][{{str_replace(' ', '_', strtolower($input['key']))}}]"
                       data-name="student[{{$row->id}}][{{str_replace(' ', '_', strtolower($input['key']))}}]"
                       type="text" value="{{$input['value']}}"
                       class="form-control from-input-data from-input-data @if($input['key']=='Name') remove_spaces @endif">
            </div>
        @endif

    @endforeach
</div>
