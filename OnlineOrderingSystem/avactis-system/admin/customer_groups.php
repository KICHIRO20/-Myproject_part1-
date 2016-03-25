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

		$tpl_title = getxMsg('CA','CA_CUSTOMER_GROUPS');
		$tpl_header = getxMsg('CA','CA_CUSTOMER_GROUPS');
		$tpl_onload_js = '';
		$tpl_parent = array(
					array(
						'name' => getxMsg('SYS','MENU_USERS'),
						'url' => 'users.php'
				),array(
						'name' => getxmsg('SYS','MENU_CUSTOMERS'),
						'url' => 'customers.php'
				)array(
						'name' => getxMsg('CA','CA_CUSTOMER_GROUPS'),
						'url' => 'users.php'
				));
		$tpl_help = 'customers_tab';
		$tpl_class = 'CustomerGroups';
	include('popup_window.php');
?>