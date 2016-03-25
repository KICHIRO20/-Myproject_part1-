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
 * @package MultiLang
 * @author Sergey Kulitsky
 *
 */

class do_import_labels extends AjaxAction
{
    function do_import_labels()
    {
    }

    function onAction()
    {
        global $application;
        $request = &$application -> getInstance('Request');
        $il_target = $request -> getValueByKey('il_target');

        global $_RESULT;

        switch($il_target)
        {
            case 'init':
                // clearing cache
                CCacheFactory::clearAll();

                // : here will come checking if the file is csv or xliff
                // for now: assuming it is a csv file

                $sets = array(
                    'script_code' => 'Labels_CSV_DB',
                    'script_step' => $request -> getValueByKey('script_step'),
                    'cache_dir'   => $application -> getAppIni('PATH_CACHE_DIR') . '_import_cache/'
                );

                if ($sets['script_step'] == 1)
                {
                    $sets['src_file'] = $request -> getValueByKey('src_file');
                };

                if ($sets['script_step'] == 2)
                {
                    $sets['filter'] = array(
                        'tt'   => array(
                            'found' => $request -> getValueByKey('tt_found'),
                            'new'   => $request -> getValueByKey('tt_new')
                        ),
                        'lang' => array(
                            'found' => explode('|',
                                         $request -> getValueByKey('l_found')),
                            'new'   => explode('|',
                                         $request -> getValueByKey('l_new')),
                        ),
                        'type' => array(
                            'found' => explode('|',
                                         $request -> getValueByKey('t_found')),
                            'new'   => explode('|',
                                         $request -> getValueByKey('t_new')),
                        )
                    );
                    $sets['autoclear'] = true;
                    $sets['def_lng'] = modApiFunc('MultiLang',
                                                  'getDefaultLanguage');
                }

                modApiFunc('Data_Converter', 'initDataConvert', $sets);
                $_RESULT["errors"] = modApiFunc('Data_Converter', 'getErrors');
                $_RESULT["warnings"] = modApiFunc('Data_Converter', 'getWarnings');
                $_RESULT["messages"] = modApiFunc('Data_Converter', 'getMessages');
                $_RESULT["process_info"] = modApiFunc('Data_Converter', 'getProcessInfo');
                break;

            case 'do':
                modApiFunc('Data_Converter', 'doDataConvert');
                $_RESULT["errors"] = modApiFunc('Data_Converter', 'getErrors');
                $_RESULT["warnings"] = modApiFunc('Data_Converter', 'getWarnings');
                $_RESULT["messages"] = modApiFunc('Data_Converter', 'getMessages');
                $_RESULT["process_info"] = modApiFunc('Data_Converter', 'getProcessInfo');
                break;
        };
    }

};

?>