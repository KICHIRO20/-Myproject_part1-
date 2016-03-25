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

class do_customers_export extends AjaxAction
{
    function do_customers_export()
    {}

    function onAction()
    {
        global $application;
        $request = &$application->getInstance('Request');
        $ce_target = $request->getValueByKey('ce_target');

        global $_RESULT;

        switch($ce_target)
        {
            case 'init':
                $headers = array_map(array(&$this,"_add_header_prefix"),$request->getValueByKey('attrs'));
                ksort($headers);
                $sets = array(
                    'script_code' => 'Customers_DB_CSV'
                   ,'script_step' => 1
                   ,'customers_filter' => unserialize(gzinflate(base64_decode($request->getValueByKey('customers_filter'))))
                   ,'headers' => $headers
                   ,'out_file' => $application->getAppIni('PATH_CACHE_DIR').'customers.csv'
                   ,'csv_delimiter' => ';'
                );
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
            case 'get_csv':
                header ('Pragma: no-cache');
                header ('Expires: ' . gmdate("D, d M Y H:i:s", time()) . ' GMT');
                header ('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header ('Content-Type: application/csv');
                header ('Content-Length: '.filesize($application->getAppIni('PATH_CACHE_DIR').'customers.csv'));
                header ('Content-Disposition: attachment; filename="customers.csv"');
                $csv_file = fopen($application->getAppIni('PATH_CACHE_DIR').'customers.csv','r');
                while(!feof($csv_file))
                    echo fread($csv_file,4096);
                fclose($csv_file);
                unlink($application->getAppIni('PATH_CACHE_DIR').'customers.csv');
                die();
                break;
        };
    }

    function _add_header_prefix($a)
    {
        return 'Customer'.$a;
    }
};

?>