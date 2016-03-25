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
		$tpl_styles = array('select2','admin-login-soft','components','plugins','admin-layout','admin-default','admin-custom');
		$tpl_title = getxMsg('SYS','SIGN_IN_PASSWD_RECOVERY_PAGE_TITLE');
		//$tpl_header = getxMsg('SYS','ADMIN_ADD_MEMBER_PAGE_TITLE');
		//$tpl_onload_js = '';
		//$tpl_help = 'admin_edit_member';
		$tpl_class = 'AdminPasswordRecovery';
		$tpl_scripts = array('jquery.validate','jquery-backstretch','select2','asc-admin','admin-layout','admin-login-soft','admin-avactis-md5','admin_print_scripts');
	include('popup_window.php');
	include('stat/stat.php');
?>




