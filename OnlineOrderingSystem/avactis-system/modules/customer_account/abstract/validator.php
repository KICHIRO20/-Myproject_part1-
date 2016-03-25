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
 * @package CustomerAccount
 * @author Egor V. Derevyankin
 *
 */

class CAValidator
{
    function CAValidator()
    {}

    function isValid($attr, &$value)
    {
        $method_name = "__is_valid_";

        switch(_ml_strtolower($attr))
        {
            case 'accountname':
            case 'email':
            case 'passwd':
            case 'country':
            case 'country_state':
                $method_name .= $attr;
                break;
            default:
                $method_name .= 'string';
                break;
        };

        if(method_exists($this,$method_name))
        {
            return $this->$method_name($value);
        }
        else
        {
            return false;
        };
    }

    function __is_valid_accountname($value)
    {
        if(!is_string($value))
        {
            return false;
        };

        $login_rx = "/^[a-z0-9_\-\.\@]+$/i";
        return (preg_match($login_rx,$value) == 1);
    }

    /**
     * @param string $value
     */
    function __is_valid_email($value)
    {
        if(!is_string($value) || (strlen($value) > 254))
        {
            return false;
        };
        $value = trim($value);

	$email_rx = "/^([0-9a-zA-Z]([-.\w]*[0-9a-zA-Z])*@([0-9a-zA-Z]+[-\w]*[0-9a-zA-Z]\.)+[a-zA-Z]{2,9})$/";

        return (preg_match($email_rx,$value) == 1);
    }

    /**
     * @param array $value
     * array(
     *   'passwd' => string
     *   're-type' => string
     * );
     */
    function __is_valid_passwd($value)
    {
        if(!is_array($value)
            or count($value) != 2
            or !array_key_exists('passwd',$value)
            or !array_key_exists('re-type',$value))
        {
            return false;
        };

        $filtered = array_filter($value,'is_string');
        if(empty($filtered))
        {
            return false;
        };

        $filtered = array_filter($value);
        if(count($filtered) != 2)
        {
            return false;
        };

        if($value['passwd'] !== $value['re-type'])
        {
            return false;
        };

        return true;
    }

    function __is_valid_country($country_id)
    {
        if(modApiFunc('Location','getCountryCode',$country_id) != "")
        {
            return true;
        }
        else
        {
            return false;
        };
    }

    function __is_valid_country_state(&$country_state_array)
    {
        if($country_state_array['state_id'] != null)
        {
            $states = modApiFunc('Location','getStates',$country_state_array['country_id']);
            if(array_key_exists($country_state_array['state_id'], $states))
            {
                return true;
            }
            else
            {
                return false;
            };
        };

        if($country_state_array['state_text'] != null)
        {
            if($this->__is_valid_string($country_state_array['state_text']))
            {
                $country_state_array['state_id'] = $country_state_array['state_text'];
                return true;
            }
            else
            {
                return false;
            };
        };

        return false;
    }

    function __is_valid_string(&$string)
    {
        if(!is_string($string))
        {
            return false;
        };

        if(_ml_strlen($string) > 256)
        {
            $string = _ml_substr($string, 0, 256);
            return true;
        };

        return true;
    }
};

?>