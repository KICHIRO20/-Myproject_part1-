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

class update_images_of_product extends AjaxAction
{
    function update_images_of_product()
    {
    }

    function onAction()
    {
        global $application;
        $request = &$application->getInstance('Request');
        $product_id = $request->getValueByKey('product_id');

        $_alt_text = $request->getValueByKey('img_alt_text');
        $images_data = array();
        foreach($_alt_text as $image_id => $alt_text)
            $images_data[$image_id] = array('alt_text' => preparehtmldisplay($alt_text));

        modApiFunc('Product_Images','updateImagesOfProduct',$images_data);

        modApiFunc('Session','set','ResultMessage','MSG_IMAGES_UPDATED');

        $r = new Request();
        $r->setView('PI_ImagesList');
        $r->setKey('product_id',$product_id);
        $application->redirect($r);
    }
};

?>