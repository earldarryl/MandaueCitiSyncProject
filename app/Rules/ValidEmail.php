<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidEmail implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $value = strtolower(trim($value));

        $allowedDomains = [
            'gmail.com', 'googlemail.com', 'yahoo.com', 'yahoo.co.uk',
            'outlook.com', 'hotmail.com', 'live.com', 'msn.com',
            'icloud.com', 'me.com', 'mac.com', 'zoho.com', 'protonmail.com',
            'gmx.com', 'gmx.de'
        ];

        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $fail("The $attribute must be a valid email address.");
            return;
        }

        $domain = substr(strrchr($value, "@"), 1);

        if (!in_array($domain, $allowedDomains)) {
            $fail("The $attribute must be from a valid email provider (e.g., gmail.com, yahoo.com, outlook.com).");
            return;
        }
    }

}
