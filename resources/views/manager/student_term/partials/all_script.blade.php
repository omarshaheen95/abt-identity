{{-- Behaviour of the all students assessments page. Include after datatable.js.
     Needs: form#filter, #status_type, #status_tabs, #filters_drawer, #active_filters, #kt_reset.
     .only-corrected / .only-uncorrected / .hide-on-deleted elements follow the tab and the
     deleted status. Drawer fields with data-label show up as removable chips. --}}
<script>
    $(function () {
        var $form = $('#filter');
        var $type = $('#status_type');
        var $drawer = $('#filters_drawer');
        var CORRECTED = String($('#status_tabs [data-count="corrected"]').closest('.nav-link').data('type'));
        var UNCORRECTED = String($('#status_tabs [data-count="uncorrected"]').closest('.nav-link').data('type'));
        var liveReady = false;   // ignore the change events fired while the page sets itself up
        var resetting = false;   // the reset button fires one change per select: draw once
        var drawTimer = null;

        function redraw() {
            clearTimeout(drawTimer);
            table.DataTable().draw(true);
        }

        function scheduleDraw(delay) {
            if (!liveReady || resetting) {
                return;
            }
            clearTimeout(drawTimer);
            drawTimer = setTimeout(redraw, delay || 300);
        }

        // ---- Status tabs --------------------------------------------------------------

        function refreshVisibility() {
            var type = String($type.val());
            var deleted = $('#terms_status').length && $('#terms_status').val() !== String($('#terms_status').data('default'));

            $('.hide-on-deleted').removeClass('d-none');
            $('.only-corrected').toggleClass('d-none', type === UNCORRECTED);
            $('.only-uncorrected').toggleClass('d-none', type === CORRECTED);
            if (deleted) {
                $('.hide-on-deleted').addClass('d-none');
            }
        }

        function applyType(type) {
            $type.val(type);
            $('#status_tabs .nav-link').removeClass('active')
                .filter(function () {
                    return String($(this).data('type')) === String(type);
                }).addClass('active');
            refreshVisibility();

            // Hidden correction-only filters must not keep narrowing the uncorrected list
            if (String(type) === UNCORRECTED) {
                $drawer.find('.only-corrected input').val('');
                $drawer.find('.only-corrected select').val(null).trigger('change.select2');
            }
        }

        $('#status_tabs').on('click', '.nav-link', function (e) {
            e.preventDefault();
            applyType(String($(this).data('type')));
            redraw();
        });

        applyType($type.val());

        // ---- Live filtering -----------------------------------------------------------

        $form.on('change', 'select', function () {
            if (this.id === 'terms_status') {
                refreshVisibility();
                checkedVisible(false);
            }
            scheduleDraw();
        });
        $form.on('input', 'input[type="text"]:not([data-clear])', function () {
            scheduleDraw(450);
        });
        $form.on('keydown', 'input[type="text"]', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                redraw();
            }
        });
        // datatable.js redraws on every keyup of some inputs; the debounced handler covers them
        $form.find('input').off('keyup');

        $drawer.on('apply.daterangepicker', 'input[data-clear]', function () {
            setTimeout(function () { scheduleDraw(); }, 0);
        }).on('cancel.daterangepicker', 'input[data-clear]', function () {
            clearControl($(this));
            scheduleDraw();
        });

        // ---- Active filter chips ------------------------------------------------------

        function controlText($el) {
            if ($el.is('select')) {
                return $el.find('option:selected').filter(function () {
                    return this.value !== '';
                }).map(function () {
                    return $(this).text().trim();
                }).get().join('، ');
            }
            return $.trim($el.val());
        }

        function isActive($el) {
            var value = $el.val();
            if (value === null || value === '' || (Array.isArray(value) && value.length === 0)) {
                return false;
            }
            var def = $el.data('default');
            return def === undefined || String(value) !== String(def);
        }

        function clearControl($el) {
            var def = $el.data('default');
            if ($el.is('select')) {
                $el.val(def !== undefined ? String(def) : null).trigger('change.select2');
            } else {
                $el.val('');
            }
            if ($el.data('clear')) {
                $($el.data('clear')).val('');
            }
            if ($el.attr('id') === 'terms_status') {
                refreshVisibility();
            }
        }

        function refreshChips() {
            var $box = $('#active_filters').empty();
            var count = 0;

            $drawer.find('[data-label]').each(function () {
                var $el = $(this);
                if ($el.closest('.d-none').length || !isActive($el)) {
                    return;
                }
                count++;
                var $chip = $('<span class="filter-chip"></span>')
                    .append($('<span></span>').text($el.data('label') + ':'))
                    .append($('<b></b>').text(controlText($el)))
                    .append($('<i class="fa fa-times chip-remove"></i>'));
                $chip.data('control', $el);
                $box.append($chip);
            });

            if (count > 1) {
                $box.append($('<a href="#!" class="fs-8 text-danger ms-1" id="chips_clear_all"></a>').text(@json(t('Clear'))));
            }
            $('#more_filters_count').text(count).toggleClass('d-none', count === 0);
        }

        function clearDrawer() {
            $drawer.find('[data-label]').each(function () {
                clearControl($(this));
            });
            redraw();
        }

        $('#active_filters').on('click', '.chip-remove', function () {
            clearControl($(this).closest('.filter-chip').data('control'));
            redraw();
        }).on('click', '#chips_clear_all', function (e) {
            e.preventDefault();
            clearDrawer();
        });
        $('#drawer_clear').on('click', clearDrawer);

        table.on('preXhr.dt', refreshChips);

        // ---- Reset --------------------------------------------------------------------

        // Capture phase: runs before datatable.js' own reset handler, which empties the
        // inputs and selects and then draws once. Selects marked reset-no (sort, deleted
        // status) are skipped there, so they go back to their defaults here first.
        document.getElementById('kt_reset').addEventListener('click', function () {
            resetting = true;
            clearTimeout(drawTimer);
            $drawer.find('[data-default]').each(function () {
                clearControl($(this));
            });
        }, true);
        $('#kt_reset').on('click', function () {
            applyType('');
            setTimeout(function () {
                resetting = false;
                refreshChips();
            }, 0);
        });

        // ---- Counters & selection -----------------------------------------------------

        table.on('xhr.dt', function (e, settings, json) {
            if (json && json.counts) {
                $.each(json.counts, function (key, value) {
                    $('[data-count="' + key + '"]').text(Number(value).toLocaleString());
                });
            }
        });

        function refreshSelection() {
            var count = getCheckedRows().length;
            $('#selected_count').text(count);
            $('#selected_info').toggleClass('d-none', count === 0);
        }

        table.on('change', 'input:checkbox', function () {
            setTimeout(refreshSelection, 0);
        });
        table.on('draw.dt', refreshSelection);
        $('#clear_selection').on('click', function (e) {
            e.preventDefault();
            $("input:checkbox[name='rows[]'], .group-checkable").prop('checked', false);
            $('#datatable tbody tr').removeClass('active');
            checkedVisible(false);
            refreshSelection();
        });

        // ---- Initial state ------------------------------------------------------------

        if ($('#year_id').val()) {
            $('#year_id').trigger('change');
        }
        refreshChips();
        setTimeout(function () {
            liveReady = true;
        }, 0);
    });
</script>
