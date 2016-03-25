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
		$tpl_title = getxMsg('SYS','TIMELINE_PAGE_TITLE');
		$tpl_header = getxMsg('TL','VIEW_PAGE_TITLE');
		$tpl_onload_js = '';
		$tpl_parent = array(
					array(
						'name' => getxmsg('SYS','ADMIN_MENU_HEADER_002'),
						'url' => 'tools.php'
					),
					array(
						'name' => getxMsg('TL','VIEW_PAGE_TITLE'),
						'url' => ''
				));
		$tpl_help = 'System_Logs';
		$tpl_class = 'TimelineView';
//		$tpl_scripts = array('admin-layout','admin-avactis-main','admin-avactis-validate');
	include('admin.tpl.php');
?>