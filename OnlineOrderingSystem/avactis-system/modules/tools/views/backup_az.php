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
 * @author Alexey Florinsky
 */
class Backup
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * AvactisHomeNews constructor
     */
    function Backup()
    {
    }

    /**
     *
     */
    function outputBackupFilesList()
    {
        if (_ml_strtoupper(_ml_substr(PHP_OS, 0, 3)) === 'WIN')
        {
            $retval = modApiFunc('TmplFiller', 'fill', "tools/backup/","item_win_os.tpl.html", array());
            return $retval;
        }

        global $application;
        $retval = "";

        $directory = $application->getAppIni('PATH_SYSTEM_DIR')."backup/";
        $n = 0;
        $backup_list = array();
        if ($dir = dir($directory))
        {
            while ($file = $dir->read())
            {
                if ( _ml_substr($file, _ml_strrpos($file, '.')+1) == "abi")
                {
                    $backup_file_name = _ml_substr($file, 0, _ml_strrpos($file, '.'));
                    if (file_exists($directory.$backup_file_name.".tar.gz"))
                    {
                        $backup_info = _parse_ini_file($directory.$backup_file_name.".abi");

                        if (isset($backup_info["asc_version"])
                            &&
                            $backup_info["asc_version"] == PRODUCT_VERSION_NUMBER
//                            &&
//                            isset($backup_info["backup_file_hash"])
//                            &&
//                            $backup_info["backup_file_hash"] == md5(file_get_contents($directory.$backup_file_name.".tgz"))
                           )
                        {
                            $request = new Request();
                            $request->setView('Downdoad');
                            $request->setKey('directory', 'backup');
                            $request->setKey('file', $backup_file_name . ".tar.gz");
                            $DownloadLink = $request->getURL();


                            $request = new Request();
                            $request->setView('AdminBackup');
                            $request->setAction('BackupDeleteAction');
                            $request->setKey('file', $backup_file_name);
                            $DeleteFormAction = $request->getURL();

                            $this->_Template_Contents = array(
                                                              'BackupFileName' => $backup_file_name
                                                             ,'DownloadLink' => $DownloadLink
                                                             ,'InfoLink' => ""//$InfoLink
                                                             ,'BackupFileDate' => modApiFunc("Localization", "timestamp_date_format", $backup_info['backup_date'])." ". modApiFunc("Localization", "timestamp_time_format", $backup_info['backup_date'])
                                                             ,'BackupFileSize' => modApiFunc("Localization", "num_format", ($backup_info['backup_file_size']/1024))." Kb"
                                                             ,'N' => $n
                                                             );
                            $backup_list[] = $this->_Template_Contents;
                            $n++;
                        }
                    }
                }
            }
            $dir->close();
        }
        for ($i=(sizeof($backup_list)-1); $i>=0; $i--)
        {
            $this->_Template_Contents = $backup_list[$i];
            $application->registerAttributes($this->_Template_Contents);
            $retval.= modApiFunc('TmplFiller', 'fill', "tools/backup/","item.tpl.html", array());
        }
        if ($n == 0)
        {
            $retval.= modApiFunc('TmplFiller', 'fill', "tools/backup/","item_na.tpl.html", array());
        }
                $retval.= modApiFunc('TmplFiller', 'fill', "tools/backup/","item_empty.tpl.html", array());
        return $retval;
    }

    function outputWarnings()
    {
        global $application;
        $MessageResources = &$application->getInstance('MessageResources');
        $Warnings = "";
        if (modApiFunc("Tools", "isBackupFolderNotWritable"))
        {
            $this->BackupFolderWarning = true;
            $this->_Template_Contents = array(
                                              'WarningMessage' => $MessageResources->getMessage(new ActionMessage(array('SETUP_WARNING_BACKUP_FOLDER_IS_NOT_WRITABLE', modApiFunc("Tools", "getBackupDir"))))
                                             );
            $application->registerAttributes($this->_Template_Contents);
            $Warnings.= modApiFunc('TmplFiller', 'fill', "tools/backup/","warnings.tpl.html", array());
        }
        if (modApiFunc("Catalog", "isImageFolderNotWritable"))
        {
            $this->ImagesFolderWarning = true;
            $this->_Template_Contents = array(
                                              'WarningMessage' => $MessageResources->getMessage(new ActionMessage(array('SETUP_WARNING_IMAGE_FOLDER_IS_NOT_WRITABLE', modApiFunc("Catalog", "getImagesDir"))))
                                             );
            $application->registerAttributes($this->_Template_Contents);
            $Warnings.= modApiFunc('TmplFiller', 'fill', "tools/backup/","warnings.tpl.html", array());
        }
        return $Warnings;
    }

    /**
     *
     */
    function output()
    {
        global $application;

        $request = new Request();
        $request->setView('AdminBackupCreate');
        //$request->setAction('DBStat');
        $Backup_Create_Link = $request->getURL();

        $request = new Request();
        $request->setView('AdminBackup');
        $request->setAction('BackupDeleteAction');
        $request->setKey('file', '');
        $DeleteFormAction = $request->getURL();

        $request = new Request();
        $request->setView('Downdoad');
        $request->setKey('directory', 'backup');
        $request->setKey('file', '');
        $DownloadFormAction = $request->getURL();

        $request = new Request();
        $request->setView('AdminBackupRestore');
        $request->setAction('DBStat');//SetRestoreFile');
        $request->setKey('BackupFile', '');
        $RestoreFormAction = $request->getURL();

        $template_contents = array(
                                   "Items" => $this->outputBackupFilesList()
                                  ,"Warnings" => $this->outputWarnings()
                                  ,"Backup_Create_Link" => $Backup_Create_Link
                                  ,"DeleteFormAction" => $DeleteFormAction
                                  ,"DownloadFormAction" => $DownloadFormAction
                                  ,"RestoreFormAction" => $RestoreFormAction
                                  ,"BackupFolderWarning" => ($this->BackupFolderWarning? "display: none;":"")
                                  ,"ImagesFolderWarning" => ($this->ImagesFolderWarning? "display: none;":"")
                                  );
        $this->_Template_Contents = $template_contents;
        $application->registerAttributes($this->_Template_Contents);
        return modApiFunc('TmplFiller', 'fill', "tools/backup/","container.tpl.html", array());
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

    var $BackupFolderWarning = false;

    var $ImagesFolderWarning = false;

    /**#@-*/

}
?>