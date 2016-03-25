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
 * Displays Administration Menu.
 *
 * @package Avactis
 * @subpackage Administration
 */

global $menu, $submenu, $parent_file;

/**
 * Display menu.
 *
 * @access private
 * @since 4.7.0
 *
 * @param array $menu
 * @param array $submenu
 * @param bool $submenu_as_parent
 */
function _asc_menu_output( $menu, $submenu, $submenu_as_parent = true ) {
	global $self, $parent_file, $submenu_file, $plugin_page;

	echo "<div class='page-sidebar navbar-collapse collapse'>";
	echo "<ul class='page-sidebar-menu page-sidebar-menu-hover-submenu' data-auto-scroll='true' data-slide-speed='200'>";
	$first = true;
	// 0 = menu_title, 1 = capability, 2 = menu_slug, 3 = page_title, 4 = classes, 5 = hookname, 6 = icon_url
	foreach ( $menu as $key => $item ) {
		$admin_is_parent = false;
		$class = array();
		$spanclass = array();
		if ( $parent_file == $item[2] ) {
			$class[] = 'active';
			$spanclass[] = 'selected';
			$first = false;
		}

		$submenu_items = false;

		if ( ! empty( $submenu[$item[2]] ) ) {
			$class[] = '';
			if (empty($spanclass)) $spanclass[] = 'arrow';
			$submenu_items = $submenu[$item[2]];
		}

		if ( ! empty( $item[4] ) )
			$class[] = esc_attr( $item[4] );

		$class = $class ? ' class="' . join( ' ', $class ) . '"' : '';
		$spanclass = $spanclass ? ' class="' . join( ' ', $spanclass ) . '"' : '';
		$id = ! empty( $item[5] ) ? ' id="' . preg_replace( '|[^a-zA-Z0-9_:.]|', '-', $item[5] ) . '"' : '';

		$img = $img_style = '';

		$img_class = ' dashicons-before';

		if ( ! empty( $item[6] ) ) {
			$img = '<img src="' . $item[6] . '" alt="" />';

			if ( 'none' === $item[6] || 'div' === $item[6] ) {
				$img = '<br />';
			} elseif ( 0 === strpos( $item[6], 'data:image/svg+xml;base64,' ) ) {
				$img = '<br />';
				$img_style = ' style="background-image:url(\'' . esc_attr( $item[6] ) . '\')"';
				$img_class = ' svg';
			} elseif ( 0 === strpos( $item[6], 'dashicons-' ) ) {
				$img = '<br />';
				$img_class = ' dashicons-before ';// . sanitize_html_class( $item[6] );
			}
		}

		$title = /*wptexturize(*/ $item[0]; //);

		echo "\n\t<li$class$id>";
		echo "<a href='$item[2]'><i class='$item[6] pull-left'></i><span class='title'>$title</span><span$spanclass></span></a>";

		if ( ! empty( $submenu_items ) ) {
			echo "\n\t<ul class='sub-menu'>";
			//echo "<li class='wp-submenu-head'>{$item[0]}</li>";
			$first = true;

			// 0 = menu_title, 1 = capability, 2 = menu_slug, 3 = page_title, 4 = classes
			foreach ( $submenu_items as $sub_key => $sub_item ) {
				$class = array();
				if ( $first ) {
					$class[] = '';
					$first = false;
				}
				$menu_file = $item[2];
				if ( false !== ( $pos = strpos( $menu_file, '?' ) ) )
					$menu_file = substr( $menu_file, 0, $pos );

				if ( isset( $submenu_file ) ) {
					if ( $submenu_file == $sub_item[2] )
						$class[] = 'current';
				// If plugin_page is set the parent must either match the current page or not physically exist.
				// This allows plugin pages with the same hook to exist under different parents.
				}

				$class = $class ? ' class="' . join( ' ', $class ) . '"' : '';

				echo "<li$class><a href='{$sub_item[2]}'$class><i class='icon-link'></i><span class='title'>$sub_item[0]</span><span class='arrow'></span></a></li>";


			}
			echo "</ul>";
		}
		echo "</li>";
	}
	echo "</ul>";
	echo "</div>";
}

_asc_menu_output( $menu, $submenu );

do_action( 'adminmenu' );
?>