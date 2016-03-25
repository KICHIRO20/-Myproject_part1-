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
		if(isset($_GET['page_id'])||isset($_POST['page_id'])){
			$tpl_title = getxMsg('CMS','CMS_EDIT_PAGE');
			$tpl_header = getxMsg('CMS','CMS_EDIT_PAGE');
		}else{
			$tpl_title = getxMsg('CMS','CMS_ADD_PAGE');
			$tpl_header = getxMsg('CMS','CMS_ADD_PAGE');
		}
		$tpl_onload_js = '';
		$tpl_parent = array(
					array(
						'name' => getxmsg('SYS','MENU_STOREFRONT_DESIGN'),
						'url' => 'storefront_design.php'
				),
					array(
						'name' => getxmsg('SYS','CMS_HEADER_001'),
						'url' => 'cms_pages.php'
				),
				array(
						'name' => $tpl_title,
						'url' => ''
				));
		$tpl_help = 'cms_tab';
		$tpl_class = 'CMS_Page_Data';
//		$tpl_scripts = array('admin-layout','admin-avactis-main','admin-avactis-validate');
//		$tpl_jquery_ready = array('ASC_ADMIN.init();');
	include('admin.tpl.php');
?>