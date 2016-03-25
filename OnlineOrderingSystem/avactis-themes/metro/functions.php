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
/*
  Select template for main menu.

  This hook is used to select tpl to display  to be shown from cms/menu folder
  To show default look of main menu return false
  To show category view

  @return boolean

*/

      if(preg_match("/functions.php/i", $_SERVER['PHP_SELF'])){
          die("ERROR, ACCESS FORBIDDED");
       }

	add_filter('category_menu_container','select_main_menu_container',10,1);

        function select_main_menu_container($return)
         {
             return false;
         }

function register_my_menus() {
  register_nav_menus(
    array(
      'header-menu' => 'Header Menu',
      'extra-menu' => 'Extra Menu',
      'footer-menu' => 'Footer Menu'
    )
  );
}
add_action( 'after_theme_loaded', 'register_my_menus' );
?>