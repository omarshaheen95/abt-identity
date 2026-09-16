/*
 * Client side half of App\Support\PasswordPolicy.
 *
 * Powers the show/hide toggle, the "generate" button and the live rule
 * checklist rendered by resources/views/components/password-fields.blade.php.
 * Everything is wired through event delegation so the script can sit anywhere
 * in the document and needs no jQuery.
 */
(function () {
    'use strict';

    var policy = window.PASSWORD_POLICY || {};
    var MIN = policy.min || 8;
    var SYMBOLS = policy.symbols || '!@#$%^&*()-_=+[]{};:,.?';

    var LETTERS = 'abcdefghijkmnopqrstuvwxyz';
    var UPPER = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
    var DIGITS = '23456789';

    function randomInt(max) {
        if (window.crypto && window.crypto.getRandomValues) {
            var buffer = new Uint32Array(1);
            // reject the tail of the range so every value stays equally likely
            var limit = Math.floor(0xFFFFFFFF / max) * max;
            do {
                window.crypto.getRandomValues(buffer);
            } while (buffer[0] >= limit);
            return buffer[0] % max;
        }
        return Math.floor(Math.random() * max);
    }

    function pick(pool) {
        return pool.charAt(randomInt(pool.length));
    }

    function generate(length) {
        length = Math.max(length || 12, MIN);

        var all = LETTERS + UPPER + DIGITS + SYMBOLS;
        var characters = [pick(LETTERS), pick(UPPER), pick(DIGITS), pick(SYMBOLS)];

        while (characters.length < length) {
            characters.push(pick(all));
        }

        // Fisher-Yates so the guaranteed characters are not always up front
        for (var i = characters.length - 1; i > 0; i--) {
            var j = randomInt(i + 1);
            var tmp = characters[i];
            characters[i] = characters[j];
            characters[j] = tmp;
        }

        return characters.join('');
    }

    /**
     * Symbol test by lookup rather than by a generated character class: the
     * symbol list contains regex metacharacters, and escaping it into a class
     * is easy to get subtly wrong.
     */
    function hasSymbol(value) {
        for (var i = 0; i < value.length; i++) {
            if (SYMBOLS.indexOf(value.charAt(i)) !== -1) {
                return true;
            }
        }
        return false;
    }

    function checks(value, min) {
        return {
            length: value.length >= (min || MIN),
            letter: /[A-Za-z]/.test(value),
            digit: /[0-9]/.test(value),
            symbol: hasSymbol(value)
        };
    }

    function groupOf(element) {
        return element.closest('[data-password-group]');
    }

    function renderRules(group) {
        if (!group) {
            return;
        }
        var input = group.querySelector('[data-password-input]');
        var list = group.querySelector('[data-password-rules]');
        if (!input || !list) {
            return;
        }

        // admin side forms hand over a temporary password and carry their own
        // shorter minimum on the input
        var min = parseInt(input.getAttribute('data-password-min'), 10) || MIN;
        var state = checks(input.value, min);

        Array.prototype.forEach.call(list.querySelectorAll('[data-rule]'), function (item) {
            var passed = state[item.getAttribute('data-rule')];
            var icon = item.querySelector('i');
            if (icon) {
                icon.className = passed ? 'la la-check-circle text-success' : 'la la-times-circle text-danger';
            }
            item.classList.toggle('text-success', !!passed);
        });
    }

    function confirmationFor(group) {
        if (!group) {
            return null;
        }
        return document.querySelector('[data-password-confirm="' + group.getAttribute('data-password-group') + '"]');
    }

    function setVisible(input, visible) {
        input.type = visible ? 'text' : 'password';
        var button = input.parentNode.querySelector('[data-password-toggle] i');
        if (button) {
            button.className = visible ? 'la la-eye-slash' : 'la la-eye';
        }
    }

    document.addEventListener('click', function (event) {
        var toggle = event.target.closest('[data-password-toggle]');
        if (toggle) {
            event.preventDefault();
            var field = toggle.parentNode.querySelector('input');
            if (field) {
                setVisible(field, field.type === 'password');
            }
            return;
        }

        var generateButton = event.target.closest('[data-password-generate]');
        if (generateButton) {
            event.preventDefault();
            var group = groupOf(generateButton);
            var input = group ? group.querySelector('[data-password-input]') : null;
            if (!input) {
                return;
            }

            var value = generate(12);
            input.value = value;
            setVisible(input, true);

            var confirmation = confirmationFor(group);
            if (confirmation) {
                confirmation.value = value;
                setVisible(confirmation, true);
            }

            var copyButton = group.querySelector('[data-password-copy]');
            if (copyButton) {
                copyButton.classList.remove('d-none');
            }

            // let jQuery validation and any listeners see the new value
            input.dispatchEvent(new Event('input', {bubbles: true}));
            input.dispatchEvent(new Event('change', {bubbles: true}));
            if (confirmation) {
                confirmation.dispatchEvent(new Event('input', {bubbles: true}));
                confirmation.dispatchEvent(new Event('change', {bubbles: true}));
            }

            renderRules(group);
            return;
        }

        var copy = event.target.closest('[data-password-copy]');
        if (copy) {
            event.preventDefault();
            var copyGroup = groupOf(copy);
            var source = copyGroup ? copyGroup.querySelector('[data-password-input]') : null;
            if (!source || !source.value) {
                return;
            }
            var done = function () {
                if (window.toastr) {
                    window.toastr.success(policy.copied || 'Copied');
                }
            };
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(source.value).then(done);
            } else {
                source.select();
                document.execCommand('copy');
                done();
            }
        }
    });

    document.addEventListener('input', function (event) {
        var input = event.target.closest('[data-password-input]');
        if (input) {
            renderRules(groupOf(input));
        }
    });

    document.addEventListener('DOMContentLoaded', function () {
        Array.prototype.forEach.call(document.querySelectorAll('[data-password-group]'), renderRules);
    });
})();
