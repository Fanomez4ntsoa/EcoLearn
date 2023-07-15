<?php

namespace App\EcoLearn\Libraries\Helpers;

class EmailHelper
{
    /**
     * Check if email is Valid
     *
     * @param string $email
     * @return boolean|null
     */
    public static function isValid(string $email): ?bool
    {
        $pattern = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';

        $allowedDomains = [
            PATTERN_GMAIL,
            PATTERN_YAHOO,
            PATTERN_OUTLOOK,
            PATTERN_MAIL,
            PATTERN_CLOUD,
            PATTERN_MICROSOFT
        ];

        $allowedExtensions = [
            PATTERN_EXTENSION_GMAIL,
            PATTERN_EXTENSION_YAHOO,
            PATTERN_EDU
        ];

        if (preg_match($pattern, $email) === 1) {
            $parts = explode('@', $email);
            $domain = end($parts);
            $domainParts = explode('.', $domain);
            $extension = end($domainParts);

            return in_array($domain, $allowedDomains) && in_array($extension, $allowedExtensions);
        }

        return null;
    }
}