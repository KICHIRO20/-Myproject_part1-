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

		$tpl_title = getxMsg('SYS','BACKUP_PAGE_TITLE');
		$tpl_header = getxMsg('SYS','ADMIN_BACKUP_PAGE_NAME');
		$tpl_onload_js = '';
		$tpl_parent = array(
					array(
						'name' => getxmsg('SYS','MENU_TOOLS'),
						'url' => 'tools.php'
				),
					array(
						'name' => getxmsg('SYS','BACKUP_PAGE_TITLE'),
						'url' => 'admin_backup.php'
				));
		$tpl_help = 'admin_backup';
		$tpl_class = 'Backup';
	include('admin.tpl.php');
?>