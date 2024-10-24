
let options_last_index = {} //to save index when add new input and using container_id as key

$(document).ready(function () {
    //on event submit form
    $(".questions-form").validate();

    $('.questions-form').submit(function (e) {
        e.preventDefault();
        let form_id = '#'+$(this).attr('id');
        //check form is validate
        if ($(form_id).valid()) {
            //get form data
            let formData = new FormData(this);
            //get form action
            let action = $(form_id).attr('action');
            //get form method
            let method = $(form_id).attr('method');
            //show loading from dashboard_assets directory
            showLoadingModal()

            //send ajax
            $.ajax({
                url: action,
                method: method,
                data: formData,
                processData: false,
                contentType: false,
                success: function (data) {
                    //hide loading
                    hideLoadingModal();
                    //show message
                    Swal.fire({
                        text: data.message,
                        icon: "success",
                        buttonsStyling: false,
                        confirmButtonText:OK ,
                        customClass: {
                            confirmButton: "btn font-weight-bold btn-light-primary"
                        }
                    }).then(function () {
                        //reload page
                        location.reload();
                    });
                },
                error: function(response){
                    //hide loading
                    hideLoadingModal();
                    var messages = response.responseJSON.errors;
                    var errorMessage = response.responseJSON.message;
                    //check error messages is array
                    if (Array.isArray(messages)) {
                        //loop on error messages
                        $.each(messages, function (key, value) {
                            //show error message
                            let input = $('input[name="'+value.name+'"]')
                            input.addClass('is-invalid');

                            //add validate message in label
                            let label =  $('label[id="'+value.name+'-error"]')
                            if (label.length === 0){
                                //create label if not found
                                input.parent().append('<label id="'+value.name+'-error" class="is-invalid invalid-feedback" for="'+value.name+'">'+errorMessage+'</label>')
                            }else {
                                //update text if found
                                label.removeAttr('style')
                                label.html(errorMessage)
                            }
                        });
                    }
                    toastr.error(errorMessage);
                }

            });
        }


    });

});

/**
 * Add new option for choose the correct answer question
 * @param start_index
 * @param options_group_id
 * @param question_id
 */
function addNewOption(start_index,options_group_id,question_id) {
    if(options_last_index[options_group_id]){
        options_last_index[options_group_id] += 1
    }else {
        options_last_index[options_group_id] = start_index
    }


   // var option_html_id = 'option_q'+question_id+'_i'+options_last_index[options_group_id]
    let option =
        '<div class="col-12 row mb-2 options-'+question_id+'" id="question'+question_id+'_option'+options_last_index[options_group_id]+'">' +
        ' <div class="col-2">'+
        '<label class="mb-2">'+IMAGE+':</label>'+
        '<div class="d-flex flex-row align-items-center">'+
        '<input type="file" name="question_data['+question_id+'][options]['+options_last_index[options_group_id]+'][image]" class="form-control ">'+
        '</div>'+
        '</div>'+
        '<div class="col-10">'+
        '<div class="d-flex flex-row align-items-center mb-2">' +
        '<label class="m-0 me-2">'+(options_last_index[options_group_id]+1)+':</label>' +
        '<div class="form-check form-check-custom form-check-solid form-check-sm">' +
        '<input required="" class="form-check-input" name="question_data['+question_id+'][correct_answer_index]"' +
        ' type="radio" value="'+options_last_index[options_group_id]+'" class="ml-1" />' +
        '</div>' +
        '<a class="ms-auto font-weight-bold cursor-pointer" style="font-size: 1rem;color: #ff0000"' +
        ' onclick="deleteOptionElement(\''+question_id+'\',\''+options_last_index[options_group_id]+'\')">'+DELETE+'</a>' +
        '</div>' +
        '<input required="" type="text" class="form-control" ' +
        'name="question_data[' + question_id + '][options]['+options_last_index[options_group_id]+'][content]" value="">' +
        '</div>'+
        '</div>'
    $('#'+options_group_id).append(option)
}


/**
 * Add new option for matching question
 * @param start_index
 * @param group_id
 * @param question_id
 */
function addNewMatchOption(start_index,group_id,question_id) {

    if(options_last_index[group_id]){
        options_last_index[group_id] += 1
    }else {
        options_last_index[group_id] = start_index
    }
    console.log('index',group_id)
    console.log('index',options_last_index)
    console.log('march_start_index',start_index)

    var option_html_id = 'match_option_q'+question_id+'_o'+options_last_index[group_id]

    let option =
        ' <div class="form-group row options-'+question_id+'" id="question'+question_id+'_option'+options_last_index[group_id]+'">' +
        '<div class="col-lg-2">' +
        '<label>'+IMAGE+' : </label>' +
        ' <input type="file" name="question_data['+question_id+'][options]['+options_last_index[group_id]+'][image]" class="form-control">' +
        '</div>'+
        '<div class="col-lg-5">' +
        '<label>'+OPTION+':'+(options_last_index[group_id]+1)+'</label>' +
        '<input required class="form-control" name="question_data['+question_id+'][options]['+options_last_index[group_id]+'][content]" value="" type="text">' +
        '</div>' +
        '<div class="col-lg-4">' +
        '<label>'+ANSWER+':</label>' +
        '<input required class="form-control" name="question_data['+question_id+'][options]['+options_last_index[group_id]+'][answer]" type="text">' +
        '</div>'+
        '<button class="btn btn-icon btn-danger mt-6" type="button" onclick="deleteOptionElement(\''+question_id+'\',\''+options_last_index[group_id]+'\')">' +
        '<i class="la la-close la-2"></i>' +
        '</button>' +
        '</div>'
    $('#'+group_id).append(option)
}


/**
 * Add new option for sorting question
 * @param start_index
 * @param options_group_id
 * @param question_id
 */
function addNewSortOption(start_index,options_group_id,question_id) {
    if(options_last_index[options_group_id]){
        options_last_index[options_group_id] += 1
    }else {
        options_last_index[options_group_id] = start_index
    }

    let option =
        '<div class="col-12 row mb-2 options-'+question_id+'" id="question'+question_id+'_option'+options_last_index[options_group_id]+'">' +
        '<div class="col-2">'+
        '<label class="mb-2">'+IMAGE+':</label>'+
        '<div class="d-flex flex-row align-items-center">'+
        '<input type="file" name="question_data[' + question_id + '][options]['+options_last_index[options_group_id]+'][image]" class="form-control ">'+
        '</div>'+
        '</div>'+
        '<div class="col-10">'+
        '<div class="d-flex flex-row align-items-center mb-2">' +
        '<label class="m-0">'+(options_last_index[options_group_id]+1)+':</label>' +
        '<a class="ms-auto font-weight-bold cursor-pointer" style="font-size: 1rem;color: #ff0000"' +
        ' onclick="deleteOptionElement(\''+question_id+'\',\''+options_last_index[options_group_id]+'\')">'+DELETE+'</a>' +
        '</div>' +
        '<input required type="text" class="form-control" ' +
        'name="question_data[' + question_id + '][options]['+options_last_index[options_group_id]+'][content]" value="">' +
        '</div>'+
        '</div>'
    $('#'+options_group_id).append(option)
}


/**
 * Delete option from any question
 * @param question_id
 * @param option_id
 * @param type
 */
function deleteOptionRequest(question_id,option_id,type) {
    if ($('.options-'+question_id).length>3){
        let elementId = 'question'+question_id+'_option'+option_id
        deleteOption(elementId,option_id,type)
    }else {
        messageAlert('The question must have at the least 3 options', "error")
        return '';
    }
}

/**
 * Delete option from any question
 * @param question_id
 * @param option_id
 */
function deleteOptionElement(question_id,option_id) {
    if ($('.options-'+question_id).length>3){
        let elementId = 'question'+question_id+'_option'+option_id
        $('#'+elementId).remove()
    }else {
        messageAlert('The question must have at the least 3 options', "error")
        return '';
    }
}

/**
 * Delete Option Request
 * @param element_id
 * @param option_id
 * @param type
 */
function deleteOption(element_id,option_id,type){

    showAlert(DELETE_MESSAGE_TITLE,DELETE_MESSAGE_BODY,'warning',CONFIRM_TEXT,CANCEL_TEXT,(status)=>{
        if (status){
            $.ajax({
                method: "POST",
                url: DELETE_OPTION_URL,
                data: {
                    'id': option_id,
                    'type': type,
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                },
                success:function (res){
                    if (res.status){
                        $('#'+element_id).remove()
                        messageAlert( res.message, "success")
                    }else {
                        messageAlert( res.message, "error")
                    }
                },
                error:function (request,error){
                    messageAlert( request.responseJSON.message, "error")
                    //console.log('error',request.responseJSON.message)

                }
            })
        }
    })

}


/***
 * Delete question files like [image - audio - question_reader]
 * @param id
 * @param file_type
 */
$('.delete-file').on('click',function() {
    let question = $(this)
    let id = question.data('id')
    let type = question.data('type')
    showAlert(DELETE_MESSAGE_TITLE,DELETE_MESSAGE_BODY,'warning',CONFIRM_TEXT,CANCEL_TEXT,(status)=>{
        if (status){
            $.ajax({
                method: "POST",
                url: DELETE_QUESTION_FILE_URL,
                data: {
                    'id': id,
                    'file_type': type,
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                },
                success:function (res){
                    if (res.status){
                        question.parent().remove()
                        Swal.fire("", res.message, "success")
                    }else {
                        messageAlert( res.message, "error")
                    }
                },
                error:function (request,error){
                    messageAlert(request.responseJSON.message, "error")
                }
            })
        }
    })
})


/**
 * Delete image from  option
 * @param option_id
 * @param type
 */
$('.delete-option-image').on('click',function(){
    let option = $(this)
    let option_id = option.data('option-id')
    let type = option.data('type')
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
                url: DELETE_OPTION_IMAGE_URL,
                data: {
                    'id': option_id,
                    'type': type,
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                },
                success:function (res){
                    if (res.status){
                        // document.location.reload()
                        option.parent().remove()
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
})
