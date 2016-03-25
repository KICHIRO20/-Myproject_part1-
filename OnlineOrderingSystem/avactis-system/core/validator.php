<?php
/***********************************************************************
| Avactis (TM) Shopping Cart software developed by HBWSL.
| http://www.avactis.com
| -----------------------------------------------------------------------
| All source codes & content (c) Copyright 2004-2010, HBWSL.
| unless specifically noted otherwise.
| =============================================
| This source code is released under the Avactis License Agreement.
| The latest version of this license can be found here:
| http://www.avactis.com/license.php
|
| By using this software, you acknowledge having read this license agreement
| and agree to be bound thereby.
|
 ***********************************************************************/
?><?php

/**
 * @package Core
 * @author Alexey Florinsky
 *
 */

class Validator
{
    function Validator()
    {

    }

    function alwaysValid($value)
    {
        return true;
    }

    /**
     * @param string $value
     */
    function isValidEmail($value)
    {
        if(!is_string($value))
        {
            return false;
        };

        $value = trim($value);
        $email_rx = "/^[a-z0-9]+([\.\-_][a-z0-9_-]+)*@[a-z0-9\.\-]+?\.[a-z]{2,4}$/i";
        return (preg_match($email_rx,$value) == 1);
    }

    function isNotEmpty($value)
    {
        return !isempty($value);
    }

    function isValidInt($value)
    {
        return Validator::isNotEmpty($value) and ( (string)(intval($value)) == (string)$value );
    }

    function isValidArray($value)
    {
        return $value !== null and is_array($value);
    }

    function isValidFloat($value)
    {
        return Validator::isNotEmpty($value) and ( (string)(floatval($value)) == (string)$value );
    }

    function isValidStringMaxLength($value, $len)
    {
        return Validator::isNotEmpty($value) and ( _ml_strlen($value) <= $len );
    }

    function isValidStringMinLength($value, $len)
    {
        return Validator::isNotEmpty($value) and ( _ml_strlen($value) >= $len );
    }

};

?>