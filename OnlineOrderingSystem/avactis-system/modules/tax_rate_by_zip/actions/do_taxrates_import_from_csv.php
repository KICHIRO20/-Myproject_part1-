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

class DoTaxRatesImportFromCSV extends AjaxAction
{
    function DoTaxRatesImportFromCSV()
    {
    }

    function onAction()
    {
        global $application;
        $request = &$application->getInstance('Request');
        $sub_action = $request->getValueByKey('sub_action');

        $out_file_path = $application->getAppIni('PATH_CACHE_DIR').'__clean_tax_rates.csv'; //

        global $_RESULT;

        switch($sub_action)
        {
            case 'init':
                $sets = array();
                $headers = array("ZipCode", "Zip5Low", "Zip5High", "Zip5Mask", "Zip4Low", "Zip4High", "SalesTaxRatePercent");
                $sets['src_file'] = $request->getValueByKey('input_csv_file');
                $sets['out_file']= $out_file_path;
                //$sets['csv_delimiter'] = ',';
                $sets['header_rx'] = '/^[A-Za-z0-9]*$/';
                $sets['script_code'] = 'TaxRates_CSV_DB';
                $sets['script_step'] = 1;
                $sets["headers"] = $headers;
                modApiFunc('Data_Converter','initDataConvert',$sets);
                $_RESULT["errors"] = modApiFunc('Data_Converter','getErrors');
                $_RESULT["warnings"] = modApiFunc('Data_Converter','getWarnings');
                $_RESULT["messages"] = modApiFunc('Data_Converter','getMessages');
                $_RESULT["process_info"] = modApiFunc('Data_Converter','getProcessInfo');
                break;
            case 'do':
                modApiFunc('Data_Converter','doDataConvert');
                $_RESULT["errors"] = modApiFunc('Data_Converter','getErrors');
                $_RESULT["warnings"] = modApiFunc('Data_Converter','getWarnings');
                $_RESULT["messages"] = modApiFunc('Data_Converter','getMessages');
                $_RESULT["process_info"] = modApiFunc('Data_Converter','getProcessInfo');
                break;

            case 'initStep2':
                $sets = array();
                $headers = array("ZipCode", "Zip5Low", "Zip5High", "Zip5Mask", "Zip4Low", "Zip4High", "SalesTaxRatePercent");
                $sets["sid"] = $request->getValueByKey("sid");
                $sets["updateSid"] = $request->getValueByKey("updateSid", 0);
                $sets['src_file'] = $out_file_path;
                $sets['out_file']= $out_file_path;
                $sets['script_code'] = 'TaxRates_CSV_DB';
                $sets['script_step'] = 2;
                $sets["headers"] = $headers;
                modApiFunc('Data_Converter','initDataConvert',$sets);
                $_RESULT["errors"] = modApiFunc('Data_Converter','getErrors');
                $_RESULT["warnings"] = modApiFunc('Data_Converter','getWarnings');
                $_RESULT["messages"] = modApiFunc('Data_Converter','getMessages');
                $_RESULT["process_info"] = modApiFunc('Data_Converter','getProcessInfo');
                break;
            case 'doStep2':
                modApiFunc('Data_Converter','doDataConvert');
                $_RESULT["errors"] = modApiFunc('Data_Converter','getErrors');
                $_RESULT["warnings"] = modApiFunc('Data_Converter','getWarnings');
                $_RESULT["messages"] = modApiFunc('Data_Converter','getMessages');
                $_RESULT["process_info"] = modApiFunc('Data_Converter','getProcessInfo');
                break;
        };
    }
};

?>