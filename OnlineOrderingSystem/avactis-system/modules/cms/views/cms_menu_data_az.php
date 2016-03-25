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

/**
 * Definition of CMS_Menu_Data viewer
 * The viewer is used to show/edit a given menu
 */
class CMS_Menu_Data
{
    /**
     * Constructor
     */
    function CMS_Menu_Data()
    {
        $this -> mTmplFiller = new TmplFiller();
    }

    /**
     * The main function to output the viewer
     */
    function output()
    {
        global $application;

        // getting menu id
        // if not specified then a new menu is being added
        $menu_id = modApiFunc('Request', 'getValueByKey', 'menu_id');
        if (!$menu_id)
            $page_id = 0;

        $this -> _Menu_Data = modApiFunc('CMS', 'searchMenu',
                                         array('menu_id' => $menu_id));

        // getting menu data
        if (!empty($this -> _Menu_Data))
        {
            // the menu_id is specified and valid
            $this -> _Menu_Data = array_pop($this -> _Menu_Data);
            $this -> _Menu_Data['items'] = modApiFunc('CMS', 'getMenuItems',
                                                      $menu_id, false);
        }
        else
        {
            // the menu_id is eihter not specified or not valid
            // assuming adding a new menu
            $this -> _Menu_Data = array('menu_id' => 0,  'menu_index' => '',
                                        'menu_name' => '', 'template' => '',
                                        'items' => array());
        }

        // restoring data from session if any
        // use case: restoring submitted form with an error
        if (modApiFunc('Session', 'is_set', 'SavedMenuData'))
        {
            $this -> _Menu_Data = modApiFunc('Session', 'get',
                                             'SavedMenuData');
            modApiFunc('Session', 'un_set', 'SavedMenuData');
        }

        // getting current system pages
        $this -> _System_Pages = modApiFunc('CMS', 'getSystemPageList');

        // getting current static pages
        $this -> _Static_Pages = modApiFunc('CMS', 'getPageTree', 0);

        $template_contents = array(
            'ResultMessage'   => $this -> outputResultMessage(),
            'MenuJSCode'      => $this -> outputJSCode(),
            'MenuID'          => $this -> _Menu_Data['menu_id'],
            'MenuIndex'       => prepareHTMLDisplay(
                                     @$this -> _Menu_Data['menu_index']
                                 ),
            'MenuName'        => prepareHTMLDisplay(
                                     @$this -> _Menu_Data['menu_name']
                                 ),
            'MenuTemplate'    => prepareHTMLDisplay(
                                     @$this -> _Menu_Data['template']
                                 ),
            'MenuItems'       => $this -> outputMenuItems(),
            'MenuItemCount'   => count($this -> _Menu_Data['items']),
            'MenuSystemPages' => $this -> outputSystemPages(),
            'MenuStaticPages' => $this -> outputStaticPages(),
            'MenuSystemCount' => count($this -> _System_Pages),
            'MenuStaticCount' => count($this -> _Static_Pages),
            'SortItems'       => $this -> outputSortItems(),
            'EditMenuTitle'   => ((@$this -> _Menu_Data['menu_id'] > 0)
                                  ? getMsg('CMS', 'CMS_EDIT_MENU')
                                  : getMsg('CMS', 'CMS_ADD_MENU')),
        );
        $this -> _Template_Contents = $template_contents;
        $application -> registerAttributes($this -> _Template_Contents);
        return $this -> mTmplFiller -> fill(
                   'cms/cms_menu_data/',
                   'container.tpl.html',
                   array()
               );
    }

    /**
     * Outputs menu items
     */
    function outputMenuItems()
    {
        global $application;

        $result = '';

        if (!is_array($this -> _Menu_Data['items']) ||
            empty($this -> _Menu_Data['items']))
            return $result;

        foreach($this -> _Menu_Data['items'] as $k => $item)
        {
            $template_contents = array(
                'MenuItemParity'      => ($k % 2),
                'MenuItemID'          => $item['menu_item_id'],
                'MenuItemName'        => $item['item_name'],
                'MenuItemType'        => $item['item_type'],
                'MenuItemLink'        => $item['item_link'],
                'MenuItemLinkURL'     => (in_array($item['item_type'], array(CMS_MENU_ITEM_TYPE_URL, CMS_MENU_ITEM_TYPE_EXTERNAL_URL))
                                         ? $item['item_link'] : ''),
                'MenuItemParam1'      => prepareHTMLDisplay($item['param1']),
                'MenuItemParam2'      => prepareHTMLDisplay($item['param2']),
                'MenuItemStatus'      => $item['item_status'],
                'MenuItemSystemPages' => $this -> outputSystemPages(
                                             $item['item_link']
                                         ),
                'MenuItemStaticPages' => $this -> outputStaticPages(
                                             $item['item_link']
                                         ),
                'MenuSystemCount' => count($this -> _System_Pages),
                'MenuStaticCount' => count($this -> _Static_Pages)
            );
            $this -> _Template_Contents = $template_contents;
            $application -> registerAttributes($this -> _Template_Contents);
            $result .= $this -> mTmplFiller -> fill(
                           'cms/cms_menu_data/',
                           'menu_item.tpl.html',
                           array()
                       );
        }

        return $result;
    }

    /**
     * Outputs System Pages select box
     */
    function outputSystemPages($selected = '')
    {
        $result = '';
        foreach($this -> _System_Pages as $page)
        {
            $name = $page;
            if (isset($this -> _mapping[_ml_strtolower($page)]))
            {
                if (!$this -> _mapping[_ml_strtolower($page)])
                    continue;
                else
                    $name = $this -> _mapping[_ml_strtolower($page)];
            }
            $page = prepareHTMLDisplay($page);
            $name = prepareHTMLDisplay($name);
            $result .= '<option value="' . $page . '"' .
                       (($page == $selected) ? ' selected="selected"' : '') .
                       '>' . $name . '</option>';
        }

        return $result;
    }

    /**
     * Outputs System Pages select box
     */
    function outputStaticPages($selected = '')
    {
        $result = '';
        foreach($this -> _Static_Pages as $page)
        {
            $page['name'] = prepareHTMLDisplay($page['name']);
            $result .= '<option value="' . $page['page_id'] . '"' .
                       (($page['page_id'] == $selected)
                           ? ' selected="selected"' : '') .
                       '>' . $page['name'] . '</option>';
        }

        return $result;
    }

    /**
     * Fills the sort selert box
     */
    function outputSortItems()
    {
        $output = '';
        if (is_array($this -> _Menu_Data['items']))
            foreach($this -> _Menu_Data['items'] as $v)
                $output .= '<option value="' . $v['menu_item_id'] . '">' .
                           prepareHTMLDisplay($v['item_name']) . '</option>';

        return $output;
    }

    /**
     * Outputs the result message
     * Note: the message is taken from the session
     * Use case: it contains the result of the previous action
     */
    function outputResultMessage()
    {
        global $application;

        if (modApiFunc('Session', 'is_set', 'ResultMessage'))
        {
            $msg = modApiFunc('Session', 'get', 'ResultMessage');
            modApiFunc('Session', 'un_set', 'ResultMessage');
            $template_contents = array(
                "ResultMessage" => getMsg('CMS', $msg)
            );
            $this -> _Template_Contents=$template_contents;
            $application -> registerAttributes($this -> _Template_Contents);
            return $this -> mTmplFiller -> fill(
                       'cms/cms_menu_data/',
                       'result-message.tpl.html',
                       array()
                   );
        }
        else
        {
            return '';
        }
    }

    /**
     * Outputs the parent window reloading javascript code if needed
     * use case: the session variable is set in the action class
     */
    function outputJSCode()
    {
        if (modApiFunc('Session', 'is_set', 'CMS_ReloadParentWindow'))
        {
            modApiFunc('Session', 'un_set', 'CMS_ReloadParentWindow');
            return $this -> mTmplFiller -> fill(
                                'cms/cms_menu_data/',
                                'reload-parent-js.tpl.html', array()
                            );
        }

        return '';
    }

    /**
     * Processes the tags
     */
    function getTag($tag)
    {
        return getKeyIgnoreCase($tag, $this->_Template_Contents);
    }

    var $_Template_Contents;
    var $mTmplFiller;
    var $_Menu_Data;
    var $_System_Pages;
    var $_Static_Pages;

    var $_mapping = array(
        'productlist'                => 'Category Page',
        'productinfo'                => 'Product Page',
        'searchresult'               => '',
        'cart'                       => 'Cart',
        'checkout'                   => 'Checkout',
        'closed'                     => '',
        'download'                   => '',
        'registration'               => 'Registration',
        'accountactivation'          => '',
        'customerpersonalinfo'       => 'Customer Personal Info',
        'customerordershistory'      => 'Customer Orders History',
        'customerorderinfo'          => '',
        'customerorderinvoice'       => '',
        'customerorderdownloadlinks' => '',
        'customersignin'             => '',
        'customernewpassword'        => '',
        'customerchangepassword'     => 'Customer Change Password',
        'customerforgotpassword'     => 'Customer Forgot Password',
        'customeraccounthome'        => 'Customer Account Home',
        'customerreviews'            => '',
        'cmspage'                    => '',
        'wishlist'                   => 'Wishlist'
    );
}

?>