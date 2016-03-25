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
 * @package RelatedProducts
 * @author Egor V. Derevyankin
 *
 */

class save_rp_links extends AjaxAction
{
    function save_rp_links()
    {}

    function onAction()
    {
        global $application;

        $request = new Request();
        $product_id = $request->getValueByKey('product_id');
        $rp_ids = $request->getValueByKey('to_save');

        modApiFunc('Related_Products','deleteAllRPLinksFromProduct',$product_id);

        $errors = array();
        if($rp_ids != null)
        {
            if(!modApiFunc('Related_Products','addRPLinksToProduct',$product_id,$rp_ids))
            {
                $errors[] = 'E_RP_NOT_SAVED';
            };
        };

        if(empty($errors))
        {
            modApiFunc('Session','set','ResultMessage','MSG_RP_SAVED');
        }
        else
        {
            modApiFunc('Session','set','Errors',$errors);
        };

        $request->setView('related_products');
        $request->setKey('product_id', $product_id);

        $application->redirect($request);
    }
};

?>