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
 * @package CMS
 * @author Sergey Kulitsky
 *
 */

class update_pages extends AjaxAction
{
    function update_pages()
    {
    }

    function onAction()
    {
        global $application;
        $request = &$application -> getInstance('Request');

        // getting posted page data
        $data = $request -> getValueByKey('data');
        $delete = $request -> getValueByKey('delete');

        // getting mode
        $action = $request -> getValueByKey('mode');

        if ($action == 'update')
        {
            $this -> savePostedData($data);

            // setting ResultMessage
            modApiFunc('Session', 'set', 'ResultMessage',
                       'CMS_MSG_PAGES_UPDATED');
        }
        elseif ($action == 'delete' && is_array($delete))
        {
            foreach($delete as $page_id => $v)
                modApiFunc('CMS', 'deletePage', $page_id);

            // setting ResultMessage
            modApiFunc('Session', 'set', 'ResultMessage',
                       'CMS_MSG_PAGES_DELETED');
        }
        elseif ($action == 'sort')
        {
            $sort = explode('|', $request -> getValueByKey('cms_pages_sort_order_hidden'));
            modApiFunc('CMS', 'updatePagesSortOrder', $sort);
            modApiFunc('Session', 'set', 'ResultMessage', 'CMS_MSG_PAGES_SORTED');
        }

        // prepare the redirect
        $req_to_redirect = new Request();
        $req_to_redirect -> setView(CURRENT_REQUEST_URL);
        $application -> redirect($req_to_redirect);
    }

    function savePostedData($data)
    {
        if (!is_array($data))
            return;

        foreach($data as $k => $v)
        {
            $params = array();

            if (in_array(@$v['status'], array('A', 'H', 'D')))
                $params['status'] = $v['status'];

            if (in_array(@$v['availability'], array('A', 'R', 'C')))
                $params['availability'] = $v['availability'];

            if (isset($v['parent_id']) && ($v['parent_id'] == 0
                || _ml_strpos('/' . modApiFunc('CMS', 'getPagePath',
                                               $v['parent_id']),
                              '/' . $k . '/') === false))
                $params['parent_id'] = $v['parent_id'];

            if (!empty($params))
            {
                $params['page_id'] = $k;
                execQuery('UPDATE_CMS_PAGE_DATA', $params);
            }
        }
    }
}