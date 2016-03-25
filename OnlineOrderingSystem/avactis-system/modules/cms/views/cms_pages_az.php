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
class CMS_Pages
{

    /**
     * Contructor
     */
    function CMS_Pages()
    {
        modApiFunc('paginator', 'setCurrentPaginatorName', 'CMS_Pages_AZ');

        // filling search filter
        $this -> setSearchFilter();

        // initializing the template filler
        $this -> mTmplFiller = new TmplFiller();
    }

    function setSearchFilter()
    {
        global $application;
        $request = &$application -> getInstance('Request');

        // getting data from request
        $this -> _filter = $request -> getValueByKey('filter');
        $this -> _filter['action'] = $request -> getValueByKey('action');

        if (!isset($this -> _filter['action'])
            || $this -> _filter['action'] != 'search')
        {
            // restoring the filter if no action is provided
            // use case: changing the page or rows per page (paginator links)
            if (modApiFunc('Session', 'is_set', 'CMS_PAGES_FILTER'))
            {
                // getting from the session
                $this -> _filter = modApiFunc('Session', 'get',
                                              'CMS_PAGES_FILTER');
            }
            else
            {
                // setting default filter
                $this -> _filter = array(
                    'mode'      => 'search',
                    'parent_id' => ''
                );
            }
        }

        // validating the filter
        if (isset($this -> _filter['parent_id'])
            && $this -> _filter['parent_id'] < 0)
            unset($this -> _filter['parent_id']);

        if (isset($this -> _filter['name'])
            && !$this -> _filter['name'])
            unset($this -> _filter['name']);

        if (isset($this -> _filter['status'])
            && !$this -> _filter['status'])
            unset($this -> _filter['status']);

        if (isset($this -> _filter['availability'])
            && !$this -> _filter['availability'])
            unset($this -> _filter['availability']);

        // saving the filter
        modApiFunc('Session', 'set', 'CMS_PAGES_FILTER', $this -> _filter);

        // filling up the paginator data
        $this -> _filter['paginator'] = null;
        $this -> _filter['paginator'] = modApiFunc('CMS', 'searchPgPages',
                                                   $this -> _filter,
                                                   PAGINATOR_ENABLE);
    }

    /**
     * The main function to output the given view.
     */
    function output()
    {
        global $application;

        // saving request url (to restore it in action classes)
        modApiFunc('Session', 'set', 'CMS_PAGES_AZ_URL',
                   modApiFunc('Request', 'selfURL'));

        // getting the list of pages
        $this -> _found_pages = modApiFunc('CMS', 'searchPages',
                                           $this -> _filter);

        $this -> _page_tree = modApiFunc('CMS', 'getPageTree');

        $template_contents = array(
            'ResultMessage' => $this -> outputResultMessage(),
            'PageFilter'    => $this -> outputFilter(),
            'PageCount'     => count($this -> _found_pages),
            'FoundPages'    => $this -> outputPages(),
            'SortItems'     => $this -> outputSortItems()
        );
        $this -> _Template_Contents = $template_contents;
        $application -> registerAttributes($this -> _Template_Contents);
        return $this -> mTmplFiller -> fill('cms/cms_pages/',
                                            'container.tpl.html', array());
    }

    /**
     * Outputs Filter form
     */
    function outputFilter()
    {
        global $application;
        $template_contents = array(
            'FilterName'      => @$this -> _filter['name'],
            'AnyStatus'       => ((isset($this -> _filter['status']))
                                 ? '' : 'Y'),
            'ActiveStatus'    => ((@$this -> _filter['status'] == 'A')
                                 ? 'Y' : ''),
            'HiddenStatus'    => ((@$this -> _filter['status'] == 'H')
                                 ? 'Y' : ''),
            'DisabledStatus'  => ((@$this -> _filter['status'] == 'D')
                                 ? 'Y' : ''),
            'AnyAvail'        => ((isset($this -> _filter['availability']))
                                 ? '' : 'Y'),
            'AllAvail'        => ((@$this -> _filter['availability'] == 'C')
                                 ? 'Y' : ''),
            'RegAvail'        => ((@$this -> _filter['availability'] == 'R')
                                 ? 'Y' : ''),
            'AnonymAvail'     => ((@$this -> _filter['availability'] == 'A')
                                 ? 'Y' : ''),
            'ParentSelectBox' => $this -> outputParentSelectBox(
                                              -1,
                                              @$this -> _filter['parent_id']
                                          )
        );
        $this -> _Template_Contents = $template_contents;
        $application -> registerAttributes($this -> _Template_Contents);
        return $this -> mTmplFiller -> fill('cms/cms_pages/',
                                            'filter.tpl.html', array());
    }

   /**
    * Outputs found pages
    */
   function outputPages()
   {
        global $application;

        $result = '';

        foreach($this -> _found_pages as $page)
        {
            $template_contents = array(
                'PageID'     => $page['page_id'],
                'PageIndex'  => $page['page_index'],
                'PageName'   => $page['name'],
                'PageParent' => $this -> outputParentSelectBox(
                                    $page['page_id'],
                                    $page['parent_id']
                                ),
                'PageStatus' => $page['status'],
                'PageAvail'  => $page['availability']
            );

            $this -> _Template_Contents = $template_contents;
            $application -> registerAttributes($this->_Template_Contents);
            $result .= $this -> mTmplFiller -> fill('cms/cms_pages/',
                                                    'page.tpl.html', array());
        }

        if(count($this -> _found_pages) < 5)
        {
            for($i = count($this -> _found_pages); $i < 5; $i++)
            {
                $result .= $this -> mTmplFiller -> fill('cms/cms_pages/',
                                                        'nopage.tpl.html',
                                                        array());
            }
        }

        return $result;
   }

    /**
     * Outputs the parent page select box
     */
    function outputParentSelectBox($id = -1, $selected = -1)
    {
        $result = '';
        if ($id == -1)
            $result = '<option value="">' . getMsg('CMS', 'CMS_ANY') .
                      '</option>' . "\n";
        $result .= '<option value="0"' . (($selected == '0')
                             ? ' selected="selected"' : '') . '>' .
                   getMsg('CMS', 'CMS_ROOT') . '</option>' . "\n";

        $subtree = false;
        $level = '';
        foreach($this -> _page_tree as $v)
        {
            if ($v['page_id'] == $id)
            {
                $subtree = true;
                $level = $v['level'];
            }
            if (_ml_strlen($v['level']) <= _ml_strlen($level)
                && $v['page_id'] != $id)
            {
                $level = '';
                $subtree = false;
            }
            if ($v['page_id'] != $id && !$subtree)
                $result .= '<option value="' . $v['page_id'] . '"' .
                           (($v['page_id'] == $selected)
                             ? ' selected="selected"' : '') . '>' .
                           str_replace(' ', '&nbsp;', $v['level']) .
                           $v['name'] . '</option>' . "\n";
        }

        return $result;
    }

    /**
     * Fills the sort selert box
     */
    function outputSortItems()
    {
        $output = '';
        if (is_array($this -> _found_pages))
            foreach($this -> _found_pages as $v)
                $output .= '<option value="' . $v['page_id'] . '">' .
                           prepareHTMLDisplay($v['name']) . '</option>';

        return $output;
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
        return $obj -> output('CMS_Pages_AZ', 'CMS_Pages');
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
        return $obj -> output('CMS_Pages_AZ', 'CMS_Pages',
                              'PGNTR_CMS_PAGE_ITEMS');
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
            return $this -> mTmplFiller -> fill('cms/cms_pages/',
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
    var $_found_pages;
    var $_page_tree;
}
?>