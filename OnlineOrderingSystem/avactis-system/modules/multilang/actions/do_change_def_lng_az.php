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

class do_change_def_lng extends AjaxAction
{
    function do_change_def_lng()
    {
    }

    function onAction()
    {
        global $application;
        $request = &$application -> getInstance('Request');
        $cdl_target = $request -> getValueByKey('cdl_target');

        global $_RESULT;

        switch($cdl_target)
        {
            case 'init':
                $sets = array();
                $sets['script_code'] = 'ML_Records_DB_DB';
                $sets['script_step'] = 1;
                $sets['old_data'] = array();
                $sets['new_data'] = array();
                $sets['new_lng'] = $request -> getValueByKey('lng_target');
                $sets['old_lng'] = modApiFunc('MultiLang', 'getDefaultLanguage');

                $new_action = $request -> getValueByKey('new_action');
                $new_action = explode('|', $new_action);
                foreach($new_action as $v)
                    if ($v)
                        $sets['new_data'][] = array('label' => $v, 'pos' => 0,
                                                    'finished' => false);

                $old_action = $request -> getValueByKey('old_action');
                $old_action = explode('|', $old_action);
                foreach($old_action as $v)
                    if ($v)
                        $sets['old_data'][] = array('label' => $v, 'pos' => 0,
                                                    'finished' => false);

                if (!$sets['old_lng'])
                    $sets['old_data'] = array();

                $sets['bulk_number'] = 1000;

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