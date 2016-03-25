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
 * @package Bestsellers
 * @author Egor V. Derevyankin
 *
 */

class save_bs_links_and_settings extends AjaxAction
{
    function save_bs_links_and_settings()
    {}

    function onAction()
    {
        global $application;

        $request = new Request();
        $category_id = $request->getValueByKey('category_id');
        $tree_id = $request->getValueByKey('tree_id');
        $bs_ids = $request->getValueByKey('to_save');

        modApiFunc('Bestsellers_API','deleteAllBSLinksFromCategory',$category_id);

        $errors = array();
        if($bs_ids != null)
        {
            if(!modApiFunc('Bestsellers_API','addBSLinksToCategory',$category_id,$bs_ids))
            {
                $errors[] = 'E_BS_NOT_SAVED';
            };
        };

        if(empty($errors))
        {
            $sets = $request->getValueByKey('sets');
            $sets['BS_FROM_STAT_PERIOD'] = $sets['BS_FROM_STAT_PERIOD']['count'] * $sets['BS_FROM_STAT_PERIOD']['type'];
            modApiFunc('Bestsellers_API','updateSettings',$category_id,$sets);
        };

        if(empty($errors))
        {
            modApiFunc('Session','set','ResultMessage','MSG_BS_SAVED');
        }
        else
        {
            modApiFunc('Session','set','Errors',$errors);
        };

       $request->setView('PopupWindow');
       // $request->setView('BS_LinksList');
        $request->setKey('page_view','BS_LinksList');
        $request->setKey('category_id', $category_id);
        $request->setKey('tree_id', $tree_id);

        $application->redirect($request);
    }
};


?>