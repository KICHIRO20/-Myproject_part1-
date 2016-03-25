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
 * Tools Module, Backup View
 *
 * @package Tools
 * @author Alexander Girin
 */
class BackupInfo
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Constructor
     */
    function BackupInfo()
    {
    }

    function outputResultMessage()
    {
        global $application;
        if(modApiFunc("Session","is_set","ResultMessage"))
        {
            $msg=modApiFunc("Session","get","ResultMessage");
            modApiFunc("Session","un_set","ResultMessage");
            $template_contents=array(
                "ResultMessage" => getMsg('SYS',$msg)
            );
            $this->_Template_Contents=$template_contents;
            $application->registerAttributes($this->_Template_Contents);
            $this->mTmplFiller = &$application->getInstance('TmplFiller');
            return $this->mTmplFiller->fill("tools/backup_info/", "result-message.tpl.html",array());
        }
        else
        {
            return "";
        }
    }


    /**
     *
     */
    function output()
    {
        global $application;

        $request = new Request();
        $request->setView('AdminBackupInfo');
        $request->setAction('UpdateBackupInfo');
        $FormAction = $request->getURL();

        $backup_name = modApiFunc("Tools", "getCurrentBackupFile");
        $backup_dir = $application->getAppIni('PATH_BACKUP_DIR');
        $backup_info = _parse_ini_file($backup_dir.$backup_name."/info/backup.ini");

        $log_file_content = @file_get_contents($backup_dir.$backup_name."/info/backup.log");
//        $txt_file_content = @file_get_contents($backup_dir.$backup_name."/info/backup.txt");
        $template_contents = array(
                                   'FormAction' => $FormAction
                                  ,'BackupName' => $backup_name
                                  ,'BackupSQLSize' => modApiFunc("Localization", "num_format", ($backup_info['sql_file_size']/1024))." Kb"
                                  ,'BackupImgSize' => modApiFunc("Localization", "num_format", ($backup_info['img_dir_size']/1024))." Kb"
                                  ,'BackupCreationDate' => modApiFunc("Localization", "timestamp_date_format", $backup_info['backup_start_time'])
                                  ,'BackupCreationStartTime' => modApiFunc("Localization", "timestamp_time_format", $backup_info['backup_start_time'])
                                  ,'BackupCreationFinishTime' => modApiFunc("Localization", "timestamp_time_format", $backup_info['backup_end_time'])
                                  ,'History' => $log_file_content
                                  ,'Comments' => ""//$txt_file_content
                                  ,'CommentsHash' => ""//md5($txt_file_content)
                                  ,'ResultMessage' => $this->outputResultMessage()
                                  );
        $this->_Template_Contents = $template_contents;
        $application->registerAttributes($this->_Template_Contents);
        return modApiFunc('TmplFiller', 'fill', "tools/backup_info/","container.tpl.html", array());
    }

    function getTag($tag)
    {
        global $application;
        $value = null;
        if (array_key_exists($tag, $this->_Template_Contents))
        {
            $value = $this->_Template_Contents[$tag];
        }
        return $value;
    }
    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */


    /**#@-*/

}
?>