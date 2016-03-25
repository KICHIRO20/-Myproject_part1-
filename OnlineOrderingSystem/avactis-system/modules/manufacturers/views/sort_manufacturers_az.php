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
 * @author Vadim Lyalikov
 * @package Manufacturers
 * @access  public
 */
class SortManufacturers
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
    function SortManufacturers()
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
     * @return HTML code
     */
    function outputOptionsList(&$OptionsListHiddenArray)
    {
        $List = modApiFunc('Manufacturers', 'getManufacturersList');

        $OptionsList = '';
        foreach ($List as $Info)
        {
            $_id = $Info['manufacturer_id'];
            $_name = $Info['manufacturer_name'];
            $OptionsList.= '<option value='.$_id.'>'.$_name.'</option>';
            array_push($OptionsListHiddenArray, $_id);
        }
        return $OptionsList;
    }

    /**
     *
     * @return HTML code
     */
    function getOptionsListHiddenArray()
    {
        $List = modApiFunc('Manufacturers', 'getManufacturersList');

        $OptionsListHiddenArray = array();
        foreach ($List as $Info)
        {
            $_id = $Info['manufacturer_id'];
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
        $request->setView  ( 'SortManufacturers' );
        $request->setAction( 'SaveSortedManufacturers' );
        return $request->getURL();
    }


    /**
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
           ,'SaveSortHref'
        ));

        $retval = modApiFunc('TmplFiller', 'fill', "manufacturers/sort_manufacturers/","list.tpl.html", array());
        return $retval;
    }

    /**
     * @ describe the function ProductList->getTag.
     */
    function getTag($tag)
    {
        global $application;
        $value = null;
        $OptionsListHiddenArray = array();
        loadCoreFile('html_form.php');
        $HtmlForm = new HtmlForm();
        switch ($tag)
        {
            case 'HiddenArrayViewState':
                $value = $this->outputViewState();
                break;
            case 'HiddenFieldAction':
                $value = $HtmlForm->genHiddenField('asc_action', 'SaveSortedManufacturers');
                break;
            case 'OptionsList':
                $value = $this->outputOptionsList($OptionsListHiddenArray);
                break;
            case 'OptionsListHidden':
                $OptionsListHiddenArray = $this->getOptionsListHiddenArray();
                $value = implode('|', $OptionsListHiddenArray);
                break;
            case 'SaveSortHref':
                $value = $this->outputSaveSortHref();
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