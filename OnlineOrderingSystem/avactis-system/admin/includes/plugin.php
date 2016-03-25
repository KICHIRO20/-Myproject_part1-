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
//

//
// Menu
//

/**
 * Add a top level menu page
 *
 * This function takes a capability which will be used to determine whether
 * or not a page is included in the menu.
 *
 * The function which is hooked in to handle the output of the page must check
 * that the user has the required capability as well.
 *
 * @param string $page_title The text to be displayed in the title tags of the page when the menu is selected
 * @param string $menu_title The text to be used for the menu
 * @param string $capability The capability required for this menu to be displayed to the user.
 * @param string $menu_slug The slug name to refer to this menu by (should be unique for this menu)
 * @param callback $function The function to be called to output the content for this page.
 * @param string $icon_url The url to the icon to be used for this menu.
 *     * Pass a base64-encoded SVG using a data URI, which will be colored to match the color scheme.
 *       This should begin with 'data:image/svg+xml;base64,'.
 *     * Pass the name of a Dashicons helper class to use a font icon, e.g. 'dashicons-chart-pie'.
 *     * Pass 'none' to leave div.wp-menu-image empty so an icon can be added via CSS.
 * @param int $position The position in the menu order this one should appear
 *
 * @return string The resulting page's hook_suffix
 */
function add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function = '', $icon_url = '', $position = null ) {
	global $menu, $admin_page_hooks, $_registered_pages, $_parent_pages;

	$menu_slug = plugin_basename( $menu_slug );

	$admin_page_hooks[$menu_slug] = sanitize_title( $menu_title );

	$hookname = 'admin_template_page';

	if ( !empty( $function ) && !empty( $hookname ))
		add_action( $hookname, $function );

	if ( empty($icon_url) ) {
		$icon_url = 'dashicons-admin-generic';
		$icon_class = 'menu-icon-generic ';
	} else {
		//$icon_url = set_url_scheme( $icon_url );
		$icon_class = $icon_url;
	}

	$new_menu = array( $menu_title, $capability, $menu_slug, $page_title, 'menu-top ' . $icon_class . $hookname, $hookname, $icon_url );

	if ( null === $position )
		$menu[] = $new_menu;
	else
		$menu[$position] = $new_menu;

	$_registered_pages[$hookname] = true;

	// No parent as top level
	$_parent_pages[$menu_slug] = false;

	return $hookname;
}
//ex: add_menu_page("DashboardNew","DashboardNew","read","dashboard.php","","icon-home",'5.2');

/**
 * Add a sub menu page
 *
 * This function takes a capability which will be used to determine whether
 * or not a page is included in the menu.
 *
 * The function which is hooked in to handle the output of the page must check
 * that the user has the required capability as well.
 *
 * @param string $parent_slug The slug name for the parent menu (or the file name of a standard WordPress admin page)
 * @param string $page_title The text to be displayed in the title tags of the page when the menu is selected
 * @param string $menu_title The text to be used for the menu
 * @param string $capability The capability required for this menu to be displayed to the user.
 * @param string $menu_slug The slug name to refer to this menu by (should be unique for this menu)
 * @param callback $function The function to be called to output the content for this page.
 *
 * @return string|bool The resulting page's hook_suffix, or false if the user does not have the capability required.
 */
function add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function = '' ) {
	global $submenu;
	global $menu;
	global $_asc_real_parent_file;
	global $_asc_submenu_nopriv;
	global $_registered_pages;
	global $_parent_pages;

	$menu_slug = plugin_basename( $menu_slug );
	$parent_slug = plugin_basename( $parent_slug);

	if ( isset( $_asc_real_parent_file[$parent_slug] ) )
		$parent_slug = $_asc_real_parent_file[$parent_slug];

	/*
	 * If the parent doesn't already have a submenu, add a link to the parent
	 * as the first item in the submenu. If the submenu file is the same as the
	 * parent file someone is trying to link back to the parent manually. In
	 * this case, don't automatically add a link back to avoid duplication.
	 */
	if (!isset( $submenu[$parent_slug] ) && $menu_slug != $parent_slug ) {
		foreach ( (array)$menu as $parent_menu ) {
			if ( $parent_menu[2] == $parent_slug )
				$submenu[$parent_slug][] = array_slice( $parent_menu, 0, 4 );
		}
	}

	$submenu[$parent_slug][] = array ( $menu_title, $capability, $menu_slug, $page_title );

	$hookname = 'admin_template_page';
	if (!empty ( $function ) && !empty ( $hookname ))
		add_action( $hookname, $function );

	$_registered_pages[$hookname] = true;

	// No parent as top level.
	$_parent_pages[$menu_slug] = $parent_slug;

	return $hookname;
}
?>