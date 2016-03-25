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
 * @package
 * @author
 *
 */

class emails_export extends AjaxAction
{
    function emails_export()
    {
    }

    /*
    function onAction()
    {
        global $application;
        $request = &$application->getInstance('Request');
        $sub_action = $request->getValueByKey('sub_action');

        $is_csv = $request->getValueByKey('export_format') == 'csv';
        $ext = $is_csv ? 'csv' : 'txt';
        $out_file_path = $application->getAppIni('PATH_CACHE_DIR').'__emails.'.$ext;

        global $_RESULT;

        switch($sub_action)
        {
            case 'init':
                $sets = array();
                $sets['topics'] = $request->getValueByKey('topics');
                $sets['out_file']= $out_file_path;
                $sets['csv_delimiter'] = ';';
                $sets['script_code'] = $is_csv ? 'Emails_DB_CSV' : 'Emails_DB_Text';
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
                $d = new CStoreDatetime();
                $datetime = date('Y-m-d-H-i-s', $d->getTimestamp());

                header ("Pragma: no-cache");
                header ("Expires: " . gmdate("D, d M Y H:i:s", time()) . " GMT");
                header ("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header ("Content-Type: application/csv");
                header ("Content-Length: ".filesize($out_file_path));
                header ("Content-Disposition: attachment; filename=\"emails-".$datetime.'.'.$ext);

                $iif_file = fopen($out_file_path,'r');
                if ($iif_file) {
                    while(!feof($iif_file)) {
                        echo fread($iif_file,4096);
                    }
                }
                fclose($iif_file);
                unlink($out_file_path);
                exit();
        };
    }
    */

    function onAction()
    {
        global $application;
        $request = &$application->getInstance('Request');
        $sub_action = $request->getValueByKey('sub_action');

        $topics_ids = explode(',', $request->getValueByKey('topics'));
        $is_csv = $request->getValueByKey('export_format') == 'csv';
        $ext = $is_csv ? 'csv' : 'txt';
        $out_file_path = $application->getAppIni('PATH_CACHE_DIR').'__emails.'.$ext;

        global $_RESULT;

        switch($sub_action)
        {
            case 'get':
                $d = new CStoreDatetime();
                $datetime = date('Y-m-d-H-i-s', $d->getTimestamp());

                header ('Pragma: no-cache');
                header ('Expires: ' . gmdate('D, d M Y H:i:s', time()) . ' GMT');
                header ('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header ('Content-Type: application/'.($is_csv ? 'csv' : 'force-download'));
                header ('Content-Disposition: attachment; filename="emails-'.$datetime.'.'.$ext.'"');

                if ($is_csv) {
                    $this->getTopicsEmails($topics_ids);
                }
                else {
                    $this->getEmails($topics_ids);
                }
                exit();
        };
    }

    function getEmails($topics_ids)
    {
        $emails = modApiFunc('Subscriptions', 'getEmailsByTopicsIds', $topics_ids);
        foreach($emails as $email) {
            echo $email, "\r\n";
        }
    }

    function getTopicsEmails($topics_ids)
    {
        global $application;
        loadCoreFile('csv_parser.php');

        $temp_file = $application->getAppIni('PATH_CACHE_DIR').'__emails_temp';
        $topics_emails = modApiFunc('Subscriptions', 'getTopicsEmailsByTopicsIds', $topics_ids);
        $topics_names = modApiFunc('Subscriptions', 'getTopicsNamesByIds', $topics_ids);

        $csv_worker = new CSV_Writer(session_id());
        $csv_worker->setOutFile($temp_file);
        $csv_worker->setLayout(array('Topic Name', 'E-mail'));
        $csv_worker->setDelimetr(';');
        $csv_worker->setNewLineType('win');
        $csv_worker->writeLayout();
        foreach (array_keys($topics_emails) as $i) {
            $csv_worker->writeArray(array(
                    'Topic Name' => $topics_names[ $topics_emails[$i]['topic_id'] ],
                    'E-mail' => $topics_emails[$i]['email'],
                    ));
        }
        #$this->_csv_worker->flush();
        $csv_worker->closeOutFile();
        header ("Content-Length: ".@filesize($temp_file));
        @readfile($temp_file);
    }

};

?>