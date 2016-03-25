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
 * @package Look & Feel
 * @author Sergey Kulitsky
 *
 */

/**
 * Definition of SkinList viewer
 * The viewer is used to manage storefront skins in the store
 */
class CustomSkinList
{
    /**
     * Constructor
     */
    function CustomSkinList()
    {
    }

    /**
     * The main function to output the viewer content.
     */
    function output()
    {
        global $application;

        $current_skin = $application -> currentSkin;
        $skins = modApiFunc('Look_Feel', 'getDetailedSkinList', '');

        $result = "<select name=\"customSkin\" onchange=\"document.location='index.php?set_custom_skin=' + this.value + '&amp;returnURL="
               . urlencode(modApiFunc('Request', 'selfURL')) . "'\">";
        foreach($skins as $skin)
        {
            $result .= '<option value="' . $skin['skin'] . '" ' .
                       (($skin['skin'] == $current_skin)
                           ? 'selected="selected"' : '') . '>' .
                       ((isset($skin['name']))
                           ? $skin['name'] : $skin['skin']) . '</option>';
        }
        $result .= '</select>';

        $result = "<div class='skin_select'><span class='panel_title'>Avactis Demo Store</span><span class='skin_selector'>Select skin: $result</span></div>";

        return $result;
    }
}