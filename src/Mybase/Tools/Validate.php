<?php

namespace App\Mybase\Tools;

class Validate
{
    protected static $REGEX_PATTERN_DOMAIN = "/^(?!\-)(?:(?:[a-zA-Z\d][a-zA-Z\d\-]{0,61})?[a-zA-Z\d]\.){1,126}(?!\d+)[a-zA-Z\d]{1,63}$/i";

    /**
     * Validate the provided domain name
     * 
     * @param string|null $domainName The domain to validate
     * @return bool
     */
    public static function domain(?string $domainName): bool
    {
        if (!$domainName) {
            return false;
        }

        return (preg_match(self::$REGEX_PATTERN_DOMAIN, $domainName));
    }
}
