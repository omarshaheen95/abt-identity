var table = $('#datatable');
let lang = $('html').attr('lang');
let columnDefs = [];
let createdRow = null;

table.addClass('table-bordered') //make table bordered

let checkbox = {
    targets: 0,
    width: '30px',
    className: 'dt-left',
    orderable: false,
    render: function (data, type, full, meta) {
        return '<div class="form-check form-check-custom form-check-solid form-check-sm">' +
            '   <input value="' + data + '" name="rows[]" class="form-check-input checkable checkbox" type="checkbox" value="1" id="flexCheckDefault"/>' +
            '     <label class="form-check-label " for="flexCheckDefault">' +
            '      </label></div>';
    }

}

columnDefs.push(checkbox)


if(typeof COLUMN_DEFS!=='undefined'){
    COLUMN_DEFS.forEach((item)=>{
        columnDefs.push(item)
    })
}
if(typeof CREATED_ROW!=='undefined'){
    createdRow = CREATED_ROW;
}


table.DataTable({
    processing: true,
    serverSide: true,
    responsive: true,
    ordering: false,
    searching: false,
    //scrollX: true,
    // DOM Layout settings


    lengthMenu: [5, 10, 25, 50],

    pageLength: 10,

    language: lang==='ar'?{'url':DatatableArabicURL}:{'lengthMenu': 'Display _MENU_'},

    ajax: {
        url: TABLE_URL,
        data: function (d) {
            var frm_data = $('#filter').serializeArray();
            if (frm_data){
                $.each(frm_data, function (key, val) {
                    d[val.name] = val.value;
                });
                //get value directly from element has [direct-value] class
                $('select.direct-value').each(function () {
                    let val = $(this).val()
                    let name = $(this).attr('name');
                    delete d[name];
                    name = name.replace("[]", "");

                    if (val instanceof Array){
                        if (val.length>0){
                            d[name] = $(this).val();
                        }
                    }else if (val){
                        d[name] = $(this).val();
                    }

                })

            }
            console.log('form',d)
        }
    },
    headerCallback: function (thead, data, start, end, display) {
        thead.getElementsByTagName('th')[0].innerHTML = `
                <div class="form-check form-check-custom form-check-solid form-check-sm">
                    <input class="form-check-input group-checkable" type="checkbox" id="flexCheckDefault"/>
                    <label class="form-check-label " for="flexCheckDefault">
                    </label>
                </div>`;

    },
    columnDefs: columnDefs,
    createdRow:createdRow,
    columns: TABLE_COLUMNS,

});


table.on('change', '.group-checkable', function () {
    var set = $(this).closest('table').find('td:first-child .checkable');
    var checked = $(this).is(':checked');
    $(set).each(function () {
        if (checked) {
            $(this).prop('checked', true);
            $(this).closest('tr').addClass('active');
        } else {
            $(this).prop('checked', false);
            $(this).closest('tr').removeClass('active');
        }
    });
    checkedVisible()
});

table.on('change', 'tbody tr  .checkbox', function () {
    $(this).parents('tr').toggleClass('active');
    checkedVisible()
});

table.on('draw.dt', function () {
    checkedVisible(false);
});

$(document).on('click', '.delete_row', (function () {
    var row_id = [$(this).attr('data-id')];
    if (typeof DELETE_URL !=="undefined"){
        Swal.fire({
            title: DELETE_MESSAGE,
            text: DELETE_SUB_MESSAGE,
            confirmButtonColor: '#ff091d',
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: CONFIRM_TEXT,
            cancelButtonText: CANCEL_TEXT,
        }).then(function (result) {
            if (result.isConfirmed) {
                showLoadingModal()
                let request_data = {
                    'row_id': row_id,
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    '_method': 'DELETE',
                }
                if ($('#delete_students_with_file').is(":checked")){
                    request_data['delete_students'] = true
                }

                $.ajax({
                    type: "POST", //we are using GET method to get data from server side
                    url: DELETE_URL, // get the route value
                    data: request_data, //set data
                    success:function (data) {
                        hideLoadingModal()
                        if(data.success)
                        {
                            table.DataTable().draw(true);
                            Swal.fire("", data.message, "success")
                        }else{
                            Swal.fire("", data.message, "error")
                        }
                    },
                    error:function (error) {
                        hideLoadingModal()
                        let message = error.responseJSON.message
                        Swal.fire("", message, "error")
                    }
                })


            }
        });
    }

}));
$(document).on('click', '#delete_rows', (function () {
    var row_id = [];
    if (typeof DELETE_URL !=="undefined"){
        $("input:checkbox[name='rows[]']:checked").each(function () {
            row_id.push($(this).val());
        });
        Swal.fire({
            title: DELETE_MESSAGE,
            text: DELETE_SUB_MESSAGE,
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: CONFIRM_TEXT,
            cancelButtonText: CANCEL_TEXT,
        }).then(function (result) {
            if (result.isConfirmed) {
                showLoadingModal()
                let request_data = {
                    'row_id': row_id,
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    '_method': 'DELETE',
                }

                if ( $('#delete_students_with_file').prop('checked',true)){
                    request_data['delete_students'] = true
                }

                $.ajax({
                    type: "POST",
                    url: DELETE_URL,
                    data:request_data , //set data
                    success:function (data) {
                        hideLoadingModal()
                        if(data.success)
                        {
                            $('.group-checkable').prop('checked', false);
                            checkedVisible(false)
                            table.DataTable().draw(false);
                            Swal.fire(
                                "",
                                data.message,
                                "success"
                            )
                        }else{
                            Swal.fire(
                                "",
                                data.message,
                                "error"
                            )
                        }
                    },
                    error:function (error) {
                        hideLoadingModal()
                        let message = error.responseJSON.message
                        Swal.fire("", message, "error")
                    }
                });

            }

        });
    }

}));
$('#kt_search').click(function () {
    table.DataTable().draw(true);
});

//search in table when any input has class [direct-search] on keyup
$('.direct-search').on('keyup',function () {
    table.DataTable().draw(true);
});

$('#kt_reset').on('click', function(e) {
    e.preventDefault();
    $('.filter input').each(function() {
        if ($(this).is(':radio') && $(this).val() == ''){
            $(this).prop('checked', true);
        }else if($(this).is(':radio') && $(this).val() != ''){
            $(this).prop('checked', false);
        }else {
            $(this).val('');
        }
    });
    $(".filter select").each(function() {
        if (!$(this).hasClass('reset-no')){
            $(this).val('').trigger('change');
        }
    });


    table.DataTable().draw(true);
    checkedVisible(false)
});

function excelExport(url) {
    showLoadingModal()
    var xhr = new XMLHttpRequest();
    xhr.open('POST', url, true);
    xhr.responseType = 'blob';

    // Set the appropriate request headers
    xhr.setRequestHeader('Content-Type', 'application/json');

    xhr.onload = function() {
        hideLoadingModal()
        if (xhr.status === 200) {
            // Get the file name from the Content-Disposition header
            var contentDisposition = xhr.getResponseHeader('Content-Disposition');
            var filename = '';

            if (contentDisposition && contentDisposition.indexOf('attachment') !== -1) {
                var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                var matches = filenameRegex.exec(contentDisposition);

                if (matches != null && matches[1]) {
                    filename = matches[1].replace(/['"]/g, '');
                }
            }

            // Create a temporary link element to trigger the download
            var link = document.createElement('a');
            link.href = window.URL.createObjectURL(xhr.response);
            link.download = filename;

            // Simulate a click on the link to start the download
            link.click();
        }else {
            var errorReader = new FileReader();

            errorReader.onloadend = function() {
                var errorData = JSON.parse(errorReader.result);
                toastr.error(errorData.message);
                console.log('Error:', errorData);
                // Handle error response here
            };

            errorReader.readAsText(xhr.response);
        }
    };

    var data = {
        '_token': $('meta[name="csrf-token"]').attr('content'),
    };

    //get form data
    var frm_data = $('#filter').serializeArray();
    if (frm_data){
        $.each(frm_data, function (key, val) {
            data[val.name] = val.value;
        });
        //get value directly from element has [direct-value] class
        $('select.direct-value').each(function () {
            let val = $(this).val()
            let name = $(this).attr('name');
            delete data[name];
            name = name.replace("[]", "");

            if (val instanceof Array){
                if (val.length>0){
                    data[name] = $(this).val();
                }
            }else if (val){
                data[name] = $(this).val();
            }

        })
    }

    if ($("input:checkbox[name='rows[]']:checked").length>0){
        //export selected rows
        var row_id = [];

        //get and set checked rows ids
        $("input:checkbox[name='rows[]']:checked").each(function () {
            row_id.push($(this).val());
        });
        data.row_id = row_id
    }

    xhr.send(JSON.stringify(data));


    //uncheck and hide dropdown
    // $('#actions_dropdown').addClass('d-none');
    // rows_checkbox.each(function () {
    //     $(this).prop('checked',false)
    // });
}

function checkedVisible(status=true) {
    if($("input:checkbox[name='rows[]']:checked").length > 0 && status)
    {
        //$('#actions_dropdown').removeClass('d-none');
        $('.checked-visible').each(function() {
            $(this).removeClass('d-none');
        });
    }else{
        // $('#actions_dropdown').addClass('d-none');
        $('.checked-visible').each(function() {
            $(this).addClass('d-none');
        });
    }
}

