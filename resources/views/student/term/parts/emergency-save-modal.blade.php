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

                            <div class="form-group">
                                <label for="emergency-password-input" class="form-label" style="color: #ff0404">
                                    @if(app()->getLocale()=='en')
                                        Emergency Password
                                    @else
                                        كلمة مرور الحفظ الطارئ
                                    @endif
                                </label>
                                <div class="position-relative">
                                    <input id="emergency-password-input" class="form-control" type="password" placeholder="ABT-XXXX" />
                                    <span class="position-absolute top-50 translate-middle-y" id="toggle-password" style="right: 15px; cursor: pointer;">
                                        <i class="fas fa-eye" id="eye-icon"></i>
                                    </span>
                                </div>
                                <div id="password-error" class="text-danger mt-2 d-none">
                                    <small>
                                        <i class="fas fa-times-circle"></i>
                                        @if(app()->getLocale()=='en')
                                            Invalid password. Please enter the correct emergency save password.
                                        @else
                                            كلمة المرور غير صحيحة. يرجى إدخال كلمة مرور الحفظ الطارئ الصحيحة.
                                        @endif
                                    </small>
                                </div>
                            </div>
                            <a href="#!" class="btn d-none mt-3" id="confirm-emergency-save" style="background-color:#ff0404;border-color: #e03e2d">
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

@push('style')
    <style>
        #emergency-password-input {
            -webkit-text-security: disc !important;
            text-security: disc !important;
            font-family: text-security-disc !important;
            color: #000 !important;
            padding-right: 45px !important;
            text-align: left;
        }

        #emergency-password-input[type="text"] {
            -webkit-text-security: none !important;
            text-security: none !important;
            font-family: inherit !important;
        }

        #emergency-password-input::-ms-reveal,
        #emergency-password-input::-ms-clear {
            display: none !important;
        }

        #toggle-password {
            z-index: 10;
        }

        #toggle-password:hover {
            opacity: 0.7;
        }

        #password-error {
            animation: shake 0.3s;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
    </style>
@endpush

@push('script')
    <script>
        $(document).ready(function() {
            let btn_emg = $('#confirm-emergency-save');
            let school_id = {{$student->school_id}};
            let passwordInput = $('#emergency-password-input');
            let togglePassword = $('#toggle-password');
            let eyeIcon = $('#eye-icon');
            let passwordError = $('#password-error');

            // Toggle password visibility
            togglePassword.on('click', function() {
                let type = passwordInput.attr('type');

                if (type === 'password') {
                    passwordInput.attr('type', 'text');
                    eyeIcon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    passwordInput.attr('type', 'password');
                    eyeIcon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });

            // Password validation
            passwordInput.on('input', function () {
                let value = $(this).val();

                if ('ABT-' + school_id === value) {
                    $(this).removeClass('border-danger').addClass('border-success').prop('disabled', true);
                    passwordError.addClass('d-none');
                    btn_emg.removeClass('d-none').prop('disabled', false);
                } else {
                    if (value.length > 0) {
                        if (!$(this).hasClass('border-danger')) {
                            $(this).addClass('border-danger');
                        }
                        // Show error only if user has typed something
                        if (value.length >= 4) {
                            passwordError.removeClass('d-none');
                        }
                    } else {
                        $(this).removeClass('border-danger');
                        passwordError.addClass('d-none');
                    }
                    btn_emg.addClass('d-none').prop('disabled', true);
                }
            });

            // Reset on modal close
            $('#emergency-save-modal').on('hidden.bs.modal', function () {
                passwordInput.val('').removeClass('border-danger').attr('type','password').prop('disabled',false);
                eyeIcon.removeClass('fa-eye-slash').addClass('fa-eye');
                passwordError.addClass('d-none');
                btn_emg.addClass('d-none').prop('disabled', true);
            });
        });
    </script>
@endpush
