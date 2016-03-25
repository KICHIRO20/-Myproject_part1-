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
 * CheckoutPaymentModuleCodInputAZ view
 *
 * @package PaymentModuleCod
 * @author Egor Makarov
 */
class CheckoutPaymentModuleCodInputAZ extends pm_sm_input_az
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
    function CheckoutPaymentModuleCodInputAZ()
    {
        global $application;
        $this->MessageResources = &$application->getInstance('MessageResources',"payment-module-cod-messages", "AdminZone");
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
     * Initializes data from the POST array.
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
        $settings = modApiFunc("Payment_Module_Cod", "getSettings");
        foreach ($settings as $key => $value)
        {
            switch($key)
            {
                case "MODULE_NAME": $this->POST["ModuleName"] = $value; break;
                case "PER_ORDER_SHIPPING_FEE": $this->POST["PerOrderShippingFee"] = $value; break;
//                case "MODULE_ORDER_STATUS_ID": $this->POST["ModuleOrderStatusId"] = $value; break;
            }
        }
        $this->POST["status"] = modApiFunc("Payment_Module_Cod", "isActive");
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
            $result .= $this->mTmplFiller->fill("payment_module_cod/", "error.tpl.html", array());
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
                                          "Active"          => ($status)? "checked" : ""
                                         ,"ActiveMessage"   => $this->MessageResources->getMessage('MODULE_STATUS_ACTIVE')
                                         ,"Inactive"        => ($status)? "" : "checked"
                                         ,"InactiveMessage" => $this->MessageResources->getMessage('MODULE_STATUS_INACTIVE')
                                         );
        $application->registerAttributes($this->_Template_Contents);
        $retval.= $this->mTmplFiller->fill("payment_module_cod/", "status.tpl.html", array());
        return $retval;
    }

    /**
     *
     */
    function outputOrderStatus()
    {
        global $application;
        $retval = "";
        for ($i=1; $i<5; $i++)
        {
            $retval.= "<option value=\"$i\" ";
            $retval.= ($this->POST["ModuleOrderStatusId"] == $i)? "SELECTED " : "";
            $retval.= ">" . $this->MessageResources->getMessage('MODULE_ORDER_STATUS_00'.$i) . "</option>";
        }
        $status = $this->POST["status"];
        $this->_Template_Contents = array(
                                          "Active"          => ($status)? "checked":""
                                         ,"ActiveMessage"   => $this->MessageResources->getMessage('MODULE_STATUS_ACTIVE')
                                         ,"Inactive"        => ($status)? "":"checked"
                                         ,"InactiveMessage" => $this->MessageResources->getMessage('MODULE_STATUS_INACTIVE')
                                         );
        $application->registerAttributes($this->_Template_Contents);
        $retval.= $this->mTmplFiller->fill("payment_module_cod/", "status.tpl.html", array());
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
        $request->setAction("update_cod");
        $form_action = $request->getURL();

        $template_contents = array(
                                    "EditCodForm"        => $HtmlForm1->genForm($form_action, "POST", "EditCodForm")
                                   ,"HiddenArrayViewState"  => $this->outputViewState()
                                   ,"ModuleType"            => $this->MessageResources->getMessage('MODULE_TYPE')
                                   ,"ModuleName"            => $this->MessageResources->getMessage('MODULE_NAME')
                                   ,"Subtitle"              => $this->MessageResources->getMessage('FORM_SUBTITLE')
                                   ,"Errors"                => $this->outputErrors()

                                   ,"ModuleStatusFieldName" => $this->MessageResources->getMessage('MODULE_STATUS_FIELD_NAME')
                                   ,"ModuleStatusFieldHint" => $this->Hints->getHintLink(array('MODULE_STATUS_FIELD_NAME', 'payment-module-cod-messages'))
                                   ,"ModuleStatusField"     => $this->outputStatus()

                                   ,"ModuleMethodNameFieldName"   => $this->MessageResources->getMessage('MODULE_METHOD_NAME_FIELD_NAME')
                                   ,"ModuleNameFieldHint"   => $this->Hints->getHintLink(array('MODULE_METHOD_NAME_FIELD_NAME', 'payment-module-cod-messages'))
                                   ,"ModuleNameField"       => $HtmlForm1->genInputTextField("128", "ModuleName", "75", prepareHTMLDisplay($this->POST["ModuleName"]))

                                   ,"ModulePerOrderShippingFeeFieldName"   => $this->MessageResources->getMessage('MODULE_PER_ORDER_SHIPPING_FEE_FIELD_NAME')
                                   ,"ModulePerOrderShippingFeeFieldHint"   => $this->Hints->getHintLink(array('MODULE_PER_ORDER_SHIPPING_FEE_FIELD_NAME', 'payment-module-cod-messages'))
                                   ,"ModulePerOrderShippingFeeField"       => $HtmlForm1->genInputTextField("128", "PerOrderShippingFee", "75", prepareHTMLDisplay($this->POST["PerOrderShippingFee"]))
                                   ,"CostFormat"            => modApiFunc("Localization", "format_settings_for_js", "currency")

                                   ,"ModuleDescrFieldName"  => $this->MessageResources->getMessage('MODULE_DESCR_FIELD_NAME')
                                   ,"ModuleDescrFieldHint"  => $this->Hints->getHintLink(array('MODULE_DESCR_FIELD_NAME', 'payment-module-cod-messages'))
                                   ,"ModuleDescrField"      => $HtmlForm1->genInputTextAreaField("75", "ModuleDescr", "5")
                                   ,"ModuleDescrFieldValue" => $this->MessageResources->getMessage("MODULE_DESCR")

                                   ,"Alert_001"             => $this->MessageResources->getMessage('ALERT_001')
                                   ,"Alert_002"             => $this->MessageResources->getMessage('ALERT_002')
                                   ,"Alert_003"             => $this->MessageResources->getMessage('ALERT_003')
                                  );

        $this->_Template_Contents = $template_contents;
        $application->registerAttributes($this->_Template_Contents);
        $obj = &$application->getInstance('MessageResources');
        $output = modApiFunc('TmplFiller', 'fill',
                              './../../js/','validate.msgs.js.tpl',
                              array(
                                    "INTEGER" => $obj->getMessage( new ActionMessage(array('PRDADD_001')) )
                                   ,"FLOAT"   => $obj->getMessage( new ActionMessage(array('PRDADD_002')) )
                                   ,"STRING1024"=> $obj->getMessage( new ActionMessage(array('PRDADD_007')) )
                                   ,"STRING128"=> $obj->getMessage( new ActionMessage(array('PRDADD_008')) )
                                   ,"STRING256"=> $obj->getMessage( new ActionMessage(array('PRDADD_009')) )
                                   ,"STRING512"=> $obj->getMessage( new ActionMessage(array('PRDADD_010')) )
                                   ,"CURRENCY"=> addslashes($obj->getMessage( new ActionMessage(array('CURRENCY_FIELD',
                                                         modApiFunc("Localization", "FloatToFormatStr", 12.35, "currency"),
                                                         modApiFunc("Localization", "FloatToFormatStr", 34, "currency"),
                                                         modApiFunc("Localization", "FloatToFormatStr", 99.00, "currency")))))
                                   ,"WEIGHT"   => addslashes($obj->getMessage( new ActionMessage(array('WEIGHT_FIELD',
                                                         modApiFunc("Localization", "FloatToFormatStr", 23.325, "weight"),
                                                         modApiFunc("Localization", "FloatToFormatStr", 34, "weight"),
                                                         modApiFunc("Localization", "FloatToFormatStr", 99.2, "weight")))))
                                   ,"ITEM"     => $obj->getMessage( new ActionMessage(array('ITEM_FIELD')))
                                   )
                            );
        return $output. $this->mTmplFiller->fill("payment_module_cod/", "list.tpl.html", array());
    }

    /**
     *
     */
    function genInputCheckBox($name, $checked, $text)
    {
        $retval = "name=\"{$name}\" " . ($checked ? " checked " : "") . " value=\"{$text}\"";
        return $retval;
    }

    /**
     * Returns the tag output, whose name is specified in $tag.
     */
    function getTag($tag)
    {
        global $application;
        $value = "";

        if ($tag == "Error")
        {
            $value = $this->_error;
        }
        elseif ($tag == "ErrorIndex")
        {
                $value = $this->_error_index;
        }
        else
        {
            $value = getKeyIgnoreCase($tag, $this->_Template_Contents);
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