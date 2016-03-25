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
class SaveSortedProducts extends AjaxAction
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
    function SaveSortedProducts()
    {
    }


    /**
     *
     *
     * Action: SaveSortProducts.
     *
     * @ finish the functions on this page
     */
    function onAction()
    {
        global $application;
        $request = $application->getInstance('Request');

        $SessionPost = $_POST;

        $prodSortOrder = $request->getValueByKey( 'ObjectList_hidden' );
        $prodSortOrderArray = explode('|', $prodSortOrder);

        modApiFunc('Session', 'set', 'SessionPost', $SessionPost);
        if ($prodSortOrderArray != NULL)
        {
            modApiFunc('Catalog', 'setProductsSortOrder', $prodSortOrderArray, modApiFunc('CProductListFilter','getCurrentCategoryId'));
        }
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