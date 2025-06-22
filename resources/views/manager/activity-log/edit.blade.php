@extends('manager.layout.container')
@section('title')
    {{t('Show Activity Log')}}
@endsection

@push('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('manager.activity-log.index') }}">
            {{t('Activity Log')}}
        </a>
    </li>
    <li class="breadcrumb-item text-muted">
        {{t('Show Activity Log')}}
    </li>
@endpush
@section('style')
    <style>
        .text-overflow-dynamic-container {
            position: relative;
            max-width: 100%;
            padding: 0 !important;
            display: -webkit-flex;
            display: -moz-flex;
            display: flex;
            vertical-align: text-bottom !important;
        }
        .text-overflow-dynamic-ellipsis {
            position: absolute;
            white-space: nowrap;
            overflow-y: visible;
            overflow-x: hidden;
            text-overflow: ellipsis;
            -ms-text-overflow: ellipsis;
            -o-text-overflow: ellipsis;
            max-width: 100%;
            min-width: 0;
            width:100%;
            top: 0;
            left: 0;
        }
        .text-overflow-dynamic-container:after,
        .text-overflow-dynamic-ellipsis:after {
            content: '-';
            display: inline;
            visibility: hidden;
            width: 0;
        }
        .object-display, .array-display {
            text-align: left;
            padding: 8px;
            font-size: 13px;
            line-height: 1.4;
        }
        .object-display .mb-1, .array-display .mb-1 {
            margin-bottom: 4px;
            word-break: break-word;
        }
    </style>
@endsection
@section('content')
    <div class="table-container">
        <table class="table table-bordered">
            <thead>
            <tr class="" style="background-color: #4ad386">
                <th scope="col" colspan="3">
                    <div class="d-flex justify-content-between">
                        <div>
                            {{t('Causer')}}: {{ optional($activity->causer)->name}}
                        </div>
                        <div>
                            {{t('Date')}}: {{$activity->created_at}}

                        </div>
                    </div>

                </th>

            </tr>
            <tr class="" style="background-color: #9dbaff">

                <th scope="col" colspan="3">
                    <div class="d-flex justify-content-between">
                        <div>
                            {{t('Subject')}}: {!!$activity->clickable_subject_type!!}
                        </div>
                        <div>
                            {{t('ID')}}: {{ $activity->subject_id}}
                        </div>

                    </div>

                </th>

            </tr>

            <tr class="text-center">
                <th scope="col" style="background-color: #d1e1ff">{{t('Column Name')}}</th>
                <th scope="col" style="background-color: #ffd376">{{t('Old Data')}}</th>
                <th scope="col" style="background-color: #beffd9">{{t('New Data')}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($new as $key=>$value)
                <tr class="text-center">
                    <th scope="row" style="background-color: #d1e1ff">{{$key}}</th>
                    <td style="background-color: #ffd376">
                        @if(isset($old[$key]))
                            @if(is_object($old[$key]))
                                <div class="object-display">
                                    @foreach($old[$key] as $objKey => $objValue)
                                        <div class="mb-1">
                                            <strong>{{$objKey}}:</strong>
                                            @if(is_array($objValue) || is_object($objValue))
                                                {{json_encode($objValue, JSON_UNESCAPED_UNICODE)}}
                                            @else
                                                {{$objValue}}
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @elseif(is_array($old[$key]))
                                <div class="array-display">
                                    @foreach($old[$key] as $arrKey => $arrValue)
                                        <div class="mb-1">
                                            <strong>[{{$arrKey}}]:</strong>
                                            @if(is_array($arrValue) || is_object($arrValue))
                                                {{json_encode($arrValue, JSON_UNESCAPED_UNICODE)}}
                                            @else
                                                {{$arrValue}}
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-overflow-dynamic-container">
                                    <span class="text-overflow-dynamic-ellipsis">
                                        {{$old[$key]}}
                                    </span>
                                </span>
                            @endif
                        @else
                            <span class="text-overflow-dynamic-container">
                                <span class="text-overflow-dynamic-ellipsis">
                                    {{t('Empty')}}
                                </span>
                            </span>
                        @endif
                    </td>
                    <td style="background-color: #beffd9">
                        @if(is_object($value))
                            <div class="object-display">
                                @foreach($value as $objKey => $objValue)
                                    <div class="mb-1">
                                        <strong>{{$objKey}}:</strong>
                                        @if(is_array($objValue) || is_object($objValue))
                                            {{json_encode($objValue, JSON_UNESCAPED_UNICODE)}}
                                        @else
                                            {{$objValue}}
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @elseif(is_array($value))
                            <div class="array-display">
                                @foreach($value as $arrKey => $arrValue)
                                    <div class="mb-1">
                                        <strong>[{{$arrKey}}]:</strong>
                                        @if(is_array($arrValue) || is_object($arrValue))
                                            {{json_encode($arrValue, JSON_UNESCAPED_UNICODE)}}
                                        @else
                                            {{$arrValue}}
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <span class="text-overflow-dynamic-container">
                                <span class="text-overflow-dynamic-ellipsis">
                                    {{$value}}
                                </span>
                            </span>
                        @endif
                    </td>
                </tr>
            @endforeach

            </tbody>

        </table>
    </div>
@endsection
