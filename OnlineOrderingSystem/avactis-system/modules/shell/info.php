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
 * @package Shell
 * @author Egor V. Derevyankin
 *
 */

$moduleInfo = array
    (
        'name'          => 'Shell',
        'shortName'     => 'SH',
        'groups'        => 'Main',
        'description'   => 'Shell module',
        'version'       => '0.1.47700',
        'author'        => 'Egor V. Derevyankin',
        'contact'       => '',
        'systemModule'  => false,
        'mainFile'      => 'shell_api.php',
        'constantsFile' => 'const.php',
        'resFile'       => 'shell-messages',
        'actions' => array
        (
            'AdminZone' => array(
                'get_folder_content' => 'get_folder_content.php'
               ,'shell_upload_file' => 'shell_upload_file.php'
               ,'shell_del_uploaded' => 'shell_del_uploaded.php'
           ),
        ),
        'hooks' => array
        (
        ),
        'views' => array
        (
            'AdminZone' => array(
                'FSBrowser'  => 'fs_browser.php'
               ,'FileSelector' => 'file_selector.php'
               ,'ServerFileSelector' => 'server_file_selector.php'
            ),
            'CustomerZone' => array(
            )
        )
    );


?>