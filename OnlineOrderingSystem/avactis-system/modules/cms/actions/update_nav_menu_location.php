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
 * @package CMS
 * @author Mugdha Wadhokar
 *
 */

class update_nav_menu_location extends AjaxAction
{
    function update_nav_menu_location()
    {
    }

    function onAction()
    {
        global $application;
        $request = &$application -> getInstance('Request');
        $nav_location_data = $request -> getValueByKey('nav_menu');
        $reg_nav_menu['nav_menu_location'] = $nav_location_data;

         $skin = modApiFunc('Look_Feel', 'getCurrentSkin');
         $option_name = "reg_menu_".$skin;
         asc_update_option($option_name,$reg_nav_menu);

         $req_to_redirect = new Request();
         $req_to_redirect -> setView(CURRENT_REQUEST_URL);
         $req_to_redirect -> setKey('page_view', 'CMS_Nav_Menu');
         $application -> redirect($req_to_redirect);
    }

}
?>