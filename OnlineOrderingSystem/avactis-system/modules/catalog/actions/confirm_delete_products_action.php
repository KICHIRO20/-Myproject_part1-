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
class ConfirmDeleteProducts extends AjaxAction
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
    function ConfirmDeleteProducts()
    {
    }


    /**
     *
     *
     * Action: ConfirmDeleteProducts.
     *
     * @ finish the functions on this page
     */
    function onAction()
    {
        $SessionPost = $_POST;
        modApiFunc('Session', 'set', 'SessionPost', $SessionPost);

        $category_id = modApiFunc('CProductListFilter','getCurrentCategoryId');
        $ProdsId = explode("|",$SessionPost['product_ids']);
        if ($ProdsId != NULL)
        {
            if (modApiFunc('Session', 'is_set', 'SearchProductFormFilter'))
                modApiFunc('Catalog','delProductLinksFromAllCategories',$ProdsId);
            else
                modApiFunc('Catalog','delProductLinksFromCategory',$category_id,$ProdsId);
            $to_real_delete = array();
            foreach($ProdsId as $product_id)
            {
                if(!modApiFunc('Catalog','doesProductHaveLinks',$product_id))
                    $to_real_delete[] = $product_id;
            };

            if(!empty($to_real_delete))
            {
                modApiFunc('Catalog','setEditableProductsID',$to_real_delete);
                modApiFunc('Catalog','deleteProductsArray',$to_real_delete);
            }
            else
            {
                modApiFunc('Catalog','unsetEditableProductsID');
            };
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