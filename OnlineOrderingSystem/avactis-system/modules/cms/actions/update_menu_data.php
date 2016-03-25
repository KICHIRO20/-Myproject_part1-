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

class update_menu_data extends AjaxAction
{
    function update_menu_data()
    {
    }

    function onAction()
    {
        global $application;
        $request = &$application -> getInstance('Request');

        // getting mode
        $mode = $request -> getValueByKey('mode');
        // preventing adding several menu at once...
        // they may have the same system name which causes error
        $application -> enterCriticalSection('update_menu_data');

        // getting posted page data
        $menu_data = $request -> getValueByKey('menu_data');
        $menu_data['menu_name'] = $menu_data['menu_index'];
        if ($mode == 'update')
        {
            // validating data
            $error = $this -> validatePostedData($menu_data);

            if ($error)
            {
                // leaving critical sections
                $application -> leaveCriticalSection();

                // if there is an error save the data and reload the page
                modApiFunc('Session', 'set', 'SavedMenuData', $menu_data);

                $menu_id = $menu_data['menu_id'];
            }
            else
            {
                // no errors... ready to save the data
                $menu_id = $this -> savePostedData($menu_data);

                // saving the links
                $links = $request -> getValueByKey('link');
                if (is_array($links))
                    foreach($links as $k => $v)
                    {
                        $v['menu_id'] = $menu_id;
                        $v['menu_item_id'] = $k;

                        // getting link depending on the type
                        if ($v['item_type'] == CMS_MENU_ITEM_TYPE_URL)
                            $v['item_link'] = $v['link_url'];
                        elseif ($v['item_type'] == CMS_MENU_ITEM_TYPE_EXTERNAL_URL)
                        {
                            $v['item_link'] = $v['link_external_url'];
                            if(!preg_match('/^(.*)http(s?):\/\/(.*)$/', $v['item_link']))
                                $v['item_link'] = 'http://'.$v['item_link'];
                        }
                        elseif ($v['item_type'] == CMS_MENU_ITEM_TYPE_SYSTEM_PAGE)
                            $v['item_link'] = $v['link_system_page'];
                        elseif ($v['item_type'] == CMS_MENU_ITEM_TYPE_STATIC_PAGE)
                            $v['item_link'] = $v['link_static_page'];
                        else
                            $v['item_link'] = '';

                        $v['param1'] = '';
                        $v['param2'] = '';

                        if ($v['item_type'] == CMS_MENU_ITEM_TYPE_SYSTEM_PAGE
                            && _ml_strtolower($v['item_link']) == 'productlist')
                            $v['param1'] = $v['list_catid'];

                        if ($v['item_type'] == CMS_MENU_ITEM_TYPE_SYSTEM_PAGE
                            && _ml_strtolower($v['item_link']) == 'productinfo')
                            $v['param2'] = $v['info_prodid'];

                        $v['item_status'] = ((isset($v['item_status']))
                                              ? CMS_MENU_ITEM_STATUS_ACTIVE
                                              : CMS_MENU_ITEM_STATUS_INACTIVE);

                        execQuery('UPDATE_CMS_MENU_ITEM_DATA', $v);
                    }

                // reload the parent window while reloading
//                modApiFunc('Session', 'set', 'CMS_ReloadParentWindow', 1);

                // setting ResultMessage
                if (@$menu_data['menu_id'] <= 0)
                    modApiFunc('Session', 'set', 'ResultMessage',
                               'CMS_MSG_MENU_ADDED');
                else
                    modApiFunc('Session', 'set', 'ResultMessage',
                               'CMS_MSG_MENU_UPDATED');
            }
        }
        elseif ($mode == 'add_link')
        {
            $link_data = $request -> getValueByKey('link_new');
            if ($menu_data['menu_id'])
            {
                $link_data['menu_id'] = $menu_data['menu_id'];

                // getting link depending on the type
                if ($link_data['item_type'] == CMS_MENU_ITEM_TYPE_URL)
                    $link_data['item_link'] = $link_data['link_url'];
                elseif ($link_data['item_type'] == CMS_MENU_ITEM_TYPE_EXTERNAL_URL)
                {
                    $link_data['item_link'] = $link_data['link_external_url'];
                    if(!preg_match('/^(.*)http(s?):\/\/(.*)$/', $link_data['item_link']))
                        $link_data['item_link'] = 'http://'.$link_data['item_link'];
                }
                elseif ($link_data['item_type'] == CMS_MENU_ITEM_TYPE_SYSTEM_PAGE)
                    $link_data['item_link'] = $link_data['link_system_page'];
                elseif ($link_data['item_type'] == CMS_MENU_ITEM_TYPE_STATIC_PAGE)
                    $link_data['item_link'] = $link_data['link_static_page'];
                else
                    $link_data['item_link'] = '';

                $link_data['param1'] = '';
                $link_data['param2'] = '';
                if ($link_data['item_type'] == CMS_MENU_ITEM_TYPE_SYSTEM_PAGE
                    && _ml_strtolower($link_data['item_link']) == 'productlist')
                    $link_data['param1'] = $link_data['list_catid'];

                if ($link_data['item_type'] == CMS_MENU_ITEM_TYPE_SYSTEM_PAGE
                    && _ml_strtolower($link_data['item_link']) == 'productinfo')
                    $link_data['param2'] = $link_data['info_prodid'];

                // getting the sort order number to put the new item to the end
                $link_data['sort_order'] = modApiFunc('CMS',
                                                      'getMenuLastOrderNumber',
                                                      $menu_data['menu_id']);

                $link_data['item_status'] = ((isset($link_data['item_status']))
                                              ? CMS_MENU_ITEM_STATUS_ACTIVE
                                              : CMS_MENU_ITEM_STATUS_INACTIVE);

                execQuery('INSERT_CMS_NEW_MENU_ITEM', $link_data);

                modApiFunc('Session', 'set', 'ResultMessage',
                           'CMS_MSG_MENU_ITEM_ADDED');

                // reload the parent window while reloading
//                modApiFunc('Session', 'set', 'CMS_ReloadParentWindow', 1);
            }

            $menu_id = $menu_data['menu_id'];
        }
        elseif ($mode == 'delete_links')
        {
            $delete = $request -> getValueByKey('delete');

                execQuery('DELETE_CMS_MENU_ITEMS',array('ids' => $delete));

            modApiFunc('Session', 'set', 'ResultMessage','CMS_MSG_MENU_ITEM_DELETED');

            // reload the parent window while reloading
            modApiFunc('Session', 'set', 'CMS_ReloadParentWindow', 1);

            $menu_id = $menu_data['menu_id'];
        }

        elseif($mode == 'delete_menu')
        {
           $delete_menu_id = $request -> getValueByKey('deleteMenu');
           modApiFunc('CMS', 'deleteMenu', $delete_menu_id);
           // setting ResultMessage
            modApiFunc('Session', 'set', 'ResultMessage',
                       'CMS_MSG_MENU_DELETED');
        }
        elseif ($mode == 'sort_links')
        {
            $sort = explode('|',
                $request -> getValueByKey('cms_menu_items_sort_order_hidden'));
            modApiFunc('CMS', 'updateMenuItemsSortOrder', $sort);
            modApiFunc('Session', 'set', 'ResultMessage',
                       'CMS_MSG_MENU_ITEM_SORTED');

            $menu_id = $menu_data['menu_id'];
        }

        // leaving critical sections
        $application -> leaveCriticalSection();

        // prepare the redirect
        $req_to_redirect = new Request();
        $req_to_redirect -> setView(CURRENT_REQUEST_URL);
//        $req_to_redirect -> setKey('page_view', 'CMS_Menu_Data');
        $req_to_redirect -> setKey('menu_id', $menu_id);
        $application -> redirect($req_to_redirect);
    }

    function validatePostedData($menu_data)
    {
        // checking if menu system name is specified
        if (!isset($menu_data['menu_index'])
            || $menu_data['menu_index'] == '')
        {
            modApiFunc('Session', 'set', 'ResultMessage',
                       'CMS_MSG_MENU_SPECIFY_SYSTEM_NAME');
            return 'MN';
        }

        // checking if menu system name is unique
        if (modApiFunc('CMS', 'searchMenu',
                       array('menu_index' => $menu_data['menu_index'],
                             'excl_id' => @$menu_data['menu_id'])))
        {
            modApiFunc('Session', 'set', 'ResultMessage',
                       'CMS_MSG_MENU_SYSTEM_NAME_EXISTS');
            return 'MNNU';
        }

        return '';
    }

    function savePostedData($menu_data)
    {
        global $application;

        if (@$menu_data['menu_id'] <= 0)
        {
            // here we create a new menu
            execQuery('INSERT_CMS_NEW_MENU', $menu_data);
            $mysql = &$application -> getInstance('DB_MySQL');
            $menu_data['menu_id'] = $mysql -> DB_Insert_Id();
        }
        else {
            // updating menu information
            execQuery('UPDATE_CMS_MENU_DATA', $menu_data);
        }

        return $menu_data['menu_id'];
    }
}