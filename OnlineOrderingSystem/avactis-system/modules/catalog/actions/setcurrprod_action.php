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
 * Action handler on SetCurrentProduct.
 *
 * @package Catalog
 * @access  public
 */
class SetCurrentProduct extends AjaxAction
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
    function SetCurrentProduct()
    {
        global $application;

        $this->pCatalog = &$application->getInstance('Catalog');
    }

    /**
     * Sets current inventory product from Request.
     *
     * Action: setCurrCat
     *
     * @ finish the functions on this page
     */
    function onAction()
    {
        global $application;
        $request = $application->getInstance('Request');

        $prod_id = $request->getValueByKey( 'prod_id' );
        if ($request->getValueByKey('del_info'))
        {
            if ($request->getValueByKey( 'del_info' ) == "true")
            {
//                $this->pCatalog->setDisplayDeleteInfo();
            }
        }

        if ($prod_id != NULL)
        {
            $this->pCatalog->setCurrentProductID( $prod_id );

            //
            $product_obj = new CProductInfo($this->pCatalog->getCurrentProductID());
            modApiFunc("CProductListFilter", "changeCurrentCategoryId", $product_obj->chooseCategoryID());
        }

        if ($request->getValueByKey('keep_editor_state') === null)
        {
            modApiFunc('Session','un_set','ProductInfoWYSIWYGEditorEnabled');
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