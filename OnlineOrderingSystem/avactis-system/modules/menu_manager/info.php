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
 * Cart module meta info.
 *
 * @package Cart
 * @author Alexander Girin
 */

$moduleInfo = array
(
    'name'         => 'MenuManager',
    'shortName'    => 'MENU_MNGR',
    'groups'       => 'Main',
    'description'  => 'To manage menus in Admin',
    'version'      => '0.1.47700',
    'author'       => 'Avactis Team',
    'contact'      => '',
    'systemModule' => false,
    'mainFile'     => 'menu_api.php',
    'resFile'      => '',


    'views' => array
    (
        'AdminZone' => array
        (
        	'AdminMenuManager' => 'admin_menu_az.php'
        ),

    )
);
?>