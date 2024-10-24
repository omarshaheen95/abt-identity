$(document).ready(function () {
    getMarksSum()
})

$('input').change(function (e) {
    getMarksSum() //refresh marks when input value changed
})

//submit questions form
$('#btn_save_questions').click(function (){
    let marks_sum = getMarksSum();
    // let subject_marks_sum = getSubjectSum();
    let subject_all_marks_sum = getSumAllSubjects();

    if (marks_sum>100 || marks_sum<100){
        Swal.fire({
            text: ERROR_MESSAGE_BODY,
            icon: "error",
            confirmButtonText: OK_TEXT,
        })
    }else if (!subject_all_marks_sum){
        return false;
        // Swal.fire({
        //     text: SUBJECT_MARKS_SUM_MESSAGE,
        //     icon: "error",
        //     confirmButtonText: OK_TEXT,
        // })
    }else {

        Swal.fire({
            title: ADD_QUESTION_MESSAGE_TITLE,
            text: ADD_QUESTION_MESSAGE_BODY,
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: CONFIRM_TEXT,
            cancelButtonText: CANCEL_TEXT,
        }).then(function (result) {
            if (result.isConfirmed) {
                $('#questions_form').submit()
            }}
        )
    }

})


//add dynamic questions
$('#btn_add_question').click(function () {

    let question_type = $('#question_type').val()
    let question_subject = $('#question_subject').val()
    let question_mark = parseFloat($('#question_mark').val())
    let marks_sum = getMarksSum();
    console.log(question_type)
    if (!question_type){
        alert(PLEASE_SELECT_TYPE)
        return '';
    }
    if (!question_mark && !question_mark<=100){
        alert(PLEASE_ADD_MARK)
        return '';
    }

    if (marks_sum>100 || marks_sum+question_mark>100){
        alert(MAX_MARKS)
        return '';
    }



    let randomId = Date.now()
    let question = `
        <div class="d-flex align-items-center pt-6 gap-2" id="container_${QUESTIONS_COUNT}">
            <strong class="font-size-h6 align-self-end mb-3 mr-3">${QUESTIONS_COUNT + 1}-</strong>
            <div class="d-flex flex-column w-25 mr-2">
                <label for="select_${QUESTIONS_COUNT}">${QUESTION_TYPE} :</label>
                <select class="form-control form-select" data-control="select2" data-hide-search="true" id="select_${QUESTIONS_COUNT}" name="questions[${randomId}][type]">
                    ${getTypesSelect(question_type)}
                </select>
            </div>
            <div class="d-flex flex-column w-300px mr-2">
                <label for="select_subject_${QUESTIONS_COUNT}">${QUESTION_SUBJECT} :</label>
                <select class="form-control subject_type form-select s-subject" data-control="select2" data-hide-search="true" id="select_subject_${QUESTIONS_COUNT}" name="questions[${randomId}][subject_id]">
                    ${getSubjectsSelect(question_subject)}
                </select>
            </div>
            <div class="d-flex flex-column w-100px">
                <label for="marks_input_${QUESTIONS_COUNT}">${QUESTION_MARK} :</label>
                <input class="form-control mark subject${question_subject}-mark" placeholder="Marks" type="number" id="marks_input_${QUESTIONS_COUNT}" name="questions[${randomId}][mark]" value="${question_mark}">
            </div>
            <a id="btn_delete" onclick="deleteQuestion(${QUESTIONS_COUNT})" class="btn btn-icon btn-danger align-self-end mb-2 ml-3" style="height: 30px;width: 30px">
                <i class="fa fa-trash font-size-h6"></i>
            </a>
        </div>
    `;
    $('#questions_form').append(question)
    $('#question_type').val(null).trigger('change');
    $('#question_subject').val(null).trigger('change');
    $('#question_mark').val('')

    QUESTIONS_COUNT++;

    getMarksSum()

})

function deleteQuestion(index){
    //delete from local
    $('#container_'+index).remove()
    getMarksSum()

}

function deleteQuestionRequest(index){
    let question_id = $('#question_id_'+index).val()

    Swal.fire({
        title: DELETE_MESSAGE_TITLE,
        text: DELETE_MESSAGE_BODY,
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: CONFIRM_TEXT,
        cancelButtonText: CANCEL_TEXT,
    }).then(function (result) {
        if (result.isConfirmed) {
            $.ajax({
                method: "POST",
                url: DELETE_QUESTION_URL,
                data: {
                    'id': question_id,
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                },
                success:function (res){
                    if (res.status){
                        console.log('delete question db');
                        $('#container_'+index).remove()
                        setQuestionCount(-1)
                        getMarksSum()
                        Swal.fire("", res.message, "success")
                    }else {
                        Swal.fire("", res.message, "error")
                    }
                },
                error:function (request,error){
                    Swal.fire("", request.responseJSON.message, "error")
                    //console.log('error',request.responseJSON.message)

                }
            })


        }

    });
}

function getSumAllSubjects() {
    // Get all inputs that have the class 'mark'
    let status = true;
    let subjects_marks = {};

    $('.mark').each((index, item) => {
        // Get the value of the first previous select that has class 's-subject'
        let subject_id = $(item).parent().prev().find('.subject_type').val();

        // Initialize or accumulate marks for the subject
        if (subjects_marks[subject_id]) {
            subjects_marks[subject_id] += parseFloat($(item).val());
        } else {
            subjects_marks[subject_id] = parseFloat($(item).val());
        }
    });

    // Check the total marks for each subject against its limit
    questionsSubjects.forEach(function (subject) {
        let totalMarks = subjects_marks[subject.value] || 0; // Get the total marks or default to 0

        if (totalMarks > subject.mark || totalMarks < subject.mark) {
            status = false;
            console.log(subjects_marks);
            console.log(subject.name);
            console.log('Subject ID: ' + subject.value);
            console.log('Subject ' + subject.name + ' total marks: ' + totalMarks);

            // Show error alert
            Swal.fire({
                text: 'The marks sum for ' + subject.name + ' subject must be: ' + subject.mark,
                icon: "error",
                confirmButtonText: 'OK',
            });

            // Exit the loop once an error is found
            return false; // This will break the forEach loop
        }
    });

    return status;
}

function getMarksSum(){
    let marks = 0;
    let questions_count =0 ;
    $('.mark').each((index , item)=>{
        marks += parseFloat($(item).val());
        questions_count +=1
    })
    $('#questions_marks').text('').append(marks) //refresh marks sum in html text
    // console.log('marks',marks)
    $('#questions_count').text('').append(questions_count)

    return marks;
}

function getTypesSelect(question_type){
    let options = []
    for (let i = 0; i < questionsTypes.length; i++){
        if (questionsTypes[i].value === question_type){
           // console.log('question type',questionsTypes[i].value+'='+question_type)

            options.push('<option value="'+questionsTypes[i].value+'" selected>'+questionsTypes[i].name+'</option>')
        }else {
            options.push('<option value="'+questionsTypes[i].value+'">'+questionsTypes[i].name+'</option>')
        }
    }
    return options;
}
function getSubjectsSelect(question_subject){
    let options = []
    for (let i = 0; i < questionsSubjects.length; i++){
        if (questionsSubjects[i].value === question_subject){
            options.push('<option value="'+questionsSubjects[i].value+'" selected>'+questionsSubjects[i].name+'</option>')
        }else {
            options.push('<option value="'+questionsSubjects[i].value+'">'+questionsSubjects[i].name+'</option>')
        }
    }
    return options;
}
