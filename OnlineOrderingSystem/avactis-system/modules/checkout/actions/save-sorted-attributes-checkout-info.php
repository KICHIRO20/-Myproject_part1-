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
 * Catalog module.
 * Action handler on .
 *
 * @package Catalog
 * @access  public
 */
class SaveSortedAttributes extends AjaxAction
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Action constructor.
     *
     * @ finish the functions on this page
     */
    function SaveSortedAttributes()
    {
    }


    /**
     *
     *
     * Action: SaveSortCat.
     *
     * @ finish the functions on this page
     */
    function onAction()
    {
        global $application;
        $request = $application->getInstance('Request');

        $SessionPost = $_POST;

        $attrSortOrder = $request->getValueByKey( 'ObjectList_hidden' );
        $attrSortOrderArray = explode('|', $attrSortOrder);

        $variantId = $request->getValueByKey( 'VariantId' );

        modApiFunc('Session', 'set', 'SessionPost', $SessionPost);

        if ($attrSortOrderArray != NULL)
        {
            modApiFunc('Checkout', 'setAttributesSortOrder', $attrSortOrderArray, $variantId);
        }

        modApiFunc('Checkout', 'updateCheckoutFormHash');
    }

    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    /**#@-*/

}

?>