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
loadClass('DataFilterDefault');

class DataFilterCSVTaxRatesToClean extends DataFilterDefault
{

//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

	function DataFilterCSVTaxRatesToClean()
	{
	}

	/**
	 *               -
	 *
	 * @param array $settings -        settings
	 * @param ref array of strings $warnings -                      ,
     * @param ref array of strings $errors -              ,
	 */
	function initWork($settings)
	{
	    if ($settings["script_step"] == 1)
	    {
	        // check headers
    	    $csv_headers = @func_get_arg(1);

    	    $msg = array();
    	    if (!$this->areHeadersValid($csv_headers, $msg))
    	       $this->_errors = getMsg('TAX_ZIP', 'IMPORT_SET_WRONG_HEADERS');;

    	    $this->_settings["headers"] = $csv_headers;

    	    $str = '';
    	    foreach ($msg as $key => $value) {
    	    	if ($key) $str .= ", ";
    	    	$str .= $value;
    	    }

    	    $this->_messages = getMsg('TAX_ZIP', 'IMPORT_SET_WE_HAVE_HEADERS') . $str;
	    }
        $this->_process_info['status'] = 'INITED';
	}

    function finishWork()
    {
    }

    function saveWork()
    {
  //      $this->_warnings = "save work";
    }

    function loadWork()
    {
        $this->_warnings = "";
        $this->_messages = "";
    }

    function clearWork()
    {
  //      $this->_warnings = "clear work";
    }

 	/**
     *                                             .
     *
     * @param array $data -
     * @param ref array of strings $warnings -                      ,
     * @param ref array of strings $errors -              ,
     * @return array of arrays of     '<tag name>' => '<tag value>'
     */
	function doWork($data)
	{
	    if ($this->isDataStringValid($data["item_data"]))
	    {
           $data["item_data"] = $this->fillUpDataString($data["item_data"]);
	    }
	    else
	    {
	       $data["item_data"]["isValid"] = false;
	    }

		return $data;
	}

	/**
	 * function checks, if necessary headers exist in csv file
	 * Rule = (Zip5 or Zip5Low or Zip5High) and (Rate)
	 *
	 * @param array $headers
	 * @return bool
	 */
	function areHeadersValid($headers, &$msg)
	{
	    if (!is_array($headers) || empty($headers))
	       return false;

	    $zipcode_present = false;
	    $zip5low_present = false;
	    $zip5high_present = false;
	    $rate_present = false;

	    foreach ($headers as $value)
	    {
	        if (_ml_strtolower($value) == "zipcode")
	        {
	           $zipcode_present = true;
	           $msg[] = "ZipCode";
	        }
            if (_ml_strtolower($value) == "zip5low")
            {
               $zip5low_present = true;
               $msg[] = "Zip5Low";
            }
            if (_ml_strtolower($value) == "zip5high")
            {
               $zip5high_present = true;
               $msg[] = "Zip5High";
            }
            if (_ml_strtolower($value) == "zip4low")
            {
                $msg[] = "Zip4Low";
            }
            if (_ml_strtolower($value) == "zip4high")
            {
                $msg[] = "Zip4High";
            }
            if (_ml_strtolower($value) == "salestaxratepercent")
            {
               $rate_present = true;
               $msg[] = "SalesTaxRatePercent";
            }
	    }

	    if (!$rate_present)
	       return false;

	    if (!($zipcode_present || $zip5low_present))
	       return false;

	    return true;
	}

	function isDataStringValid($str)
	{
	    if (!isset($str["SalesTaxRatePercent"]) || $str["SalesTaxRatePercent"] == '')
        {
     //       $this->_warnings = "                ".',                     SalesTaxRatePercent.';
            return false;
        }

	    if (
	          (!isset($str["ZipCode"]) || $str["ZipCode"] == 0 || $str["ZipCode"] == '')
	       && (!isset($str["Zip5Low"]) || $str["Zip5Low"] == 0 || $str["Zip5Low"] == '')
	       )
	    {
	//        $this->_warnings = "                ".',     ZipCode.'.print_r($this->_process_info, true);
	        return false;
	    }

	 //   $this->_warnings = '';
	    return true;
	}

	function fillUpDataString($str)
	{
	    $new_str = array(
	                        "ZipCode"  => 0
	                       ,"Zip5Low"  => 0
	                       ,"Zip5High" => 0
	                       ,"Zip5Mask" => '^$'
	                       ,"Zip4Low"  => 0
	                       ,"Zip4High" => 0
	                       ,"SalesTaxRatePercent" => $this->fetchRate($str["SalesTaxRatePercent"])
	                       ,"isValid"  => true
	    );

	    // Zip5 && Zip5Mask
	    if (isset($str["ZipCode"]) && $str["ZipCode"] != '')
	    {
	        if (_ml_strpos($str["ZipCode"], '*') !== FALSE)
	        {
	            $new_str["Zip5Mask"] = $this->fillUpZip5Mask($str["ZipCode"]);
	        }
	        else
	        {
	           $new_str["ZipCode"] = intval($str["ZipCode"]);
	        }
	    }
	    else if (isset($str["Zip5Low"]) && $str["Zip5Low"] != '')
	    {
            if (_ml_strpos($str["Zip5Low"], '*') !== FALSE)
            {
                $new_str["Zip5Mask"] = $this->fillUpZip5Mask($str["Zip5Low"]);
            }
            else
            {
               $new_str["ZipCode"] = intval($str["Zip5Low"]);
            }
	    }

	    // Zip5Low && Zip5High
	    if (isset($str["Zip5Low"])         // Zip5Low present
	        && $str["Zip5Low"] != ''       // and Zip5Low is not empty
	        && isset($str["Zip5High"])     // and Zip5High present
	        && $str["Zip5High"] != ''      // and Zip5High is not empty
	        && _ml_strpos($str["Zip5Low"], '*') === FALSE) // and Zip5Low not a mask
	    {
            $new_str["Zip5Low"] = intval($str["Zip5Low"]);
            $new_str["Zip5High"] = intval($str["Zip5High"]);
	    }

        // Zip4Low - 0
        if (!isset($str["Zip4Low"]) || $str["Zip4Low"] == 0)
        {
            $new_str["Zip4Low"] = 0;
        }
        else if (isset($str["Zip4Low"]))
        {
            $new_str["Zip4Low"] = intval($str["Zip4Low"]);
        }

        // Zip4High - 9999
        if (!isset($str["Zip4High"]) || $str["Zip4High"] == 0)
        {
            $new_str["Zip4High"] = 9999;
        }
        else if (isset($str["Zip4High"]))
        {
            $new_str["Zip4High"] = intval($str["Zip4High"]);
        }

        return $new_str;
	}

	function fillUpZip5Mask($zip5)
	{
        $zip5 = str_replace("*****", "[0-9]{1,5}", $zip5);
        $zip5 = str_replace("****",  "[0-9]{1,5}", $zip5);
        $zip5 = str_replace("***",   "[0-9]{1,5}", $zip5);
        $zip5 = str_replace("**",    "[0-9]{1,5}", $zip5);
        $zip5 = str_replace("*",     "[0-9]{1,5}", $zip5);
        $zip5 = "^$zip5$";
        return $zip5;
	}


	/**
	 * function gets a string, possibly representing a float value
	 * it is an absolute value
	 * we need a float percentage value
	 *
	 * @param unknown_type $rate
	 */
	function fetchRate($rate)
	{
	    $rate = preg_replace("/[^0-9.,]/", '', $rate);
	    return floatval(str_replace(',', '.', $rate));
	}
}


?>