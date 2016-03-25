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

class do_products_import extends AjaxAction
{
    function do_products_import()
    {
    }

    function onAction()
    {
        global $application;
        $request = &$application->getInstance('Request');
        $pi_target = $request->getValueByKey('pi_target');

        global $_RESULT;

        switch($pi_target)
        {
            case 'init':
/*                $sets['script_code'] = 'Products_CSV_DB';
                $sets['script_step'] = $request->getValueByKey('script_step');
                $sets['src_file'] = $request->getValueByKey('src_file');
                $sets['csv_delimiter'] = $this->CSV_DELIMITERS[$request->getValueByKey('csv_delimiter')];
                $sets['target_ptype'] = $request->getValueByKey('target_ptype');
                $sets['target_category'] = $request->getValueByKey('target_category');
                $sets['cache_dir'] = $application->getAppIni('PATH_CACHE_DIR').'_import_cache/';
                $sets['src_images_dir'] = $request->getValueByKey('src_images_dir');
                $sets['autoclear'] = $request->getValueByKey('autoclear')=='Y' ? true : false;*/

                CCacheFactory::clearAll();

                $sets = array(
                    'script_code' => 'Products_CSV_DB'
                   ,'script_step' => $request->getValueByKey('script_step')
                   ,'cache_dir' => $application->getAppIni('PATH_CACHE_DIR').'_import_cache/'
                );

                if($sets['script_step']==1)
                {
                    $sets = array_merge($sets,array(
                        'src_file' => $request->getValueByKey('src_file')
                       ,'header_rx' => "/^Product[A-Za-z0-9_]+$/"
                    ));
                };

                if($sets['script_step']==2)
                {
                    $sets = array_merge($sets,array(
                        'target_ptype' => $request->getValueByKey('target_ptype')
                       ,'target_category' => $request->getValueByKey('target_category')
                       ,'src_images_dir' => $request->getValueByKey('src_images_dir')
                       ,'src_images_tar' => $request->getValueByKey('src_images_tar')
                       ,'autoclear' => true
                    ));
                };

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
        };
    }

};

?>