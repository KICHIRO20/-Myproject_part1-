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
		$tpl_title = getxMsg('SYS','CONFIG_CREDIT_CARDS_EDITOR');
		$tpl_header = getxMsg('SYS','CONFIG_CREDIT_CARDS_EDITOR');
//		$tpl_onload_js = 'unmark_all_available_modules_selects(); unmark_selected_modules_select();';
		$tpl_parent = array(
					array(
						'name' => getxmsg('SYS','MENU_SETTINGS'),
						'url' => 'settings.php'
				),
					array(
						'name' => getxmsg('SYS','MENU_STORE_SETTINGS'),
						'url' => 'store_settings.php'
				),
					array(
						'name' => getxmsg('SYS','CONFIG_CREDIT_CARDS_EDITOR'),
						'url' => ''
				));
		$tpl_help = 'credit_card_editr';
		$tpl_class = 'CreditCardSettings';
	include('admin.tpl.php');
?>