//get guard type from first word after / in url
var guard = window.location.pathname.split('/')[1];

function getSectionByYear(year, select) {
    if (year !== '' && year !== undefined) {
        var school = $('#school_id').val();
        console.log(school);
        //check school is not empty of undefined or empty array
        if (school !== '' && school !== undefined && school !== 'undefined' && school.length !== 0) {
            console.log("Fetching sections for school: " + school + " and year: " + year);
            $.ajax({
                url: GetSectionRoute,
                data: {
                    school_id: school,
                    year_id: year,
                },
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    $(select).empty();
                    $(select).append('<option></option>');
                    $.each(data, function (key, value) {
                        $(select).append(value);
                    });
                }
            });
        }
    }
}
function getLevelsByYear(year, select, multipleOptions) {
    if (year !== '' && year !== undefined) {
        $.ajax({
            url: LevelGradesRoute + '?year_id=' + year + '&multipleOptions=' + multipleOptions,
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

$(document).on('change', '#year_id', function () {
    var year = $(this).val();
    if (year && $('#grades_names').length) {
        console.log('Grades names ID exists');
        getSectionByYear(year, '#grades_names');
    }
    if (year && $('#levels_id').length) {
        if ($($('#levels_id')).attr('multiple') != undefined) {
            var multipleOptions = 1;
        }else{
            var multipleOptions = 0;
        }
        getLevelsByYear(year, '#levels_id', multipleOptions);
    }
});
// Optimized single event handler for school selection
$(document).on('change', '#school_id', function () {
    // Cache commonly used elements for better performance
    const $yearSelect = $('#year_id');
    const $sectionSelect = $('#grades_names');
    const $rangesType = $('#ranges_type');

    const schoolId = $(this).val();
    const yearId = $yearSelect.val();

    // Handle section dependency
    if (schoolId && yearId && $sectionSelect.length) {
        getSectionByYear(yearId, '#grades_names');
    }
    // Handle report type options
    if ($rangesType.length) {
        console.log('Ranges type select exists');
        const $selectedOption = $(this).find('option:selected');
        const reportData = {
            expected: !!$selectedOption.data('expected-report'),
            advanced: !!$selectedOption.data('advanced-report')
        };

        // Reset and configure options
        $rangesType.find('option').prop('disabled', true).prop('selected', false);

        // Enable and select appropriate options
        if (reportData.expected) {
            $rangesType.find('option[value="1"]').prop('disabled', false).prop('selected', true);
        }
        if (reportData.advanced) {
            $rangesType.find('option[value="0"]').prop('disabled', false).prop('selected', !reportData.expected);
        }

        // Refresh Select2 if it's being used
        // if (typeof $rangesType.select2 === 'function') {
        $rangesType.select2();
        // }
    }

    if(guard === 'manager' || guard === 'inspection'){
        const selectedOptions = $('#school_id option:selected');
        let allowReport = false;
        selectedOptions.each((index,option)=>{
            // console.log('school:'+$(option).val()+'||| allow-report-value:'+parseInt($(option).data('allow-report'))+'||| allow-report:'+(parseInt($(option).data('allow-report')) === 1))
            if (parseInt($(option).data('allow-report')) === 0){
                allowReport= true;
                return;
            }
        })

        $('.card-body').toggleClass('not-allowed-report', allowReport);
        $('#not-allowed-report-alert').toggleClass('d-none', !allowReport);
    }

});


//Report Configurations
// Handle grade selection logic with enhanced visuals
const $allGradesToggle = $('#all_grades_toggle');
const $gradeCheckboxes = $('.grade-checkbox');

// Function to update grade item visual state
function updateGradeItemVisual($checkbox) {
    const $gradeItem = $checkbox.closest('.grade-item');

    if ($checkbox.is(':checked')) {
        $gradeItem.addClass('border-warning bg-light-warning');
        $gradeItem.removeClass('border-gray-300');
    } else {
        $gradeItem.removeClass('border-warning bg-light-warning');
        $gradeItem.addClass('border-gray-300');
    }
}

// Make grade items clickable anywhere
$('.grade-item').on('click', function (e) {
    e.preventDefault();
    e.stopPropagation();

    const $checkbox = $(this).find('.grade-checkbox');
    const currentState = $checkbox.is(':checked');

    // Toggle the checkbox state
    $checkbox.prop('checked', !currentState);

    // Trigger change event to update visuals
    $checkbox.trigger('change');
});

// Handle direct checkbox clicks
$('.grade-checkbox').on('click', function (e) {
    e.stopPropagation();
    // Let the checkbox handle its own state change
    $(this).trigger('change');
});

// Handle checkbox changes
$('.grade-checkbox').on('change', function () {
    const totalGrades = $gradeCheckboxes.length;
    const checkedGrades = $gradeCheckboxes.filter(':checked').length;

    $allGradesToggle.prop('checked', checkedGrades === totalGrades);
    $allGradesToggle.prop('indeterminate', checkedGrades > 0 && checkedGrades < totalGrades);

    // Update visual state for this specific grade
    updateGradeItemVisual($(this));
});

// Initialize visual states
$gradeCheckboxes.each(function () {
    updateGradeItemVisual($(this));
});

// When "Select All" is clicked
$allGradesToggle.on('change', function () {
    $gradeCheckboxes.prop('checked', this.checked);
    $gradeCheckboxes.each(function () {
        updateGradeItemVisual($(this));
    });
});

// When individual grade checkboxes are clicked - removed to avoid duplication

// Add hover effects for grade items
$('.grade-item').on('mouseenter', function () {
    if (!$(this).find('.grade-checkbox').is(':checked')) {
        $(this).addClass('border-primary bg-light-primary');
    }
}).on('mouseleave', function () {
    if (!$(this).find('.grade-checkbox').is(':checked')) {
        $(this).removeClass('border-primary bg-light-primary');
    }
});

// Handle special category visual states with button toggles
const $specialCategoryToggles = $('.special-category-toggle');

function updateSpecialCategoryVisual($toggle) {
    const $categoryItem = $toggle.closest('.special-category-item');
    const $includedBtn = $categoryItem.find('.category-btn-included');
    const $excludedBtn = $categoryItem.find('.category-btn-excluded');

    if ($toggle.is(':checked')) {
        // Show INCLUDED state
        $categoryItem.addClass('border-warning bg-light-warning');
        $categoryItem.removeClass('border-secondary bg-light-secondary border-gray-300');
        $includedBtn.removeClass('d-none').addClass('btn-warning text-gray-800');
        $excludedBtn.addClass('d-none').removeClass('btn-secondary text-gray-800');
    } else {
        // Show EXCLUDED state
        $categoryItem.addClass('border-secondary bg-light-secondary');
        $categoryItem.removeClass('border-warning bg-light-warning border-gray-300');
        $excludedBtn.removeClass('d-none').addClass('btn-secondary text-gray-800');
        $includedBtn.addClass('d-none').removeClass('btn-warning text-gray-800');
    }
}

// Make special category items clickable
$('.special-category-item').on('click', function (e) {
    // Don't trigger if clicking directly on the hidden checkbox
    if (!$(e.target).is('input[type="checkbox"]')) {
        const $toggle = $(this).find('.special-category-toggle');
        $toggle.prop('checked', !$toggle.prop('checked')).trigger('change');
    }
});

// Handle special category toggle changes
$specialCategoryToggles.on('change', function () {
    updateSpecialCategoryVisual($(this));
});

// Initialize special category visual states
$specialCategoryToggles.each(function () {
    updateSpecialCategoryVisual($(this));
});

// Add hover effects for special categories
$('.special-category-item').on('mouseenter', function () {
    $(this).addClass('shadow-sm').css('transform', 'translateY(-2px)');
}).on('mouseleave', function () {
    $(this).removeClass('shadow-sm').css('transform', 'translateY(0px)');
});

// Prevent default form submission
$('#form_data').on('submit', function (e) {
    e.preventDefault();
});

// Handle report generation
$('.get-report').on('click', function (e) {
    e.preventDefault();
    const route = $(this).data('route');
    let formData = $("#form_data").serialize();

    if (!$('#form_data').validate().form()) {
        // If the form is invalid, show an error message
        Swal.fire({
            icon: 'error',
            title: ERROR_TITLE,
            text: ERROR_MESSAGE
        });
        return;
    }
    window.open(route + '?' + formData, "_blank");
});
