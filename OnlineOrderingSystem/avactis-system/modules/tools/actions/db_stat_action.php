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
 *
 * @package Tools
 * @author Alexander Girin
 */
class DBStat extends AjaxAction
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * DBStat constructor
     */
    function DBStat()
    {
    }

    /**
     *
     */
    function onAction()
    {
        global $application;

		CCacheFactory::clearAll();

        modApiFunc("Tools", "clearBackupSession");
        modApiFunc("Tools", "setDBStat", modApiFunc("Modules_Manager", "getTablesAndRecordsCount"));
        modApiFunc("Tools", "setCurrentBackupTable", 0);
        modApiFunc("Tools", "setCurrentBackupTableLimit", 0);
        modApiFunc("Tools", "setDBRecordsExported", 0);
        $request = $application->getInstance('Request');
        $filename = $request->getValueByKey('BackupFile');
        if ($filename)
        {
            modApiFunc("Tools", "setRestoreStatus", 'BACKUP');
            modApiFunc("Tools", "setRestoreFile", $filename);
            $filename = modApiFunc("Tools", "getRestoreFile");
            $full_filename = $application->getAppIni('PATH_BACKUP_DIR').$filename."/dump.sql";
            $handle = @fopen($full_filename, "rb");
            $backup_file_content = @fread($handle, 1024);
            @fclose($handle);
            $error = "";
            $backup_info = @_parse_ini_file($application->getAppIni('PATH_BACKUP_DIR').$filename."/info/backup.ini");
            if (!isset($backup_info["asc_version"]) || $backup_info["asc_version"] != PRODUCT_VERSION)
            {
                $error = "BCP_RESTORE_ERR_003";
            }
            elseif (!$backup_file_content)
            {
                $error = "BCP_RESTORE_ERR_001";
            }
            elseif (_ml_strpos($backup_file_content, "-- HASH: ") === false)
            {
                $error = "BCP_RESTORE_ERR_002";
            }
            else
            {
                $hash = _byte_substr($backup_file_content, 9, 32);
                //
                $handle = fopen($full_filename, "rb");
                $md5_temp = '';
                //
                $begin = _byte_strpos($backup_file_content, "\n") + _byte_strlen("\n");
                fseek($handle, $begin);
                while (!feof($handle)) {
                  $contents = fread($handle, 1048576);
                  $md5_temp .= md5($contents);
                }
                $counted_file_hash = md5($md5_temp);
                fclose($handle);
                //                :
                if ($hash != $counted_file_hash)
                {
                    $error = "BCP_RESTORE_ERR_002";
                }
            }
            if ($error)
            {
                modApiFunc("Tools", "setRestoreError", $error);
            }
            else
            {
                modApiFunc("Tools", "setStringsCountInRestoreFile", $filename);
            }
        }
        modApiFunc("Tools", "saveState");
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