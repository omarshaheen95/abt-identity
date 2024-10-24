//document onChange event
$(document).on('change', '#year_id', function () {
    var year = $(this).val();
    //check element has multiple attribute
    if ($($('#levels_id')).attr('multiple') != undefined) {
        var multipleOptions = 1;
    }else{
        var multipleOptions = 0;
    }


    if (year){
        getLevelsByYear(year, '#levels_id', multipleOptions);
        //check if class name select exist
    }else {
        $('#levels_id').empty();
    }
});
function getLevelsByYear(year, select, multipleOptions) {
    if (year !== '' && year !== undefined) {
        $.ajax({
            url: LevelGradesRoute + '?id=' + year + '&multipleOptions=' + multipleOptions,
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


function initializeDateRangePicker(id = "date_range_picker", range = []) {
    const dateRangePicker = $('#' + id);
    if (dateRangePicker.length>0){
        if (range.length > 0) {
            var rangePickerAttr = {
                startDate: moment(range[0]),
                endDate: moment(range[1]),
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
            };
        }else {
            var rangePickerAttr = {
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
            };
        }
        dateRangePicker.daterangepicker(rangePickerAttr);
        dateRangePicker.on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format("YYYY-MM-DD") + ' - ' + picker.endDate.format("YYYY-MM-DD"));
            $('#start_' + id).val(picker.startDate.format("YYYY-MM-DD"));
            $('#end_' + id).val(picker.endDate.format("YYYY-MM-DD"));
        });

        dateRangePicker.on('cancel.daterangepicker', function (ev, picker) {
            $(this).val('');
        });
    }

}
