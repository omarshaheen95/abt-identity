$(function () {

    $('input[name="registration_date"]').daterangepicker({
        autoUpdateInput: false,
        locale: {
            cancelLabel: 'Clear'
        },
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
    });

    $('input[name="registration_date"]').on('apply.daterangepicker', function (ev, picker) {
        $(this).val(picker.startDate.format("YYYY-MM-DD") + ' - ' + picker.endDate.format("YYYY-MM-DD"));
        $('#start_registration_date').val(picker.startDate.format("YYYY-MM-DD"));
        $('#end_registration_date').val(picker.endDate.format("YYYY-MM-DD"));
    });

    $('input[name="registration_date"]').on('cancel.daterangepicker', function (ev, picker) {
        $(this).val('');
    });

});

$(document).on('change', '#year_id', function () {
    var year = $(this).val();
    if ($('#levels_id').attr('multiple') != undefined) {
        var multipleOptions = 1;
    }else{
        var multipleOptions = 0;
    }
    if (year){
        getLevelsByYear(year, '#levels_id', multipleOptions);
        //check if class name select exist
        if ($('#class_name').length) {
            getSectionByYear(year, '#class_name');
        }
    }else {
        $('#levels_id').empty();
    }
});
$(document).on('change', '#school_id', function () {
    var school = $(this).val();
    var year = $('#year_id').val();
    if (school > 0 && year > 0){
        if ($('#class_name').length) {
            getSectionByYear(year, '#class_name');
        }
    }else {
        if ($('#class_name').length) {
            $('#class_name').empty();
        }
    }
});
//ajax get levels by year and push to select
function getLevelsByYear(year, select, multipleOptions) {
    if (year !== '' && year !== undefined) {
        $.ajax({
            url: LevelGradesRoute+'?id=' + year + '&multipleOptions=' + multipleOptions,
            type: 'GET',
            dataType: 'json',
            success: function (data) {
                $(select).empty();
                $.each(data, function (key, value) {
                    $(select).append(value);
                });
            }
        });
    }
}
function getSectionByYear(year, select) {
    if (year !== '' && year !== undefined) {
        var school = $('#school_id').val();
        $.ajax({
            url:GetSectionRoute,
            data: {
                id: year,
                school_id: school
            },
            type: 'GET',
            dataType: 'json',
            success: function (data) {
                $(select).empty();
                $.each(data, function (key, value) {
                    $(select).append(value);
                });
            }
        });
    }
}



