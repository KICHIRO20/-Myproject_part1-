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
 * @package Catalog
 * @author Egor V. Derevyankin
 *
 */

class update_product_cats extends AjaxAction
{
    function update_product_cats()
    {}

    function onAction()
    {
        global $application;
        $request = &$application->getInstance('Request');
        $product_id = $request->getValueByKey('product_id');
        $cats_ids = array_keys($request->getValueByKey('product_cats'));

        $prodObj = &$application->getInstance('CProductInfo',$product_id);
        $cur_cats = $prodObj->getCategoriesIDs();

        $cats_to_del = array_diff($cur_cats,$cats_ids);
        $cats_to_add = array_diff($cats_ids,$cur_cats);

        if(!empty($cats_to_del))
            modApiFunc('Catalog','delProductLinksFromCategories',$product_id,$cats_to_del);

        if(!empty($cats_to_add))
            foreach($cats_to_add as $category_id)
                modApiFunc('Catalog','addProductLinkToCategory',$product_id,$category_id);

        modApiFunc('Session','set','mustReloadParent',true);
        modApiFunc('Session','set','ResultMessage','MSG_PROD_CATS_UPDATED');

        $r = new Request();
        $r->setView('MngProductCats');
        $r->setKey('product_id',$product_id);
        $application->redirect($r);
    }
};

?>