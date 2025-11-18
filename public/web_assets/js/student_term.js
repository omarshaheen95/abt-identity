

//show or hide file or textarea input
function showArticleQTextarea(question_id){
    $('#article_text_'+question_id).removeClass('d-none')
    $('#article_file_'+question_id).addClass('d-none')
}

function showArticleQFileInput(question_id){
    $('#article_text_'+question_id).addClass('d-none')
    $('#article_file_'+question_id).removeClass('d-none')
}
//Validation
function validation(){
    let errors = 0
    $('.question-card').each(function () {
        let questionId = $(this).attr('data-id');
        let questionType = $('input[name="questions['+questionId+'][type]"]').val()
        // console.log(questionId+'***'+questionType)

        switch (questionType) {
            case 'true_false':{
                let hasAnswer = $(this).find('.answer-group input[type="radio"]:checked').length > 0;
                if (!hasAnswer){
                    $(this).find(".answer-group").addClass("border border-danger");
                }else {
                    $(this).find(".answer-group").removeClass("border border-danger");
                }
                if (!hasAnswer){
                    errors++;
                }
                break;
            }
            case 'multiple_choice':{
                let hasAnswer ;
                if ($(this).find('.answer-group input[type="radio"]').length > 0){
                    hasAnswer = $(this).find('.answer-group input[type="radio"]:checked').length > 0
                }

                if (!hasAnswer){
                    $(this).find(".answer-group").addClass("border border-danger");
                    errors++;
                }else {
                    $(this).find(".answer-group").removeClass("border border-danger");
                }

                break;
            }case 'matching':{
                let hasAnswer = $(this).find('.matchOptions .matching-answer-input').length === 0
                if (!hasAnswer){
                    $(this).find(".row .item-container").addClass("border border-danger p-3");
                    errors++;
                }else {
                    $(this).find(".row .item-container").removeClass("border border-danger p-3");
                }

                break;
            }case 'sorting':{
                let hasAnswer = $(this).find('.sortOptions .sort-answer-input').length === 0
                if (!hasAnswer){
                    $(this).find(".row .item-container").addClass("border border-danger p-3");
                    errors++;
                }else {
                    $(this).find(".row .item-container").removeClass("border border-danger p-3");
                }

                break;
            }
            case 'fill_blank':{
                let hasAnswer = $(this).find('.fillBlankOptions .blank-answer-input').length === 0
                if (!hasAnswer){
                    $(this).find(".blankAnswers").addClass("border border-danger p-3");
                    errors++;
                }else {
                    $(this).find(".blankAnswers").removeClass("border border-danger p-3");
                }

                break;
            }
            case 'article':{
                let hasAnswer =
                    $.trim($(this).find('.answer-box textarea').val()).length === 0 &&
                    $(this).find('.answer-box .files-upload')[0].files.length === 0

                if (hasAnswer){
                    $(this).find(".answer-box").addClass("border border-danger p-3");
                    errors++;
                }else {
                    $(this).find(".answer-box").removeClass("border border-danger p-3");
                }

                break;
            }
        }
    })
    return errors <= 0;
}

//-----------------------------------------------------------------------------
function examFormSubmit(with_validation=true){

        let valid = true;
        if (with_validation){
            valid = validation();
        }

        if ($("#submit-term").length>0){
            $("#submit-term").modal("hide");
        }

        if (valid){
            // $("#exam-form").addClass("d-none");
            // $("#save-form").removeClass("d-none");
            // $("#exams").submit();
            let form = $('#exams');
            var URL = form.attr('action');
            var METHOD = form.attr('method');
            var fd = new FormData(form[0]);

            $.ajax({
                type: METHOD,
                url: URL,
                data: fd,
                processData: false,
                contentType: false,
                success: function (data) {
                    showToastify(data.message, 'success')
                    setTimeout(function () {
                        window.location.replace(data.data);
                    },1000)
                },
                error: function (xhr, status, error) {
                    let message =  $('html').attr('lang')==='ar'?'خطأ في حفظ الاحتبار!':'Error in saving the assessment!'
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    showToastify(message, "error");

                    if(xhr.responseText){
                        let error_message = JSON.parse(xhr.responseText);
                        if(error_message.status === 401){
                            location.reload();
                        }
                    }
                }

            })
        }else {
            if ($("#submit-term").length>0){
                $("#submit-term").modal("hide");
            }
            let message =  $('html').attr('lang')==='ar'?'يجب الإجابة على جميع الأسئلة':'You must answered for all questions'
            showToastify(message, "error");
        }


}


$(document).ready(function () {

    /*---------------------------------------------------
        timer
    ---------------------------------------------------*/
    //function to add minutes to the current time
    function addMinutes(date, minutes) {
        var new_time = new Date(date.getTime() + minutes*60000);
        return new_time.valueOf()
    }

    if (typeof STORAGE_KEY !=='undefined' && typeof REMIND_TIME !=='undefined' && typeof MAIN_TIME !=='undefined' && typeof studentIsDemo !=='undefined'){
        //check spent_time in local storage if it exists and has a value
        if(window.localStorage.getItem(STORAGE_KEY) !== null && !studentIsDemo)
        {
            var spend_time = window.localStorage.getItem(STORAGE_KEY);
            //console.log('spent times: ' + spend_time);
            //minus spent time from the main time
            REMIND_TIME = MAIN_TIME - spend_time;
        }else{
            REMIND_TIME = MAIN_TIME;
        }

        addMinutes(new Date(), REMIND_TIME);
        //console.log(addMinutes(new Date(), REMIND_TIME));

        var selectedDate = addMinutes(new Date(), REMIND_TIME);

        var spent_time = 0;
        var last_spent = 0;

        $('#clock').countdown(selectedDate.toString())
            .on('update.countdown', function (event) {
                var format = '%H:%M:%S';
                $(this).html(event.strftime(format));
                $("#timer-ago").val(event.strftime(format));
                //calculate spent time by seconds and convert it to minutes
                last_spent = MAIN_TIME - event.offset.minutes
                if(last_spent !== spent_time)
                {
                    //save spent time in local storage
                    spent_time = last_spent;
                    window.localStorage.setItem(STORAGE_KEY, last_spent);
                    //console.log('spent time: ' + last_spent);
                }

            })
            .on('finish.countdown', function (event) {
                $(this).parent().addClass('disabled').html('The assessment time has expired!');
                showToastify("The Time has expired!", "error");
                //reset the time in local storage
                // window.localStorage.removeItem(STORAGE_KEY);
                if (typeof SUBMIT_ASSESSMENT_WHEN_TIMEOUT !=='undefined' && SUBMIT_ASSESSMENT_WHEN_TIMEOUT){
                    examFormSubmit(false)
                }

            });
    }


    /*---------------------------------------------------
        navigation
    ---------------------------------------------------*/
    $('.btn_nav').on('click', function () {
        $('.btn_nav_active').each(function () {
            $(this).removeClass('btn_nav_active')
        })
        $(this).addClass('btn_nav_active')
    })

    var allNextBtn = $('.exam-view .btn-next'),
        allPrevBtn = $('.exam-view .btn-prev'),
        submitBtn = $('.exam-view .btn-submit'),
        leaveBtn = $('.leave-exam');

    allNextBtn.click(function () {
        let tab_index = $(this).data('tab-index') + 1
        // $('#btn_nav_'+tab_index)[0].click();
        document.getElementById('btn_nav_' + tab_index).click()

    });

    allPrevBtn.click(function () {
        let tab_index = $(this).data('tab-index') - 1
        document.getElementById('btn_nav_' + tab_index).click()
        // $('#btn_nav_'+tab_index)[0].click();
    });

    submitBtn.click(function (e) {
        e.preventDefault();
        $("#submit-term").modal("show");
    });

    leaveBtn.click(function (e) {
        e.preventDefault();
        $("#leave-term-modal").modal("show");
    });

    //Emergency Save --------------------------------------------------------------------------------------------
    // Emergency save keyboard shortcut (Ctrl+Space)
    $(document).keydown(function(e) {
        if (e.ctrlKey && e.keyCode === 32) {
            e.preventDefault();
            $('#emergency-save-modal').modal('show');
        }
    });

    // Emergency save button click
    $('#confirm-emergency-save').on('click', function() {
        $('#emergency-save-modal').modal('hide');
        $('#emergency-save-indicator').fadeIn(200);
        $('#emergency-save-input').val(1)
        setTimeout(function() {
            examFormSubmit(false); // Submit without validation
        }, 500);
    });

    /*---------------------------------------------------
        word count
    ---------------------------------------------------*/

    $(document).on("keyup", ".textarea textarea", function () {

        var value = $.trim($(this).val()),
            count = value === '' ? 0 : value.split(' ').length;
        $(this).parent().find(".word-count span").text(count);
    });


});

