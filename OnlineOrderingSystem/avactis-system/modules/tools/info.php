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
 * News module meta info.
 *
 * @package News
 * @author Alexey Florinsky
 */

$moduleInfo = array
    (
        'name'         => 'Tools', #
        'shortName'    => 'TOOLS',
        'groups'       => 'Main',
        'description'  => 'Tools module',
        'version'      => '0.1.47700',
        'author'       => 'Alexey Florinsky',
        'contact'      => '',
        'systemModule'  => false,
        'mainFile'     => 'tools_api.php',

        'actions' => array
        (
            #                ,              action'
            #                           action' .
            # 'action_class_name' => 'action_file_name'
            'AdminZone' => array(
                'DBStat' => 'db_stat_action.php'
               ,'BackupProgressAction' => 'backup_progress_action.php'
               ,'BackupImagesProgressAction' => 'backup_images_progress_action.php'
               ,'RestoreProgressAction' => 'restore_progress_action.php'
               ,'RestoreImagesProgressAction' => 'restore_images_progress_action.php'
               ,'BackupCancel' => 'backup_cancel_action.php'
               ,'BackupDeleteAction' => 'backup_delete_action.php'
               ,'SetRestoreFile' => 'set_restore_file_action.php'
               ,'SetCurrentBackupFile' => 'set_current_backup_file_action.php'
               ,'UpdateBackupInfo' => 'update_backup_info_action.php'
               ,'CreateAutoBackup' => 'auto_backup_action.php'
           ),
        ),

        'hooks' => array
        (
            # 'hook_class_name' => array ( 'onAction'  => 'action_class_name',
            #                              'Hook_File' => 'hook_file_name' )
        ),

        'views' => array
        (
            'AdminZone' => array
            (
                'AutoBackupCreate'     => 'auto_backup_create_az.php'
               ,'Backup'         => 'backup_az.php'
               ,'BackupInfo'     => 'backup_info_az.php'
               ,'BackupCreate'   => 'backup_create_az.php'
               ,'BackupRestore'  => 'backup_restore_az.php'
               ,'BackupDelete'   => 'backup_delete_az.php'
               ,'BackupDeleteProgress' => 'backup_delete_progress_az.php'
               ,'BackupProgress' => 'backup_progress_az.php'
               ,'RestoreProgress'=> 'restore_progress_az.php'
               ,'Support'        => 'support_az.php'
               ,'ServerInfo'     => 'server_info_az.php'
               ,'CZLayoutsList'  => 'cz_layouts_list_az.php'
            ),
        )
    );
?>