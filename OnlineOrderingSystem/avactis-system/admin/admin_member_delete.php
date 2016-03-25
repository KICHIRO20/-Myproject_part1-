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

		$tpl_title = getxMsg('SYS','ADMIN_DELETE_MEMBER_PAGE_TITLE');
		$tpl_header = getxMsg('SYS','ADMIN_DELETE_MEMBER_PAGE_TITLE');
		$tpl_onload_js = '';
		$tpl_parent = array(
				array(
						'name' => getxMsg('SYS','MENU_USERS'),
						'url' => 'users.php'
				),
					array(
						'name' => getxmsg('SYS','MENU_CONFIGURATION'),
						'url' => 'admin_members.php'
				),
				array(
						'name' => getxmsg('SYS','ADMIN_DELETE_MEMBER_PAGE_TITLE'),
						'url' => ''
				));
		$tpl_help = 'admin_delete_member';
		$tpl_class = 'AdminMemberDelete';
	include('admin.tpl.php');
?>