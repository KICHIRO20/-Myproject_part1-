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
 * Checkout Module, AddTaxName View.
 *
 * @package Checkout
 * @author Alexey Florinsky
 */
class AddTaxName
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
    function AddTaxName()
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
                "included_into_price"   => $SessionPost["included_into_price"],
                "TaxName"               => $SessionPost["TaxName"],
                "Id"                    => $SessionPost["Id"],
                "Address"               => $SessionPost["Address"]
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
                "Edit"                  => false
               ,"included_into_price"   => "false"
               ,"TaxName"               => ""
               ,"Id"                    => ""
               ,"Address"               => "1"
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

    function outputAdressesList()
    {
        $retval = "";
        $list = modApiFunc("Taxes", "getAddressesList");
        foreach ($list as $address)
        {
            if($address['id'] != TAXES_STORE_OWNER_ADDRESS_ID)
            {
                $retval.= "<option value=\"".$address['id']."\"".($address['id'] == $this->POST["Address"]? "SELECTED":"").">".$this->MessageResources->getMessage($address['name'])."</option>";
            }
        }
        //                 "                                       (                   )"
        $retval.= "<option value=\"".TAXES_TAX_NAME_DOES_NOT_NEED_ADDRESS_ID."\"".(TAXES_TAX_NAME_DOES_NOT_NEED_ADDRESS_ID == $this->POST["Address"]? "SELECTED":"").">".$this->MessageResources->getMessage('TAX_ADDRESS_NAME_1025')."</option>";
        return $retval;
    }

    function outputAdressesListForIncludedTaxes()
    {
        $retval = "";
        $list = modApiFunc("Taxes", "getAddressesList");
        foreach ($list as $address)
        {
            if($address['id'] == TAXES_STORE_OWNER_ADDRESS_ID)
            {
//                $retval.= "<option value=\"".$address['id']."\"".($address['id'] == $this->POST["Address"]? "SELECTED":"").">".$this->MessageResources->getMessage($address['name'])."</option>";
            }
        }
        //                 "                                       (                   )"
        $retval.= "<option value=\"".TAXES_TAX_NAME_DOES_NOT_NEED_ADDRESS_ID."\"".(TAXES_TAX_NAME_DOES_NOT_NEED_ADDRESS_ID == $this->POST["Address"]? "SELECTED":"").">".$this->MessageResources->getMessage('TAX_ADDRESS_NAME_1025')."</option>";
        return $retval;
    }

    function formAction()
    {
        global $application;
        $request = new Request();
        $request->setView  ('AddTaxName');
        $request->setAction('AddTaxNameAction');
        return $request->getURL();
    }

    function outputSubtitle()
    {
        return $this->MessageResources->getMessage('ADD_TAX_NAME_PAGE_SUBTITLE');
    }

    function outputButton()
    {
        return $this->MessageResources->getMessage('BTN_ADD');
    }

    function outputIncludedIntoPriceCheckbox()
    {
        $included_into_price = ($this->POST["included_into_price"] == "true")? "CHECKED" : "";

        if ($this->POST["Edit"] == true)
        {
            $retval = "<INPUT TYPE='checkbox' NAME='included_into_price' id='included_into_price' style='display: none' $included_into_price>";
            $retval .= ($this->POST["included_into_price"] == "true") ? getMsg("SYS", 'GNRL_SET_YES_LABEL') : getMsg("SYS", 'GNRL_SET_NO_LABEL');
        }
        else
        {
            $retval = '<INPUT TYPE="checkbox" NAME="included_into_price" id="included_into_price" '.$included_into_price.' onclick="included_into_price_onclick();">';
        }
        return $retval;
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
                                         ,"IncludedIntoPriceCheckbox" => $this->outputIncludedIntoPriceCheckbox()
                                         ,'TaxName'     => $this->POST["TaxName"]
                                         ,'Id'          => $this->POST["Id"]
                                         ,'AddressList' => $this->outputAdressesList()
                                         ,'AddressListForIncludedTaxes' => $this->outputAdressesListForIncludedTaxes()
                                         ,'HideAddress' => ($this->POST["Address"] == TAXES_TAX_NAME_DOES_NOT_NEED_ADDRESS_ID) ? 'true' : 'false'
                                         );
        $application->registerAttributes($this->_Template_Contents);
        return modApiFunc('TmplFiller', 'fill', "taxes/tax-settings-add-name/","container.tpl.html", array());
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