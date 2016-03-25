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
 * UUIDUtils class.
 *                                                .
 *
 * An example of encoding URL to get SEF URL:
 * <code>
 * ...
 * <code>
 *
 * @package Core
 * @author  Vadim Lyalikov
 * @access  public
 */

class UUIDUtils
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Object constructor
     */
    function UUIDUtils()
    {
    }

    function convert($from_style, $to_style, $text)
    {
        switch($from_style)
        {
            case "minuses_and_capitals":
            {
                switch($to_style)
                {
                    case "js":
                        return str_replace("-", "_", $text);
                };
            };
        };
        return false;
    }

    static function getRegexpPattern($style = "minuses_and_capitals")
    {
        switch($style)
        {
            case "minuses_and_capitals":
                return "/([0-9A-F]{8}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{12})$/";
            case "js":
                return "/([0-9A-F]{8}_[0-9A-F]{4}_[0-9A-F]{4}_[0-9A-F]{4}_[0-9A-F]{12})$/";
            default:
                return false;
        }
    }

    function getLength($style = "minuses_and_capitals")
    {
        switch($style)
        {
            case "minuses_and_capitals":
                return _ml_strlen("AAAAAAAA-AAAA-AAAA-AAAA-AAAAAAAAAAAA");
            case "js":
                return _ml_strlen("AAAAAAAA_AAAA_AAAA_AAAA_AAAAAAAAAAAA");
            default:
                return false;
        }
    }

    static function cut_uuid_suffix($text, $style)
    {
        //                . CCInfo             .
        // :                  getValidatedDataStructure            PersonInfoVariantID,         .
        $uuid_pattern = UUIDUtils::getRegexpPattern($style);
        $matches = array();
        if(preg_match($uuid_pattern, $text, $matches))
        {
            $text = _ml_substr($text, 0, _ml_strlen($text) - UUIDUtils::getLength($style));
        }
        else
        {
        }
        return $text;
    }

    /**#@-*/
}