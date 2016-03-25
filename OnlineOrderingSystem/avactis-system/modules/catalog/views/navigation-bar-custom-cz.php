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
 * Catalog Navigator.
 *
 * @package Catalog
 * @access  public
 * @author  Alexey Florinsky
 */
class NavigationBarCustom
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Gets a template format for the given view.
     */
    function getTemplateFormat()
    {
    	$format = array(
    	    'layout-file'        => 'navigation-bar-custom-config.ini'
    	   ,'files' => array(
    	        'Container'               => TEMPLATE_FILE_SIMPLE
    	       ,'Empty'                   => TEMPLATE_FILE_SIMPLE
    	       ,'CategoryDefault'         => TEMPLATE_FILE_SIMPLE
    	       ,'CategoryDefaultSelected' => TEMPLATE_FILE_SIMPLE
    	       ,'CategoryDefaultWithoutNested' => TEMPLATE_FILE_SIMPLE
    	       ,'CategoryDefaultSelectedWithoutNested' => TEMPLATE_FILE_SIMPLE
              )
           ,'options' => array(
    	    )
    	);
    	return $format;
    }

    /**
     * The view constructor.
     *
     * @ finish the functions on this page
     */
    function NavigationBarCustom()
    {
        global $application;

        #check if fatal errors exist in the block tag
        $this->NoView = false;
        if ($application->issetBlockTagFatalErrors("NavigationBarCustom"))
        {
            $this->NoView = true;
        }

        $this->pCatalog = &$application->getInstance('Catalog');

        $this->settings = array(
                                // 'ALL', 'CURRENT_PATH'
                                'EXPAND_TREE_NODES' => 'ALL', //$application->getAppIni('NB_EXPAND_TREE_NODES'),

                                // Category ID
                                'TREE_ROOT' => 1, //$application->getAppIni('NB_TREE_ROOT_CID'),

                                // CATEGORY_ONLY, CATEGORY_PATH
                                'SELECTED' => 'CATEGORY_ONLY',

                                // YES, NO
                                'DISPLAY_ROOT' => 'NO',
                               );

    }

    /**
     * Returns the Catalog Navigator Tree view.
     */
    function outputCatalogNavigatorTree($start_with_category_id = null)
    {
        global $application;
        static $level_offset = null;

        $tree = "";
        $categories_list = null;

        if ($start_with_category_id == null)
        {
            $start_with_category_id = $this->settings['TREE_ROOT'];
            // array of online direct subcategories only
            if ($this->settings['DISPLAY_ROOT'] == 'YES')
            {
                $categories_list = array(new CCategoryInfo($start_with_category_id));
            }
        }

        // array of online direct subcategories only
        if ($categories_list === null)
        {
            $categories_list = modApiFunc("Catalog", "getDirectSubcategoriesListFull", $start_with_category_id, true);
        }

        // necessary tests before output
        $start_category_obj = new CCategoryInfo($start_with_category_id);
        if ($categories_list == null ||
            $start_category_obj->getCategoryTagValue('recursivestatus', CATEGORYINFO_NOT_LOCALIZED_DATA) == CATEGORY_STATUS_OFFLINE)
        {
            return "";
        }

        // prepare plain-list of all parent IDs of the __current__ category
        $parents = modApiFunc('Catalog', 'getCategoryFullPath', $this->cat_id);
        $parent_ids = array();
        foreach ($parents as $catInfo)
        {
            $parent_ids[] = $catInfo['id'];
        }

        foreach ($categories_list as $categoryInfo)
        {
            $_selected = in_array($categoryInfo->getCategoryTagValue('id'), $parent_ids);
            $_selected_current = $this->cat_id == $categoryInfo->getCategoryTagValue('id');
            $_without_nested = ! $categoryInfo->getCategoryTagValue('hasonlinesubcategories');

            // select template
            if ($_selected && $_without_nested)
            {
                $template = ($_selected_current || $this->settings['SELECTED']=='CATEGORY_PATH') ? 'CategoryDefaultSelectedWithoutNested' : 'CategoryDefaultWithoutNested';
            }
            else if ($_selected && !$_without_nested)
            {
                $template = ($_selected_current || $this->settings['SELECTED']=='CATEGORY_PATH') ? 'CategoryDefaultSelected' : 'CategoryDefault';
            }
            else if (!$_selected && $_without_nested)
            {
                $template = 'CategoryDefaultWithoutNested';
            }
            else // !$_selected && !$_without_nested
            {
                if ($this->settings['EXPAND_TREE_NODES'] == 'ALL')
                {
                    $template = 'CategoryDefault';
                }
                else // $this->settings['EXPAND_TREE_NODES'] == 'CURRENT_PATH'
                {
                    $template = 'CategoryDefaultWithoutNested';
                }
            }

            //
            //                    $this->_Current_Category,
            //                                              .
            //                                                                            .
            $_prev__this_Current_Category = $this->_Current_Category;
            $this->_Current_Category = $categoryInfo;
            $tree .= $this->mTmplFiller->fill($template);
            $this->_Current_Category = $_prev__this_Current_Category;
        }
        return $tree;
    }


    /**
     * Returns the generated ProductList view.
     *
     * @return string
     */
    function output()
    {
        global $application;

        #define whether to output the view or not
        if ($this->NoView)
        {
            $application->outputTagErrors(true, "NavigationBarCustom", "Errors");
            return;
        }
        else
        {
            $application->outputTagErrors(true, "NavigationBarCustom", "Warnings");
        }

        $this->cat_id = modApiFunc('CProductListFilter','getCurrentCategoryId');

        $args = func_get_args();
        $this->settings['TREE_ROOT'] = (isset($args[0]) && $args[0]!==null) ? $args[0] : 1;
        $this->settings['DISPLAY_ROOT'] = (isset($args[1]) && $args[1]===true) ? 'YES' : 'NO';
        $this->settings['SELECTED'] = (isset($args[2]) && $args[2]==='SELECT_CATEGORY_ONLY') ? 'CATEGORY_ONLY' : 'CATEGORY_PATH';
        $this->settings['EXPAND_TREE_NODES'] = (isset($args[3]) && $args[3]==='EXPAND_TREE_NODES') ? 'ALL' : 'CATEGORY_PATH';


        $this->mTmplFiller = new TemplateFiller();
        $this->template = $application->getBlockTemplate('NavigationBarCustom');
        $this->mTmplFiller->setTemplate($this->template);

        $application->registerAttributes(array('Local_NestedCategories','Local_RootCategoryID'));

        if ($this->cat_id == 1)
        {
            $this->categoryInfo = new CCategoryInfo($this->cat_id);
            if (! $this->categoryInfo->getCategoryTagValue("hasonlinesubcategories"))
            {
                $retval = $this->mTmplFiller->fill("Empty");
            }
            else
            {
                $retval = $this->mTmplFiller->fill("Container");
            }
        }
        else
        {
            $retval = $this->mTmplFiller->fill("Container");
        }
        return $retval;
    }

    /**
     * Processes tags in the templates for the given view.
     *
     * @return string tag value, if tag has been processed. NULL, otherwise.
     */
    function getTag($tag)
    {
        $value = null;
    	switch ($tag)
    	{
    	    case 'Local_Items':
                $value = $this->outputCatalogNavigatorTree();
    	        break;

            case 'Local_NestedCategories':
                $value = $this->outputCatalogNavigatorTree($this->_Current_Category->getCategoryTagValue('id'));
                break;

            case 'Local_RootCategoryID':
                $value = $this->settings['TREE_ROOT'];
                break;

    		default:
    		    list($entity, $tag) = getTagName($tag);
        	    if ($entity == 'category' && is_object($this->_Current_Category))
        	    {
                    $value = $this->_Current_Category->getCategoryTagValue($tag);
        	    }
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

    /**
     * Pointer to the module object.
     */
    var $pCatalog;

    /**
     * Pointer to the template filler object.
     * It needs to track sequences of identical templates, like lists.
     */
    var $mTmplFiller;

    var $cat_id;

    /**
     * Current category info, selected during the category tree traverse.
     *
     * @var array
     */
    var $_Current_Category;

    var $_Subcategories;

    /**
     * The template selected for the view.
     *
     * @var array
     */
    var $template;

    var $settings;

    /**#@-*/
}
?>