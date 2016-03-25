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
 * Catalog Sort Category view.
 *
 * @author Alexander Girin
 * @package Catalog
 * @access  public
 */
class SortCategories
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
    function SortCategories()
    {
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
     * Returns the HTML code of the sorted objects type.
     *
     * @return HTML code
     */
    function outputSortObject($type)
    {
        global $application;
        $obj = &$application->getInstance('MessageResources');
        switch ($type)
        {
            case "CS": $res = 'SORT_CTGR_OBJ_CAP_SNGL'; break;
            case "CP": $res = 'SORT_CTGR_OBJ_CAP_PLRL'; break;
            case "S": $res = 'SORT_CTGR_OBJ_SNGL'; break;
            case "P": $res = 'SORT_CTGR_OBJ_PLRL'; break;
        }
        return $obj->getMessage(new ActionMessage($res));
    }

    /**
     * Returns the HTML code of the sorted objects type.
     *
     * @return HTML code
     */
    function outputSortSubject()
    {
        // do not modify it! (submit renaming to af!)
        // its value refers to Page Help
        return "subcategories";
    }

    /**
     * Returns the HTML code of the sorted categories list.
     *
     * @return HTML code
     */
    function outputOptionsList($CatID, &$OptionsListHiddenArray)
    {
        # Get CCategoryInfo Object List
        $Cat_List = modApiFunc('Catalog', 'getDirectSubcategoriesListFull', $CatID);

        $OptionsList = '';
        foreach ($Cat_List as $catInfo)
        {
            $_id = $catInfo->getCategoryTagValue('id');
            $_name = $catInfo->getCategoryTagValue('name');
            $OptionsList.= '<option value='.$_id.'>'.$_name.'</option>';
            array_push($OptionsListHiddenArray, $_id);
        }
        return $OptionsList;
    }

    /**
     * Returns the ordered array of the sorted categoty ids.
     *
     * @return HTML code
     */
    function getOptionsListHiddenArray($CatID)
    {
        # Get CCategoryInfo Object List
        $Cat_List = modApiFunc('Catalog', 'getDirectSubcategoriesListFull', $CatID);

        $OptionsListHiddenArray = array();
        foreach ($Cat_List as $catInfo)
        {
            $_id = $catInfo->getCategoryTagValue('id');
            array_push($OptionsListHiddenArray, $_id);
        }
        return $OptionsListHiddenArray;
    }

   /**
    * Returns the reference.
    *
    * @return
    */

    function outputSaveSortHref()
    {
        $request = new Request();
        $request->setView  ( 'SortCategories' );
        $request->setAction( 'SaveSortedCategories' );
        return $request->getURL();
    }


    /**
     * Returns the Catalog Sort Category view.
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
        $application->registerAttributes(array(
            'HiddenArrayViewState'
           ,'HiddenFieldAction'
           ,'OptionsList'
           ,'OptionsListHidden'
           ,'SaveCatHref'
           ,'Sort_Object_CP'
           ,'Sort_Object_CS'
           ,'Sort_Object_P'
           ,'Sort_Object_S'
           ,'Sort_Subject'
        ));

        $retval = modApiFunc('TmplFiller', 'fill', "catalog/sort_cat/","list.tpl.html", array());
        return $retval;
    }

    function getHiddenFiled()
    {
        return $HtmlForm->genHiddenField('asc_action', 'SaveSortedCategories');
    }

    /**
     * @ describe the function ProductList->getTag.
     */
    function getTag($tag)
    {
        global $application;
        $value = null;
        $CatID = modApiFunc('CProductListFilter','getCurrentCategoryId');
        loadClass('CCategoryInfo');
        $currCategoryObj = new CCategoryInfo($CatID);
        $OptionsListHiddenArray = array();
        loadCoreFile('html_form.php');
        $HtmlForm = new HtmlForm();
        switch ($tag)
        {
            case 'HiddenArrayViewState':
                $value = $this->outputViewState();
                break;
            case 'HiddenFieldAction':
                $value = $this->getHiddenFiled();
                break;
            case 'OptionsList':
                $value = $this->outputOptionsList($CatID, $OptionsListHiddenArray);
                break;
            case 'CategoryName':
                $value = $currCategoryObj->getCategoryTagValue('name');
                break;
            case 'OptionsListHidden':
                $OptionsListHiddenArray = $this->getOptionsListHiddenArray($CatID);
                $value = implode('|', $OptionsListHiddenArray);
                break;
            case 'SaveCatHref':
                $value = $this->outputSaveSortHref();
                break;
            case 'Sort_Object_CP':
                $value = $this->outputSortObject('CP');
                break;
            case 'Sort_Object_CS':
                $value = $this->outputSortObject('CS');
                break;
            case 'Sort_Object_P':
                $value = $this->outputSortObject('P');
                break;
            case 'Sort_Object_S':
                $value = $this->outputSortObject('S');
                break;
            case 'Sort_Subject':
                $value = $this->outputSortSubject();
                break;
            case 'Breadcrumb':
                $obj = &$application->getInstance('Breadcrumb');
                $value = $obj->output(false);
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

    /**#@-*/

}
?>