
//cache result in session
function saveResult(id) {
    // let value = $('#'+id).val()
    // sessionStorage.setItem(id,value)
    sessionStorage.clear()
    $('input[type="radio"]:checked').each(function(index,item){
        sessionStorage.setItem(item.id,'1')
    });
    $("input[type='text'], textarea").each(function(index,item){
        if($(this).val()){
            sessionStorage.setItem(item.id,$(this).val())
        }
    });
}

function getAndSetResults() {
    $('input[type="radio"]').each(function(index,item){
        let result = sessionStorage.getItem(item.id)
        if (result){
            $(this).attr('checked', 1);
        }
    });
    $("input[type='text'], textarea").each(function(index,item){
        let result = sessionStorage.getItem(item.id)
        if (result){
            $(this).val(result)
        }
    });
}

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
function examFormSubmit(){
    if (validation()){
        $("#submit-term").modal("hide");
        $("#exam-form").addClass("d-none");
        $("#save-form").removeClass("d-none");
        $("#exams").submit();
    }else {
        $("#submit-term").modal("hide");
        showToastify("You must answered for all questions", "error");

    }
}


$(document).ready(function () {

    /*---------------------------------------------------
        timer
    ---------------------------------------------------*/
    let clock = $('#clock');
    if (clock.length > 0 && typeof TIME !== 'undefined' && TIME) {
        var qnt = TIME,
            val = (qnt * 60 * 60 * 1000),
            selectedDate = new Date().valueOf() + val;

        clock.countdown(selectedDate.toString())
            .on('update.countdown', function (event) {
                var format = '%H:%M:%S';
                $(this).html(event.strftime(format));
                $("#timer-ago").val(event.strftime(format));
                //localStorage.setItem("timer_val", event.offset.totalSeconds);
            })
            .on('finish.countdown', function (event) {
                $(this).parent().addClass('disabled').html('This Time has expired!');
                showToastify("The Time has expired!", "error");
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



    /*---------------------------------------------------
        word count
    ---------------------------------------------------*/

    $(document).on("keyup", ".textarea textarea", function () {

        var value = $.trim($(this).val()),
            count = value === '' ? 0 : value.split(' ').length;
        $(this).parent().find(".word-count span").text(count);
    });


});

