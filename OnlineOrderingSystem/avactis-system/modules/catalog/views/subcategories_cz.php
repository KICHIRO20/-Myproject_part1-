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
 *
 * @package Catalog
 * @access  public
 * @author  Alexander Girin
 */
class Subcategories
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
    	    'layout-file'        => 'subcategory-list-config.ini'
    	   ,'files' => array(
    	        'Container'      => TEMPLATE_FILE_SIMPLE
    	       ,'ContainerEmpty' => TEMPLATE_FILE_SIMPLE
    	       ,'Item'           => TEMPLATE_FILE_SIMPLE
    	    )
    	   ,'options' => array(
    	        'Columns'        => TEMPLATE_OPTION_REQUIRED
    	    )
    	);
    	return $format;
    }

    /**
     * The view constructor.
     *
     * @ finish the functions on this page
     */
    function Subcategories()
    {
        global $application;

        #check if fatal errors exist in the block tag
        $this->NoView = false;
        if ($application->issetBlockTagFatalErrors("SubcategoryList"))
        {
            $this->NoView = true;
        }
    }

    /**
     * Generates the main page containing the subcategory list.
     */
    function getSubcategoriesList()
    {
        global $application;


        $disable_trtd = $application->getAppIni('PRODUCT_LIST_DISABLE_TR_TD');
        if ($disable_trtd != null && strtolower($disable_trtd) === 'yes')
            $disable_trtd = true;
        else
            $disable_trtd = false;

        $items = "";
        $col = 1;
        $columns = intval($application->getBlockOption($this->template, "Columns"));
        $this->_Subcategory_Info = null;
        foreach ($this->subcatList as $subcatInfo)
        {
            if ($col == 1)
            {
                if ($disable_trtd == false) $items .= '<tr><td>';
                $col++;
            }
            else
            {
                if ($disable_trtd == false) $items .= '<td>';
                $col++;
            }
            if ($col > $columns)
            {
                $col = 1;
            }
            $this->_Subcategory_Info = &$subcatInfo;
            $items .= $this->templateFiller->fill("Item");
        }
        if (empty($this->subcatList))
        {
            if ($disable_trtd == false) $items = '<tr><td></td></tr>';
        }
        $this->_Subcategory_Info = null;
        return $items;
    }

/*    function getViewCacheKey()
    {
        return modApiFunc('CProductListFilter','getCurrentCategoryId');
    }//*/

    /**
     * Returns the Subcategories Listing view.
     *
     * @return string the Subcategories List view.
     */
    function output()
    {
        global $application;

        #define whether to output the view or not
        if ($this->NoView)
        {
            $application->outputTagErrors(true, "SubcategoryList", "Errors");
            return "";
        }
        else
        {
            $application->outputTagErrors(true, "SubcategoryList", "Warnings");
        }

        $this->templateFiller = new TemplateFiller();

       /*
         Check whether the parameter specified by the category id was passed to
         the Subcategories() block. In such case the Subcategories block will
         output specified category info, inpsite of the current category.
        */
        $CatID = @func_get_arg(0);
        $CurrentCatalogCategoryID = null;

        if ($CatID === false)
        {
            $CatID = modApiFunc('CProductListFilter','getCurrentCategoryId');
        }
        else
        {
           /*
             The category id was passed to the Subcategories block.
             In order all the Category* tags output values for the specified
             category, but not for the current one, to the template-container,
             override the current category value in the Catalog module.
             Return the current value after outputting the Subcategories block.
            */
            $CurrentCatalogCategoryID = modApiFunc('CProductListFilter','getCurrentCategoryId');
            modApiFunc('CProductListFilter','disableSynchronization');
            modApiFunc('CProductListFilter','changeCurrentCategoryId',$CatID);
        }

        $this->_CatID = $CatID;
        $this->template = $application->getBlockTemplate('Subcategories');
        $this->templateFiller->setTemplate($this->template);

        $this->subcatList = modApiFunc("Catalog", "getDirectSubcategoriesListFull", $this->_CatID, true);

        $cat = new CCategoryInfo($CatID);

        if (NULL == $this->subcatList || $cat->getCategoryTagValue('RecursiveStatus') != CATEGORY_STATUS_ONLINE)
        {
            $retval = $this->templateFiller->fill("ContainerEmpty");
        }
        else
        {
            $retval = $this->templateFiller->fill("Container");
        }

       /*
         Restore the current category value in the Catalog module,
         if it has been overridden by the time of outputting the Subcategories block.
        */
        if ($CurrentCatalogCategoryID !== null)
        {
            modApiFunc('CProductListFilter','changeCurrentCategoryId',$CurrentCatalogCategoryID);
            modApiFunc('CProductListFilter','enableSynchronization');
        }

        return $retval;
    }

    /**
     * Processes tags in the templates for the given view.
     *
     * @return string tag value, if tag has been processed. NULL, otherwise.
     */
    function getTag($tag, $arg_list = array())
    {
        global $application;
        $value = null;
        $Category_Descr = &$application->getInstance('CCategoryInfo', $this->_CatID);
        switch ($tag)
        {
        	case 'Local_Items':
        		$value = $this->getSubcategoriesList();
        		break;

    	    default:
    	        list($entity, $tag) = getTagName($tag);
        	    if ($entity == 'category' && is_object($this->_Subcategory_Info))
        	    {
        	        $value = $this->_Subcategory_Info->getCategoryTagValue($tag);
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

    var $_Subcategory_Info;

    /**
     * Pointer to the template filler object.
     * It needs to track sequences of identical templates, like lists.
     */
    var $mTmplFiller;
    /**#@-*/

}
?>