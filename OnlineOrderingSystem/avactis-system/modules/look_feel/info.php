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
 * @package Look & Feel
 * @author Sergey Kulitsky
 *
 */
$moduleInfo = array
    (
        'name'          => 'Look_Feel',
        'shortName'     => 'LF',
        'groups'        => 'Main',
        'description'   => 'Look & Feel module',
        'version'       => '0.1.47700',
        'author'        => 'Sergey Kulitsky',
        'contact'       => '',
        'constantsFile' => 'const.php',
        'systemModule'  => false,
        'mainFile'      => 'look_feel_api.php',
        'resFile'       => 'look-feel-messages',
        'actions' => array
        (
            'AdminZone' => array(
                'Change_Skin' => 'change_skin_az.php',
                'Edit_Skin' => 'edit_skin_az.php',
            )
        ),
        'views' => array
        (
            'AdminZone' => array(
                'SkinList' => 'skin_list_az.php'
            ),
            'CustomerZone' => array(
                'CustomSkinList' => 'custom_skin_list_cz.php'
            )
        )
    );

?>