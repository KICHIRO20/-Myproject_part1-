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
 * Definition of CMS_Page_Data viewer
 * The viewer is used to show/edit a given page
 */
class CMS_Page_Data
{
    /**
     * Constructor
     */
    function CMS_Page_Data()
    {
        $this -> mTmplFiller = new TmplFiller();
    }

    /**
     * The main function to output the viewer
     */
    function output()
    {
        global $application;

        // getting page id
        // if not specified then a new page is being added
        $page_id = modApiFunc('Request', 'getValueByKey', 'page_id');
        if (!$page_id)
            $page_id = 0;

        $this -> _Page_Data = modApiFunc('CMS', 'searchPages',
                                         array('page_id' => $page_id));

        // getting page data
        if (!empty($this -> _Page_Data))
        {
            // the page_id is specified and valid
            $this -> _Page_Data = array_pop($this -> _Page_Data);
        }
        else
        {
            // the page_id is eihter not specified or not valid
            // assuming adding a new page
            $this -> _Page_Data = array('page_id' => 0,  'availability' => 'C',
                                        'status' => 'A', 'parent_id' => 0);
        }

        // restoring data from session if any
        // use case: restoring submitted form with an error
        if (modApiFunc('Session', 'is_set', 'SavedPageData'))
        {
            $this -> _Page_Data = modApiFunc('Session', 'get',
                                             'SavedPageData');
            modApiFunc('Session', 'un_set', 'SavedPageData');
        }

        $template_contents = array(
            'PageJSCode'          => $this -> outputJSCode(),
            'PageID'              => $this -> _Page_Data['page_id'],
            'PageIndex'           => prepareHTMLDisplay(
                                         @$this -> _Page_Data['page_index']
                                     ),
            'PageName'            => prepareHTMLDisplay(
                                         @$this -> _Page_Data['name']
                                     ),
            'ParentSelectBox'     => $this -> outputParentSelectBox(),
            'PageContent'         => prepareHTMLDisplay(
                                         @$this -> _Page_Data['descr']
                                     ),
            'PageStatus'          => @$this -> _Page_Data['status'],
            'PageSEOPrefix'        => prepareHTMLDisplay(
                                        @$this -> _Page_Data['seo_prefix']
           			     ),
	    'PageSEOTitle'        => prepareHTMLDisplay(
                                         @$this -> _Page_Data['seo_title']
                                     ),
            'PageMETADescription' => prepareHTMLDisplay(
                                         @$this -> _Page_Data['seo_descr']
                                     ),
            'PageMETAKeywords'    => prepareHTMLDisplay(
                                         @$this -> _Page_Data['seo_keywords']
                                     ),
            'PageAvailability'    => @$this -> _Page_Data['availability'],
            'ResultMessage'       => $this -> outputResultMessage(),
            'EditPageTitle'       => ((@$this -> _Page_Data['page_id'] > 0)
                                     ? getMsg('CMS', 'CMS_EDIT_PAGE')
                                     : getMsg('CMS', 'CMS_ADD_PAGE')),
        );
        $this -> _Template_Contents = $template_contents;
        $application -> registerAttributes($this -> _Template_Contents);
        return $this -> mTmplFiller -> fill(
                   'cms/cms_page_data/',
                   'container.tpl.html',
                   array()
               );
    }

    /**
     * Outputs the parent page select box
     */
    function outputParentSelectBox()
    {
        $result = '<option value="0"' .
                  (($this -> _Page_Data['parent_id'] == 0)
                   ? ' selected="selected"' : '') . '>' .
                  getMsg('CMS', 'CMS_ROOT') . '</option>' . "\n";
        $page_tree = modApiFunc('CMS', 'getPageTree');

        $subtree = false;
        $level = '';
        foreach($page_tree as $v)
        {
            if ($v['page_id'] == $this -> _Page_Data['page_id'])
            {
                $subtree = true;
                $level = $v['level'];
            }
            if (_ml_strlen($v['level']) <= _ml_strlen($level)
                && $v['page_id'] != $this -> _Page_Data['page_id'])
            {
                $level = '';
                $subtree = false;
            }
            if ($v['page_id'] != $this -> _Page_Data['page_id'] && !$subtree)
                $result .= '<option value="' . $v['page_id'] . '"' .
                           (($this -> _Page_Data['parent_id'] == $v['page_id'])
                            ? ' selected="selected"' : '') . '>' .
                           str_replace(' ', '&nbsp;', $v['level']) .
                           $v['name'] . '</option>' . "\n";
        }

        return $result;
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
                       'cms/cms_page_data/',
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
                                'cms/cms_page_data/',
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
    var $_Page_Data;
    var $mTmplFiller;
}

?>