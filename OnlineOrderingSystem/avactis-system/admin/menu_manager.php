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
		$tpl_title = getxMsg('SYS','CMS_HEADER_002');
		$tpl_header = getxMsg('SYS','CMS_HEADER_002');
		$tpl_onload_js = '';
		$tpl_parent = array(
					array(
						'name' => getxmsg('SYS','MENU_STOREFRONT_DESIGN'),
						'url' => 'storefront_design.php'
				),
					array(
						'name' => getxmsg('SYS','CMS_HEADER_002'),
						'url' => ''
				));
		$tpl_help = 'cms_menu_tab';
		$tpl_class = 'CMS_Nav_Menu';
        	include('admin.tpl.php');
?>