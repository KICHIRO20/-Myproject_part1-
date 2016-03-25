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
class MoveToCategory extends AjaxAction
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
    function MoveToCategory()
    {
    }


    /**
     *
     *
     * Action: ConfirmDeleteCategory.
     *
     * @ finish the functions on this page
     */
    function onAction()
    {
        global $application;
        $request = $application->getInstance('Request');

        $SessionPost = $_POST;

        $newParentCatId = modApiFunc("Catalog", "getMoveToCategoryID");
        $CurrentCatId = modApiFunc('CProductListFilter','getCurrentCategoryId');
        $cid = modApiFunc("Catalog", "getEditableCategoryID");

        modApiFunc('Session', 'set', 'SessionPost', $SessionPost);

        if ( ($newParentCatId != NULL && $cid != NULL) && ($CurrentCatId != $newParentCatId) )
        {
            modApiFunc('Catalog', 'moveCategory', $newParentCatId, $cid);
        }

        modApiFunc('CProductListFilter','changeCurrentCategoryId',$newParentCatId);
        modApiFunc('Catalog', 'unsetMoveToCategoryID');
        modApiFunc("Catalog", "unsetEditableCategoryID");
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