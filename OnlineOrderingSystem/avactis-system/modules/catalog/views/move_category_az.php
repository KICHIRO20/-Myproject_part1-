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
 * Catalog Move Category view.
 *
 * @package Catalog
 * @access  public
 * @author  Girin Alexander
 */
class MoveCategory extends Breadcrumb
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
    function MoveCategory()
    {
        global $application;

        $this->mTmplFiller = &$application->getInstance('TmplFiller');

        if(modApiFunc("Session", "is_Set", "SessionPost"))
        {
            // eliminate copying on construction
            $SessionPost = modApiFunc("Session", "get", "SessionPost");
            $this->ViewState = $SessionPost["ViewState"];

            //Remove some data, that should not be recent to action, from ViewState.
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

    function getLinkToView($cid)
    {
        global $application;

        $_request = new Request();
        $_request->setView  ( 'MoveCategory' );
        $_request->setAction( "SetMoveToCat" );
        $_request->setKey   ( "category_id", $cid );

        return $_request->getURL();
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
     *
     * @ finish the functions on this page
     */
    function outputSubcategoriesList($cid)
    {
        global $application;

        # Get CCategoryInfo object list
        $SubcategoriesList = modApiFunc('Catalog', 'getDirectSubcategoriesListFull', $cid);

        $retval = "";
        foreach ($SubcategoriesList as $key => $value)
        {
            $this->_Current_Category = &$value;

            $this->_Current_Category->setAdditionalCategoryTag('Name', $value->getCategoryTagValue('name'));
            $this->_Current_Category->setAdditionalCategoryTag('CatLink', $this->getLinkToView($value->getCategoryTagValue('id')));
            $application->registerAttributes($this->_Current_Category->getAdditionalCategoryTagList());

            if ($value->getCategoryTagValue('id') != modApiFunc("Catalog", "getEditableCategoryID"))
            {
                $retval .= $this->mTmplFiller->fill("catalog/move_cat/", "list_item.tpl.html", array());
            }
            else
            {
                $retval .= $this->mTmplFiller->fill("catalog/move_cat/", "list_item_inactive.tpl.html", array());
            }
        }
        if($retval == "")
        {
            $retval .= $this->mTmplFiller->fill("catalog/move_cat/", "list_item_empty.tpl.html", array());
        }

        return $retval;
    }

    function outputMoveHref()
    {
        $request = new Request();
        $request->setView  ( 'MoveCategory' );
        $request->setAction( 'MoveToCategory' );
        return $request->getURL();
    }

    function outputMoveObject()
    {
        global $application;
        $obj = &$application->getInstance('MessageResources');
        $cat = new CCategoryInfo(modApiFunc('Catalog', 'getEditableCategoryID'));
        return $obj->getMessage(new ActionMessage('MOVE_CTGR_HEADER')).' '. $cat->getCategoryTagValue('Name');
    }

    function outputMoveSubject()
    {
        // do not modify it! (submit renaming to af!)
        // its value refers to Page Help
        return 'category';
    }

    function outputAction()
    {
        return 'MoveToCategory';
    }

    function outputNewLocation($moveto_category_full_path)
    {
        return $this->outputLocationBreadcrumb($moveto_category_full_path, true, "MoveCategory");
    }

    function outputButton()
    {
        global $application;
        $obj = &$application->getInstance('MessageResources');
        return $obj->getMessage('BTN_MOVE_SUBCTGR');
    }

    /**
     * Returns the Catalog Navigator view.
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
                'HiddenArrayViewState'
               ,'asc_action'
               ,'New_Path'
               ,'New_Path_Breadcrumb'
               ,'Move_Href'
               ,'Move_Object'
               ,'Move_Subject'
               ,'Button'
            )
        );

        $retval = $this->mTmplFiller->fill("catalog/move_cat/", "list.tpl.html", array());
        return $retval;
    }

    /**
     * @ describe the function ProductList->getTag.
     */
    function getTag($tag)
    {
        global $application;
        $value = null;
        $CatID = modApiFunc('CProductListFilter','getCurrentCategoryId');
        $moveto_category_full_path = modApiFunc("Catalog", "getCategoryFullPath", modApiFunc("Catalog", "getMoveToCategoryID"));
        switch ($tag)
        {
            case 'HiddenArrayViewState':
                $value = $this->outputViewState();
                break;
            case 'asc_action':
                $value = $this->outputAction();
                break;
            case 'Items':
                $value = $this->outputSubcategoriesList(modApiFunc("Catalog", "getMoveToCategoryID"));
                break;
            case 'New_Path':
                $value = $this->outputLocationBreadcrumb($moveto_category_full_path, true);
                break;
            case 'New_Path_Breadcrumb':
                $value = $this->outputNewLocation($moveto_category_full_path);
                break;
            case 'Move_Href':
                $value = $this->outputMoveHref();
                break;
            case 'Move_Object':
                $value = $this->outputMoveObject();
                break;
            case 'Move_Subject':
                $value = $this->outputMoveSubject();
                break;
    	    case 'Breadcrumb':
                $obj = &$application->getInstance('Breadcrumb');
                $value = $obj->output(false);
    	        break;
            case 'Button':
                $value = $this->outputButton();
                break;
            default:
                if (_ml_strpos($tag, 'Category') === 0)
                {
                    $tag = _ml_substr($tag, _ml_strlen('Category'));
                }
                if ( $this->_Current_Category->isTagExists($tag)) {
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

    var $_Current_Category = array();
    /**#@-*/

}
?>