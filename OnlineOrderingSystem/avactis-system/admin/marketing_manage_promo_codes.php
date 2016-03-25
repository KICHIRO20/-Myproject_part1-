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
		$tpl_title = getxMsg('SYS','MRKTNG_TAB_PROMO_CODES_MENU_TITLE');
		$tpl_header = getxMsg('SYS','MRKTNG_MNG_PROMO_CODES_PAGE_NAME');
		$tpl_onload_js = '';
		$tpl_parent = array(
					array(
						'name' => getxmsg('SYS','MENU_MARKETING'),
						'url' => 'marketing.php'
				),
					array(
						'name' => getxmsg('SYS','MRKTNG_TAB_PROMO_CODES_MENU_TITLE'),
						'url' => ''
				));
		$tpl_help = 'marketing_promo_codes';
		$tpl_class = 'PromoCodesNavigationBar';
//		$tpl_scripts = array('select2','asc-admin','admin-index','admin-layout','admin-avactis-main','admin-avactis-new-window');
	include('admin.tpl.php');
?>