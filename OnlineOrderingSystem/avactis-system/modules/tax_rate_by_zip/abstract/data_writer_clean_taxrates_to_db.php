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
loadClass('DataWriterDefault');
loadCoreFile('csv_parser.php');

class DataWriterCleanTaxRatesToDB extends DataWriterDefault
{

//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

	function DataWriterCleanTaxRatesToDB()
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
        $this->clearWork();

        $this->_settings = array(
            'out_file' => $settings['out_file']
           ,"sid"      => $settings["sid"]
           ,"updateSid" => $settings["updateSid"]
           ,'headers'  => $settings['headers']
           ,'csv_delimiter' => ','
           ,"total_string_number" => 0
           ,"valid_string_number" => 0
        );

        $_SESSION["rates_import_data"] = array();
        modApiFunc("TaxRateByZip", "clearSetInDB", $this->_settings["sid"]);
	    $this->_messages = getMsg('TAX_ZIP', 'IMPORT_SET_STARTING_IMPORT');
        $this->_process_info['status'] = 'INITED';
	}

 	/**
 	 *                            .
     *
     * @param array $data -                      '<tag name>' => '<tag value>'
 	 * @param ref array of strings $warnings -                      ,
     * @param ref array of strings $errors -              ,
 	 */
	function doWork($data)
	{
        $_SESSION["rates_import_data"][] = $data["item_data"];
	}

    function finishWork()
    {
        global $application;

        // activate set
        modApiFunc("TaxRateByZip", "activateSetInDB", $this->_settings["sid"]);

        // delete temp file
        if (is_file($application->getAppIni("PATH_CACHE_DIR")."__clean_tax_rates.csv"))
            unlink($application->getAppIni("PATH_CACHE_DIR")."__clean_tax_rates.csv");

        if ($this->_settings["updateSid"])
        {
            $this->_settings["substitute"] = true;
        }
    }

    function saveWork()
    {
        modApiFunc("TaxRateByZip", "addRatesArrayToDB", $_SESSION["rates_import_data"], $this->_settings["sid"]);
        $_SESSION["rates_import_data"] = array();

        if (isset($this->_settings["substitute"]) && $this->_settings["substitute"] == true)
        {
            modApiFunc("TaxRateByZip", "substituteSetInDB", $this->_settings["updateSid"], $this->_settings["sid"]);
        }

        modApiFunc('Session','set','DataWriterCleanTaxRatesToDB',$this->_settings);
    }

    function loadWork()
    {
        if(modApiFunc('Session','is_set','DataWriterCleanTaxRatesToDB'))
        {
            $this->_settings = modApiFunc('Session','get','DataWriterCleanTaxRatesToDB');
            return;
        };

        $this->_settings = null;
        $this->_csv_worker = null;
    }

    function clearWork()
    {
        modApiFunc('Session','un_set','DataWriterCleanTaxRatesToDB');
        $this->_settings = null;
        $this->_csv_worker = null;
    }
}



?>