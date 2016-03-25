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

class del_images_from_product extends AjaxAction
{
    function del_images_from_product()
    {
    }

    function onAction()
    {
        global $application;
        $request = &$application->getInstance('Request');

        $product_id = $request->getValueByKey('product_id');
        $del_from_server = $request->getValueByKey('delete_from_server');
        $images_ids = array_keys($request->getValueByKey('pi_to_del'));


        if($product_id != null and $images_ids != null)
            modApiFunc('Product_Images','delImagesFromProduct',$product_id,$images_ids,$del_from_server);

        modApiFunc('Session','set','ResultMessage','MSG_IMAGES_WERE_DELETED');

        $r = new Request();
        $r->setView('PI_ImagesList');
        $r->setKey('product_id',$product_id);
        $application->redirect($r);
    }
};

?>