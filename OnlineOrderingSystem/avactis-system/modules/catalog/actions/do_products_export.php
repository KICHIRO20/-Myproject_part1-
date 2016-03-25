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
 * @package Catalog
 * @author Egor V. Derevyankin
 *
 */

class do_products_export extends AjaxAction
{
    function do_products_export()
    {
    }

    function onAction()
    {
        global $application;
        $request = &$application->getInstance('Request');
        $pe_target = $request->getValueByKey('pe_target');

        $dateStr = date("Y-m-d_H-i-s");

        global $_RESULT;

        switch($pe_target)
        {
            case 'init':
                $sets = array(
                    'script_code' => 'Products_DB_CSV'
                   ,'script_step' => $request->getValueByKey('script_step')
                   ,'product_type_id' => 0
                   ,'product_category_id' => $request->getValueByKey('ProductCategory')
                   ,'categories_export_recursively' => $request->getValueByKey('Recursively')
                   ,'use_bulks' => true
                );

                if($sets['script_step']==1)
                {
                    $sets = array_merge($sets, array(
                        'need_attrs' => true
                    ));
                }

                if($sets['script_step']==2)
                {
                    $headers = array_map(array(&$this,"_add_header_prefix"),$request->getValueByKey('attrs'));
                    ksort($headers);
                    $sets = array_merge($sets, array(
                        'out_file' => $application->getAppIni('PATH_CACHE_DIR').'products.csv'
                       ,'headers' => $headers
                       ,'csv_delimiter' => ";"
                       ,'images_processing' => ($request->getValueByKey('images_processing')=='Y') ? true : false
                       ,'images_action' => $request->getValueByKey('images_action')
                       ,'images_tar_file' => ($request->getValueByKey('images_action') == 1) ? $application->getAppIni('PATH_CACHE_DIR').'images.tar' : $request->getValueByKey('images_dir_path').'images.tar'
                       ,'images_dir_path' => $request->getValueByKey('images_dir_path')
                    ));
                }

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
                header ("Pragma: no-cache");
                header ("Expires: " . gmdate("D, d M Y H:i:s", time()) . " GMT");
                header ("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header ("Content-Type: application/csv");
                header ("Content-Length: ".filesize($application->getAppIni('PATH_CACHE_DIR').'products.csv'));
                header ("Content-Disposition: attachment; filename=\"avactis-products_".$dateStr.".csv\"");
                $csv_file = fopen($application->getAppIni('PATH_CACHE_DIR').'products.csv','r');
                while(!feof($csv_file))
                    echo fread($csv_file,4096);
                fclose($csv_file);
                unlink($application->getAppIni('PATH_CACHE_DIR').'products.csv');
                die();
                break;
            case 'get_images':
                header ("Pragma: no-cache");
                header ("Expires: " . gmdate("D, d M Y H:i:s", time()) . " GMT");
                header ("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header ("Content-Type: application/tar");
                header ("Content-Length: ".filesize($application->getAppIni('PATH_CACHE_DIR').'images.tar'));
                header ("Content-Disposition: attachment; filename=\"avactis-product-images_".$dateStr.".tar\"");
                $csv_file = fopen($application->getAppIni('PATH_CACHE_DIR').'images.tar','r');
                while(!feof($csv_file))
                    echo fread($csv_file,4096);
                fclose($csv_file);
                unlink($application->getAppIni('PATH_CACHE_DIR').'images.tar');
                die();
                break;
        };
    }

    function _add_header_prefix($a)
    {
        return 'Product'.$a;
    }

};

?>