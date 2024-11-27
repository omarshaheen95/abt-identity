
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


//-----------------------------------------------------------------------------
function examFormSubmit(){
    $("#exams").submit();
    $("#submit-term").modal("hide");
    $("#exam-form").addClass("d-none");
    $("#save-form").removeClass("d-none");
}

function openCalculator(){
    $("#calculator-modal").modal("show");
}
$(document).ready(function () {

    setMatchingOptionsHeight() //for responsive height for matching question
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


    /*---------------------------------------------------
        sortable
    ---------------------------------------------------*/



    $(function () {
        $(".sortable1, .sortable2").sortable({
            connectWith: ".connectedSortable"
        }).disableSelection();
    });

    $(".sortable2").droppable({
        drop: function () {
            $questionId = $(this).attr('question-id');
            setTimeout(function () {
                $i = 1;
                $('.sortable2[question-id = ' + $questionId + '] li span').each(function () {
                    //$(this).html($i++ );
                });
            }, 1);
            setTimeout(function () {
                $i = 1;
                $('.sortable2[question-id = ' + $questionId + '] li input').each(function () {
                    $(this).val($i++);
                });
            }, 1);
        }
    });

    $(".sortable1").droppable({
        drop: function () {
            setTimeout(function () {
                $('.sortable1 li span').each(function (i) {
                    var humanNum = i + 1;
                    $(this).html('');
                });
            }, 1);
            setTimeout(function () {
                $('.sortable1 li input').each(function (i) {
                    var humanNum = i + 1;
                    $(this).val('');
                });
            }, 1);
        }
    });

    $(document).on('click', '.sortable1 li', function () {
        var ele = $(this);
        let question_id = $(this).closest("ul").attr("question-id")
        var next_answer_box_length = ele.parent().parent().parent().find('.sortable2 li').length + 1;
        var next_answer_box = ele.parent().parent().parent().find('.sortable2');
        let matchingEle = ele.parent().parent().parent().find('.matching')
        ele.find('input').val(next_answer_box_length);
        ele.clone().appendTo(next_answer_box);
        ele.remove();
        setTimeout(function () {
            let count = 1;
            $('.sortable2[question-id = ' + question_id + '] li input').each(function (i) {
                $(this).val(count);
                count++;
            });
        }, 1);

        if (matchingEle.hasClass('matching')){ //get just container has (matching) class and set height
            matchOptionsHeight(matchingEle)
        }
    });

    $(document).on('click', '.sortable2 li', function () {
        let question_id = $(this).closest("ul").attr("question-id")
        var ele = $(this);
        var next_answer_box = ele.parent().parent().parent().parent().parent().parent().parent().find('.sortable1');
        ele.find('input').val("")
        if (ele.parent().parent().parent().parent().hasClass('matching')){
            ele.css('height','auto');
        }
        ele.clone().appendTo(next_answer_box);
        ele.remove();
        setTimeout(function () {
            let count = 1;
            $('.sortable2[question-id = ' + question_id + '] li input').each(function (i) {
                $(this).val(count);
                count++;
            });
        }, 1);
    });


    /*---------------------------------------------------
       formula
   ---------------------------------------------------*/
    let formula  = $('.formula-input');
    if (formula.length>0){
        //each element has class .formula-input
        formula.each(function () {
            var mathInput = $(this).val();
            var previewElementID = $(this).next().find('.formula-preview').attr('id');
            // console.log(previewElementID);
            var previewElement = document.getElementById(previewElementID);
            previewElement.innerHTML = "\\(" + mathInput + "\\)";
            MathJax.Hub.Queue(["Typeset", MathJax.Hub, previewElement]);
        });
    }

});

//set height for element children
function matchOptionsHeight(elm) {
    let heights = [];
    let count = 0;
    let matchContent = elm.children()

    //get height for each match option
    let ul22 = matchContent.eq(1).children().find('ul').eq(1).find('li')
    ul22.each(function () {
        heights.push($(this).outerHeight())
    })

    //set height for each match content or image
    let ul11 = matchContent.eq(0).children().find('li')
    ul11.each(function () {
        if (heights[count]>0){
            $(this).css('height',heights[count])
        }
        count++
    })
    //set height for each match option  background [the div behind match option]
    count  = 0;
    let ul33 = matchContent.eq(1).children().find('ul').eq(0).find('li')
    ul33.each(function () {
        if (heights[count]>0){
            $(this).css('height',heights[count])
        }
        count++
    })
}
//set height for all element has this class in exam
function setMatchingOptionsHeight() {
    $('.matching').each(function () {
       matchOptionsHeight($(this))
    })
}
