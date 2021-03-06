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
 * paypal_cc_input_az view
 *
 * @package PaymentModulePaypal CC
 * @author Vadim Lyalikov
 */
class paypal_cc_input_az extends pm_sm_input_az
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Modules_Manager constructor.
     */
    function paypal_cc_input_az()
    {
        global $application;
        $this->MessageResources = &$application->getInstance('MessageResources',"payment-module-paypal-messages", "AdminZone");
        $this->Hints = &$application->getInstance('Hint');
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

    /**
     *Initializes data from the POST array.
     */
    function copyFormData()
    {
        // eliminate copying on construction
        $SessionPost = modApiFunc("Session", "get", "SessionPost");
        $this->ViewState = $SessionPost["ViewState"];
        //Remove some data, that should not be resent to action, from ViewState.
        if(isset($this->ViewState["ErrorsArray"]) &&
           count($this->ViewState["ErrorsArray"]) > 0)
        {
            $this->ErrorsArray = $this->ViewState["ErrorsArray"];
            unset($this->ViewState["ErrorsArray"]);
        }

        $this->POST = $SessionPost;
    }

    /**
     * Initializes data from the database.
     */
    function initFormData()
    {
        $this->POST = array();
        $settings = modApiFunc("Payment_Module_Paypal_CC", "getSettings");
        foreach ($settings as $key => $value)
        {
            switch($key)
            {
                case "MODULE_NAME": $this->POST["ModuleName"] = $value; break;
                case "MODULE_EMAIL": $this->POST["ModuleEmail"] = $value; break;
                case "MODULE_MODE" : $this->POST["ModuleMode"] = $value; break;
                case "MODULE_CART" : $this->POST["ModuleCart"] = $value; break;
                case "MODULE_BILLING_INFO" : $this->POST["ModuleBillingInfo"] = $value; break;
                case "MODULE_ADDRESS_OVERRIDE": $this->POST["ModuleAddressOverride"] = $value; break;
            }
        }
        $this->POST["status"] = modApiFunc("Payment_Module_Paypal_CC", "isActive");
        $this->ViewState =
            array(
                "hasCloseScript"  => "false"
               ,"FormSubmitValue" => "save"
                 );
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
     * Outputs errors.
     */
    function outputErrors()
    {
        global $application;
        if (!is_array($this->ErrorsArray) || sizeof($this->ErrorsArray) == 0)
        {
            return;
        }
        $result = "";
        $application->registerAttributes(array('ErrorIndex', 'Error'));
        $this->_error_index = 0;
        foreach ($this->ErrorsArray as $error)
        {
            $this->_error_index++;
            $this->_error = $this->MessageResources->getMessage($error);
            $result .= $this->mTmplFiller->fill("payment_module_paypal/", "error.tpl.html", array());
        }
        return $result;
    }

    /**
     * Outputs the module status.
     */
    function outputStatus()
    {
        global $application;
        $retval = "";
        $status = $this->POST["status"];
        $this->_Template_Contents = array(
                                          "Active"          => ($status)? "checked":""
                                         ,"ActiveMessage"   => $this->MessageResources->getMessage('MODULE_STATUS_ACTIVE')
                                         ,"Inactive"        => ($status)? "":"checked"
                                         ,"InactiveMessage" => $this->MessageResources->getMessage('MODULE_STATUS_INACTIVE')
                                         );
        $application->registerAttributes($this->_Template_Contents);
        $retval.= $this->mTmplFiller->fill("payment_module_paypal/", "status.tpl.html", array());
        return $retval;
    }

    function outputModeOptions()
    {
        $retval = "";
        $options = array(array("value" => "1", "text" => $this->MessageResources->getMessage('MODE_001')), array("value" => "2", "text" => $this->MessageResources->getMessage('MODE_002')));
        foreach ($options as $option)
        {
            $retval.= "<option value='".$option["value"]."' ".($option["value"]==$this->POST["ModuleMode"]? "selected":"").">".$option["text"]."</option>";
        }
        return $retval;
    }

    function outputCartOptions()
    {
        $retval = "";
        $options = array(array("value" => "1", "text" => $this->MessageResources->getMessage('YES_LABEL')), array("value" => "2", "text" => $this->MessageResources->getMessage('NO_LABEL')));
        foreach ($options as $option)
        {
            $retval.= "<option value='".$option["value"]."' ".($option["value"]==$this->POST["ModuleCart"]? "selected":"").">".$option["text"]."</option>";
        }
        return $retval;
    }

    function outputBillingInfoOptions()
    {
        $retval = "";
        $options = array(array("value" => "1", "text" => $this->MessageResources->getMessage('YES_LABEL')), array("value" => "2", "text" => $this->MessageResources->getMessage('NO_LABEL')));
        foreach ($options as $option)
        {
            $retval.= "<option value='".$option["value"]."' ".($option["value"]==$this->POST["ModuleBillingInfo"]? "selected":"").">".$option["text"]."</option>";
        }
        return $retval;
    }

    function outputModuleAddressOverrideOptions()
    {
        $retval = "";
        $options = array(array("value" => "1", "text" => $this->MessageResources->getMessage('YES_LABEL')), array("value" => "2", "text" => $this->MessageResources->getMessage('NO_LABEL')));
        foreach ($options as $option)
        {
            $retval.= "<option value='".$option["value"]."' ".($option["value"]==$this->POST["ModuleAddressOverride"]? "selected":"").">".$option["text"]."</option>";
        }
        return $retval;
    }

    function outputOrderStatus()
    {
        global $application;
        $retval = "";
        for ($i=1; $i<5; $i++)
        {
            $retval.= "<option value=\"$i\" ";
            $retval.= ($this->POST["ModuleOrderStatusId"] == $i)? "SELECTED ":"";
            $retval.= ">".$this->MessageResources->getMessage('MODULE_ORDER_STATUS_00'.$i)."</option>";
        }
        $status = $this->POST["status"];
        $this->_Template_Contents = array(
                                          "Active"          => ($status)? "checked":""
                                         ,"ActiveMessage"   => $this->MessageResources->getMessage('MODULE_STATUS_ACTIVE')
                                         ,"Inactive"        => ($status)? "":"checked"
                                         ,"InactiveMessage" => $this->MessageResources->getMessage('MODULE_STATUS_INACTIVE')
                                         );
        $application->registerAttributes($this->_Template_Contents);
        $retval.= $this->mTmplFiller->fill("payment_module_paypal/", "status.tpl.html", array());
        return $retval;
    }

    /**
     *
     */
    function output()
    {
        global $application;
        loadCoreFile('html_form.php');
        $HtmlForm1 = new HtmlForm();
        if($this->ViewState["hasCloseScript"] == "true")
        {
            modApiFunc("application", "closeChild_UpdateParent");
            return;
        }

        $request = new Request();
        $request->setView('CheckoutPaymentModuleSettings');
        $request->setAction("update_paypal");
        $form_action = $request->getURL();

        $template_contents = array(
                                    "EditPayPalForm"        => $HtmlForm1->genForm($form_action, "POST", "EditPayPalForm")
                                   ,"HiddenArrayViewState"  => $this->outputViewState()
                                   ,"ModuleType"            => $this->MessageResources->getMessage('MODULE_TYPE')
                                   ,"ModuleName"            => $this->MessageResources->getMessage('MODULE_NAME')
                                   ,"Subtitle"              => $this->MessageResources->getMessage('FORM_SUBTITLE')
                                   ,"Errors"                => $this->outputErrors()
                                   ,"ModuleStatusFieldName" => $this->MessageResources->getMessage('MODULE_STATUS_FIELD_NAME')
                                   ,"ModuleStatusFieldHint" => $this->Hints->getHintLink(array('MODULE_STATUS_FIELD_NAME', 'payment-module-paypal-messages'))
                                   ,"ModuleStatusField"     => $this->outputStatus()
                                   ,"ModuleMethodNameFieldName"   => $this->MessageResources->getMessage('MODULE_METHOD_NAME_FIELD_NAME')
                                   ,"ModuleNameFieldHint"   => $this->Hints->getHintLink(array('MODULE_METHOD_NAME_FIELD_NAME', 'payment-module-paypal-messages'))
                                   ,"ModuleNameField"       => $HtmlForm1->genInputTextField("128", "ModuleName", "75", prepareHTMLDisplay($this->POST["ModuleName"]))
//                                   ,"ModuleDescrFieldName"  => $this->MessageResources->getMessage('MODULE_DESCR_FIELD_NAME')
//                                   ,"ModuleDescrFieldHint"  => $this->Hints->getHintLink(array('MODULE_DESCR_FIELD_NAME', 'payment-module-paypal-messages'))
//                                   ,"ModuleDescrField"      => $HtmlForm1->genInputTextAreaField("75", "ModuleDescr", "5")
//                                   ,"ModuleDescrFieldValue" => $this->POST["ModuleDescr"]
                                   ,"ModuleEmailFieldName"  => $this->MessageResources->getMessage('MODULE_EMAIL_FIELD_NAME')
                                   ,"ModuleEmailFieldHint"  => $this->Hints->getHintLink(array('MODULE_EMAIL_FIELD_NAME', 'payment-module-paypal-messages'))
                                   ,"ModuleEmailField"      => $HtmlForm1->genInputTextField("128", "ModuleEmail", "30", prepareHTMLDisplay($this->POST["ModuleEmail"]))
                                   ,"ModuleBillingInfoFieldName" => $this->MessageResources->getMessage('MODULE_BILLING_INFO_FIELD_NAME')
                                   ,"ModuleBillingInfoFieldHint" => $this->Hints->getHintLink(array('MODULE_BILLING_INFO_FIELD_NAME', 'payment-module-paypal-messages'))
                                   ,"ModuleBillingInfoOptions"   => $this->outputBillingInfoOptions()
                                   ,"ModuleAddressOverrideFieldName" => $this->MessageResources->getMessage('MODULE_ADDRESS_OVERRIDE_FIELD_NAME')
                                   ,"ModuleAddressOverrideFieldHint" => $this->Hints->getHintLink(array('MODULE_ADDRESS_OVERRIDE_FIELD_NAME', 'payment-module-paypal-messages'))
                                   ,"ModuleAddressOverrideOptions"   => $this->outputModuleAddressOverrideOptions()
                                   ,"ModuleCartFieldName"    => $this->MessageResources->getMessage('MODULE_CART_FIELD_NAME')
                                   ,"ModuleCartFieldHint"    => $this->Hints->getHintLink(array('MODULE_CART_FIELD_NAME', 'payment-module-paypal-messages'))
                                   ,"ModuleCartOptions"      => $this->outputCartOptions()
                                   ,"ModuleModeFieldName"    => $this->MessageResources->getMessage('MODULE_MODE_FIELD_NAME')
                                   ,"ModuleModeFieldHint"    => $this->Hints->getHintLink(array('MODULE_MODE_FIELD_NAME', 'payment-module-paypal-messages'))
                                   ,"ModuleModeOptions"      => $this->outputModeOptions()
//                                   ,"ModuleOrderStatusFieldName"=> $this->MessageResources->getMessage('MODULE_ORDER_STATUS_FIELD_NAME')
//                                   ,"OrderStatus"           => $this->outputOrderStatus()
                                   ,"Alert_001"             => $this->MessageResources->getMessage('ALERT_001')
                                   ,"Alert_002"             => $this->MessageResources->getMessage('ALERT_002')
                                   ,"Alert_003"             => $this->MessageResources->getMessage('ALERT_003')
                                   ,"PmSmAcceptedCurrencies" => $this->outputAcceptedCurrencies()
                                  );

        $this->_Template_Contents = $template_contents;
        $application->registerAttributes($this->_Template_Contents);
        return $this->mTmplFiller->fill("payment_module_paypal/", "list.tpl.html",array());
    }

    function getTag($tag)
    {
        global $application;
        switch($tag)
        {
            case "ErrorIndex":
                $value = $this->_error_index;
                break;
            case "Error":
                $value = $this->_error;
                break;
            default:
        $value = getKeyIgnoreCase($tag, $this->_Template_Contents);
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

    var $POST;

    /**
     * View state structure. It comes from action.
     * $SessionPost["ViewState"] structure example:
     * <br>array
     * <br>(
     * <br>    "hasCloseScript"  = "false"           //true/false
     * <br>    "ErrorsArray"     =  array()          //true/false
     * <br>    "LargeImage"      = "image.jpg"       //
     * <br>    "SmallImage"      = "image_small.jpg" //
     * <br>)
     */
    var $ViewState;

    /**
     * List of error ids. It comes from action.
     */
    var $ErrorsArray;
    var $ErrorMessages;

    var $_Template_Contents;

    var $MessageResources;
    var $_error_index;
    var $_error;

    /**#@-*/

}
?>