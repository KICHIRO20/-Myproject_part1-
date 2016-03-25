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
 * Catalog module.
 * Catalog Categories Breadcrumb.
 *
 * @package Catalog
 * @access  public
 * @author Alexander Girin
 */
class Breadcrumb
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     *                                                     .
     */
    function getTemplateFormat()
    {
    	$format = array(
    	    'layout-file'        => 'breadcrumb-config.ini'
    	   ,'files' => array(
    	        'Container'      => TEMPLATE_FILE_SIMPLE
    	       ,'Empty'          => TEMPLATE_FILE_SIMPLE
    	       ,'Item'           => TEMPLATE_FILE_SIMPLE
    	       ,'ItemLink'       => TEMPLATE_FILE_SIMPLE
    	       ,'Separator'      => TEMPLATE_FILE_SIMPLE
    	    )
    	   ,'options' => array(
    	    )
    	);
    	return $format;
    }

    /**
     * The view constructor.
     */
    function Breadcrumb()
    {
        global $application;

        #
        $this->NoView = false;
        if ($application->issetBlockTagFatalErrors("Breadcrumb"))
        {
            $this->NoView = true;
        }
    }

    /**
     *
     */
    function getLinkToCatalogProdList($cid)
    {
        global $application;
        /*
        $_request = new Request();
        $_request->setView  ( 'ProductList' );
        $_request->setAction( "SetCurrCat" );
        $_request->setKey   ( "category_id", $cid );
        $_request->setCategoryID($cid);
        return $_request->getURL();
        */
        $catObj = &$application->getInstance('CCategoryInfo',$cid);
        return $catObj->getCategoryTagValue('link');
    }

    /**
     *
     */
    function outputCategoriesBreadCrumb()
    {
        $full_path_arr = modApiFunc('Catalog', 'getCategoryFullPath', $this->cat_id);
        $this->_Current_Category = null;
        if (sizeof($full_path_arr)>0)
        {
            $breadcrumb = '';
            if (sizeof($full_path_arr)==1)
            {
                $breadcrumb.= $this->templateFiller->fill("ItemLink");
            }
            else
            {
                $this->_Current_Category = array_shift($full_path_arr);
                $breadcrumb.= $this->templateFiller->fill("ItemLink");
                $breadcrumb.= $this->templateFiller->fill("Separator");
                $current_cat = array_pop($full_path_arr);
                if (sizeof($full_path_arr)>0)
                {
                    foreach ($full_path_arr as $catInfo)
                    {
                        $this->_Current_Category = $catInfo;
                        $breadcrumb.= $this->templateFiller->fill("ItemLink");
                        $breadcrumb.= $this->templateFiller->fill("Separator");
                    }
                }
                $this->_Current_Category = $current_cat;
                global $application;
                $request = &$application->getInstance('Request');
                if($request->getValueByKey('asc_ajax_req')===null) // not AJAX Request
                {
                    $tag_stack = $application->tag_stack;
                    if((!empty($tag_stack) && is_array($tag_stack)
                                          && $tag_stack[count($tag_stack)-2]=='ProductList')
                        || $request->getCurrentAction() == 'SetCurrCat')
                    {
                        $breadcrumb.= $this->templateFiller->fill("Item");
                    }
                    else
                    {
                        $breadcrumb.= $this->templateFiller->fill("ItemLink");
                    }
                }
                else    // AJAX Request
                {
                    if($request->getValueByKey('options_sent')===null) // request from product-list.php
                    {
                        $breadcrumb.= $this->templateFiller->fill("Item");
                    }
                    else    // request from product-info.php
                    {
                        $breadcrumb.= $this->templateFiller->fill("ItemLink");
                    }
                }
            }
            return $breadcrumb;
        }
        else
        {
            return '';
        }
        $this->_Current_Category = null;
    }

    /**
     * Return the Catalog Navigator view.
     *
     * @ finish the functions on this page
     */
    function output()
    {
        global $application;

        #
        if ($this->NoView)
        {
            $application->outputTagErrors(true, "Breadcrumb", "Errors");
            return "";
        }
        else
        {
            $application->outputTagErrors(true, "Breadcrumb", "Warnings");
        }
        $this->templateFiller = &$application->getInstance('TemplateFiller');
        $this->template = $application->getBlockTemplate('Breadcrumb');
        $this->templateFiller->setTemplate($this->template);

        $this->cat_id = func_num_args() > 0 ? @func_get_arg(0) : false;
        if ($this->cat_id === false)
        {
            $this->cat_id = modApiFunc('CProductListFilter','getCurrentCategoryId');
        }
        if (NULL == $this->cat_id)
        {
            $retval = $this->templateFiller->fill("Empty");
        }
        else
        {
            $retval = $this->templateFiller->fill("Container");
        }
        return $retval;
    }

    /**
     *                                                               .
     * @return string              ,                   .       - NULL.
     */
    function getTag($tag)
    {
        $value = null;
    	switch ($tag)
    	{
    	    case 'Local_Items':
    	        $value = $this->outputCategoriesBreadCrumb();
    	        break;
    		case 'CategoryName':
    			$value = $this->getCategoryName();
    			break;
    		case 'CategoryLink':
    		    if ($this->_Current_Category != null)
    		    {
        			$value = $this->getLinkToCatalogProdList($this->_Current_Category['id']);
    		    }
    			break;

    		default:
    			break;
    	}
    	return $value;
    }

    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    function getCategoryName()
    {
        if ($this->_Current_Category == null) {
            return null;
        }
        $value = $this->_Current_Category['name'];
        if (_ml_strlen($value) > 27) {
            $value = preg_replace('/^(\S+)\s.*\s(\S+)$/', '\\1 ... \\2', $value);
            if (_ml_strlen($value) > 27) {
                $value = _ml_substr($value, 0, 12).'...';
            }
        }
        return $value;
    }

    var $_Current_Category;

    /**
     *                  TemplateFiller.
     * @var TemplateFiller
     */
    var $templateFiller;

    /**
     *                         .
     * @var array
     */
    var $template;

    var $cat_id;

    /**#@-*/

}
?>