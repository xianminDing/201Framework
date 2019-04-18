<?php

/*
|---------------------------------------------------------------
| Performs transformations on resource names, ie, urls, classes,
| methods, variables.
|---------------------------------------------------------------
| @package ZOL
|
*/

class ZOL_Inflector
{

    /*
    |---------------------------------------------------------------
    | Returns the full Manager name given the short name, ie,
    | faq becomes FaqMgr.
    |---------------------------------------------------------------
    | @param string $name
    | @return string
    |
    */
    public static function getControllerClassName($name)
    {
        //  if controller suffix has been left out, append it
        if (strtolower(substr($name, -3)) != 'Mgr') {
            $name .= 'Mgr';
        }
        return ucfirst($name);
    }

    /*
    |---------------------------------------------------------------
    | Converts "string with spaces" to "camelCase" string.
    |---------------------------------------------------------------
    | @param   string $s
    | @return  string
    |
    */
    public static function camelise($s)
    {
        $ret = '';
        $i = 0;

        $s = preg_replace('!\s+!', ' ', $s);
        $s = trim($s);
        $aString = explode(' ', $s);
        foreach ($aString as $value) {
            if ($i == 0) {
                $ret .= strtolower($value);
            } else {
                $ret .= ucfirst(strtolower($value));
            }
            $i++;
        }
        return $ret;
    }

    public static function getTitleFromCamelCase($camelCaseWord)
    {
        if (!self::isCamelCase($camelCaseWord)) {
            return $camelCaseWord;
        }
        $ret = '';
        for ($x = 0; $x < strlen($camelCaseWord); $x ++) {
            if (preg_match("/[A-Z]/", $camelCaseWord{$x})) {
                $ret .= ' ';
            }
            $ret .= $camelCaseWord{$x};
        }
        return ucfirst($ret);
    }

    public static function isCamelCase($str)
    {
        //  ensure no non-alpha chars
        if (preg_match("/[^a-z].*/i", $str)) {
            return false;
        }
        //  and at least 1 capital not including first letter
        for ($x = 1; $x < strlen($str)-1; $x ++) {
            if (preg_match("/[A-Z]/", $str{$x})) {
                return true;
            }
        }
        return false;
    }

    public static function isConstant($str)
    {
        if (empty($str)) {
            return false;
        }
        if (preg_match('/sessid/i', $str)) {
            return false;
        }
        $pattern = '@^[A-Z_\'][A-Z_0-9\']*$@';
        if (!preg_match($pattern, $str)) {
            return false;
        }
        return true;
    }


    /*
    |---------------------------------------------------------------
    | Returns a human-readable string from $lower_case_and_underscored_word,
    | by replacing underscores with a space, and by upper-casing the initial characters.
    |---------------------------------------------------------------
    | @param string $lower_case_and_underscored_word String to be
    |  made more readable
    | @return string Human-readable string
    |
    */
    public static function humanise($lowerCaseAndUnderscoredWord)
    {
        $replace = ucwords(str_replace('_', ' ', $lowerCaseAndUnderscoredWord));
        return $replace;
    }

    /*
    |---------------------------------------------------------------
    | Returns "Class_Name" as "Class/Name.php".
    |---------------------------------------------------------------
    | @param string $str The class name.
    | @return string The class as a file name
    |
    */
    public static function classToFile($str)
    {
        return ZOL_PATH . '/' . str_replace('_', '/', $str) . '.php';
    }
}
