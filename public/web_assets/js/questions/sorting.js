$(function () {
    $(".sortAnswers, .sortOptions").sortable({
        connectWith: ".sortConnected",
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
            } else {
                // If they match, allow the drop and update the inputs
                setTimeout(function () {
                    $i = 1;
                    $('.sortAnswers[data-question = ' + targetQuestionId + '] li input').each(function () {
                        $(this).val($i++);
                    });
                }, 1);
            }
        }
    }).disableSelection();
});

$(".sortAnswers").droppable({
    drop: function () {

        $questionId = $(this).attr('data-question');
        //alert($questionId);


        setTimeout(function () {
            $i = 1;
            $('.sortAnswers[data-question= ' + $questionId + '] div span').each(function () {
                //$(this).html($i++ );
            });
        }, 1);
        setTimeout(function () {
            $i = 1;
            $('.sortAnswers[data-question= ' + $questionId + '] div input').each(function () {
                $(this).val($i++);
            });
        }, 1);
    }
});

$(".sortOptions").droppable({
    drop: function () {
        setTimeout(function () {
            $('.sortOptions div span').each(function (i) {
                var humanNum = i + 1;
                $(this).html('');
            });
        }, 1);
        setTimeout(function () {
            $('.sortOptions div input').each(function (i) {
                var humanNum = i + 1;
                $(this).val('');
            });
        }, 1);
    }
});

$(document).on('click', '.sortOptions div', function () {
    var ele = $(this);
    var $questionId = ele.parent().attr('data-question');
    var next_answer_box_length = ele.parent().parent().parent().find('.sortAnswers div').length + 1;
    var next_answer_box = ele.parent().parent().parent().find('.sortAnswers');
    ele.find('input').val(next_answer_box_length);
    ele.clone().appendTo(next_answer_box);
    ele.remove();

    setTimeout(function () {
        $i = 1;
        $('.sortAnswers[data-question= ' + $questionId + '] div input').each(function () {
            $(this).val($i++);
        });
    }, 1);
});

$(document).on('click', '.sortAnswers div', function () {
    var ele = $(this);
    var $questionId = ele.parent().attr('data-question');
    var next_answer_box = ele.parents().eq(8).find('.sortOptions');
    ele.find('input').val("");
    ele.clone().appendTo(next_answer_box);
    ele.remove();

    setTimeout(function () {
        $i = 1;
        $('.sortOptions[data-question= ' + $questionId + '] div input').each(function () {
            $(this).val('');
        });
        $('.sortAnswers[data-question= ' + $questionId + '] div input').each(function () {
            $(this).val($i++);
        });
    }, 10);

});
