function showAlert(title,message,icon,confirmButtonText,cancelButtonText=null,callback) {


    let swalBody = {
        title: title,
        text: message,
        icon: icon,
        confirmButtonText: confirmButtonText
    }

    if (cancelButtonText){
        swalBody['showCancelButton']=true
        swalBody['cancelButtonText']=cancelButtonText
    }

    Swal.fire(swalBody).then(
        function (result) {
            if (result.isConfirmed) {
                callback(true)
            }else {
                callback(false)
            }
        }
    )

}
function messageAlert(message,icon){
    Swal.fire("", message, icon)
}


function showLoadingModal(title=null,message=null,progress_color='#FFCB58'){
    let lang = $('html').attr('lang');
    if (!title){
        if (lang==='ar'){
            title = 'جاري تنفيذ طلبك';
        }else {
            title =  'The request is being executed'
        }
    }
    if (!message){
        if (lang==='ar'){
            message = 'الرجاء الإنتظار...';
        }else {
            message =  'Please wait ...'
        }
    }
    $('body').append('<div class="modal fade" id="loading-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">\n' +
        '    <div class="modal-dialog modal-dialog-centered">\n' +
        '        <div class="modal-content">\n' +
        '            <div id="loading-modal-close" class="btn btn-icon btn-sm btn-active-light-primary ms-2 d-none" data-bs-dismiss="modal" aria-label="Close">\n' +
        '                <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>\n' +
        '            </div>\n' +
        '            <div class="modal-body">\n' +
        '                <div class="d-flex flex-column align-items-center p-5" >\n' +
        '                    <div class="fw-bold fs-3 mb-10">'+title+'</div>\n' +
        '                    <div  style="height: 65px;width: 65px;">' +
        '                    <span class="svg-icon svg-icon-5x">' +
        '                       <svg xmlns:svg="http://www.w3.org/2000/svg" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.0" width="64px" height="64px" viewBox="0 0 128 128" xml:space="preserve"><rect x="0" y="0" width="100%" height="100%" fill="#FFFFFF"/><g><linearGradient id="linear-gradient"><stop offset="0%" stop-color="#ffffff"/><stop offset="100%" stop-color="'+progress_color+'"/></linearGradient><path d="M63.85 0A63.85 63.85 0 1 1 0 63.85 63.85 63.85 0 0 1 63.85 0zm.65 19.5a44 44 0 1 1-44 44 44 44 0 0 1 44-44z" fill="url(#linear-gradient)" fill-rule="evenodd"/><animateTransform attributeName="transform" type="rotate" from="0 64 64" to="360 64 64" dur="1080ms" repeatCount="indefinite"/></g></svg>' +
        '                    </span>' +
        '                 </div>\n' +
        '                    <div class="mt-8 text-gray-700">'+message+'</div>\n' +
        '                </div>\n' +
        '            </div>\n' +
        '        </div>\n' +
        '    </div>\n' +
        '</div>\n')
    $('#loading-modal').modal('show')
}

function hideLoadingModal() {
    let l_modal = $('#loading-modal')
    l_modal.modal('hide')
    // l_modal.modal('dispose')
    l_modal.remove()
    $('.modal-backdrop').remove()
    $('body').removeClass('modal-open')
    $('body').removeAttr('style')
}
function copyToClipboard(element) {
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val($(element).attr('data-clipboard-text')).select();
    document.execCommand("copy");
    $temp.remove();

    if ($('html').attr('lang') === 'ar') {
        toastr.options = {
            "positionClass": "toastr-bottom-left",
            "showDuration": "100",
            "hideDuration": "1000",
            "timeOut": "1000",
            "extendedTimeOut": "1000",
        }
        toastr.info('تم النسخ إلى الحافظة');
    } else {
        toastr.options = {
            "positionClass": "toastr-bottom-right",
            "showDuration": "100",
            "hideDuration": "1000",
            "timeOut": "1000",
            "extendedTimeOut": "1000",
        }
        toastr.info('Copied to clipboard');
    }

}
//select or unselect options
function onSelectAllClick(selectId) {
    let select = $('#' + selectId);
    //select all when all is clicked
    $(select).on("select2:select select2:unselect", function (e) {
        //e.preventDefault()
        let data = e.params.data;
        console.log(e.params)
        let options = $(select).find('option');
        if (e.params.data.element.id === 'all') {
            if (data.selected) { //select
                options.each(function () {
                    $(this).prop('selected', true)
                })
                $(select).trigger('change');
            } else { //unselect
                $(select).val([])
                $(select).trigger('change');
            }
        }

    });

}

if ($('.remove_spaces').length > 0){
    $(document).on('keyup','.remove_spaces',function () {
        let value = $(this).val()
        if (value){
            value = value.replace(/\u00A0/g, ' ').replace('  ', ' ').trim();
            $(this).val(value)
        }
    })
}

function getAndSetDataOnSelectChange(on_change_name, to_select, getURL, multiple = 0, otherData = [], callback = null) {
    if (typeof getURL !== 'undefined') {
        $('select[name="' + on_change_name + '"]').change(function () {
            var id = $(this).val();
            if (id) {
                var url = getURL;

                var data = {
                    multiple: multiple
                }


                if (!Array.isArray(id)){
                    url = url.replace(':id', id);
                }else {
                    let name = on_change_name.replace('[]','')
                    data[name] = id; //id is array
                }


                if (otherData.length > 0) {
                    $.each(otherData, function (key, value) {
                        console.log($('#' + value).val());
                        data[value] = $('#' + value).val();
                    });
                }
                $.ajax({
                    type: "get",
                    url: url,
                    data: data,
                }).done(function (data) {
                    if (typeof callback === 'function') {
                        callback(true);
                    }
                    $('select[name="' + to_select + '"]').html(data.data);
                    $('select[name="' + to_select + '"]').select2({
                        'width':'100%',
                    });

                });
            }
        });
    }
}
