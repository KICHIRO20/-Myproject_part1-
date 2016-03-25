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

/**
* asc-hooks.php - Banner extension
*
* This file is loaded in memory even before the extension is loaded.
* So, this is a good place to add your hooks.
**/
	global $zone;

	if(strpos($_SERVER["REQUEST_URI"],'Banner')!= false && $zone == 'AdminZone')
	{
		/**
		 * @since v4.7.6
		 * Add title, header, onload js function calls, breadcrumbs, page-help links
		**/
		add_filter('asc_add_page_help_link','banner_page_help_link');
		add_filter('asc_add_admin_tpl_parameters','banner_admin_tpl_parameters');
	}

/**
 * @since v4.7.0
 * add menus and submenus in admin dashboard
**/
	if($zone == 'AdminZone')
		add_action('admin_menu', 'register_banner_pages');

	function register_banner_pages() {
		add_menu_page("Manage Banners","Manage Banners","","admin.php?page_view=BannerLocation","","icon-picture",'41');

		add_submenu_page( 'admin.php?page_view=BannerLocation', 'Top Banners', 'Top Banners', '', 'admin.php?page_view=BannerManagement&type=T', '' );
		add_submenu_page( 'admin.php?page_view=BannerLocation', 'Bottom Banners', 'Bottom Banners', '', 'admin.php?page_view=BannerManagement&type=B', '' );
		add_submenu_page( 'admin.php?page_view=BannerLocation', 'Left Banners', 'Left Banners', '', 'admin.php?page_view=BannerManagement&type=L', '' );
		add_submenu_page( 'admin.php?page_view=BannerLocation', 'Right Banners', 'Right Banners', '', 'admin.php?page_view=BannerManagement&type=R', '' );
	}

	function banner_page_help_link()
	{
		$add_link = array('banner_system' => array( 'VIDEO_TUTORIAL_NAME'  => '',
                                                        'PAGE_HELP_NAME' => 'Banner_System',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ));
		return $add_link;
	}

	function banner_admin_tpl_parameters() {
		$params = array(
					'tpl_title' => 'Manage Banners',
					'tpl_header' => 'Manage Banners',
					'tpl_onload_js' => '',
					'tpl_parent' => array(
							array(
									'name' => 'Manage Banners',
									'url' => 'admin.php?page_view=BannerLocation' //add parent page link here
							)),
					'tpl_help' => 'banner_system'
			);
		return $params;
	}
?>