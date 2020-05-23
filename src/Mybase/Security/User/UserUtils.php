<?php

namespace App\Mybase\Security\User;

class UserUtils
{
    CONST PASSWORD_MIN_LENGHT = 8;
    CONST PASSWORD_MAX_LENGHT = 255;

    /**
     * Validate username
     * Rules: An username should contain at least 5 characters and shouldn't contains any espace or special characters except "_" and "@"
     * 
     * @param string $username The username to validate
     * @return string The validated username
     * @throws Exception If the username doesn't respect the Rules
     */
    public static function validateUsername(string $username): string
    {
        $pattern = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i";
        if (preg_match($pattern, $username)) {
            return $username;
        }

        throw new \Exception(sprintf('"%s" is not an username valid. An username valid must contains at least 5 characters and shouldn\'t contains any espace or special characters except "_" and "@"', $username));
    }

    /**
     * Checks if the given password is valid
     * Rules: A strong password should contain at least 8 characters and include lowercase, uppercase, numbers and various special characters
     * 
     * @param int $password
     * @param int $min The minimum password lenght accepted
     * @param int $max The maximum password lenght accepted
     * @return string The validated password
     */
    public static function validatePassword(string $password, bool $strongPassword = true, int $min = self::PASSWORD_MIN_LENGHT, int $max = self::PASSWORD_MAX_LENGHT): string
    {
        /** REGEX EXPLAINATION
         * 
         * ^                The password string will start this way
         * (?=.*[a-z])      The string must contain at least 1 lowercase alphabetical character
         * (?=.*[A-Z])      The string must contain at least 1 uppercase alphabetical character
         * (?=.*[0-9])      The string must contain at least 1 numeric character
         * (?=.*[!@#$%^&*]) The string must contain at least one special character, but we are escaping reserved RegEx characters to avoid conflict
         * (?=.{PASSWORD_MIN_LENGHT, PASSWORD_MAX_LENGHT})  The string must be in range of PASSWORD_MIN_LENGHT and PASSWORD_MAX_LENGHT characters
         * 
         * @credits https://www.thepolyglotdeveloper.com/2015/05/use-regex-to-test-password-strength-in-javascript 
         */
        $passwordRegex = $strongPassword ? 
            '^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{PASSWORD_MIN_LENGHT,PASSWORD_MAX_LENGHT})': 
            '^(((?=.*[a-z])(?=.*[A-Z]))|((?=.*[a-z])(?=.*[0-9]))|((?=.*[A-Z])(?=.*[0-9])))(?=.{PASSWORD_MIN_LENGHT,PASSWORD_MAX_LENGHT})';

        $passwordRegex = str_replace(['PASSWORD_MIN_LENGHT', 'PASSWORD_MAX_LENGHT'], [$min, $max], $passwordRegex);

        if (preg_match("/$passwordRegex/", $password)) {
            return $password;
        }

        $strongPasswordMessage = "A STRONG password should contain at least $min characters and include lowercase, uppercase, numbers and various special characters";
        $mediumPasswordMessage = "A MEDIUM password should contain at least $min characters and include lowercase and uppercase";

        throw new \Exception(sprintf('"%s" is not a password valid. %s', $password, $strongPassword ? $strongPasswordMessage : $mediumPasswordMessage));
    }

    /**
     * Generates new token
     * This method is from FOS\UserBundle\Util\TokenGenerator
     * 
     * @return string
     */
    public static function generateToken(): string
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }
}
