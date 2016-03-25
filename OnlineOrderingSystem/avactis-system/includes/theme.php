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
 * Retrieve all theme modifications.
 *
 * @since 4.7.5
 *
 * @return array|null Theme modifications.
 */
function get_theme_navmenu($mode) {

	$skin = modApiFunc('Look_Feel', 'getCurrentSkin');
         $option_name = "reg_menu_".$skin;
	$theme_slug = asc_get_option( $option_name , true);
	return $theme_slug;
}

?>