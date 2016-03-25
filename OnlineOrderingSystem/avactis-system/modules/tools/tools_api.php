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
 * Tools module.
 *
 * @package Tools
 * @author Alexey Florinsky
 */
class Tools
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * News  constructor
     */
    function Tools()
    {
    }

    /**
     *                                     .
     *
     *       install()                      .
     *
     *                                          ,         ,
     * Tools::getTables()        $this->getTables()
     */
    function install()
    {
    }

    /**
     *                                     .
     *
     *
     *       uninstall()                      .
     *
     *                                          ,         ,
     * Tools::getTables()        $this->getTables()
     */
    function uninstall()
    {
    }

    /**
     *                                     backup.
     *
     * @
     * @return
     */
    function isBackupFolderNotWritable()
    {
        global $application;
        $dir_fs_name = $application->getAppIni("PATH_BACKUP_DIR");
        return !is_dir_writable($dir_fs_name);
    }

    function getBackupDir()
    {
        global $application;
        return $application->getAppIni("PATH_BACKUP_DIR");
    }

    /**
     *                                          .
     */
    function loadState()
    {
        if(modApiFunc('Session', 'is_Set', 'DBStat'))
        {
            $this->DBStat = modApiFunc('Session', 'get', 'DBStat');
        }
        else
        {
            $this->DBStat = array();
        }
        if(modApiFunc('Session', 'is_Set', 'CurrentBackupTable'))
        {
            $this->CurrentBackupTable = modApiFunc('Session', 'get', 'CurrentBackupTable');
        }
        else
        {
            $this->CurrentBackupTable = NULL;
        }
        if(modApiFunc('Session', 'is_Set', 'CurrentBackupTableLimit'))
        {
            $this->CurrentBackupTableLimit = modApiFunc('Session', 'get', 'CurrentBackupTableLimit');
        }
        else
        {
            $this->CurrentBackupTableLimit = NULL;
        }
        if(modApiFunc('Session', 'is_Set', 'DBRecordsExported'))
        {
            $this->DBRecordsExported = modApiFunc('Session', 'get', 'DBRecordsExported');
        }
        else
        {
            $this->DBRecordsExported = NULL;
        }
        if(modApiFunc('Session', 'is_Set', 'RestoreFile'))
        {
            $this->RestoreFile = modApiFunc('Session', 'get', 'RestoreFile');
        }
        else
        {
            $this->RestoreFile = NULL;
        }
        if(modApiFunc('Session', 'is_Set', 'RestoreFileOffset'))
        {
            $this->RestoreFileOffset = modApiFunc('Session', 'get', 'RestoreFileOffset');
        }
        else
        {
            $this->RestoreFileOffset = NULL;
        }
        if(modApiFunc('Session', 'is_Set', 'RestoreFileStrOffset'))
        {
            $this->RestoreFileStrOffset = modApiFunc('Session', 'get', 'RestoreFileStrOffset');
        }
        else
        {
            $this->RestoreFileStrOffset = NULL;
        }
        if(modApiFunc('Session', 'is_Set', 'StringsCountInRestoreFile'))
        {
            $this->StringsCountInRestoreFile = modApiFunc('Session', 'get', 'StringsCountInRestoreFile');
        }
        else
        {
            $this->StringsCountInRestoreFile = NULL;
        }
        if(modApiFunc('Session', 'is_Set', 'RestoreError'))
        {
            $this->RestoreError = modApiFunc('Session', 'get', 'RestoreError');
        }
        else
        {
            $this->RestoreError = NULL;
        }
        if(modApiFunc('Session', 'is_Set', 'RestoreTablePrefix'))
        {
            $this->RestoreTablePrefix = modApiFunc('Session', 'get', 'RestoreTablePrefix');
        }
        else
        {
            $this->RestoreTablePrefix = NULL;
        }
        if(modApiFunc('Session', 'is_Set', 'CurrentBackupFile'))
        {
            $this->CurrentBackupFile = modApiFunc('Session', 'get', 'CurrentBackupFile');
        }
        else
        {
            $this->CurrentBackupFile = NULL;
        }
        if(modApiFunc('Session', 'is_Set', 'RestoreStatus'))
        {
            $this->RestoreStatus = modApiFunc('Session', 'get', 'RestoreStatus');
        }
        else
        {
            $this->RestoreStatus = NULL;
        }
        if(modApiFunc('Session', 'is_Set', 'ProgressName'))
        {
            $this->ProgressName = modApiFunc('Session', 'get', 'ProgressName');
        }
        else
        {
            $this->ProgressName = NULL;
        }
        if(modApiFunc('Session', 'is_Set', 'DeletedImgQty'))
        {
            $this->DeletedImgQty = modApiFunc('Session', 'get', 'DeletedImgQty');
        }
        else
        {
            $this->DeletedImgQty = NULL;
        }
        if(modApiFunc('Session', 'is_Set', 'ImgQtyTotal'))
        {
            $this->ImgQtyTotal = modApiFunc('Session', 'get', 'ImgQtyTotal');
        }
        else
        {
            $this->ImgQtyTotal = NULL;
        }
    }


    function clearBackupSession()
    {
        modApiFunc('Session', 'un_Set', 'DBStat');
        modApiFunc('Session', 'un_Set', 'CurrentBackupTable');
        modApiFunc('Session', 'un_Set', 'CurrentBackupTableLimit');
        modApiFunc('Session', 'un_Set', 'DBRecordsExported');
        modApiFunc('Session', 'un_Set', 'BackupImagesFolder');
        modApiFunc('Session', 'un_Set', 'ImageFilesCount');
        modApiFunc('Session', 'un_Set', 'ImageFilesList');
        modApiFunc('Session', 'un_Set', 'copied_files');
        modApiFunc('Session', 'un_Set', 'BackupImagesFolderSize');
    }

    /**
     *                            .
     */
    function saveState()
    {
        if (sizeof($this->DBStat))
        {
            modApiFunc('Session', 'set', 'DBStat', $this->DBStat);
        }
        elseif (modApiFunc('Session', 'is_Set', 'DBStat'))
        {
            modApiFunc('Session', 'un_Set', 'DBStat');
        }
        if (!($this->CurrentBackupTable === NULL))
        {
            modApiFunc('Session', 'set', 'CurrentBackupTable', $this->CurrentBackupTable);
        }
        elseif (modApiFunc('Session', 'is_Set', 'CurrentBackupTable'))
        {
            modApiFunc('Session', 'un_Set', 'CurrentBackupTable');
        }
        if (!($this->CurrentBackupTableLimit === NULL))
        {
            modApiFunc('Session', 'set', 'CurrentBackupTableLimit', $this->CurrentBackupTableLimit);
        }
        elseif (modApiFunc('Session', 'is_Set', 'CurrentBackupTableLimit'))
        {
            modApiFunc('Session', 'un_Set', 'CurrentBackupTableLimit');
        }
        if (!($this->DBRecordsExported === NULL))
        {
            modApiFunc('Session', 'set', 'DBRecordsExported', $this->DBRecordsExported);
        }
        elseif (modApiFunc('Session', 'is_Set', 'DBRecordsExported'))
        {
            modApiFunc('Session', 'un_Set', 'DBRecordsExported');
        }
        if (!($this->RestoreFile === NULL))
        {
            modApiFunc('Session', 'set', 'RestoreFile', $this->RestoreFile);
        }
        elseif (modApiFunc('Session', 'is_Set', 'RestoreFile'))
        {
            modApiFunc('Session', 'un_Set', 'RestoreFile');
        }
        if (!($this->RestoreFileOffset === NULL))
        {
            modApiFunc('Session', 'set', 'RestoreFileOffset', $this->RestoreFileOffset);
        }
        elseif (modApiFunc('Session', 'is_Set', 'RestoreFileOffset'))
        {
            modApiFunc('Session', 'un_Set', 'RestoreFileOffset');
        }
        if (!($this->RestoreFileStrOffset === NULL))
        {
            modApiFunc('Session', 'set', 'RestoreFileStrOffset', $this->RestoreFileStrOffset);
        }
        elseif (modApiFunc('Session', 'is_Set', 'RestoreFileStrOffset'))
        {
            modApiFunc('Session', 'un_Set', 'RestoreFileStrOffset');
        }
        if (!($this->StringsCountInRestoreFile === NULL))
        {
            modApiFunc('Session', 'set', 'StringsCountInRestoreFile', $this->StringsCountInRestoreFile);
        }
        elseif (modApiFunc('Session', 'is_Set', 'StringsCountInRestoreFile'))
        {
            modApiFunc('Session', 'un_Set', 'StringsCountInRestoreFile');
        }
        if (!($this->RestoreError === NULL))
        {
            modApiFunc('Session', 'set', 'RestoreError', $this->RestoreError);
        }
        elseif (modApiFunc('Session', 'is_Set', 'RestoreError'))
        {
            modApiFunc('Session', 'un_Set', 'RestoreError');
        }
        if (!($this->RestoreTablePrefix === NULL))
        {
            modApiFunc('Session', 'set', 'RestoreTablePrefix', $this->RestoreTablePrefix);
        }
        elseif (modApiFunc('Session', 'is_Set', 'RestoreTablePrefix'))
        {
            modApiFunc('Session', 'un_Set', 'RestoreTablePrefix');
        }
        if (!($this->CurrentBackupFile === NULL))
        {
            modApiFunc('Session', 'set', 'CurrentBackupFile', $this->CurrentBackupFile);
        }
        elseif (modApiFunc('Session', 'is_Set', 'CurrentBackupFile'))
        {
            modApiFunc('Session', 'un_Set', 'CurrentBackupFile');
        }
        if (!($this->RestoreStatus === NULL))
        {
            modApiFunc('Session', 'set', 'RestoreStatus', $this->RestoreStatus);
        }
        elseif (modApiFunc('Session', 'is_Set', 'RestoreStatus'))
        {
            modApiFunc('Session', 'un_Set', 'RestoreStatus');
        }
        if (!($this->ProgressName === NULL))
        {
            modApiFunc('Session', 'set', 'ProgressName', $this->ProgressName);
        }
        elseif (modApiFunc('Session', 'is_Set', 'ProgressName'))
        {
            modApiFunc('Session', 'un_Set', 'ProgressName');
        }
        if (!($this->DeletedImgQty === NULL))
        {
            modApiFunc('Session', 'set', 'DeletedImgQty', $this->DeletedImgQty);
        }
        elseif (modApiFunc('Session', 'is_Set', 'DeletedImgQty'))
        {
            modApiFunc('Session', 'un_Set', 'DeletedImgQty');
        }
        if (!($this->ImgQtyTotal === NULL))
        {
            modApiFunc('Session', 'set', 'ImgQtyTotal', $this->ImgQtyTotal);
        }
        elseif (modApiFunc('Session', 'is_Set', 'ImgQtyTotal'))
        {
            modApiFunc('Session', 'un_Set', 'ImgQtyTotal');
        }
    }

    /**
     *
     *
     *                                        :
     * <code>
     *      $tables = array ();
     *      $table_name = 'table_name';
     *      $tables[$table_name] = array();
     *      $tables[$table_name]['columns'] = array
     *      (
     *          'fn1'               => 'table_name.field_name_1'
     *         ,'fn2'               => 'table_name.field_name_2'
     *         ,'fn3'               => 'table_name.field_name_3'
     *         ,'fn4'               => 'table_name.field_name_4'
     *      );
     *      $tables[$table_name]['types'] = array
     *      (
     *          'fn1'               => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
     *         ,'fn2'               => DBQUERY_FIELD_TYPE_INT .' NOT NULL'
     *         ,'fn3'               => DBQUERY_FIELD_TYPE_CHAR255
     *         ,'fn4'               => DBQUERY_FIELD_TYPE_TEXT
     *      );
     *      $tables[$table_name]['primary'] = array
     *      (
     *          'fn1'       #                                            ,          - 'fn1', 'fn2'
     *      );
     *      $tables[$table_name]['indexes'] = array
     *      (
     *          'index_name1' => 'fn2'      #                                                   ,          - 'fn2, fn3'
     *         ,'index_name2' => 'fn3'
     *      );
     * </code>
     *
     * @return array -
     */
    function getTables()
    {
    }

    function setDBStat($DBStat)
    {
        $this->DBStat = $DBStat;
    }

    function getDBStat()
    {
        return $this->DBStat;
    }

    function unsetDBStat()
    {
        $this->DBStat = array();
    }

    function setCurrentBackupTable($tableIndex)
    {
        $this->CurrentBackupTable = $tableIndex;
    }

    function getCurrentBackupTable()
    {
        return $this->CurrentBackupTable;
    }

    function unsetCurrentBackupTable()
    {
        $this->CurrentBackupTable = NULL;
    }

    function setCurrentBackupTableLimit($tableLimit)
    {
        $this->CurrentBackupTableLimit = $tableLimit;
    }

    function getCurrentBackupTableLimit()
    {
        return $this->CurrentBackupTableLimit;
    }

    function unsetCurrentBackupTableLimit()
    {
        $this->CurrentBackupTableLimit = NULL;
    }

    function setDBRecordsExported($recordsCount)
    {
        $this->DBRecordsExported = $recordsCount;
    }

    function getDBRecordsExported()
    {
        return $this->DBRecordsExported;
    }

    function unsetDBRecordsExported()
    {
        $this->DBRecordsExported = NULL;
    }

    function setRestoreTablePrefix($prefix)
    {
        $this->RestoreTablePrefix = $prefix;
    }

    function getRestoreTablePrefix()
    {
        return $this->RestoreTablePrefix;
    }

    function unsetRestoreTablePrefix()
    {
        $this->RestoreTablePrefix = NULL;
    }

    function setRestoreFile($file)
    {
        $this->RestoreFile = $file;
    }

    function getRestoreFile()
    {
        return $this->RestoreFile;
    }

    function unsetRestoreFile()
    {
        $this->RestoreFile = NULL;
    }

    function setStringsCountInRestoreFile($file)
    {
        global $application;

        $full_file_name = $application->getAppIni('PATH_BACKUP_DIR').$file."/dump.sql";
        if (file_exists($full_file_name))
        {
            $fp = fopen($full_file_name, "r");
            $count = 0;
            while (!feof($fp))
            {
                fgets($fp);
                $count++;
            }
            fclose($fp);
            $this->StringsCountInRestoreFile = $count;#*2;
        }
    }

    function getStringsCountInRestoreFile()
    {
        return $this->StringsCountInRestoreFile;
    }

    function unsetStringsCountInRestoreFile()
    {
        $this->StringsCountInRestoreFile = NULL;
    }

    function setRestoreFileOffset($offset)
    {
        $this->RestoreFileOffset = $offset;
    }

    function getRestoreFileOffset()
    {
        return $this->RestoreFileOffset;
    }

    function unsetRestoreFileOffset()
    {
        $this->RestoreFileOffset = NULL;
    }

    function setRestoreFileStrOffset($str_offset)
    {
        $this->RestoreFileStrOffset = $str_offset;
    }

    function getRestoreFileStrOffset()
    {
        return $this->RestoreFileStrOffset;
    }

    function unsetRestoreFileStrOffset()
    {
        $this->RestoreFileStrOffset = NULL;
    }

    function setRestoreError($error)
    {
        $this->RestoreError = $error;
    }

    function getRestoreError()
    {
        return $this->RestoreError;
    }

    function unsetRestoreError()
    {
        $this->RestoreError = NULL;
    }

    function setCurrentBackupFile($filename)
    {
        $this->CurrentBackupFile = $filename;
    }

    function getCurrentBackupFile()
    {
        return $this->CurrentBackupFile;
    }

    function unsetCurrentBackupFile()
    {
        $this->CurrentBackupFile = NULL;
    }

    function getCopiedImageFilesCount()
    {
        if (modApiFunc("Session", "is_Set", "copied_files"))
        {
            return modApiFunc("Session", "get", "copied_files");
        }
        return NULL;
    }

    function setRestoreStatus($status)
    {
        $this->RestoreStatus = $status;
    }

    function getRestoreStatus()
    {
        return $this->RestoreStatus;
    }

    function unsetRestoreStatus()
    {
        $this->RestoreStatus = NULL;
    }

    function setProgressName($name)
    {
        $this->ProgressName = $name;
    }

    function getProgressName()
    {
        return $this->ProgressName;
    }

    function unsetProgressName()
    {
        $this->ProgressName = NULL;
    }

    function setDeletedImgQty($qty)
    {
        $this->DeletedImgQty = $qty;
    }

    function getDeletedImgQty()
    {
        return $this->DeletedImgQty;
    }

    function unsetDeletedImgQty()
    {
        $this->DeletedImgQty = NULL;
    }

    function setImgQtyTotal($qty)
    {
        $this->ImgQtyTotal = $qty;
    }

    function getImgQtyTotal()
    {
        return $this->ImgQtyTotal;
    }

    function unsetImgQtyTotal()
    {
        $this->ImgQtyTotal = NULL;
    }

    function getImageFilesCount($type = "backup")
    {
        global $application;
        if (modApiFunc("Session", "is_Set", "BackupImagesFolder"))
        {
            $retval = 0;
            if (!modApiFunc("Session", "is_Set", "ImageFilesCount"))
            {
                if ($type == "backup")
                {
                    $dir = $application->getAppIni('PATH_IMAGES_DIR');
                }
                else
                {
                    $file = modApiFunc("Session", "get", "RestoreFile");
                    modApiFunc("Session", "un_Set", "RestoreFile");
                    $dir = $application->getAppIni('PATH_BACKUP_DIR').$file."/img/";
                    modApiFunc("Session", "set", "BackupImagesFolder", $dir);
                }
                $images_list = array();
                $obj_dir = dir($dir);
                while ($file = $obj_dir->read())
                {
                    if (!is_dir($dir . '/' . $file))
                    {
                        $images_list[] = $file ;
                        $retval++;
                    }
                }
                $obj_dir->close();
                modApiFunc("Session", "set", "ImageFilesCount", $retval);
                modApiFunc("Session", "set", "ImageFilesList", $images_list);
            }
            return modApiFunc("Session", "get", "ImageFilesCount");
        }
        return NULL;
    }


    /**
     *
     */
    function CreateBackup($type = "full")
    {
        global $application;
        $MessageResources = &$application->getInstance('MessageResources');

        $filename = $this->generateBackupFileName();
        if (!file_exists($filename))
        {
            $fp = fopen($filename, "w");
        }
        else
        {
            $fp = fopen($filename, "a");
        }
        $result = modApiFunc("DB_MySQL", "getDataDump", $this->DBStat, $this->CurrentBackupTable, $this->CurrentBackupTableLimit, $this->DBRecordsExported, $fp);
//        fwrite($fp, $result["data"]);
        fclose($fp);
        $this->CurrentBackupTable = $result["currentTable"];
        $this->CurrentBackupTableLimit = $result["currentLimit"];
        $this->DBRecordsExported = $result["recordsExported"];
        if ($this->DBRecordsExported >= $this->DBStat["Total_Records"] && $this->CurrentBackupTable >= (sizeof($this->DBStat)-2))
        {
            $this->DBStat = array();
            $this->CurrentBackupTable = NULL;
            $this->CurrentBackupTableLimit = NULL;
            $this->DBRecordsExported = NULL;
            $temp_backup_filename = modApiFunc('Session', 'get', 'TempBackupFileName');
            $temp_backup_filename = _ml_substr($temp_backup_filename, (_ml_strlen($temp_backup_filename)-10));
            $backup_filename = $application->getAppIni('PATH_BACKUP_DIR')."backup_".date("Y-m-d-H-i-s",$temp_backup_filename);
            modApiFunc("Session", "set", "BackupFileName", $backup_filename);
            @mkdir($backup_filename."_temp");
            @chmod($backup_filename."_temp", 0777);
            @mkdir($backup_filename."_temp/img");
            modApiFunc("Session", "set", "BackupImagesFolder", $backup_filename."_temp");
            modApiFunc("Session", "set", "copied_files", "no");
        }
        $this->SaveState();
    }

    /**
     *
     *
     * @param
     * @return
     */
    function BackupImages($type = "backup")
    {
        global $application;
        if ($type == "backup")
        {
            $src_dir = $application->getAppIni('PATH_IMAGES_DIR');
            $dest_dir = modApiFunc("Session", "get", "BackupImagesFolder")."/img/";
        }
        else
        {
            $src_dir = modApiFunc("Session", "get", "BackupImagesFolder");
            $dest_dir = $application->getAppIni('PATH_IMAGES_DIR');
        }
        $copied_files = modApiFunc("Session", "get", "copied_files");
        $images_list = modApiFunc("Session", "get","ImageFilesList");
        if ($copied_files == "no")
        {
            $copied_files = 0;
        }
        $start_time = time();
        $BackupImagesFolderSize = 0;
        if (modApiFunc("Session", "is_set", "BackupImagesFolderSize"))
        {
            $BackupImagesFolderSize = modApiFunc("Session", "get", "BackupImagesFolderSize");
        }
        while ( (time() - $start_time < 5) )
        {
            $image_file = array_pop($images_list);
            if ($image_file == null) break;
            copy($src_dir.$image_file, $dest_dir.$image_file);
            $file_size = filesize($dest_dir.$image_file);
            $BackupImagesFolderSize+= $file_size;
            $copied_files++;
        }
        modApiFunc("Session", "set", "BackupImagesFolderSize", $BackupImagesFolderSize);
        if ($image_file != null)
        {
            modApiFunc("Session", "set","ImageFilesList",$images_list);
            modApiFunc("Session", "set", "copied_files", $copied_files);
        }
        else
        {
            if ($type == "backup")
            {
                $MessageResources = &$application->getInstance('MessageResources');
                $temp_backup_filename = modApiFunc('Session', 'get', 'TempBackupFileName');
                $backup_filename = modApiFunc("Session", "get", "BackupFileName");
                modApiFunc('Session', 'un_Set', 'TempBackupFileName');
                @rename($backup_filename."_temp", $backup_filename);
                @chmod($backup_filename, 0777);
                $backup_file_content = file_get_contents($temp_backup_filename);

                $handle = fopen($temp_backup_filename, "rb");
                $md5_temp = '';
                while (!feof($handle)) {
                  $contents = fread($handle, 1048576);
                  $md5_temp .= md5($contents);
                }
                fclose($handle);
                $backup_file_hash = md5($md5_temp);

                modApiFunc("Session", "un_Set", "BackupFileName");
                $fp = fopen($backup_filename."/dump.sql", "w");
                fwrite($fp, "-- HASH: ".$backup_file_hash."\n");
                fwrite($fp, $backup_file_content);
                fclose($fp);
                @unlink($temp_backup_filename);
                $this->backup_filename = NULL;
                @mkdir($backup_filename."/info");
                @chmod($backup_filename."/info", 0777);
                $fp = fopen($backup_filename."/info/backup.ini", "w");
                fwrite($fp, "asc_version = \"".PRODUCT_VERSION."\"\n");
                fwrite($fp, "sql_file_name = \"$backup_filename/dump.sql\"\n");
                fwrite($fp, "sql_file_size = \"".filesize($backup_filename."/dump.sql")."\"\n");
                fwrite($fp, "img_dir_name = \"$backup_filename/img\"\n");
                fwrite($fp, "img_dir_size = \"".modApiFunc("Session", "get", "BackupImagesFolderSize")."\"\n");//$this->getDirectorySize($backup_filename."/img")."\"\n");
                modApiFunc("Session", "un_set", "BackupImagesFolderSize");
                fwrite($fp, "img_files_qty = \"".$copied_files."\"\n");
                fwrite($fp, "backup_start_time = \""._ml_substr($temp_backup_filename, (_ml_strlen($temp_backup_filename)-10))."\"\n");
                $time = time();
                fwrite($fp, "backup_end_time = \"".$time."\"\n");
                fclose($fp);
                $fp = fopen($backup_filename."/info/backup.log", "w");
                $admin_info = modApiFunc("Users", "getUserInfo", modApiFunc("Users", "getCurrentUserID"));
                if ($this->getRestoreStatus() == 'BACKUP')
                {
                    $msg = 'BCP_INFO_LOG_MSG_004';
                }
                else
                {
                    $msg = 'BCP_INFO_LOG_MSG_001';
                }
                fwrite($fp,
                $MessageResources->getMessage(new ActionMessage(array($msg,
                                                                      modApiFunc("Localization", "timestamp_date_format", $time),
                                                                      modApiFunc("Localization", "timestamp_time_format", $time),
                                                                      $admin_info['firstname'],
                                                                      $admin_info['lastname'],
                                                                      $admin_info['email']
                                                                     )
                                                               )
                                             )
                      );
                fclose($fp);
            }
            else
            {
                $MessageResources = &$application->getInstance('MessageResources');
                $fp = fopen(strtr(modApiFunc("Session", "get", "BackupImagesFolder"), array("/img/" => ""))."/info/backup.log", "a");
                $admin_info = modApiFunc("Users", "getUserInfo", modApiFunc("Users", "getCurrentUserID"));
                $time = time();
                fwrite($fp,
                $MessageResources->getMessage(new ActionMessage(array('BCP_INFO_LOG_MSG_005',
                                                                      modApiFunc("Localization", "timestamp_date_format", $time),
                                                                      modApiFunc("Localization", "timestamp_time_format", $time),
                                                                      $admin_info['firstname'],
                                                                      $admin_info['lastname'],
                                                                      $admin_info['email']
                                                                     )
                                                               )
                                             )
                      );
                fclose($fp);
            }
            modApiFunc("Session", "un_Set", "copied_files");
            modApiFunc("Session", "un_Set", "BackupImagesFolder");
            modApiFunc("Session", "un_Set", "ImageFilesCount");
            modApiFunc("Session", "un_Set", "ImageFilesList");
        }
    }

    /**
     *
     *
     * @param
     * @return
     */
    function RestoreBackup()
    {
        global $application;
        $result = modApiFunc("DB_MySQL", "importData", $application->getAppIni('PATH_BACKUP_DIR').$this->RestoreFile."/dump.sql", $this->RestoreTablePrefix, $this->RestoreFileOffset, $this->RestoreFileStrOffset);
        $this->RestoreFileOffset = $result["offset"];
        $this->RestoreFileStrOffset = $result["str_offset"];
        if ($this->RestoreFileStrOffset >= $this->StringsCountInRestoreFile)#/2)
        {
            if ($this->RestoreTablePrefix == "restore_")
            {

                $this->RestoreTablePrefix = NULL;
                $this->StringsCountInRestoreFile = NULL;
                $this->RestoreFileOffset = NULL;
                $this->RestoreFileStrOffset = NULL;
                $tables = modApiFunc("Modules_Manager", "getTablesAndRecordsCount", false);
                modApiFunc("DB_MySQL", "renameAndDeleteTablesAfterRestore", $tables);
                modApiFunc("DB_MySQL", "optimizeTables", $tables);
                modApiFunc("Session", "set", "BackupImagesFolder", $application->getAppIni('PATH_BACKUP_DIR').$this->RestoreFile);
                modApiFunc("Session", "set", "copied_files", "no");
/*
                $this->RestoreFileOffset = 0;
                $this->RestoreFileStrOffset = 0;
                $this->RestoreTablePrefix = "";
                $fp = fopen($application->getAppIni('PATH_SYSTEM_DIR')."restore", "w");
                fclose($fp);
                $this->RestoreBackup();
*/
            }
            else
            {
                $this->RestoreTablePrefix = NULL;
                $this->StringsCountInRestoreFile = NULL;
                $this->RestoreFileOffset = NULL;
                $this->RestoreFileStrOffset = NULL;
                modApiFunc("DB_MySQL", "dropTablesAfterRestore", modApiFunc("Modules_Manager", "getTablesAndRecordsCount", false));
                @unlink($application->getAppIni('PATH_SYSTEM_DIR')."restore");
                modApiFunc("Session", "set", "BackupImagesFolder", $application->getAppIni('PATH_BACKUP_DIR').$this->RestoreFile);
                modApiFunc("Session", "set", "copied_files", "no");
            }
        }
        $this->SaveState();
    }

    /**
     *
     */
    function CancelBackup()
    {
        if (modApiFunc('Session', 'is_set', 'TempBackupFileName'))
        {
            $this->CurrentBackupTable = 0;
            $this->CurrentBackupTableLimit = 0;
            $this->DBRecordsExported = 0;
            @unlink(modApiFunc('Session', 'Get', 'TempBackupFileName'));
            modApiFunc('Session', 'un_Set', 'TempBackupFileName');
            $this->backup_filename = NULL;
        }
        if (modApiFunc("Session", "is_Set", "copied_files"))
        {
//            $this->removeDirectory(modApiFunc("Session", "get", "BackupImagesFolder")."/");//."_temp/");
            modApiFunc("Session", "un_Set", "copied_files");
        }
        $this->SaveState();
    }

    function removeDirectory($directory, $start_time)
    {
        if ($dir = @dir($directory))
        {
            if ($this->DeletedImgQty === NULL)
            {
                $this->DeletedImgQty = 0;
                $backupInfo = @_parse_ini_file($directory."info/backup.ini");
                $this->ImgQtyTotal = $backupInfo["img_files_qty"]+3;
            }
            while ($file = $dir->read())
            {
                if ((time() - $start_time) >= 5)
                {
                    $dir->close();
                    $this->SaveState();
                    return;
                }
                if (!is_dir($directory.$file))
                {
                    @unlink($directory.$file);
                    $this->DeletedImgQty++;
                }
                elseif ($file != "." && $file != "..")
                {
                    $this->removeDirectory($directory.$file."/", $start_time);
                }
            }
            $dir->close();
        }
        @rmdir($directory);
        $this->SaveState();
    }

    function getDirectorySize($directory)
    {
        $total_size = 0;
        if ($directory[_byte_strlen($directory)-1] != "/")
        {
            $directory.= "/";
        }
        if ($dir = @dir($directory))
        {
            while ($file = $dir->read())
            {
                if (!is_dir($directory.$file))
                {
                    $total_size+= filesize($directory . $file);
                }
                elseif ($file != "." && $file != "..")
                {
                    $total_size+= $this->getDirectorySize($directory.$file."/");
                }
            }
            $dir->close();
        }
        return $total_size;
    }


    /**
     *
     *
     * @param
     * @return
     */
    function generateBackupFileName()
    {
        global $application;

        if(modApiFunc('Session', 'is_Set', 'TempBackupFileName'))
        {
            $this->backup_filename = modApiFunc('Session', 'get', 'TempBackupFileName');
        }
        else
        {
            $this->backup_filename = $application->getAppIni('PATH_CACHE_DIR').time();
            modApiFunc('Session', 'set', 'TempBackupFileName', $this->backup_filename);
        }
        return $this->backup_filename;
    }

    /**
     *
     *
     * @param
     * @return
     */
    function getBackupFileName()
    {
        return $this->backup_filename;
    }

    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    var $DBStat = array();

    var $CurrentBackupTable = NULL;

    var $CurrentBackupTableLimit = NULL;

    var $DBRecordsExported = NULL;

    var $RestoreTablePrefix = NULL;

    var $RestoreFile = NULL;

    var $RestoreFileOffset = NULL;

    var $RestoreFileStrOffset = NULL;

    var $StringsCountInRestoreFile = NULL;

    var $CurrentBackupFile = NULL;

    var $RestoreStatus = NULL;

    var $ProgressName = NULL;

    var $DeletedImgQty = NULL;

    var $ImgQtyTotal = NULL;

    /**#@-*/

}
?>