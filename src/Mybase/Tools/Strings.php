<?php

namespace App\Mybase\Tools;

class Strings
{
    public static function reference(int $start = 8): string
    {
        $date       = new \DateTime();
        $dayMonth   = $date->format('dm');
        $year       = $date->format('y');

        return $dayMonth . '-' . strtoupper(substr(uniqid(), $start)) . '/' . $year;
    }

    public static function replaceLocahost(string $url, string $domain): string
    {
        return str_replace(['127.0.0.1', 'localhost'], $domain, $url);
    }

    public static function castToRealType($variable)
    {
        if (!$variable) {
            return null;
        }

        $variable = trim($variable);

        if (is_float($variable)) {
            return (float)$variable;
            
        } elseif (is_numeric($variable)) {
            return (int)$variable;

        } elseif (in_array($variable, ['yes', 'no', 'true', 'false'])) {
            switch ($variable) {
                case 'yes':
                case 'true':
                    return true;

                case 'no':
                case 'false':
                    return false;

                default:
                    return false;
            }
        } else {
            return (string)$variable;
        }
    }
}
