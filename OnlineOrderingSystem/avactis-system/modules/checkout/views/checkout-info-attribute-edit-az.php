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

define("STATE_ATTRIBUTE_ID", "7");
define("COUNTRY_ATTRIBUTE_ID", "9");

/*
 * @: I guess this class and file no more required as there is no edit form for customer profile fields
 */

class CheckoutInfoAttributeEdit
{


    function CheckoutInfoAttributeEdit()
    {
        global $application;
        $this->MessageResources = &$application->getInstance('MessageResources',"checkout-messages", "AdminZone");
        $this->mTmplFiller = &$application->getInstance('TmplFiller');

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

    function initFormData()
    {
        $SessionGet = $_GET;

        $variant_id = $_GET['VariantId'];
        $attribute_id = $_GET['AttributeId'];

        $this->POST = modApiFunc("Checkout", "getPersonInfoFieldsList" , $variant_id, $attribute_id);
        $this->ViewState =
            array(
                "hasCloseScript"  => "false"
               ,"FormSubmitValue" => "save"
            );

    }

    /**
     * Copies data from the global POST to the local POST array.
     */
    function copyFormData()
    {
        $SessionPost = modApiFunc("Session", "get", "SessionPost");
        $this->ViewState = $SessionPost["ViewState"];
        if(isset($this->ViewState["ErrorsArray"]) &&
           count($this->ViewState["ErrorsArray"]) > 0)
        {
            $this->ErrorsArray = $this->ViewState["ErrorsArray"];
            unset($this->ViewState["ErrorsArray"]);
        }
        $this->POST = modApiFunc("Checkout", "getPersonInfoFieldsList" , $SessionPost["VariantId"], $SessionPost["AttributeId"]);
        $this->POST["name"] = $SessionPost["VisibleName"];
        $this->POST["descr"] = $SessionPost["Description"];
        $this->POST["visible"] = $SessionPost["IsVisible"];
        $this->POST["required"] = $SessionPost["IsRequired"];
    }


    /**
     * @return String Return html code for hidden form fields representing
     * @var $this->ViewState array.
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
     * @return HTML code for the errors
     */
    function outputErrors()
    {

        if (!is_array($this->ErrorsArray) || sizeof($this->ErrorsArray) == 0)
        {
            return;
        }
        global $application;

        $return_html_code="";
        foreach($this->ErrorsArray as $index => $value)
        {
            $this->_Template_Contents = array(
                                            "ErrorIndex"    => $index + 1,
                                            "Error"         => $this->MessageResources->getMessage($value)
                                        );
            $application->registerAttributes($this->_Template_Contents);
            $return_html_code.=$this->mTmplFiller->fill("checkout/checkout-info/", "error.tpl.html", array());
        };
        return $return_html_code;
    }

    function outputUpdateInfoHref()
    {
        $request = new Request();
        $request->setView  ( 'CheckoutInfoAttributeEdit' );
        $request->setAction( 'UpdateCheckoutInfo' );
        return $request->getURL();
    }

    function outputAttributeIsDependent()
    {
        return $this->attributeIsCountry() || $this->attributeIsState();
    }

    function outputAttributeIsCountry()
    {
        return $this->attributeIsCountry() ? "1" : "0";
    }

    function outputAttributeIsState()
    {
        return $this->attributeIsState() ? "1" : "0";
    }

    function outputCountryIsVisible()
    {
        return $this->countryIsVisible() ? "1" : "0";
    }

    function outputStateIsVisible()
    {
        return $this->stateIsVisible() ? "1" : "0";
    }


    function attributeIsState()
    {
        return $this->POST["attribute_id"] == STATE_ATTRIBUTE_ID;
    }

    function attributeIsCountry()
    {
        return $this->POST["attribute_id"] == COUNTRY_ATTRIBUTE_ID;
    }


    function countryIsVisible()
    {
        $result = modApiFunc("Checkout", "getPersonInfoFieldsList" , $this->POST["variant_id"],  COUNTRY_ATTRIBUTE_ID);
        return $result != null ? $result["visible"] : false;
    }

    function stateIsVisible()
    {
        $result = modApiFunc("Checkout", "getPersonInfoFieldsList" , $this->POST["variant_id"], STATE_ATTRIBUTE_ID);
        return $result != null ? $result["visible"] : false;
    }

    function CountryVisibleName()
    {
        $result = modApiFunc("Checkout", "getPersonInfoFieldsList" , $this->POST["variant_id"], COUNTRY_ATTRIBUTE_ID);
        return $result != null ? $result["name"] : "";
    }

    function StateVisibleName()
    {
        $result = modApiFunc("Checkout", "getPersonInfoFieldsList" , $this->POST["variant_id"], STATE_ATTRIBUTE_ID);
        return $result != null ? $result["name"] : "";
    }

    function output()
    {
        global $application;

        if($this->ViewState["hasCloseScript"] == "true")
        {
            modApiFunc("application", "closeChild_UpdateParent");
            return;
        }

        $template_contents = array(
                "HiddenArrayViewState"  => $this->outputViewState()
               ,"Errors"                => $this->outputErrors()
               ,"VariantId"             => $this->POST["variant_id"]
               ,"AttributeId"           => $this->POST["attribute_id"]

               ,"AttributeIsDependent"  => $this->outputAttributeIsDependent()
               ,"AttributeIsCountry"    => $this->outputAttributeIsCountry()
               ,"AttributeIsState"      => $this->outputAttributeIsState()
               ,"CountryIsVisible"      => $this->outputCountryIsVisible()
               ,"StateIsVisible"        => $this->outputStateIsVisible()
               ,"CountryVisibleName"    => $this->CountryVisibleName()
               ,"StateVisibleName"      => $this->StateVisibleName()

               ,"PageHeader"            => $this->MessageResources->getMessage("PAGE_HEADER")
               ,"PageName"              => $this->MessageResources->getMessage("PAGE_NAME_EDIT_ATTRIBUTES")
               ,"VisibleName_FieldName"  => $this->MessageResources->getMessage("VISIBLE_NAME")
               ,"VisibleName_FieldValue" => prepareHTMLDisplay($this->POST["name"])
               ,"Description_FieldName"  => $this->MessageResources->getMessage("DESCRIPTION")
               ,"Description_FieldValue" => prepareHTMLDisplay($this->POST["descr"])
               ,"Unremovable"           => $this->POST["unremovable"] != 0 ? "DISABLED" : ""
               ,"HintHead"              => $this->POST["unremovable"] != 0 ? "<A TITLE='".$this->MessageResources->getMessage("HINT_UNREMOVABLE")."'>" : ""
               ,"HintTail"              => $this->POST["unremovable"] != 0 ? "</A>" : ""
               ,"Visibility_FieldName"  => $this->MessageResources->getMessage("VISIBILITY")
               ,"Visibility_IsChecked"  => ($this->POST["visible"] != 0 ? "CHECKED" : "")
               ,"Required_FieldName"    => $this->MessageResources->getMessage("REQUIRED")
               ,"Required_IsChecked"    => ($this->POST["required"] != 0 ? "CHECKED" : "")
               ,"Alert_001"             => $this->MessageResources->getMessage("ALERT_001")
               ,"Alert_002"             => $this->MessageResources->getMessage("ALERT_002")
               ,"Alert_003"             => $this->MessageResources->getMessage("ALERT_003")
               ,"UpdateInfoHref"        => $this->outputUpdateInfoHref()
               ,'GroupName'             => modApiFunc("Checkout", "getVariantNameById" , $this->POST["variant_id"])
        );

        $this->_Template_Contents=$template_contents;
        $application->registerAttributes($this->_Template_Contents);

        $mainMessageResources = &$application->getInstance('MessageResources');

        $output = modApiFunc('TmplFiller', 'fill',
                              './../../js/','validate.msgs.js.tpl',
                              array(
                                    "INTEGER" => $mainMessageResources->getMessage( new ActionMessage(array('PRDADD_001')) )
                                   ,"FLOAT"   => $mainMessageResources->getMessage( new ActionMessage(array('PRDADD_002')) )
                                   ,"STRING1024"=> $mainMessageResources->getMessage( new ActionMessage(array('CATADD_001')) )
                                   ,"STRING128"=> $mainMessageResources->getMessage( new ActionMessage(array('CATADD_002')) )
                                   ,"STRING256"=> $mainMessageResources->getMessage( new ActionMessage(array('CATADD_003')) )
                                   ,"STRING512"=> $mainMessageResources->getMessage( new ActionMessage(array('CATADD_004')) )
                                   ,"CURRENCY"=> addslashes($mainMessageResources->getMessage( new ActionMessage(array('CURRENCY_FIELD',
                                                         modApiFunc("Localization", "FloatToFormatStr", 12.35, "currency"),
                                                         modApiFunc("Localization", "FloatToFormatStr", 34, "currency"),
                                                         modApiFunc("Localization", "FloatToFormatStr", 99.00, "currency")))))
                                   ,"WEIGHT"   => addslashes($mainMessageResources->getMessage( new ActionMessage(array('WEIGHT_FIELD',
                                                         modApiFunc("Localization", "FloatToFormatStr", 23.325, "weight"),
                                                         modApiFunc("Localization", "FloatToFormatStr", 34, "weight"),
                                                         modApiFunc("Localization", "FloatToFormatStr", 99.2, "weight")))))
                                   ,"ITEM"     => $mainMessageResources->getMessage( new ActionMessage(array('ITEM_FIELD')))
                                   )
                            );
        return $output.$this->mTmplFiller->fill("checkout/checkout-info/", "attribute-edit.tpl.html",array());
    }

    function getTag($tag)
    {
        global $application;
        $value = getKeyIgnoreCase($tag, $this->_Template_Contents);
        return $value;
    }

    var $_Template_Contents;
    var $MessageResources;
    var $POST;

    var $ViewState;

    var $ErrorsArray;
    var $ErrorMessages;

    var $_error_index;
    var $_error;

}


?>