/**
 * Assessment Form Auto-Save System
 *
 * This module provides automatic form state saving and restoration
 * for assessment forms. It tracks and persists user inputs, including
 * radio selections, text inputs, toggles, and drag-drop elements.
 */
(function() {
    /**
     * Configuration Settings
     * Easily modify behavior without changing core code
     */
    const CONFIG = {
        // Number of days to keep saved answers in localStorage
        maxAge: 1,

        // Enable/disable console logging
        logging: {
            enabled: true,  // Set to false to disable all logs
            save: true,     // Logs when saving state
            load: true,     // Logs when loading state
            clear: true,    // Logs when clearing state
            debug: false    // Additional debug information
        },

        // UI Configuration
        ui: {
            showSaveIndicator: true,
            indicatorDisplayTime: 1000, // milliseconds
            autoSaveInterval: 30000     // 30 seconds
        }
    };

    /**
     * Logging Utility
     * Centralized logging with configuration control
     */
    const log = {
        info: function(category, message) {
            if (CONFIG.logging.enabled && CONFIG.logging[category]) {
                console.log(message);
            }
        },
        error: function(message, error) {
            if (CONFIG.logging.enabled) {
                console.error(message, error);
            }
        },
        debug: function(message, data) {
            if (CONFIG.logging.enabled && CONFIG.logging.debug) {
                console.log(message, data);
            }
        }
    };

    /**
     * Get unique storage key for the current assessment
     * Combines term ID and student ID to create a unique identifier
     */
    function getTermKey() {
        const form = $('#exams');
        const termId = form.data('term-id');
        const studentId = form.data('student-id');
        return `abt_identity_assessment_${termId}_answers_std_${studentId}`;
    }

    /**
     * UI Management
     * Handles indicator display and UI updates
     */
    const uiManager = {
        // Initialize UI components
        init: function() {
            if (CONFIG.ui.showSaveIndicator && $('#cache-indicator').length === 0) {
                let message = $('html').attr('lang')==='ar'?'حفظ الإجابات التلقائي':'Auto Saving Answers';
                $('body').append(
                    `<div id="cache-indicator" style="position:fixed;bottom:10px;right:10px;background:rgba(0,118,164,0.7);color:white;padding:5px 10px;border-radius:4px;display:none;z-index:9999;">${message}...</div>`
                );
            }
        },

        // Show the saving indicator
        showSaveIndicator: function() {
            if (CONFIG.ui.showSaveIndicator) {
                $('#cache-indicator').fadeIn(200);

                // Hide indicator after delay
                setTimeout(function() {
                    $('#cache-indicator').fadeOut(200);
                }, CONFIG.ui.indicatorDisplayTime);
            }
        }
    };

    /**
     * Save the complete form state to localStorage
     * Captures all interactive elements and their current values/states
     */
    function saveState() {
        try {
            // Show saving indicator
            uiManager.showSaveIndicator();

            // Create data structure to hold all form state
            const data = {
                timestamp: Date.now(),
                // Radio buttons (true/false, multiple choice)
                radioButtons: {},
                // Text inputs and textareas
                textInputs: {},
                // Toggle states for writing questions
                toggleStates: {},
                // Complete HTML of match, sort, and fill blank containers
                matchOptionsHtml: {},
                matchAnswersHtml: {},
                sortOptionsHtml: {},
                sortAnswersHtml: {},
                fillBlankOptionsHtml: {},
                fillBlankAnswersHtml: {}
            };

            // Save radio buttons
            $('input[type="radio"]:checked').each(function() {
                data.radioButtons[this.id] = true;
            });

            // Save text inputs and textareas
            $('input[type="text"], textarea').each(function() {
                if ($(this).val()) {
                    data.textInputs[this.id] = $(this).val();
                }
            });

            // Save toggle states
            $('.toggle-input').each(function() {
                data.toggleStates[this.id] = $(this).prop('checked');
            });

            // Save matching questions
            $('.matchOptions').each(function() {
                const questionId = $(this).attr('data-question');
                if (questionId) {
                    data.matchOptionsHtml[questionId] = $(this).html();
                }
            });

            $('.matchAnswers').each(function() {
                const key = $(this).attr('data-question') + '_' + $(this).attr('data-index');
                if (key) {
                    data.matchAnswersHtml[key] = $(this).html();
                }
            });

            // Save sorting questions
            $('.sortOptions').each(function() {
                const questionId = $(this).attr('data-question');
                if (questionId) {
                    data.sortOptionsHtml[questionId] = $(this).html();
                }
            });

            $('.sortAnswers').each(function() {
                const questionId = $(this).attr('data-question');
                if (questionId) {
                    data.sortAnswersHtml[questionId] = $(this).html();
                }
            });

            // Save fill blank questions
            $('.fillBlankOptions').each(function() {
                const questionId = $(this).attr('data-question');
                if (questionId) {
                    data.fillBlankOptionsHtml[questionId] = $(this).html();
                }
            });

            $('.fillBlankAnswers').each(function() {
                const key = $(this).attr('data-question') + '_' + $(this).attr('data-index');
                if (key) {
                    data.fillBlankAnswersHtml[key] = $(this).html();
                }
            });

            // Save to localStorage
            localStorage.setItem(getTermKey(), JSON.stringify(data));
            log.info('save', 'Term state saved to localStorage');
        } catch (e) {
            log.error('Error saving term state:', e);
        }
    }

    /**
     * Restore saved form state from localStorage
     * Reconstructs the entire form based on saved data
     */
    function loadState() {
        try {
            const savedData = localStorage.getItem(getTermKey());
            if (!savedData) {
                log.info('load', 'No saved state found');
                return;
            }

            const data = JSON.parse(savedData);
            log.info('load', 'Loading saved state from: ' + new Date(data.timestamp));

            // Check if data is too old (based on CONFIG.maxAge)
            const now = Date.now();
            const maxAge = CONFIG.maxAge * 24 * 60 * 60 * 1000; // Convert days to milliseconds
            if (now - data.timestamp > maxAge) {
                log.info('load', 'Saved state is too old, discarding');
                localStorage.removeItem(getTermKey());
                return;
            }

            // Restore radio buttons
            for (const id in data.radioButtons) {
                $('#' + id).prop('checked', true);
            }

            // Restore text inputs and textareas
            for (const id in data.textInputs) {
                $('#' + id).val(data.textInputs[id]);
            }

            // Restore toggle states
            for (const id in data.toggleStates) {
                const toggle = $('#' + id);
                toggle.prop('checked', data.toggleStates[id]);

                // Update UI based on toggle state
                if (toggle.length > 0) {
                    let questionId = id.split('-');
                    if (questionId.length > 3) {
                        questionId = questionId[2] + '-' + questionId[3];
                    } else {
                        questionId = questionId[2];
                    }

                    if (data.toggleStates[id]) {
                        $('#textarea-' + questionId).addClass('d-none');
                        $('#upload-files-' + questionId).removeClass('d-none');
                    } else {
                        $('#textarea-' + questionId).removeClass('d-none');
                        $('#upload-files-' + questionId).addClass('d-none');
                    }
                }
            }

            // Restore matching questions
            for (const questionId in data.matchOptionsHtml) {
                $('.matchOptions[data-question="' + questionId + '"]').html(data.matchOptionsHtml[questionId]);
            }

            for (const key in data.matchAnswersHtml) {
                const parts = key.split('_');
                if (parts.length === 2) {
                    $('.matchAnswers[data-question="' + parts[0] + '"][data-index="' + parts[1] + '"]').html(data.matchAnswersHtml[key]);
                }
            }

            // Restore sorting questions
            for (const questionId in data.sortOptionsHtml) {
                $('.sortOptions[data-question="' + questionId + '"]').html(data.sortOptionsHtml[questionId]);
            }

            for (const questionId in data.sortAnswersHtml) {
                $('.sortAnswers[data-question="' + questionId + '"]').html(data.sortAnswersHtml[questionId]);

                // Re-number the inputs
                setTimeout(function() {
                    let i = 1;
                    $('.sortAnswers[data-question="' + questionId + '"] div input').each(function() {
                        $(this).val(i++);
                    });
                }, 100);
            }

            // Restore fill blank questions
            for (const questionId in data.fillBlankOptionsHtml) {
                $('.fillBlankOptions[data-question="' + questionId + '"]').html(data.fillBlankOptionsHtml[questionId]);
            }

            for (const key in data.fillBlankAnswersHtml) {
                const parts = key.split('_');
                if (parts.length === 2) {
                    $('.fillBlankAnswers[data-question="' + parts[0] + '"][data-index="' + parts[1] + '"]').html(data.fillBlankAnswersHtml[key]);
                }
            }

            // Update word counts for textareas
            $('.textarea textarea').each(function() {
                if ($(this).val() && typeof onSpacePress === 'function') {
                    onSpacePress($(this).val(), $(this).attr('id'));
                }
            });

            log.info('load', 'Term state restored successfully');
        } catch (e) {
            log.error('Error loading term state:', e);
        }
    }

    /**
     * Clear saved state from localStorage
     * Used after successful form submission
     */
    function clearState() {
        localStorage.removeItem(getTermKey());
        log.info('clear', 'Term state cleared');
    }

    /**
     * Debug Functions
     * Helpful utilities for troubleshooting
     */
    function debugElements() {
        // Log all unique data-question attributes on the page
        log.debug('Debugging - Found data-question attributes:');
        const dataQuestions = new Set();
        $('[data-question]').each(function() {
            dataQuestions.add($(this).attr('data-question'));
        });
        log.debug([...dataQuestions]);
    }

    /**
     * Event Registration
     * Sets up all event listeners for form interaction
     */
    function registerEvents() {
        // For radio buttons
        $(document).on('change', 'input[type="radio"]', function() {
            setTimeout(saveState, 100);
        });

        // For text inputs and textareas (debounced)
        let textTimer;
        $(document).on('input', 'input[type="text"], textarea', function() {
            clearTimeout(textTimer);
            textTimer = setTimeout(saveState, 500);
        });

        // For sorting and matching
        $(document).on('sortreceive sortupdate', '.sortOptions, .sortAnswers, .matchOptions, .matchAnswers, .fillBlankOptions, .fillBlankAnswers', function() {
            setTimeout(saveState, 200);
        });

        // When items are clicked to move between options and answers
        $(document).on('click', '.sortOptions div, .sortAnswers div, .matchOptions div, .matchAnswers div, .fillBlankOptions div, .fillBlankAnswers div', function() {
            setTimeout(saveState, 200);
        });

        // For toggle switches
        $(document).on('change', '.toggle-input', function() {
            setTimeout(saveState, 100);
        });
    }

    /**
     * Initialization
     * Setup the module when the DOM is ready
     */
    $(document).ready(function() {
        // Initialize UI components
        uiManager.init();

        // Run debug if enabled
        if (CONFIG.logging.debug) {
            debugElements();
        }

        // Register all event handlers
        registerEvents();

        // Set up auto-save interval
        setInterval(saveState, CONFIG.ui.autoSaveInterval);

        // Initial load of any saved state
        loadState();

        // Expose functions globally
        window.saveTermState = saveState;
        window.loadTermState = loadState;
        window.clearTermState = clearState;

        // Replace original functions with our versions
        window.saveResult = saveState;
        window.getAndSetResults = loadState;

        // Modify form submission to clear state on successful submission
        // const originalExamFormSubmit = window.examFormSubmit;
        // window.examFormSubmit = function(with_validation=true) {
        //     originalExamFormSubmit(with_validation);
        //     if (with_validation && validation()) {
        //         clearState();
        //     }
        // };
    });
})();
