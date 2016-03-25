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
    * Register navigation menus for a theme.
    *
    * @since 4.7.5
    *
    * @param array $locations Associative array of menu location identifiers (like a slug) and descriptive text.
    */
   global $_asc_registered_nav_menus;

   function register_nav_menus( $locations = array() ) {

           $_asc_registered_nav_menus = $locations;
           $reg_nav_menu['nav_menu_register'] = $locations;
           $skin = modApiFunc('Look_Feel', 'getCurrentSkin');
           $reg_nav_menu['theme'] = $skin;

           $option_name = "theme_mods_".$skin;

            $result = asc_get_option($option_name ,$default = 'true');

            if($result)
            asc_update_option($option_name,$reg_nav_menu);
            else
            asc_add_option($option_name,$reg_nav_menu);
   }

   /**
    * Register a navigation menu for a theme.
    *
    * @since 4.7.5
    *
    * @param string $location Menu location identifier, like a slug.
    * @param string $description Menu location descriptive text.
    */
   function register_nav_menu( $location, $description ) {
   	register_nav_menus( array( $location => $description ) );
   }

   /**
    * Returns an array of all registered navigation menus in a theme
    *
    * @since 4.7.5
    * @return array
    */
   function get_registered_nav_menus() {

            $skin = modApiFunc('Look_Feel', 'getCurrentSkin');
            $option_name = "theme_mods_".$skin;
            $location = asc_get_option($option_name ,$default = 'true');
          foreach($location as $location=>$discription)
          {
             if($location == 'nav_menu_register')
             {
                $_asc_registered_nav_menus = $discription;
             }
         }
   	if ( isset( $_asc_registered_nav_menus ) )
   		return $_asc_registered_nav_menus;
   	return array();
   }

   /**
    * Returns an array with the registered navigation menu locations and the menu assigned to it
    *
    * @since 4.7.5
    * @return array
    */

   function get_nav_menu_locations($r) {
   	$locations = get_theme_navmenu( 'nav_menu_locations' );
           $key_to_check = $r['theme_location'];
            $location = $locations['nav_menu_location'];
          foreach($location as $key => $value)
          {
               $key = str_replace("'", '', $key);
               if((strcmp((string)$key,(string)$key_to_check)) == 0){
               $menu_id = $value;}
          }
           $menuItem = modApiFunc('CMS', 'getMenuItems',$menu_id);
   	return ( is_array( $menuItem ) ) ? $menuItem : array();
   }

   /**
    * Whether a registered nav menu location has a menu assigned to it.
    *
    * @since 4.7.5
    * @param string $location Menu location identifier.
    * @return bool Whether location has a menu.
    */
   function has_nav_menu( $location ) {
   	$registered_nav_menus = get_registered_nav_menus();
   	if ( ! isset( $registered_nav_menus[ $location ] ) ) {
   		return false;
   	}

   	$locations = get_nav_menu_locations();
   	return ( ! empty( $locations[ $location ] ) );
   }
   ?>