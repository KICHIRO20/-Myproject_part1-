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
//    $tpl_styles = array('admin-default','admin-custom');
    $tpl_title = getxMsg('SYS','SHIPPING_GENERAL_SETTINGS_PAGE_TITLE');
    $tpl_header = getxMsg('SYS', 'GSS_PAGE_HEADER');
   // $tpl_onload_js = 'unmark_all_available_modules_selects(); unmark_selected_modules_select();';
    $tpl_parent = array(
          array(
            'name' => getxmsg('SYS','MENU_SETTINGS'),
            'url' => 'settings.php'
        ),
          array(
            'name' => getxmsg('SYS','MENU_STORE_SETTINGS'),
            'url' => 'store_settings.php'
        ),
          array(
            'name' => getxmsg('SYS','SM_PAGE_TITLE'),
            'url' => 'shipping_modules.php'
        ),
          array(
            'name' => getxmsg('SYS','SHIPPING_GENERAL_SETTINGS_PAGE_TITLE'),
            'url' => ''
        )

        );
	$tpl_help = 'store_settings_shipping';
    $tpl_class = 'ShippingCostCalculatorSettings';
//    $tpl_scripts = array('admin-layout','admin-avactis-main','admin-avactis-validate');
  include('admin.tpl.php');
?>