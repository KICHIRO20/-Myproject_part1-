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
require_once('../admin.php');

$tpl_title = getxMsg('SYS','MENU_MANAGE_EXTENSIONS');
$tpl_header = getxMsg('SYS','MENU_MANAGE_EXTENSIONS');
$tpl_onload_js = '';
$tpl_parent = array(
		array(
			'name' => getxmsg('SYS','MENU_EXTENSIONS'),
			'url' => 'extensions.php'
		),
		array(
			'name' => getxMsg('SYS','MENU_MANAGE_EXTENSIONS'),
			'url' => ''
		));


$tpl_help = 'extension_manager';
$tpl_class = 'ExtensionManager_ListView';
include('admin.tpl.php');
?>