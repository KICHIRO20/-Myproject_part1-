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

define('MAX_IMAGE_HEIGHT', 200);
define('MAX_IMAGE_WIDTH', 200);

/**
 * Catalog module.
 * Catalog Category info view.
 *
 * @author Alexander Girin
 * @package Catalog
 * @access  public
 */
class ViewCategory
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * The view constructor.
     *
     * @ finish the functions on this page
     */
    function ViewCategory()
    {
    }


    /**
     * Returns the Catalog Category Info view.
     *
     * @ finish the functions on this page
     */
    function output()
    {
        global $application;
        $obj = &$application->getInstance('MessageResources');

        $CatID = modApiFunc('Catalog', 'getEditableCategoryID');

        if ($CatID == NULL)
        {

        }
        else
        {
            $catInfo = new CCategoryInfo($CatID);

            $request = new Request();
            $request->setView  ( 'EditCategory' );
            $request->setAction( 'SetEditableCategory' );
            $request->setKey   ( 'category_id', $CatID);
            $catInfo->setAdditionalCategoryTag('EditCatHref', $request->getURL());

            $cat_status = "";
            switch($catInfo->getCategoryTagValue("Status"))
            {
                case CATEGORY_STATUS_ONLINE:
                    $cat_status = getMsg('SYS','CAT_STATUS_ONLINE');
                    break;
                case CATEGORY_STATUS_OFFLINE:
                    $cat_status = getMsg('SYS','CAT_STATUS_OFFLINE');
                    break;
                default:
                    //: report error
                    $cat_status = "";
            }

            $this->_Current_Category = $catInfo;

            $this->_Current_Category->setAdditionalCategoryTag('ShowProductsRecursivelyStatus', $catInfo->getCategoryTagValue('showproductsrecursivelystatus', CATEGORYINFO_LOCALIZED_DATA));
            $this->_Current_Category->setAdditionalCategoryTag('CategoryStatus', $catInfo->getCategoryTagValue('status', CATEGORYINFO_LOCALIZED_DATA));
            $this->_Current_Category->setAdditionalCategoryTag('AttributeImageWidth', $catInfo->getCategoryTagValue('LargeImageWidth' ));
            $this->_Current_Category->setAdditionalCategoryTag('AttributeImageHeight', $catInfo->getCategoryTagValue('LargeImageHeight'));
            $this->_Current_Category->setAdditionalCategoryTag('AttributeImageURL', $catInfo->getCategoryTagValue('LargeImageSrc'));
            $this->_Current_Category->setAdditionalCategoryTag('largeimage', $obj->getMessage(new ActionMessage('INFO_CTGR_NO_IMG')));
            $application->registerAttributes($this->_Current_Category->getAdditionalCategoryTagList());
            $application->registerAttributes(array('SmallImage'));

            if($this->_Current_Category->getCategoryTagValue('largeimagesrc') != '')
            {
                $this->_Current_Category->setAdditionalCategoryTag('largeimage',modApiFunc('TmplFiller', 'fill', "catalog/view_cat/", "attr-large-image.tpl.html", array()));
            }

            $retval = modApiFunc('TmplFiller', 'fill', "catalog/view_cat/", "list.tpl.html", array());
        }
        return $retval;
    }

    /**
     * @ describe the function ViewCategory->getTag.
     */
    function getTag($tag)
    {
        global $application;
        $value = null;
        $CatID = $this->_Current_Category->getCategoryTagValue('ID');
        switch ($tag)
        {
    	    case 'Breadcrumb':
                $obj = &$application->getInstance('Breadcrumb');
                $value = $obj->output(false);
    	        break;

            default:
                if (is_object($this->_Current_Category) && $this->_Current_Category->isTagExists($tag))
                {
                    $value = $this->_Current_Category->getCategoryTagValue($tag);
                }
                else
                {
                    if (_ml_strpos($tag, 'Category') === 0)
                    {
                        $tag = _ml_substr($tag, _ml_strlen('Category'));
                        if (is_object($this->_Current_Category) && $this->_Current_Category->isTagExists($tag))
                        {
                            $value = $this->_Current_Category->getCategoryTagValue($tag);
                        }
                    }
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

    var $_attr;
    var $_Current_Category;
    /**#@-*/

}
?>