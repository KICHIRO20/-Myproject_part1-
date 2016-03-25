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
//    : Check if the user is admin before firing this action
do_action( '_user_admin_menu' );

// Create list of page plugin hook names.
foreach ($menu as $menu_page) {
	//    : Check for any ? in the menu page link
	//if ( false !== $pos = strpos($menu_page[2], '?') ) {
	//    : Logic for any specific param passed
	//else set the hookname to the specific php file name
	$hook_name = basename($menu_page[2], '.php');
//	echo "Hookname is $hook_name";
	//$hook_name = sanitize_title($hook_name);
	if ( !$hook_name )
		continue;

		$admin_page_hooks[$menu_page[2]] = $hook_name;
	}

do_action( 'admin_menu', '' );

uksort($menu, "strnatcasecmp"); // make it all pretty

function add_cssclass($add, $class) {
	$class = empty($class) ? $add : $class .= ' ' . $add;
	return $class;
}

function add_menu_classes($menu) {
	$first = $lastorder = false;
	$i = 0;
	$mc = count($menu);
	foreach ( $menu as $order => $top ) {
		$i++;

		if ( 0 == $order ) { // dashboard is always shown/single
			$menu[0][4] = add_cssclass('active', $top[4]);
			$lastorder = 0;
			continue;
		}

		if ( 0 === strpos($top[2], 'separator') && false !== $lastorder ) { // if separator
			$first = true;
			$c = $menu[$lastorder][4];
			$menu[$lastorder][4] = add_cssclass('', $c);
			continue;
		}

		if ( $first ) {
			$c = $menu[$order][4];
			$menu[$order][4] = add_cssclass('menu-top-first', $c);
			$first = false;
		}

		if ( $mc == $i ) { // last item
			$c = $menu[$order][4];
			$menu[$order][4] = add_cssclass('menu-top-last', $c);
		}

		$lastorder = $order;
	}
	return apply_filters( 'add_menu_classes', $menu );
}


$menu = add_menu_classes($menu);

?>