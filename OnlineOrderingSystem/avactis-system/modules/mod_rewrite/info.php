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
 * @package ModRewrite
 * @author Egor V. Derevyankin
 *
 */
$moduleInfo = array
    (
        'name'         => 'Mod_Rewrite',
        'shortName'    => 'MR',
        'groups'       => 'Main',
        'description'  => 'Mod Rewrite module',
        'version'      => '0.1.47700',
        'author'       => 'Egor V. Derevyankin',
        'contact'      => '',
        'systemModule'  => false,
        'mainFile'     => 'mod_rewrite_api.php',
        'resFile'      => 'mod-rewrite-messages',
        'constantsFile' => 'const.php',

        'actions' => array
        (
           'AdminZone' => array(
               'update_mr_settings'    =>  'update_mr_settings.php'
               ,'ajax_gen_htaccess'     =>  'ajax_gen_htaccess.php'
               ,'gen_save_htaccess'     =>  'gen_save_htaccess.php'
               ,'update_mr_for_layout'  =>  'update_mr_for_layout.php'
               ,'update_mr_settings_and_layouts' => 'update_mr_settings_and_layouts.php'
           ),
        ),

        'hooks' => array
        (
        ),

        'views' => array
        (
            'AdminZone' => array(
                'MR_Settings'   =>  'mr_settings_az.php'
            ),
            'CustomerZone' => array(
            )
        )
    );

?>