$(document).ready(function () {
    $(function () {
        $(".fillBlankOptions, .fillBlankAnswers").sortable({
            connectWith: ".fillBlankConnected",
            start: function(event, ui) {
                // Store the original question-id of the dragged element
                ui.item.data('original-question-id', ui.item.parent().data('question'));
            },
            receive: function(event, ui) {
                // Get the original and target question-id
                var originalQuestionId = ui.item.data('original-question-id');
                var targetQuestionId = $(this).data('question');

                // Check if they fillBlank
                if (originalQuestionId != targetQuestionId) {
                    // If they don't fillBlank, revert the item back to its original list
                    $(ui.sender).sortable('cancel');
                }
            }
        }).disableSelection();
    });

    $(".fillBlankAnswers").droppable({
        drop: function () {

            var index = $(this).attr('data-index');
            //alert($questionId);


            setTimeout(function () {
                $i = 1;
                $('.fillBlankAnswers[data-question = ' + index + '] div span').each(function () {
                    //$(this).html($i++ );
                });
            }, 1);
            setTimeout(function () {
                $i = 1;
                $('.fillBlankAnswers[data-index = ' + index + '] div input').each(function () {
                    $(this).val(index);
                });
            }, 1);
            //check if $(this) has div.add-answer and div has question-id and not null and not style="visibility: hidden;"
            //if true then remove the div.add-answer and add the div to the fillBlankAnswers
            //if false then do nothing
            var ele = $(this);
            //check ele has div.add-answer
            if (ele.find('div.add-answer').length > 0) {
                //check ele has question-id
                var firstAddAnswer = ele.find('div.add-answer').first();
                var secondAddAnswer = ele.find('div.add-answer').last();
                var question = '';
                //check which of first and second add answer has question-id
                if (firstAddAnswer.attr('data-question') != null) {
                    // //check which of first and second add answer has not style="visibility: hidden;"
                    if (firstAddAnswer.css('visibility') !== 'hidden') {
                        //remove the div.add-answer and add the div to the fillBlankAnswers
                        question = firstAddAnswer.attr('data-question');
                        $('.fillBlankOptions[data-question = ' + question + ']').append(firstAddAnswer.clone().attr('style', ""));
                        firstAddAnswer.remove();
                    }

                } else if (secondAddAnswer.attr('data-question') != null) {
                    // //check which of first and second add answer has not style="visibility: hidden;"
                    if (secondAddAnswer.css('visibility') !== 'hidden') {
                        //remove the div.add-answer and add the div to the fillBlankAnswers
                        question = secondAddAnswer.attr('data-question');
                        $('.fillBlankOptions[data-question = ' + question + ']').append(secondAddAnswer.clone().attr('style', ""));
                        secondAddAnswer.remove();
                    }
                }
            }
        }
    });

    $(".fillBlankOptions").droppable({
        drop: function () {
            setTimeout(function () {
                $('.fillBlankOptions div span').each(function (i) {
                    var humanNum = i + 1;
                    $(this).html('');
                });
            }, 1);
            setTimeout(function () {
                $('.fillBlankOptions div input').each(function (i) {
                    $(this).val('');
                });
            }, 1);
        }
    });

    $(document).on('click', '.fillBlankOptions div', function () {
        var ele = $(this);
        var $questionId = ele.attr('data-question');
        // var next_answer_box_length = ele.parents().eq(3).find('.fillBlankAnswers div');
        //first fillBlankAnswers dose not have any div
        // var first_empty_answer_box = ele.parents().eq(3).find('.fillBlankAnswers');
        var first_empty_answer_box = ele.parents().eq(5).find('.fillBlankAnswers').filter(function() {
            return $(this).find('div').length === 0;
        }).first();
        //if first_empty_answer_box is not null then add the div to the first_empty_answer_box
        if (first_empty_answer_box.length > 0) {
            ele.find('input').val(first_empty_answer_box.attr('data-index'));
            ele.clone().appendTo(first_empty_answer_box);
            ele.remove();
        }
    });

    $(document).on('click', '.fillBlankAnswers div', function () {
        var ele = $(this);
        var $questionId = ele.attr('data-question');
        var next_answer_box = ele.parents().eq(7).find('.fillBlankOptions');
        ele.find('input').val("");
        ele.clone().appendTo(next_answer_box);
        ele.remove();

        setTimeout(function () {
            $i = 1;
            $('.fillBlankOptions[data-question= ' + $questionId + '] div input').each(function () {
                $(this).val('');
            });
        }, 10);

    });
})
