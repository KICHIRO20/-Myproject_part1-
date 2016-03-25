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

class CMSPageTree
{
    function CMSPageTree()
    {
        global $application;

        $this -> mTmplFiller = new TemplateFiller();

        // assuming we are displaying the tree up to third level
        // if more levels need to be displayed
        // extend the template set here, in cms-page-tree-block.ini
        // and in the getTemplateFormat function
        $this -> _templates = array(
            'container' => 'CMSPageTreeContainer',
            'empty'     => 'CMSPageTreeEmpty',
            'items'     => 'CMSPageTreeItems',
        );

        $this -> NoView = false;
        if ($application -> issetBlockTagFatalErrors('CMSPageTree'))
            $this -> NoView = true;
    }

    function getTemplateFormat()
    {
        $format = array(
            'layout-file' => 'cms-page-tree-block.ini',
            'files'       => array(
                'CMSPageTreeContainer' => TEMPLATE_FILE_SIMPLE,
                'CMSPageTreeEmpty'     => TEMPLATE_FILE_SIMPLE,
                'CMSPageTreeItems'     => TEMPLATE_FILE_SIMPLE
            ),
            'options'     => array(
            )
        );
        return $format;
    }

    /**
     * The output of the Viewer
     * Params: the following optional params can be accepted
     *      0: page_id, the following values are accepted:
     *         -1 - the page id should be taken from the URL
     *         0 - the root level tree will be shown
     *         positive number: the tree for the specified page will be shown
     *         other values: the root level tree will be shown
     * Note: to use other templates please create an alias for the viewer
     */
    function output()
    {
        global $application;

        if ($this -> NoView)
            return '';

        $page_id = -1;

        // getting the page_id from the params if any
        if (func_num_args() > 0)
            $page_id = func_get_arg(0);

        // if it is -1 getting it from the reguest
        if ($page_id == -1)
            $page_id = modApiFunc('Request', 'getValueByKey', 'page_id');

        // checking the page id
        $page_data = modApiFunc('CMS', 'getPageInfo', $page_id);
        if ($page_data)
            $page_id = $page_data['page_id'];
        else
            $page_data = 0;

        if (modApiFunc('Customer_Account', 'getCurrentSignedCustomer'))
            $this -> _signed = 'Y';
        else
            $this -> _signed = 'N';

        // setting the template engine
        $template_block = $application -> getBlockTemplate('CMSPageTree');
        $this -> mTmplFiller -> setTemplate($template_block);

        // registering tags
        $_tags = array(
            'Local_Tree' => $this -> outputTree($page_id, 0),
        );
        $this -> _Template_Contents = $_tags;
        $application -> registerAttributes($this -> _Template_Contents);

        return $this -> mTmplFiller -> fill($this -> _templates['container']);
    }

    /**
     * Outputs the tree
     */
    function outputTree($page_id, $level)
    {
        global $application;

        $output = '';

        $tree = modApiFunc('CMS', 'getPageTree', $page_id, 'CZ', 1,
                           $this -> _signed);

        if (!is_array($tree) || empty($tree))
            return $this -> mTmplFiller -> fill($this -> _templates['empty']);

        foreach($tree as $k => $v)
        {
            // registering tags
            $_tags = array(
                'Local_PageFirst' => ($k == 0),
                'Local_PageLast'  => ($k == count($tree) - 1),
                'Local_PageName'  => $v['name'],
                'Local_PageID'    => $v['page_id'],
                'Local_Level'     => $level,
                'Local_SubTree'   => $this -> outputTree($v['page_id'],
                                                         $level + 1)
            );
            $this -> _Template_Contents = $_tags;
            $application -> registerAttributes($this -> _Template_Contents);

            $output .= $this -> mTmplFiller -> fill(
                           $this -> _templates['items']
                       );
        }

        return $output;
    }

    /**
     * Processes local tags
     */
    function getTag($tag)
    {
        return getKeyIgnoreCase($tag, $this->_Template_Contents);
    }

    var $NoView;
    var $mTmplFiller;
    var $_Template_Contents;
    var $_signed;
};

?>