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
 * @package OrdersExport
 * @author Alexey Florinsky
 *
 */

class DoOrdersExportCSVAction extends AjaxAction
{
    function DoOrdersExportCSVAction()
    {
    }

    function onAction()
    {
        global $application;
        $request = &$application->getInstance('Request');
        $sub_action = $request->getValueByKey('sub_action');

        $out_file_path = $application->getAppIni('PATH_CACHE_DIR').'__orders.csv';

        global $_RESULT;

        switch($sub_action)
        {
            case 'init':
                $sets = array();
                $sets['orders_ids'] = $request->getValueByKey('orders_ids');
                $sets['out_file']= $out_file_path;
                $sets['csv_delimiter'] = ';';
                $sets['script_code'] = 'Orders_DB_CSV';
                $sets['script_step'] = 1;
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
            case 'get':
                $d = new CStoreDatetime();
                $datetime = date('Y-m-d-H-i-s', $d->getTimestamp());

                header ("Pragma: no-cache");
                header ("Expires: " . gmdate("D, d M Y H:i:s", time()) . " GMT");
                header ("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header ("Content-Type: application/csv");
                header ("Content-Length: ".filesize($out_file_path));
                header ("Content-Disposition: attachment; filename=\"orders-".$datetime.".csv\"");

                $iif_file = fopen($out_file_path,'r');
                while(!feof($iif_file))
                    echo fread($iif_file,4096);
                fclose($iif_file);
                unlink($out_file_path);
                die();
                break;
        };
    }
};

?>