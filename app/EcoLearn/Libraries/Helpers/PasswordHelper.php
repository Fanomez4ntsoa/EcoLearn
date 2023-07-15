<?php

namespace App\EcoLearn\Libraries\Helpers;

class PasswordHelper
{
    /**
     * Check if password is Valid
     *
     * @param string $password
     * @return boolean|null
     */
    public static function isValid(string $password): ?bool
    {
        if (strlen($password) < 8) {
            return false;
        }

        if (!preg_match('/^(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9])/', $password)) {
            return false;
        }

        if (strpos($password, ' ') !== false) {
            return false;
        }
        return true;
    }
}