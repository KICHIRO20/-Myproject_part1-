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

class do_export_labels extends AjaxAction
{
    function do_export_labels()
    {
    }

    function onAction()
    {
        global $application;
        $request = &$application -> getInstance('Request');
        $el_target = $request -> getValueByKey('el_target');

        global $_RESULT;

        switch($el_target)
        {
            case 'init':
                $sets = array();
                $sets['labels'] = $request -> getValueByKey('labels');
                if ($request -> getValueByKey('format') == 'xliff')
                {
                    $sets['script_code'] = 'Labels_DB_XLIFF';
                }
                else
                {
                    $sets['out_file'] = $application -> getAppIni('PATH_CACHE_DIR') . 'labels.csv';
                    $sets['script_code'] = 'Labels_DB_CSV';
                    $sets['csv_delimiter'] = $request -> getValueByKey('delimiter');
                    $sets['headers'] = array(
                        0 => getMsg('ML', 'ML_LABEL_TYPE'),
                        1 => getMsg('ML', 'ML_LABEL_NAME')
                    );
                    $sets['languages'] = explode('|', $request -> getValueByKey('languages'));
                    if (is_array($sets['languages']))
                        foreach($sets['languages'] as $k => $v)
                        {
                            if ($v === '0')
                            {
                                // here we have default language in a system
                                // with no language at all
                                $sets['headers'][2 + $k] = getMsg('ML', 'ML_EXPORT_LABEL_VALUE') . ' (' . getMsg('ML', 'ML_IN') . ' ' . getMsg('ML', 'ML_DEFAULT') . ')';
                                $sets['languages'][$k] = '';
                            }
                            else
                            {
                                // at least one language is found...
                                if (!$v)
                                    continue;
                                $lng_data = modApiFunc('MultiLang', 'getLanguageList', false, $v);
                                if (!$lng_data)
                                    continue;
                                $sets['headers'][2 + $k] = getMsg('ML', 'ML_EXPORT_LABEL_VALUE') . ' (' . getMsg('ML', 'ML_IN') . ' ' . $lng_data[0]['lng_name'] . ')';
                            }
                        }
                }
                $sets['script_step'] = 1;
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
            case 'get':
                header('Pragma: no-cache');
                header('Expires: ' . gmdate('D, d M Y H:i:s', time()) . ' GMT');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Content-Transfer-Encoding: binary');
                header('Content-Type: application/octet-stream');
                if ($request -> getValueByKey('format') == 'xliff')
                {
                    $filename = $application -> getAppIni('PATH_CACHE_DIR') . 'labels_xliff.xml';
                    $file = 'labels_xliff.xml';
                }
                else
                {
                    $filename = $application -> getAppIni('PATH_CACHE_DIR') . 'labels.csv';
                    $file = 'labels.csv';
                }
                header('Content-Length: ' . filesize($filename));
                header('Content-Disposition: attachment; filename="' . $file . '"');
                if (ob_get_level() !== 0)
                {
                    ob_clean();
                }
                flush();
                readfile($filename);
                unlink($filename);
                die();
                break;
        };
    }
};

?>