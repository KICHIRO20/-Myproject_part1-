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
class ConfirmDeleteCategory extends AjaxAction
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
    function ConfirmDeleteCategory()
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

        $CatsId = $request->getValueByKey('CatsId');
        $ProdsId = $request->getValueByKey('ProdsId');

        modApiFunc('Session', 'set', 'SessionPost', $SessionPost);

        if ($ProdsId != NULL)
        {
            $products_ids = explode("|", $ProdsId);
            $categories_ids = explode("|", $CatsId);

            modApiFunc('Catalog','delAllProductLinks','category_id',$categories_ids);

            $to_real_delete = array();
            foreach($products_ids as $product_id)
            {
                if(!modApiFunc('Catalog','doesProductHaveLinks',$product_id))
                    $to_real_delete[] = $product_id;
            };

            if(!empty($to_real_delete))
                modApiFunc('Catalog', 'deleteProductsArray', $to_real_delete);

            $_POST['ProdsId'] = implode("|",$to_real_delete);
        }

        if ($CatsId != NULL)
        {
            modApiFunc('Catalog', 'deleteCategoriesArray', explode("|", $CatsId));
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