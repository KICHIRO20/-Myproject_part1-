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
 * Checkout Module, AddTaxClass View
 *
 * @package Checkout
 * @author Alexey Florinsky
 */
class AddTaxClass
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
    function AddTaxClass()
    {
        global $application;
        $this->MessageResources = &$application->getInstance('MessageResources');

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
                "name"          => $SessionPost["name"],
                "id"            => $SessionPost["id"],
                "descr"         => $SessionPost["descr"]
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
                "name"          => ""
               ,"id"            => ""
               ,"descr"         => ""
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

    function formAction()
    {
        global $application;
        $request = new Request();
        $request->setView  ('AddTaxClass');
        $request->setAction('AddProdTaxClass');
        return $request->getURL();
    }

    function outputSubtitle()
    {
        return $this->MessageResources->getMessage('ADD_TAX_CLASS_PAGE_SUBTITLE');
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
                                         ,'Subtitle'    => $this->outputSubtitle()
                                         ,'Button'      => $this->outputButton()
                                         ,'TaxClassName'=> $this->POST["name"]
                                         ,'id'          => $this->POST["id"]
                                         ,'TaxClassDescr'=> $this->POST["descr"]
                                         );
        $application->registerAttributes($this->_Template_Contents);
        return modApiFunc('TmplFiller', 'fill', "taxes/tax-settings-add-class/","container.tpl.html", array());
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