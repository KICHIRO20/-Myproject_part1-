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
 * Definition of CMS_Pages viewer
 * The viewer is used to manage pages in admin zone
 */
class CMS_Menu
{

    /**
     * Contructor
     */
    function CMS_Menu()
    {
        modApiFunc('paginator', 'setCurrentPaginatorName', 'CMS_Menu_AZ');

        // setting up the filter
        $this -> setSearchFilter();

        // initializing the template filler
        $this -> mTmplFiller = new TmplFiller();
    }

    function setSearchFilter()
    {
        $this -> _filter = array();

        // filling up the paginator data
        $this -> _filter['paginator'] = null;
        $this -> _filter['paginator'] = modApiFunc('CMS', 'searchPgMenu',
                                                   $this -> _filter,
                                                   PAGINATOR_ENABLE);
    }

    /**
     * The main function to output the given view.
     */
    function output()
    {
        global $application;

        // getting the list of menu
        $this -> _found_menu = modApiFunc('CMS', 'searchMenu',
                                          $this -> _filter);

        $template_contents = array(
            'ResultMessage' => $this -> outputResultMessage(),
            'MenuCount'     => count($this -> _found_menu),
            'FoundMenu'     => $this -> outputMenu()
        );
        $this -> _Template_Contents = $template_contents;
        $application -> registerAttributes($this -> _Template_Contents);
        return $this -> mTmplFiller -> fill('cms/cms_menu/',
                                            'container.tpl.html', array());
    }

   /**
    * Outputs found menu
    */
   function outputMenu()
   {
        global $application;

        $result = '';

        foreach($this -> _found_menu as $menu)
        {
            $template_contents = array(
                'MenuID'               => $menu['menu_id'],
                'MenuName'             => $menu['menu_name'],
                'MenuIndex'            => $menu['menu_index'],
                'MenuTemplate'         => $menu['template'],
                'MenuActiveLinks'      => $menu['active_links'],
                'MenuInactiveLinks'    => $menu['inactive_links']
            );

            $this -> _Template_Contents = $template_contents;
            $application -> registerAttributes($this->_Template_Contents);
            $result .= $this -> mTmplFiller -> fill('cms/cms_menu/',
                                                    'menu.tpl.html', array());
        }

        if(count($this -> _found_menu) < 4)
        {
            for($i = count($this -> _found_menu); $i < 4; $i++)
            {
                $result .= $this -> mTmplFiller -> fill('cms/cms_menu/',
                                                        'nomenu.tpl.html',
                                                        array());
            }
        }

        return $result;
   }

    /**
     * Outputs the Paginator line
     * Note: it is required not to register the tag in the viewer
     *       for proper output
     * See: see the getTag function as well
     */
    function outputPaginatorLine()
    {
        global $application;

        $obj = &$application -> getInstance('PaginatorLine');
        return $obj -> output('CMS_Menu_AZ', 'CMS_Menu');
    }

    /**
     * Outputs the Paginator rows
     * Note: it is required not to register the tag in the viewer
     *       for proper output
     * See: see the getTag function as well
     */
    function outputPaginatorRows()
    {
        global $application;

        $obj = &$application -> getInstance('PaginatorRows');
        return $obj -> output('CMS_Menu_AZ', 'CMS_Menu',
                              'PGNTR_CMS_MENU_ITEMS');
    }

    /**
     * Outputs the result message
     * the message should be registered in the session (var: ResultMessage)
     * Use case: useful to return the result of an action
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
            $this -> _Template_Contents = $template_contents;
            $application -> registerAttributes($this -> _Template_Contents);
            return $this -> mTmplFiller -> fill('cms/cms_menu/',
                                                'result-message.tpl.html',
                                                array());
        }
        else
        {
            return '';
        }
    }

    /**
     * Returns the tag value
     * Note: since the PaginatiorLine and PaginatorRows tags cannot be
     *       registered inside the viewer the way the function processes
     *       the tags is different
     */
    function getTag($tag)
    {
        if ($tag == 'PaginatorLine')
            return $this -> outputPaginatorLine();

        if ($tag == 'PaginatorRows')
            return $this -> outputPaginatorRows();

        return getKeyIgnoreCase($tag, $this -> _Template_Contents);
    }

    var $mTmplFiller;
    var $_Template_Contents;
    var $_filter;
    var $_found_menu;
}
?>