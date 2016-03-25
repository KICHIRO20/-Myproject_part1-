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
 * @package ProductImages
 * @author Egor V. Derevyankin
 *
 */

class update_imgs_sort_order extends AjaxAction
{
    function update_imgs_sort_order()
    {
    }

    function onAction()
    {
        global $application;
        $request = &$application->getInstance('Request');

        $product_id = $request->getValueByKey('product_id');
        $sort_order = explode(",",$request->getValueByKey('sort_order'));

        modApiFunc('Product_Images','updateImagesSortOrder',$product_id,$sort_order);

        modApiFunc('Session','set','ResultMessage','MSG_SORD_ORDER_UPDATED');

        $r = new Request();
        $r->setView('PI_ImagesList');
        $r->setKey('product_id',$product_id);
        $application->redirect($r);

    }
};

?>