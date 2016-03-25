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

class update_page_data extends AjaxAction
{
    function update_page_data()
    {
    }

    function onAction()
    {
        global $application;
        $request = &$application -> getInstance('Request');

        // getting posted page data
        $page_data = $request -> getValueByKey('page_data');

        // getting mode
        $mode = $request -> getValueByKey('mode');

        // preventing adding several pages at once...
        // they may have the same system name which causes error
        $application -> enterCriticalSection('update_page_data');

        // validating data
        $error = $this -> validatePostedData($page_data);

        if ($error)
        {
            // if there is an error save the data and reload the page
            modApiFunc('Session', 'set', 'SavedPageData', $page_data);

            $page_id = $page_data['page_id'];
        }
        else
        {
            // no errors... ready to save the data
            $page_id = $this -> savePostedData($page_data);

            // reload the parent window while reloading
            modApiFunc('Session', 'set', 'CMS_ReloadParentWindow', 1);

            // setting ResultMessage
            if (@$page_data['page_id'] <= 0)
                modApiFunc('Session', 'set', 'ResultMessage',
                           'CMS_MSG_PAGE_ADDED');
            else
                modApiFunc('Session', 'set', 'ResultMessage',
                           'CMS_MSG_PAGE_UPDATED');
        }

        // leaving critical sections
        $application -> leaveCriticalSection();

        // prepare the redirect
        $req_to_redirect = new Request();
        $req_to_redirect -> setView(CURRENT_REQUEST_URL);
        $req_to_redirect -> setKey('page_view', 'CMS_Page_Data');
        $req_to_redirect -> setKey('page_id', $page_id);
        $application -> redirect($req_to_redirect);
    }

    function validatePostedData($page_data)
    {
        // checking if page name is specified
        if (!isset($page_data['name'])
            || $page_data['name'] == '')
        {
            modApiFunc('Session', 'set', 'ResultMessage',
                       'CMS_MSG_PAGE_SPECIFY_NAME');
            return 'PN';
        }

        // checking if page system name is specified
        if (!isset($page_data['page_index'])
            || $page_data['page_index'] == '')
        {
            modApiFunc('Session', 'set', 'ResultMessage',
                       'CMS_MSG_PAGE_SPECIFY_SYSTEM_NAME');
            return 'PSN';
        }

        // checking if page system name is unique
        if (modApiFunc('CMS', 'searchPages',
                       array('page_index' => $page_data['page_index'],
                             'excl_id' => @$page_data['page_id'])))
        {
            modApiFunc('Session', 'set', 'ResultMessage',
                       'CMS_MSG_PAGE_SYSTEM_NAME_EXISTS');
            return 'PSNNU';
        }

        // checking if parent page is specified
        if (!isset($page_data['parent_id'])
            || $page_data['parent_id'] < 0)
        {
            modApiFunc('Session', 'set', 'ResultMessage',
                       'CMS_MSG_PAGE_SPECIFY_PARENT_PAGE');
            return 'PP';
        }

        // checking if parent page is not a subpage of the page
        // to prevent the catch 22
        if (isset($page_data['page_id']) && $page_data['page_id']
            && _ml_strpos('/' . modApiFunc('CMS', 'getPagePath',
                                           $page_data['parent_id']),
                          '/' . $page_data['page_id'] . '/') !== false)
        {
            modApiFunc('Session', 'set', 'ResultMessage',
                       'CMS_MSG_PAGE_SPECIFY_PARENT_PAGE');
            return 'PP';
        }

        // checking if status is specified
        if (!isset($page_data['status'])
            || !in_array($page_data['status'], array('A', 'H', 'D')))
        {
            modApiFunc('Session', 'set', 'ResultMessage',
                       'CMS_MSG_PAGE_SPECIFY_STATUS');
            return 'PS';
        }

        // checking if availablility is specified
        if (!isset($page_data['availability'])
            || !in_array($page_data['availability'], array('A', 'R', 'C')))
        {
            modApiFunc('Session', 'set', 'ResultMessage',
                       'CMS_MSG_PAGE_SPECIFY_AVAILABILITY');
            return 'PA';
        }

        return '';
    }

    function savePostedData($page_data)
    {
        global $application;

        if (@$page_data['page_id'] <= 0)
        {
            // getting the sort order number to put the new item to the end
            $page_data['sort_order'] = modApiFunc('CMS',
                                                  'getPageLastOrderNumber');

            // inserting a new page
            execQuery('INSERT_CMS_NEW_PAGE', $page_data);
            $mysql = &$application -> getInstance('DB_MySQL');
            $page_data['page_id'] = $mysql -> DB_Insert_Id();
        }
        else
        {
            // updating page information
            execQuery('UPDATE_CMS_PAGE_DATA', $page_data);
        }

        return $page_data['page_id'];
    }
}