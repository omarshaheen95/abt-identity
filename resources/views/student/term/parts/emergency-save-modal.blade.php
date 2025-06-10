<div class="modal fade c-modal" id="emergency-save-modal" tabindex="-1" aria-labelledby="modalTitleId" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="exam-confirm">
                        <div class="text-end">
                            <a href="#" data-bs-dismiss="modal">
                                <img src="{{asset('web_assets/img/close.svg')}}" width="40" alt="">
                            </a>
                        </div>
                        <div class="alert alert-warning mt-4">
                            <i class="fas fa-exclamation-triangle"></i>
                            @if(app()->getLocale()=='en')
                                This will save your assessment without validation. Use only if you are experiencing issues with normal submission.
                            @else
                                سيحفظ هذا تقييمك دون التحقق من الإجابات. استخدمه فقط إذا واجهت مشاكل في الإرسال العادي.
                            @endif
                        </div>
                        <div class="content">
                            <h2 class="title fw-bold">
                                @if(app()->getLocale()=='en')
                                    Emergency Save
                                @else
                                    الحفظ الطارئ
                                @endif
                            </h2>
                            <p class="info">
                                @if(app()->getLocale()=='en')
                                    Are you sure you want to save the assessment without validation?
                                @else
                                    هل أنت متأكد أنك تريد حفظ التقييم دون التحقق منه؟
                                @endif
                            </p>
                            <a href="#!" class="btn" id="confirm-emergency-save" style="background-color:#ff0404;border-color: #e03e2d">
                                @if(app()->getLocale()=='en')
                                    Save Assessment
                                @else
                                    حفظ الاختبار
                                @endif
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>





{{--<div class="modal fade" id="emergency-save-modal" tabindex="-1" aria-labelledby="emergencySaveModalLabel" aria-hidden="true">--}}
{{--    <div class="modal-dialog">--}}
{{--        <div class="modal-content">--}}
{{--            <div class="modal-header">--}}
{{--                <h5 class="modal-title" id="emergencySaveModalLabel">Emergency Save</h5>--}}
{{--                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>--}}
{{--            </div>--}}
{{--            <div class="modal-body">--}}
{{--                <div class="alert alert-warning">--}}
{{--                    <i class="fas fa-exclamation-triangle"></i>--}}
{{--                    This will save your assessment without validation. Use only if you're experiencing issues with normal submission.--}}
{{--                </div>--}}
{{--                <p>Are you sure you want to save the assessment without validation?</p>--}}
{{--            </div>--}}
{{--            <div class="modal-footer">--}}
{{--                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>--}}
{{--                <button type="button" class="btn btn-danger" id="confirm-emergency-save">Yes, Save Now</button>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}
