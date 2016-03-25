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

class ajax_get_plist extends AjaxAction
{
    function ajax_get_plist()
    {}

    function onAction()
    {
        $request = new Request();
        $category_id = $request->getValueByKey('category_id');

        /*
         *       :
         *                                                     ,                     .
         *                                                         default
         *               AZ (                                 AZ).
         *
         *        :
         *                   Catalog::getProductListByGlobalFilter       ,
         *                                                                                      .
         *                                 default                     .
         */

        //              default
        loadClass('CProductListFilter');
        $f = new CProductListFilter();
        $params = $f->getProductListParamsObject();
        $params->category_id = $category_id;
        $params->select_mode_recursiveness = IN_CATEGORY_ONLY;

        //
        $products_array = modApiFunc('Catalog','getProductListByFilter',$params,RETURN_AS_ID_LIST);
        $products = array();

        if(!empty($products_array))
        {
            foreach($products_array as $pinfo)
            {
                $obj = new CProductInfo($pinfo['product_id']);
                $products[] = array(
                    'id' => $pinfo['product_id']
                   ,'name' => $obj->getProductTagValue('Name')
                 );
            };
        };

        global $_RESULT;
        $_RESULT['products'] = $products;
    }
};

?>