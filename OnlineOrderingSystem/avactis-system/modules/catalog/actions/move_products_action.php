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
class MoveToProducts extends AjaxAction
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
    function MoveToProducts()
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

        $from_category = modApiFunc('CProductListFilter','getCurrentCategoryId');
        $req = $_POST;
        $to_categoryval = $req['to_category_id'];
        $to_category = explode("|",$to_categoryval);

        $products_ids = $request->getValueByKey('products_ids');
		$products_ids = explode("|",$products_ids);
		$catsprods = array_combine($products_ids,$to_category);

		if($to_category != null and $products_ids != null and $from_category != $to_category)
        {

		        modApiFunc('Catalog', 'moveProducts', $from_category, $catsprods,$products_ids);
		        $cat_obj = new CCategoryInfo($to_category);
		        modApiFunc('Paginator', 'setPaginatorPageToLast', 'Catalog_ProdsList_'.$to_category, $cat_obj->getCategoryTagValue('productsnumber'));
		 };


        modApiFunc('CProductListFilter','changeCurrentCategoryId',$to_category);
        $request->setView('ProductList');
        $application->redirect($request);
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