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
//		$tpl_styles = array('admin-default','admin-custom');
		$tpl_title = getxMsg('SYS','TITLE_UNSUBSCRIBE');
		$tpl_header = getxMsg('SYS','TITLE_UNSUBSCRIBE');
		$tpl_onload_js = '';
		$tpl_parent = array(
					array(
						'name' => getxmsg('SYS','MENU_MARKETING'),
						'url' => 'marketing.php'
				),
					array(
						'name' => getxmsg('SUBSCR', 'SUBSCRIPTIONS_TITLE'),
						'url' => 'subscriptions_manage.php'
				),
					array(
						'name' => getxmsg('SYS','TITLE_UNSUBSCRIBE'),
						'url' => ''
				));
		$tpl_help = 'Subscriptions_MainPage';
		$tpl_class = 'Subscriptions_Subscribe_Page';
//		$tpl_scripts = array('admin-layout','admin-avactis-main','admin-avactis-validate');
	include('admin.tpl.php');
?>

<?php
function Subscriptions_Subscribe_Page()
{
	Subscriptions_Subscribe('unsubscribe');
}
?>