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
		$tpl_title = getxMsg('NLT','COMPOSE_TITLE');
		$tpl_header = getxMsg('NLT','COMPOSE_TITLE');
		$tpl_onload_js = '';
		$tpl_parent = array(
					array(
						'name' => getxmsg('SYS','MENU_MARKETING'),
						'url' => 'marketing.php'
				),
					array(
						'name' => getxmsg('NLT', 'NEWSLETTER_TITLE1'),
						'url' => 'newsletter_archive.php'
				),
					array(
						'name' => getxmsg('NLT', 'COMPOSE_TITLE'),
						'url' => ''
				));
		$tpl_help = 'Manage_Newsletters';
		$tpl_class = 'Newsletter_Compose';
		$tpl_scripts = array('admin-avactis-validate');
	include('admin.tpl.php');
?>