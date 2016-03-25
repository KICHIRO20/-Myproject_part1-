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
 * @package FeaturedProducts
 * @author Egor V. Derevyankin
 *
 */

class save_fp_links extends AjaxAction
{
    function save_fp_links()
    {}

    function onAction()
    {
        global $application;

        $request = new Request();
        $category_id = $request->getValueByKey('category_id');
        $tree_id = $request->getValueByKey('tree_id');
        $fp_ids = $request->getValueByKey('to_save');

        modApiFunc('Featured_Products','deleteAllFPLinksFromCategory',$category_id);

        $errors = array();
        if($fp_ids != null)
        {
            if(!modApiFunc('Featured_Products','addFPLinksToCategory',$category_id,$fp_ids))
            {
                $errors[] = 'E_FP_NOT_SAVED';
            };
        };

        if(empty($errors))
        {
            modApiFunc('Session','set','ResultMessage','MSG_FP_SAVED');
        }
        else
        {
            modApiFunc('Session','set','Errors',$errors);
        };

        $request->setView('PopupWindow');
        $request->setKey('page_view','FP_LinksList');
        $request->setKey('category_id', $category_id);
        $request->setKey('tree_id', $tree_id);

        $application->redirect($request);
    }
};

?>