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
		$tpl_title = getxMsg('SYS','LAYOUT_CMS_PAGE_TITLE');
		$tpl_header = getxMsg('LC', 'LC_LAYOUT_CMS');
		$tpl_onload_js = '';
		$tpl_parent = array(
					array(
						'name' => getxmsg('SYS','MENU_STOREFRONT_DESIGN'),
						'url' => 'storefront_design.php'
				),
					array(
						'name' => getxmsg('SYS','LAYOUT_CMS_PAGE_TITLE'),
						'url' => ''
				));
		$tpl_help = 'layout_cms';
		$tpl_class = 'LayoutCMS';
		$tpl_scripts = array("jquery","jquery-ui-core","jquery-ui-widget","jquery-ui-mouse",
								"jquery-ui-sortable","jquery-ui-draggable","sanitize_tags");
//		$tpl_jquery_ready = array('ASC_ADMIN.init();');
	include('admin.tpl.php');
?>