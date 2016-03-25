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
		$tpl_title = getxMsg('SYS','ORDERS_INFO');
		$tpl_header = getxMsg('SYS','ORDERS_INFO');
		$tpl_onload_js = '';
		$tpl_parent = array(
					array(
						'name' => getxmsg('SYS','ORDERS_PAGE_TITLE'),
						'url' => 'orders.php'
				));
		$tpl_help = 'PageTutorialHelpLinks';
		$tpl_class = 'OrderInfo';
	include('popup_window.php');
?>