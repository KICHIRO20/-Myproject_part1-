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

define('LICENSE_KEY_UNDEFINED','UNDEFINED');

define("LICENSE_KEY_FILE_OK","LICENSE_KEY_FILE_OK");
define('LICENSE_KEY_NOT_EXISTS','LICENSE_KEY_NOT_EXISTS');
define('LICENSE_KEY_NOT_READABLE','LICENSE_KEY_NOT_READABLE');
define('LICENSE_KEY_NOT_WRITEABLE','LICENSE_KEY_NOT_WRITEABLE');
define("LICENSE_KEY_FORMAT_INVALID","LICENSE_KEY_FORMAT_INVALID");

define("KEY_LENGTH",20);
define("KEY_DELIMITER","-");

define("KEY_BAD","KEY_BAD");
define("KEY_CHECKSUM_OK","KEY_CHECKSUM_OK");

/**
 * Class can be used for getting a license key.
 * For this purpose method LicenseKey::getLicenseKey is used.
 *
 * An example usage:
 *<code>
 *  $lko = new LicenseKey()
 *  $license_key = $lko->getLicenseKey();
 *</code>
 *
 * If getting the license key fails the constant value
 * LICENSE_KEY_UNDEFINED will be returned.
 *
 * @package Core
 * @author Alexey Florinsky
 * @access  public
 */
class LicenseKey
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
    function LicenseKey()
    {
        global $application;

        $extention = ".php";
        $this->license_key_file = $application->getAppIni('PATH_LICENSE_KEY_FILE').$extention;
        /*if (!is_file($this->license_key_file))
        {
            $extention = "";
            $this->license_key_file = $application->getAppIni('PATH_LICENSE_KEY_FILE');
        }*/
        $this->license_key_status = LICENSE_KEY_FILE_OK;
        $this->license_key = $this->_read_key_from_file($this->license_key_file, $extention);

        if (!$this->_is_license_key_valid($this->license_key))
        {
            $this->license_key_status = LICENSE_KEY_FORMAT_INVALID;
            $this->license_key = LICENSE_KEY_UNDEFINED;
        }
    }

    function saveLicenseKey($key)
    {
        $key_status = $this->_save_key_to_file($this->license_key_file, $key);
        $this->license_key_status = $key_status;
        return $key_status;
        /*if ($key_status === LICENSE_KEY_FILE_OK)
        {
            return true;
        }
        else
        {
            return false;
        }*/
    }

    function getLicenseKey()
    {
        return $this->license_key;
    }

    function checkLicenseKey($key)
    {
        if (!$this->_is_license_key_valid($key))
        {
            return false;
        }
        return true;

    }

    function getLicenseURL()
    {
        global $application;
        global $zone;
        if($zone == "AdminZone")
        {
        	$not_formatted_url = $application->getAppIni("SITE_AZ_URL");
        }
        else
        {
            $not_formatted_url = $application->getAppIni("SITE_URL");
        }
        $url_parts = parse_url($not_formatted_url);

        $pos = _ml_strpos($url_parts["path"], "/avactis-system");
        if (!($pos === false))
        {
            $url_parts["path"] = _ml_substr($url_parts["path"], 0, $pos+1);
        }

        return 'http://'.$url_parts["host"].$url_parts["path"];
    }


    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

     /**
     * Converts an integer into hex presentation
     *
     * @param int $dec
     * @param int $align number of bytes for hex value
     * @example _dec(10,8) returns 0000000A
     * @return int(hex)
     */
    function _dechex($dec,$align=8)
    {
        $h = dechex($dec);
        $l = _byte_strlen($h);
        if ($l < $align)
        {
            for ($i=0;$i<$align-$l;$i++)
            {
                $h = "0".$h;
            }
        }
        else if ($l > $align)
        {
            $h = _byte_substr($h,$l-$align,$l);
        }
        return _ml_strtoupper($h);

    }

    /**
     * Generates a checksum for given hex value $a
     *
     * @param string $a string of hex representation
     * @return string(hex) check sum
     */
    function generateCheckSum($a)
    {
      $left = 0x0056;
      $right = 0x00AF;
      if (_byte_strlen($a) > 0)
      {
        for ($i=1;$i<_byte_strlen($a);$i++)
        {
          $right = $right + hexdec($a{$i});
          if ($right > 0x00FF)
          {
            $right = $right - 0x00FF;
            $left = $left + $right;
          }
          if ($left > 0x00FF)
            $left = 0x00FF;
        }
      }
      $sum = ($left << 8) + $right;
      $result = $this->_dechex($sum,4);
      return $result;
    }
    /**
     * Performs a basic checksum check of a given key.
     * we calculate the checksum from the first 16 bytes of the key (as on the key creation step) and compare the calculated value with the last 4 bytes of the given key. In order to successful check the values have to match.
     *
     * @param string $key key
     * @return string key status. KEY_BAD - the checksum is invalid, KEY_CHECKSUM_OK is good
     */
    function CheckByCheckSum($key)
    {
         $key = $this->_prepareKey($key);
         if ($key === KEY_BAD)
             return $key;
         else
         {
             $s = _byte_substr($key,0,16);
             $cs = _byte_substr($key,16,20);
             $key_checksum = _ml_strtoupper($this->generateCheckSum($s));
             if ($key_checksum != $cs)
             {
                return KEY_BAD;
             }
             else
             {
                return KEY_CHECKSUM_OK;
             }
         }
    }

    /**
     * Converts the key into standart format (XXXX-XXXX-XXXX-XXXX-XXXX)
     * @param string $key
     * @return string or bool if $key is not a valid key
     */
    function formatKey($key)
    {
        if ($this->_is_license_key_valid($key))
        {
            $_key = preg_replace("/[^0-9a-fA-F]/i","",$key);
            $result = _ml_strtoupper(_byte_substr(chunk_split($_key,4,KEY_DELIMITER),0,24));
            return $result;
        }
        else
            return false;
    }

    /**
     * Converts the user inputed key into generic format (uppercase characters, no delimiters)
     * and applies the basic check (by length)
     *
     * @param string $key
     * @return string generic key representation
     *
     **/
    function _prepareKey($key)
    {
        if ($key == null || _byte_strlen($key)==0)
        {
            return KEY_BAD;
        }
        else
        {
            $_key = preg_replace("/[^0-9a-fA-F]/i","",$key);
            if (_byte_strlen($_key) != KEY_LENGTH)
                return KEY_BAD;

            return _ml_strtoupper($_key);
        }
    }

    /**
     * Does a syntaxical checking of the license key.
     * The length of the key must be exactly 8 symbols,
     * the allowable set of symbols is [0-9A-Z]
     *
     * @param string $key is a string for checking
     * @return bool TRUE if $key satisfies the requirements, FALSE otherwise
     */
    function _is_license_key_valid($key)
    {
        if ($this->CheckByCheckSum($key) === KEY_CHECKSUM_OK)
            return true;
        else
            return false;
    }

    /**
     * Checking the access and rights to license key file
     *
     * @param string $file - a path to license key file
     * @return bool if the file is present and can be read and written returns ok-status, error code otherwise
     */
    function _check_key_file($file)
    {
        $file_path = realpath($file);
        if (!empty($file_path) && file_exists($file_path)) // file exists
        {
            if (is_readable($file_path))
            {
                if (is_writeable($file_path))
                {
                    return LICENSE_KEY_FILE_OK;
                }
                else
                    return LICENSE_KEY_NOT_WRITEABLE;
            }
            else
                return LICENSE_KEY_NOT_READABLE;
        }
        else
        {
            return LICENSE_KEY_NOT_EXISTS;
        }
    }

    function _save_key_to_file($file, $key)
    {
        if ($this->_is_license_key_valid($key))
        {
            $_key = $this->formatKey($key);
            if ($_key !== false)
            {
                $str = ";<?php exit(); ?>\n";
                $str .= "key = ". $_key;

                $fp = new CFile($file);
                if (! $fp->putContent($str)) {
                    return LICENSE_KEY_NOT_WRITEABLE;
                }

                #$this->license_key = $_key;
                $this->license_key = $this->_read_key_from_file($this->license_key_file, ".php");

                if (!$this->_is_license_key_valid($this->license_key))
                {
                    $this->license_key = LICENSE_KEY_UNDEFINED;
                    return LICENSE_KEY_FORMAT_INVALID;
                }
                else
                    return LICENSE_KEY_FILE_OK;
            }
            else
               return LICENSE_KEY_FORMAT_INVALID;
        }
        else
            return LICENSE_KEY_FORMAT_INVALID;
    }

    function _read_key_from_file($file, $extention)
    {
        $key = '';
        if ($extention == ".php")
        {
            CProfiler::ioStart($file, 'parse');
            $key = @parse_ini_file($file);
            CProfiler::ioStop();
            if ($key === false) # invalid file format: cannot be parsed
            {
                $this->license_key_status = LICENSE_KEY_FORMAT_INVALID;
                return LICENSE_KEY_FORMAT_INVALID;
            }

            $key = @$key["key"];
        }

        return $this->formatKey($key);
    }

    var $license_key;
    var $license_key_file;
    var $license_key_status = "";
    /**#@-*/

}

?>