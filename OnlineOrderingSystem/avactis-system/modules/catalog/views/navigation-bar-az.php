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
 * @author  Vadim Lyalikov
 */
class NavigationBar
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
    function NavigationBar()
    {
        global $application;

        $this->pCatalog = &$application->getInstance('Catalog');

        $this->mTmplFiller = &$application->getInstance('TmplFiller');

        loadClass('CategoriesBrowserDynamic');
    }

    function getLinkToCatalogNavigator($cid)
    {
        $_request = new Request();
        $_request->setView  ( 'NavigationBar' );
        $_request->setAction( "SetCurrCat" );
        $_request->setKey   ( "category_id", $cid);

        global $application;
        return $_request->getURL();
        //@ check, whether last parameter was ever used
        //$application->href($_request, -1, -1, 'AdminZone');
    }

    function getLinkToCatalogAddCategory()
    {
        $_request = new Request();
        $_request->setView  ( 'AddCategory' );

        global $application;
        return $_request->getURL();
    }

    /**
     * Returns the Catalog Navigator list view.
     *
     * @ finish the functions on this page
     */
    function outputDirectSubcategoriesList($cid)
    {
        global $application;
        $obj = &$application->getInstance('MessageResources');

        # Get CCategoryInfo object list
        $SubcategoriesList = modApiFunc('Catalog', 'getDirectSubcategoriesListFull', $cid);

        $this->SubcatNumInCat = sizeof($SubcategoriesList);
        $retval = "";
        $count = 0;
        foreach ($SubcategoriesList as $key => $value)
        {
            $checked = "";
            $this->_Current_Category = &$value;
            $this->_Current_Category_NOT_LOCALIZED_Status_id = $value->getCategoryTagValue('Status', CATEGORYINFO_NOT_LOCALIZED_DATA);

            $cat_recursive_status_id = $value->getCategoryTagValue('RecursiveStatus', CATEGORYINFO_NOT_LOCALIZED_DATA);

            $this->_Current_Category->setAdditionalCategoryTag('Checked', $checked);
            $this->_Current_Category->setAdditionalCategoryTag('CategoryOfflineAndOnlineSubcategoriesNumber', $value->getCategoryTagValue('CategoryOfflineAndOnlineSubcategoriesNumber'));

            $application->registerAttributes($this->_Current_Category->getAdditionalCategoryTagList());
            $application->registerAttributes(array("CategoryStatus", "CategoryRecursiveStatus", "CategoryOfflineStatusReason", "CategoryOfflineStatusColor"));

            if($cat_recursive_status_id == CATEGORY_STATUS_ONLINE)//Online
            {
                $retval .= $this->mTmplFiller->fill("catalog/catalog_navigator/", "list_item.tpl.html", array());
            }
            else if($cat_recursive_status_id == CATEGORY_STATUS_OFFLINE)//Offline
            {
                $retval .= $this->mTmplFiller->fill("catalog/catalog_navigator/", "list_item_offline.tpl.html", array());
            }

            $count++;
        }

        $min_list_size = 10;
        if($count== 0)
        {
            $retval .= $this->mTmplFiller->fill("catalog/catalog_navigator/", "list_item_empty_na_values.tpl.html", array());
            $count++;
        }

        for(;$count < $min_list_size; $count++)
        {
            $retval .= $this->mTmplFiller->fill("catalog/catalog_navigator/", $count == $min_list_size -1 ? "list_bottom_item_empty.tpl.html" : "list_item_empty.tpl.html", array());
        }

        modApiFunc('Catalog', 'unsetEditableCategoryID');
        modApiFunc('Catalog', 'unsetMoveToCategoryID');
        return $retval;
    }

    function outputEditCatHomeButton()
    {
        global $application;
        $application->registerAttributes(array(
                    "EditHomeCatHref" => ""
                   ,"EditCurrCatHref" => ""
                   ));
        if(modApiFunc('CProductListFilter','getCurrentCategoryId') == 1)
        {
            $cname = 'home';
        }
        else
        {
            $cname = 'current';
        };
        $retval = $this->mTmplFiller->fill("catalog/catalog_navigator/", "edit_".$cname."_cat_button.tpl.html", array());
        return $retval;
    }

    /**
     * Returns the Catalog Navigator view.
     *
     * @ finish the functions on this page
     */
    function output()
    {
        global $application;

        $application->registerAttributes(array(
             'Category_CurrentPath'
            ,'AddCatHref'
            ,'DelCatHref'
            ,'MoveCatHref'
            ,'ViewCatHref'
            ,'EditCatHref'
            ,'SortCatHref'
            ,'SortAlertMessage'
            ,'AlertMessage'
            ,'EditHomeCatButton'
            ,'CategoriesTreeBox'
            ,'CategoriesTreeScript'
        ));
        $this->cb_obj = new CategoriesBrowserDynamic(CB_MODE_MANAGE);
        $retval = $this->mTmplFiller->fill("catalog/catalog_navigator/", "list.tpl.html", array());
        return $retval;
    }

    /**
     * @ describe the function NavigationBar->getTag.
     */
    function getTag($tag)
    {
        global $application;
        $value = null;
        switch ($tag)
        {
            case 'AddCatHref':
                $request = new Request();
                $request->setView  ( 'AddCategory' );
                $request->setAction( 'SetEditableCategory' );
                $request->setKey   ( 'category_id', '');
                $value = $request->getURL();
        	    break;

        	case 'EditCatHref':
                $request = new Request();
                $request->setView  ( 'EditCategory' );
                $request->setAction( 'SetEditableCategory' );
                $request->setKey   ( 'category_id', '');
                $value = $request->getURL();
        	    break;

    	    case 'Breadcrumb':
                $obj = &$application->getInstance('Breadcrumb');
                $value = $obj->output(true);
    	        break;

            case 'CategoryOfflineStatusReason':
                $value = $this->_Current_Category_NOT_LOCALIZED_Status_id == CATEGORY_STATUS_ONLINE ? getMsg('SYS',"CATEGORY_PARENT_STATUS_ONLINE") : "";
                break;

            case 'CategoryOfflineStatusColor':
                //                     Offline.
                //                                    Online,                 ,      Offline -        .
                $value = $this->_Current_Category_NOT_LOCALIZED_Status_id == CATEGORY_STATUS_ONLINE ? "rgb(175, 175, 175)" : "#FF0000";
                break;

            case 'CategoriesTreeBox':
                $value = $this->cb_obj->outputBox();
                break;

            case 'CategoriesTreeScript':
                $value = $this->cb_obj->outputScript();
                break;

        	default:
        	    if (_ml_strpos($tag, 'Category') === 0)
        	    {
                    $tag = _ml_substr($tag, _ml_strlen('Category'));
        	    }
                    if(!empty($this->_Current_Category))
                    {
                        if ($this->_Current_Category->isTagExists($tag)) {
                            $value = $this->_Current_Category->getCategoryTagValue($tag, CATEGORYINFO_LOCALIZED_DATA);
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

    /**
     * Pointer to the module object.
     */
    var $pCatalog;

    /**
     * Pointer to the template filler object.
     * It needs to track sequences of identical templates, like lists.
     */
    var $mTmplFiller;

    var $_Current_Category = array();

    /**
     * The number of subcategories in the category.
     */
    var $SubcatNumInCat;
    /**#@-*/

}
?>