<?php
/*
Dev Omar Shaheen
Devomar095@gmail.com
WhatsApp +972592554320
*/

namespace App\Support;

use Illuminate\Support\Str;
use Illuminate\Validation\Validator;

/**
 * Single source of truth for password strength across every guard.
 *
 * The rules are intentionally plain string rules so that
 * JsValidator::formRequest() can translate them into browser side validation.
 * Anything that cannot be expressed as a string rule (password reuse, common
 * passwords, similarity to the account identity) is applied server side only
 * through validate().
 */
class PasswordPolicy
{
    public const MIN_LENGTH = 8;

    /**
     * Relaxed floor for a temporary password an administrator types for
     * somebody else. It is only accepted together with the forced change flag,
     * so the account cannot keep it.
     */
    public const MIN_LENGTH_TEMPORARY = 6;

    /**
     * Symbols accepted by the policy and used by the generator. Kept in sync
     * with the regex below and with the client side generator.
     */
    public const SYMBOLS = '!@#$%^&*()-_=+[]{};:,.?';

    /**
     * Base words that are rejected however they are dressed up. The password is
     * normalised first (leet speak folded back to letters, digits and symbols
     * stripped from both ends), so Passw0rd1! is caught alongside password.
     */
    public const BLACKLIST = [
        'password', 'passwort', 'qwerty', 'qwertyui', 'azerty', 'welcome', 'letmein',
        'iloveyou', 'monkey', 'dragon', 'sunshine', 'princess', 'football', 'baseball',
        'master', 'shadow', 'superman', 'trustno', 'starwars', 'whatever', 'internet',
        'admin', 'administrator', 'root', 'login', 'default', 'changeme', 'secret',
        'school', 'teacher', 'student', 'abcdef', 'abcdefg', 'abcdefgh',
    ];

    /**
     * Leet speak substitutions folded back before the blacklist comparison.
     */
    private const LEET = [
        '0' => 'o', '1' => 'i', '3' => 'e', '4' => 'a', '5' => 's', '7' => 't',
        '8' => 'b', '9' => 'g', '@' => 'a', '$' => 's', '!' => 'i', '+' => 't',
    ];

    /**
     * Validation rules for a new password field.
     *
     * @param bool $required     false for admin forms where leaving the field
     *                           empty means "keep the current password"
     * @param bool $confirmed    whether a matching *_confirmation field is required
     */
    public static function rules($required = true, $confirmed = true)
    {
        $rules = [$required ? 'required' : 'nullable', 'string', 'min:' . self::MIN_LENGTH];

        if ($confirmed) {
            $rules[] = 'confirmed';
        }

        // one letter, one digit and one symbol, no whitespace
        $rules[] = 'regex:/^(?=.*[A-Za-z])(?=.*[0-9])(?=.*[' . self::regexSymbolClass() . '])[^\s]+$/';

        // returned as an array so the regex is never split on | or : by the
        // string rule parser
        return $rules;
    }

    /**
     * Rules for a password an administrator sets on somebody else's account.
     *
     * Deliberately lenient: the account holder is sent to the change password
     * screen on first sign in anyway, so the value here is a hand over secret,
     * not a password anyone keeps. The full policy is still enforced server
     * side by requireStrongUnlessForced() when the forced change flag is off.
     */
    public static function temporaryRules($required = true)
    {
        // no whitespace, the same as the full policy: a password stored with a
        // leading or trailing space can never be typed back, because the old
        // password field is compared after the request has been trimmed
        return [$required ? 'required' : 'nullable', 'string',
            'min:' . self::MIN_LENGTH_TEMPORARY, 'regex:/^[^\s]+$/'];
    }

    /**
     * Apply the full policy to an administrator set password only when it is
     * not going to be forced out on the next sign in.
     *
     * Registered through withValidator so JsValidator never sees it: the
     * browser keeps enforcing the lenient rule, and the server has the final
     * say once it can see the state of the forced change switch.
     */
    public static function requireStrongUnlessForced(Validator $validator, $field = 'password', array $identity = [])
    {
        // JsValidator checks remote rules (such as unique on the email) by
        // posting the whole form through this same request; the error would
        // land on that other field and stay cached there, so leave this check
        // to the real submit
        if (request()->has(config('jsvalidation.remote_validation_field', '_jsvalidation'))) {
            return;
        }

        $validator->after(function (Validator $validator) use ($field, $identity) {
            $data = $validator->getData();
            $password = $data[$field] ?? null;

            if (!is_string($password) || $password === '') {
                return;
            }

            if (!empty($data['force_password_change'])) {
                return; // temporary by construction, the lenient rule is enough
            }

            $check = \Illuminate\Support\Facades\Validator::make(
                [$field => $password],
                [$field => self::rules(true, false)],
                self::messages($field)
            );

            self::applyExtraChecks($check, $field, null, $identity);

            if ($check->fails()) {
                $validator->errors()->add($field, t('A password that is not forced to change must follow the full policy: :policy', ['policy' => self::message()]));
            }
        });
    }

    /**
     * Human readable description of the policy, used in form hints and as the
     * message for the regex rule.
     */
    public static function message()
    {
        return t('Password must be at least :min characters and contain at least one letter, one number and one symbol.', ['min' => self::MIN_LENGTH]);
    }

    /**
     * Validation messages keyed for the given field name.
     */
    public static function messages($field = 'password')
    {
        return [
            $field . '.regex' => self::message(),
            $field . '.min' => self::message(),
        ];
    }

    /**
     * Messages for the lenient administrator set password, so a too short
     * temporary password is not told about the full policy it is exempt from.
     */
    public static function temporaryMessages($field = 'password')
    {
        return [
            $field . '.min' => t('At least :min characters', ['min' => self::MIN_LENGTH_TEMPORARY]),
        ];
    }

    /**
     * Server side checks that cannot be expressed as string rules.
     *
     * Everything here answers the same way for every account, so one password
     * is either accepted for all of them or refused for all of them. An earlier
     * version also refused a password containing the account's own name or
     * email; it was dropped because it made the same password behave
     * differently from one account to the next, which only confused people.
     * $identity is kept in the signature so callers do not have to change.
     *
     * @param Validator    $validator
     * @param string       $field     name of the password field
     * @param string|null  $currentHash  current hashed password, to block reuse
     * @param array        $identity  no longer used, accepted for compatibility
     */
    public static function applyExtraChecks(Validator $validator, $field = 'password', $currentHash = null, array $identity = [])
    {
        $validator->after(function (Validator $validator) use ($field, $currentHash) {
            $password = $validator->getData()[$field] ?? null;

            if (!is_string($password) || $password === '') {
                return;
            }

            if (self::isCommon($password)) {
                $validator->errors()->add($field, t('This password is too common, please choose another one.'));
                return;
            }

            if ($currentHash && \Illuminate\Support\Facades\Hash::check($password, $currentHash)) {
                $validator->errors()->add($field, t('The new password must be different from the current password.'));
                return;
            }
        });
    }

    /**
     * Refuse a password the account has used recently. Server side only, since
     * only the stored hashes can answer it.
     */
    public static function applyHistoryCheck(Validator $validator, $field, $user)
    {
        if (!$user || !method_exists($user, 'passwordWasUsedBefore')) {
            return;
        }

        $validator->after(function (Validator $validator) use ($field, $user) {
            $password = $validator->getData()[$field] ?? null;

            if (!is_string($password) || $password === '' || $validator->errors()->has($field)) {
                return;
            }

            if ($user->passwordWasUsedBefore($password)) {
                $validator->errors()->add($field, t('You have used this password recently, please choose a different one. The last :count passwords are remembered.', ['count' => (int) config('password_policy.history', 5)]));
            }
        });
    }

    /**
     * Generate a password that satisfies the policy. Used by the artisan
     * command and as the server side twin of the "generate" button.
     */
    public static function generate($length = 12)
    {
        $length = max($length, self::MIN_LENGTH);

        $letters = 'abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ'; // no l, I, O
        $digits = '23456789'; // no 0, 1
        $symbols = self::SYMBOLS;
        $all = $letters . $digits . $symbols;

        $characters = [
            self::pick($letters),
            self::pick(Str::lower($letters)),
            self::pick($digits),
            self::pick($symbols),
        ];

        for ($i = count($characters); $i < $length; $i++) {
            $characters[] = self::pick($all);
        }

        shuffle($characters);

        return implode('', $characters);
    }

    /**
     * Whether the password is a well known weak one in disguise: a blacklisted
     * word padded with digits/symbols and/or written in leet speak, a run of
     * one repeated character, or a straight alphabet/number sequence.
     */
    public static function isCommon($password)
    {
        foreach (self::cores($password) as $core) {
            if ($core === '') {
                continue;
            }

            foreach (self::BLACKLIST as $word) {
                if ($core === $word) {
                    return true;
                }

                // a padded word such as Schoo1@123 still reads as "school", but
                // only count it when the word is most of what the user typed
                if (strlen($word) >= 5
                    && strpos($core, $word) !== false
                    && strlen($word) * 2 >= strlen($core)) {
                    return true;
                }
            }
        }

        // a long run of identical or consecutive characters carries no entropy,
        // wherever it sits inside the password
        foreach (self::runs($password) as $run) {
            if (preg_match('/^(.)\1+$/', $run) || self::isSequential($run)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Candidate "real words" hiding inside the password. Padding digits and
     * symbols are trimmed from both ends and leet speak is folded back; the
     * characters that stand for two different letters (1 is i or l) produce one
     * candidate each.
     */
    private static function cores($password)
    {
        $lower = Str::lower($password);
        $padding = '0123456789' . self::SYMBOLS;

        // NOTE: array_merge renumbers numeric string keys such as '1', so the
        // alternative reading of 1 has to be assigned rather than merged
        $ell = self::LEET;
        $ell['1'] = 'l';
        $ell['!'] = 'l';

        $maps = [self::LEET, $ell];

        $cores = [];
        foreach ($maps as $map) {
            // trim the padding first, then fold leet speak, and the other way
            // round: 1 can be padding in one position and a letter in another
            $cores[] = preg_replace('/[^a-z]/', '', strtr(trim($lower, $padding), $map));
            $cores[] = preg_replace('/[^a-z]/', '', strtr($lower, $map));
        }

        return array_unique($cores);
    }

    /**
     * Maximal runs of letters and of digits that are long enough to judge.
     */
    private static function runs($password)
    {
        preg_match_all('/[a-z]{6,}|[0-9]{6,}/', Str::lower($password), $matches);

        return $matches[0];
    }

    /**
     * Straight runs such as 12345678 or abcdefgh, in either direction.
     */
    private static function isSequential($value)
    {
        if (strlen($value) < 6) {
            return false;
        }

        $ascending = true;
        $descending = true;

        for ($i = 1; $i < strlen($value); $i++) {
            $step = ord($value[$i]) - ord($value[$i - 1]);
            if ($step !== 1) {
                $ascending = false;
            }
            if ($step !== -1) {
                $descending = false;
            }
        }

        return $ascending || $descending;
    }

    /**
     * Symbol list escaped for use inside a regex character class.
     */
    private static function regexSymbolClass()
    {
        return preg_quote(self::SYMBOLS, '/');
    }

    private static function pick($pool)
    {
        return $pool[random_int(0, strlen($pool) - 1)];
    }
}
