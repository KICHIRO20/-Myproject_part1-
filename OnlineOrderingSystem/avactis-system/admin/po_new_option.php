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
		$tpl_styles = array('admin-default','admin-custom');
		$tpl_title = getxMsg('PO','ADD_NEW_OPTION');
		$tpl_header = getxMsg('PO','ADD_NEW_OPTION');
		$tpl_onload_js = '';
		$tpl_parent = array(
					array(
						'name' => getxmsg('SYS','MENU_CATALOG'),
						'url' => 'catalog.php'
				),
					array(
						'name' => getxmsg('SYS','MNG_PRD_PAGE_NAME'),
						'url' => 'catalog_manage_products.php'
				),
					array(
						'name' => getxmsg('PO','ADD_NEW_OPTION'),
						'url' => ''
				),
		);
		$tpl_help = 'product_options_add_edit';
		$tpl_tinymce = 'yes';
		$tpl_class = 'PO_AddOption';
	include('admin.tpl.php');
?>