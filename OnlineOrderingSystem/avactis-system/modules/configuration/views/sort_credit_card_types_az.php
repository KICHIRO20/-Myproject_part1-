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
 * Configuration module.
 * Credit Card Types Sort view.
 *
 * @author Alexander Girin
 * @package Configuration
 * @access  public
 */
class SortCreditCardTypes
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
    function SortCreditCardTypes()
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
     * Returns the HTML code of the sorted credit card types list.
     *
     * @return HTML code
     */
    function outputOptionsList()
    {
        $cc_types = modApiFunc("Configuration", "getCreditCardSettings", false);

        $OptionsList = '';
        foreach ($cc_types as $type)
        {
            $_id = $type['id'];
            $_name = $type['name'];
            $OptionsList.= '<option value='.$_id.'>'.$_name.'</option>';
        }
        return $OptionsList;
    }

    /**
     * Returns the ordered array of the sorted credit card types ids.
     *
     * @return HTML code
     */
    function getOptionsListHiddenArray()
    {
        $cc_types = modApiFunc("Configuration", "getCreditCardSettings", false);

        $OptionsListHiddenArray = array();
        foreach ($cc_types as $type)
        {
            array_push($OptionsListHiddenArray, $type['id']);
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
        $request->setView  ( 'SortCreditCardTypes' );
        $request->setAction( 'UpdateCreditCardSettings' );
        $request->setKey( "FormSubmitValue", "SaveSortOrder" );
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
           ,'SaveSortHref'
        ));

        $retval = modApiFunc('TmplFiller', 'fill', "configuration/sort_credit_card_types/","list.tpl.html", array());
        return $retval;
    }

    /**
     * @ describe the function ProductList->getTag.
     */
    function getTag($tag)
    {
        global $application;
        $value = null;
        loadCoreFile('html_form.php');
        $HtmlForm = new HtmlForm();
        switch ($tag)
        {
            case 'HiddenArrayViewState':
                $value = $this->outputViewState();
                break;
            case 'HiddenFieldAction':
                $value = $HtmlForm->genHiddenField('asc_action', 'UpdateCreditCardSettings');
                break;
            case 'OptionsList':
                $value = $this->outputOptionsList();
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