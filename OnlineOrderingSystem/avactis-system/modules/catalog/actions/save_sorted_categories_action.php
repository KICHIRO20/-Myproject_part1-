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
class SaveSortedCategories extends AjaxAction
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
    function SaveSortedCategories()
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

        $catSortOrder = $request->getValueByKey( 'ObjectList_hidden' );
        $catSortOrderArray = explode('|', $catSortOrder);

        modApiFunc('Session', 'set', 'SessionPost', $SessionPost);
        if ($catSortOrderArray != NULL)
        {
            modApiFunc('Catalog', 'setCategorySortOrder', $catSortOrderArray);
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