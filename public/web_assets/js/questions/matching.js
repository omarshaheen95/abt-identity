$(function () {
    $(".matchOptions, .matchAnswers").sortable({
        connectWith: ".matchConnected",
        start: function(event, ui) {
            // Store the original question-id of the dragged element
            ui.item.data('original-question-id', ui.item.parent().data('question'));
        },
        receive: function(event, ui) {
            // Get the original and target question-id
            var originalQuestionId = ui.item.data('original-question-id');
            var targetQuestionId = $(this).data('question');

            // Check if they match
            if (originalQuestionId != targetQuestionId) {
                // If they don't match, revert the item back to its original list
                $(ui.sender).sortable('cancel');
            }
        }
    }).disableSelection();
});

$(".matchAnswers").droppable({
    drop: function () {

        var index = $(this).attr('data-index');
        //alert($questionId);


        setTimeout(function () {
            $i = 1;
            $('.matchAnswers[data-question = ' + index + '] div span').each(function () {
                //$(this).html($i++ );
            });
        }, 1);
        setTimeout(function () {
            $i = 1;
            $('.matchAnswers[data-index = ' + index + '] div input').each(function () {
                $(this).val(index);
            });

        }, 1);
        //check if $(this) has div.add-answer and div has question-id and not null and not style="visibility: hidden;"
        //if true then remove the div.add-answer and add the div to the matchAnswers
        //if false then do nothing
        var ele = $(this);
        //check ele has div.add-answer
        if (ele.find('div.add-answer').length > 0) {
            //check ele has question-id
            var firstAddAnswer = ele.find('div.add-answer').first();
            var secondAddAnswer = ele.find('div.add-answer').last();
            //check which of first and second add answer has question-id
            if (firstAddAnswer.attr('data-question') != null) {
                // //check which of first and second add answer has not style="visibility: hidden;"
                if (firstAddAnswer.css('visibility') !== 'hidden') {
                    //remove the div.add-answer and add the div to the matchAnswers
                    var question = firstAddAnswer.attr('data-question');
                    $('.matchOptions[data-question = ' + question + ']').append(firstAddAnswer.clone().attr('style', ""));
                    firstAddAnswer.remove();
                }
            } else if (secondAddAnswer.attr('data-question') != null) {
                // //check which of first and second add answer has not style="visibility: hidden;"
                if (secondAddAnswer.css('visibility') !== 'hidden') {
                    //remove the div.add-answer and add the div to the matchAnswers
                    var question = secondAddAnswer.attr('data-question');
                    $('.matchOptions[data-question = ' + question + ']').append(secondAddAnswer.clone().attr('style', ""));
                    secondAddAnswer.remove();
                }
            }
        }
    }
});

$(".matchOptions").droppable({
    drop: function () {
        setTimeout(function () {
            $('.matchOptions div span').each(function (i) {
                var humanNum = i + 1;
                $(this).html('');
            });
        }, 1);
        setTimeout(function () {
            $('.matchOptions div input').each(function (i) {
                var humanNum = i + 1;
                $(this).val('');
            });
        }, 1);


    }
});

$(document).on('click', '.matchOptions div', function () {
    var ele = $(this);
    var $questionId = ele.attr('data-question');
    // var next_answer_box_length = ele.parents().eq(3).find('.matchAnswers div');
    //first matchAnswers dose not have any div
    // var first_empty_answer_box = ele.parents().eq(3).find('.matchAnswers');
    var first_empty_answer_box = ele.parents().eq(3).find('.matchAnswers').filter(function() {
        return $(this).find('div').length === 0;
    }).first();
    //if first_empty_answer_box is not null then add the div to the first_empty_answer_box
    if (first_empty_answer_box.length > 0) {
        ele.find('input').val(first_empty_answer_box.attr('data-index'));
        ele.clone().appendTo(first_empty_answer_box);
        ele.remove();
    }
});

$(document).on('click', '.matchAnswers div', function () {
    var ele = $(this);
    var $questionId = ele.attr('data-question');
    var next_answer_box = ele.parents().eq(8).find('.matchOptions');
    ele.find('input').val("");
    ele.clone().appendTo(next_answer_box);
    ele.remove();

    setTimeout(function () {
        $i = 1;
        $('.matchOptions[data-question= ' + $questionId + '] div input').each(function () {
            $(this).val('');
        });
    }, 10);

});
