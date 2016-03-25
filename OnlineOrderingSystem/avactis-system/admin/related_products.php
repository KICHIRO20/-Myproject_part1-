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
		$tpl_title = getxMsg('RP','MNG_RELATED_PRODUCTS');
		$tpl_header = getxMsg('RP','MNG_RELATED_PRODUCTS');
		$tpl_onload_js = '';
		$tpl_parent = array(
					array(
						'name' => getxmsg('SYS','CTLG_TAB_PAGE_NAME'),
						'url' => 'catalog.php'
				),
					array(
						'name' => getxmsg('SYS','MNG_PRD_PAGE_NAME'),
						'url' => 'catalog_manage_products.php'
				),
					array(
						'name' => getxmsg('RP','MNG_RELATED_PRODUCTS'),
						'url' => ''
				),
		);
		$tpl_help = 'related_products';
		$tpl_tinymce = 'yes';
		$tpl_class = 'RP_LinksList';
	include('admin.tpl.php');
?>