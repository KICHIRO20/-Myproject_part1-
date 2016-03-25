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
 * @package QuickBooks
 * @author Egor V. Derevyankin
 *
 */

class do_orders_export extends AjaxAction
{
    function do_orders_export()
    {
    }

    function onAction()
    {
        global $application;
        $request = &$application->getInstance('Request');
        $qbe_target = $request->getValueByKey('qbe_target');

        global $_RESULT;

        switch($qbe_target)
        {
            case 'init':
                $sets=modApiFunc('Quick_Books','getSettings');
                $sets['orders_ids'] = $request->getValueByKey('orders_ids');
                $sets['out_file']=$application->getAppIni('PATH_CACHE_DIR').'orders.iif';
                $sets['script_code'] = 'Orders_DB_IIF';
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
                header ("Pragma: no-cache");
                header ("Expires: " . gmdate("D, d M Y H:i:s", time()) . " GMT");
                header ("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header ("Content-Type: text/iif");
                header ("Content-Length: ".filesize($application->getAppIni('PATH_CACHE_DIR').'orders.iif'));
                header ("Content-Disposition: attachment; filename=\"orders.iif\"");
                $iif_file = fopen($application->getAppIni('PATH_CACHE_DIR').'orders.iif','r');
                while(!feof($iif_file))
                    echo fread($iif_file,4096);
                fclose($iif_file);
                unlink($application->getAppIni('PATH_CACHE_DIR').'orders.iif');
                die();
                break;
        };
    }
};

?>