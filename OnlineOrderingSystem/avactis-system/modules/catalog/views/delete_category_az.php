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
_use(dirname(__FILE__).'/breadcrumb-az.php');

/**
 * Catalog module.
 * Catalog Category Delete view.
 *
 * @author Alexander Girin
 * @package Catalog
 * @access  public
 */
class DeleteCategory extends Breadcrumb
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
    function DeleteCategory()
    {
        global $application;
        $this->mTmplFiller = &$application->getInstance('TmplFiller');

        if(modApiFunc("Session", "is_Set", "SessionPost"))
        {
            // eliminate copying on construction
            $SessionPost = modApiFunc("Session", "get", "SessionPost");
            $this->ViewState = $SessionPost["ViewState"];

            //Remove some data, that should not be sent to action, one more time from ViewState.
            if($this->ViewState["hasError"] == "true")
            {
                $this->ErrorsArray = $this->ViewState["ErrorsArray"];
                unset($this->ViewState["ErrorsArray"]);
            }

            modApiFunc('Session', 'un_Set', 'SessionPost');
        }
        else
        {
            $this->ViewState =
                array(
                    "hasError"          => "false",
                    "hasCloseScript"    => "false"
                     );
            $this->POST = array();
        }
    }

    /**
     * Returns the HTML code of the hidden fields of the array ViewState.
     *
     * @return HTML code
     */
    function outputViewState()
    {
        $retval = "";
        foreach ($this->ViewState as $key => $value)
        {
            $retval .= "<input type=\"hidden\" name=\"ViewState[" .$key . "]\" value=\"" . $value . "\">";
        }
        return $retval;
    }

    /**
     * Gets the deleted category Id.
     *
     * @return integer category id
     */
    function getCategoryID()
    {
        return modApiFunc('Catalog', 'getEditableCategoryID');
    }

    /**
     * Returns the HTML code of the deleted object type.
     *
     * @return HTML code
     */
    function outputDeleteObject()
    {
        global $application;
        $obj = &$application->getInstance('MessageResources');
        return $obj->getMessage(new ActionMessage('DEL_CTGR_HEADER'));
    }

    function outputDeleteSubject()
    {
        // do not modified it! (submit renaming to af!)
        // it value refer to Page Help
        // do not modify it! (submit renaming to af!)
        // its value refers to Page Help
        return 'category';
    }

    /**
     * Returns the HTML code of the warning message.
     *
     * @return HTML code
     */
    function outputDeleteWarning($category_name)
    {
        global $application;
        $obj = &$application->getInstance('MessageResources');
        return $obj->getMessage(new ActionMessage(array('DEL_CTGR_WARNING', $category_name)));
    }

    /**
     * Returns the HTML code of the deleted object list.
     *
     * @return HTML code
     */
    function outputListItems($CatID)
    {
        global $application;
        $catitems = '';

       /*
         Get a list of all subcategories without paginator.
        */
        $SubcategoriesFullList = modApiFunc('Catalog', 'getSubcategoriesFullListWithParent', $CatID);

       /*
         Look through the list of categories and:
         - save each of them in the array of the $this->CatsId ID category,
         - get a product list in each category, without paginator, i.e. the whole list
         - save each of them in the array of the $this->ProdsId ID of each product
         - generate HTML to output one category
        */
        $i = 0;

        foreach ($SubcategoriesFullList as $catInfo)
        {
           /*
             This array of the categories ID will be outputted in the form of
             the hidden field.
             These very categories will be deleted from the catalog.
            */
            array_push($this->CatsId, $catInfo['id']);

           /*
             Execute a query for optimization.
            */
            $result = modApiFunc('Catalog', 'selectCatgoryProducts', $catInfo['id']);

            foreach ($result as $productid)
            {
               /*
                 This array of the products ID will be outputted in the form of
                 the hidden field.
                 These very products will be deleted from the catalog.
                */
                array_push($this->ProdsId, $productid['id']);
            }

            /*
                                                  HTML
            */
            $categoryInfo = new CCategoryInfo($catInfo['id']);
            $ProductsCountTotal = $categoryInfo->getCategoryTagValue("productsnumberrecursively");
            $ProductsCountInCat = $categoryInfo->getCategoryTagValue("productsnumber");
            $full_subcat_path_arr = modApiFunc('Catalog', 'getCategoryFullPath', $catInfo['id']);

            $categoryInfo->setAdditionalCategoryTag('N', ($i+1));
            $categoryInfo->setAdditionalCategoryTag('Category_Full_Name', $this->outputLocationBreadcrumb($full_subcat_path_arr, false));
            $categoryInfo->setAdditionalCategoryTag('Products_Count_In_Cat', $ProductsCountInCat);
            $categoryInfo->setAdditionalCategoryTag('Products_Count_Total', $ProductsCountTotal);

            $this->_Current_Category = $categoryInfo;
            $application->registerAttributes($this->_Current_Category->getAdditionalCategoryTagList());
            $catitems.= modApiFunc('TmplFiller', 'fill', "catalog/del_cat/","list_item.tpl.html",array());
            $i++;
        }

        return $catitems;
    }

    /**
     * Returns the Form Action reference.
     *
     * @return Form Action
     */
    function outputDeleteHref()
    {
        $request = new Request();
        $request->setView  ( 'DeleteCategory' );
        $request->setAction( 'ConfirmDeleteCategory' );
        return $request->getURL();
    }

    /**
     * Returns the name of the action class.
     *
     * @return Form Action
     */
    function outputAction()
    {
        return 'ConfirmDeleteCategory';
    }

    /**
     * Returns the Catalog Category Info view.
     *
     * @ finish the functions on this page
     */
    function output()
    {
        if($this->ViewState["hasCloseScript"] == "true")
        {
            modApiFunc("application", "closeChild_UpdateParent");
            return;
        }

        global $application;
        $application->registerAttributes(
            array(
                'HiddenArrayViewState'  => ''
               ,'asc_action'                => ''
               ,'HiddenFieldAction'     => ''
               ,'HiddenFieldCatsId'     => ''
               ,'HiddenFieldProdsId'    => ''
               ,'Category_Path'         => ''
               ,'Delete_Warning'        => ''
               ,'DeleteCatHref'         => ''
               ,'Delete_Object'         => ''
               ,'Delete_Subject'         => ''
               ,'ProductDelMessage'     => ''
            )
        );

        $retval = modApiFunc('TmplFiller', 'fill', "catalog/del_cat/","list.tpl.html", array());
        return $retval;
    }

    /**
     * @ describe the function ProductList->getTag.
     */
    function getTag($tag)
    {
        global $application;
        $value = "";
        $CatID = $this->getCategoryID();
        $full_path_arr = modApiFunc('Catalog', 'getCategoryFullPath', $CatID);
        loadCoreFile('html_form.php');
        $HtmlForm = new HtmlForm();
        switch ($tag)
        {
            case 'HiddenArrayViewState':
                $value = $this->outputViewState();
                break;
            case 'asc_action':
                $value = $this->outputAction();
                break;
            case 'Items':
                if ($CatID != NULL)
                {
                    $value = $this->outputListItems($CatID);
                }
                break;
            case 'HiddenFieldAction':
                $value = $HtmlForm->genHiddenField('asc_action', $this->outputAction());
                break;
            case 'HiddenFieldCatsId':
                $value = $HtmlForm->genHiddenField('CatsId', implode("|", $this->CatsId));
                break;
            case 'HiddenFieldProdsId':
                $value = $HtmlForm->genHiddenField('ProdsId',  implode("|", $this->ProdsId));
                break;
            case 'Category_Path':
                $value = $this->outputLocationBreadcrumb($full_path_arr, false);
                break;
            case 'Delete_Warning':
                $value = $this->outputDeleteWarning(prepareHTMLDisplay($full_path_arr[sizeof($full_path_arr)-1]['name']));
                break;
            case 'DeleteCatHref':
                $value = $this->outputDeleteHref();
                break;
            case 'Delete_Object':
                $value = $this->outputDeleteObject();
                break;
            case 'Delete_Subject':
                $value = $this->outputDeleteSubject();
                break;
            case 'Category_Full_Name':
                $value = $this->_Current_Category->getCategoryTagValue('Category_Full_Name');
                break;

            case 'ProductDelMessage':
                $cats_ids = $this->_Current_Product->getCategoriesIDs();
                if(count($cats_ids) == 1 || modApiFunc('Session', 'is_set', 'SearchProductFormFilter'))
                {
                    $value = getMsg('CTL','MSG_REAL_PRODUCT_DEL');
                }
                else
                {
                    unset($cats_ids[array_search($CatID,$cats_ids)]);
                    $strings = array();
                    foreach($cats_ids as $cat_id)
                    {
                        $full_path = modApiFunc('Catalog','getCategoryFullPath',$cat_id);
                        $names = array();
                        foreach($full_path as $pci)
                            $names[] = $pci['name'];
                        $strings[] = implode("/",$names);
                    };
                    $value = getMsg('CTL','MSG_LINK_PRODUCT_DEL',implode("<br>",$strings));
                }
                break;

            default:
                if ((_ml_strpos($tag, 'Category') === 0))
                {
                    $stag = _ml_substr($tag, _ml_strlen('Category'));
                    if (is_object($this->_Current_Category) && $this->_Current_Category->isTagExists($tag) )
                    {
                        $value = $this->_Current_Category->getCategoryTagValue($tag);
                        break;
                    }
                }
                if ((_ml_strpos($tag, 'Product') === 0))
                {
                    $stag = _ml_substr($tag, _ml_strlen('Product'));
                    if (is_object($this->_Current_Product) && $this->_Current_Product->isTagExists($stag) )
                    {
                        $value = $this->_Current_Product->getProductTagValue($stag);
                        break;
                    }
                }

                if (is_object($this->_Current_Category) && $this->_Current_Category->isTagExists($tag) )
                {
                    $value = $this->_Current_Category->getCategoryTagValue($tag);
                }

                if (is_object($this->_Current_Product) && $this->_Current_Product->isTagExists($tag) )
                {
                    $value = $this->_Current_Product->getProductTagValue($tag);
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

    var $mTmplFiller;

    var $CatsId = array();

    var $ProdsId = array();

    var $_Current_Category = array();
    var $_Current_Product = array();
    /**#@-*/

}
?>