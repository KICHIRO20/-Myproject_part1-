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
 * Checkout Module, AddTaxDisplayOption View.
 *
 * @package Checkout
 * @author Alexey Florinsky
 */
class AddTaxDisplayOption
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * A constructor.
     */
    function AddTaxDisplayOption()
    {
        global $application;
        $this->MessageResources = &$application->getInstance('MessageResources');

        $this->TaxList = modApiFunc("Taxes", "getTaxNamesList");

        if(modApiFunc("Session", "is_Set", "SessionPost"))
        {
            $this->copyFormData();
            modApiFunc('Session', 'un_Set', 'SessionPost');
        }
        else
        {
            $this->initFormData();
        }
    }

    function copyFormData()
    {
        // eliminate copying on construction
        $SessionPost = modApiFunc("Session", "get", "SessionPost");
        $this->ViewState = $SessionPost["ViewState"];

        //Remove some data, that should not be sent to action one more time, from ViewState.
        if(isset($this->ViewState["ErrorsArray"]) &&
           count($this->ViewState["ErrorsArray"]) > 0)
        {
            $this->ErrorsArray = $this->ViewState["ErrorsArray"];
            unset($this->ViewState["ErrorsArray"]);
        }
        $this->POST  =
            array(
                "FormulaView"           => isset($SessionPost["FormulaView"]) ? $SessionPost["FormulaView"] : ''
               ,"Formula"               => $SessionPost["Formula"]
               ,"Id"                    => $SessionPost["Id"]
               ,"OptionId"              => isset($SessionPost["OptionId"]) ? $SessionPost["OptionId"] : ''
               ,"Display"               => $SessionPost["Display"]
            );
    }

    function initFormData()
    {
        $this->ViewState =
            array(
                "hasCloseScript" => "false"
                 );
        $this->POST  =
            array(
                "FormulaView"           => ""
               ,"Formula"               => ""
               ,"Id"                    => ""
               ,"OptionId"              => "1"
               ,"Display"               => ""
            );
    }

    function outputViewState()
    {
        $retval = "";
        foreach ($this->ViewState as $key => $value)
        {
            $retval .= "<input type=\"hidden\" name=\"ViewState[" .$key . "]\" value=\"" . $value . "\">";
        }
        return $retval;
    }

    function outputTaxList()
    {
        $retval = "";
        foreach ($this->TaxList as $taxInfo)
        {
            $retval.= "<option value=\"".$taxInfo['Id']."\" TaxName = \"".$taxInfo['Name']."\">".prepareHTMLDisplay($taxInfo['Name'])."</option>";
        }
        return $retval;
    }

    function outputOptionsList()
    {
        $retval = "";
        $list = modApiFunc("Taxes", "getDisplayOptionsList");
        foreach ($list as $option)
        {
            $retval.= "<option value=\"".$option['id']."\"".($option['id'] == $this->POST["OptionId"]? "SELECTED":"").">".$this->MessageResources->getMessage($option['name'])."</option>";
        }
        return $retval;
    }

    function formAction()
    {
        global $application;
        $request = new Request();
        $request->setView  ('AddTaxDisplayOption');
        $request->setAction('AddTaxDisplayOptionAction');
        return $request->getURL();
    }

    function outputSubtitle()
    {
        return $this->MessageResources->getMessage('ADD_TAX_DISPLAY_OPTION_PAGE_SUBTITLE');
    }

    function outputButton()
    {
        return $this->MessageResources->getMessage('BTN_ADD');
    }

    /**
     *
     */
    function output()
    {
        global $application;

        if($this->ViewState["hasCloseScript"] == "true")
        {
            modApiFunc("application", "closeChild_UpdateParent");
            return;
        }

        $this->_Template_Contents = array(
                                          'FormAction'  => $this->formAction()
                                         ,'HiddenArrayViewState' => $this->outputViewState()
                                         ,'Subtitle'             => $this->outputSubtitle()
                                         ,'Button'               => $this->outputButton()
                                         ,'Id'              => $this->POST["Id"]
                                         ,'FormulaView'     => prepareHTMLDisplay($this->POST["FormulaView"])
                                         ,'Formula'         => $this->POST["Formula"]
                                         ,'Display'         => $this->POST["Display"]
                                         ,'TaxItems'        => $this->outputTaxList()
                                         ,'OptionsList'     => $this->outputOptionsList()
                                         );
        $application->registerAttributes($this->_Template_Contents);
        return modApiFunc('TmplFiller', 'fill', "taxes/tax-settings-add-display/","container.tpl.html", array());
    }

    function getTag($tag)
    {
        global $application;
        $value = null;
        if (array_key_exists($tag, $this->_Template_Contents))
        {
            $value = $this->_Template_Contents[$tag];
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