<script>
    jQuery(document).ready(function () {

        $("<?= $validator['selector']; ?>").each(function () {
            $(this).validate({
                errorElement: 'span',
                errorClass: 'invalid-feedback',

                errorPlacement: function (error, element) {
                    if (element.hasClass('select2-hidden-accessible')) {
                        // element after first next element
                        error.insertAfter(element.next());
                        // else just place the validation message immediately after the input
                    }else if (element.prop('type') === 'checkbox' || element.prop('type') === 'radio') {
                        // element after first next element
                        error.insertAfter(element.parent().parent());
                        // else just place the validation message immediately after the input
                    } else if (element.parent('.input-group').length ||
                        element.prop('type') === 'checkbox' || element.prop('type') === 'radio') {
                        error.insertAfter(element.parent());
                        // else just place the validation message immediately after the input
                    } else {
                        error.insertAfter(element);
                    }

                },
                highlight: function (element) {
                    if ($(element).hasClass('select2-hidden-accessible')) {
                        $(element).next('.select2-container').removeClass('is-valid').addClass('is-invalid'); // add the Bootstrap error class to the control group
                    } else {
                        $(element).closest('.form-control').removeClass('is-valid').addClass('is-invalid'); // add the Bootstrap error class to the control group
                    }
                },

                <?php if (isset($validator['ignore']) && is_string($validator['ignore'])): ?>

                ignore: "<?= $validator['ignore']; ?>",
                <?php endif; ?>


                unhighlight: function (element) {
                    if ($(element).hasClass('select2-hidden-accessible')) {
                        $(element).next('.select2-container').removeClass('is-invalid').addClass('is-valid');
                    } else {
                        $(element).closest('.form-control').removeClass('is-invalid').addClass('is-valid');
                    }
                },

                success: function (element) {
                    if ($(element).hasClass('select2-hidden-accessible')) {
                        $(element).next('.select2-container').removeClass('is-invalid').addClass('is-valid'); // remove the Boostrap error class from the control group
                    } else {
                        $(element).closest('.form-control').removeClass('is-invalid').addClass('is-valid'); // remove the Boostrap error class from the control group
                    }
                },

                focusInvalid: true,
                <?php if (Config::get('jsvalidation.focus_on_error')): ?>
                invalidHandler: function (form, validator) {

                    if (!validator.numberOfInvalids())
                        return;

                    $('html, body').animate({
                        scrollTop: $(validator.errorList[0].element).offset().top
                    }, <?= Config::get('jsvalidation.duration_animate') ?>);

                },
                <?php endif; ?>

                rules: <?= json_encode($validator['rules']); ?>
            });
        });
    });
</script>
