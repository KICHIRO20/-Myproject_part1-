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
 * Payment Module.
 * This action is responsible for update PayPal settings.
 *
 * @package PaymentModulePaypal CC
 * @access  public
 * @author Girin Alexander
 */
class update_paypal extends update_pm_sm
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Action constructor.
     */
    function update_paypal()
    {
    }

    function saveDataToDB($SessionPost)
    {
        modApiFunc("Checkout", "setModuleActive", (modApiFunc("Payment_Module_Paypal_CC", "getUid")), ($SessionPost["status"]=="active")? true:false);

        $Settings = array(
                          "MODULE_NAME"  => $SessionPost["ModuleName"]
//                         ,"MODULE_DESCR" => $SessionPost["ModuleDescr"]
                         ,"MODULE_EMAIL" => $SessionPost["ModuleEmail"]
                         ,"MODULE_MODE" => $SessionPost["ModuleMode"]
                         ,"MODULE_CART" => $SessionPost["ModuleCart"]
                         ,"MODULE_BILLING_INFO" => $SessionPost["ModuleBillingInfo"]
                         ,"MODULE_ADDRESS_OVERRIDE" => $SessionPost["ModuleAddressOverride"]
                         );
        modApiFunc("Payment_Module_Paypal_CC", "updateSettings", $Settings);
    }

    /**
     *
     */
    function onAction()
    {
        global $application;
        parent::onAction();
        $request = $application->getInstance('Request');

        $SessionPost = array();
        if(modApiFunc('Session', 'is_Set', 'SessionPost'))
        {
            _fatal(array( "CODE" => "CORE_050"), __CLASS__, __FUNCTION__);
        }

        $SessionPost = $_POST;

        switch($SessionPost["ViewState"]["FormSubmitValue"])
        {
            case "save" :
            {
                $nErrors = 0;
                $SessionPost["ViewState"]["ErrorsArray"] = array();

                if($SessionPost["status"] == "active")
                {
                    //         ,       CheckoutFormEditor'
                    //         PersonInfo     : BillingInfo, ShippingInfo.
                    if(modApiFunc("Checkout", "arePersonInfoTypesActive", array("billingInfo", "shippingInfo")) === true)
                    {
                    }
                    else
                    {
                        $SessionPost["status"]= "inactive";
                        $SessionPost["ViewState"]["ErrorsArray"][] = "MODULE_ERROR_NO_PERSON_INFO_TYPES";
                    }
                }

                if(empty($SessionPost["ModuleName"]))
                {
                    $SessionPost["ViewState"]["ErrorsArray"]["ModuleName"] = new ActionMessage(array("ALERT_001_PHP"));
                }

                if(empty($SessionPost["ModuleEmail"]))
                {
                    $SessionPost["ViewState"]["ErrorsArray"]["ModuleEmail"] = new ActionMessage(array("ALERT_002_PHP"));
                }
                elseif(modApiFunc("Users", "isValidEmail", $SessionPost["ModuleEmail"]) == false)
                {
                    $SessionPost["ViewState"]["ErrorsArray"]["ModuleEmail"] = new ActionMessage(array("ALERT_003_PHP"));
                }

                $nErrors = sizeof($SessionPost["ViewState"]["ErrorsArray"]);

                if($nErrors == 0)
                {
                    unset($SessionPost["ViewState"]["ErrorsArray"]);
                    $this->saveDataToDB($SessionPost);
                    $SessionPost["ViewState"]["hasCloseScript"] = "true";
                }
                break;
            }
            default :
                _fatal(array( "CODE" => "CORE_051"), __CLASS__, __FUNCTION__, $request->getValueByKey('FormSubmitValue'));
                break;
        }

        modApiFunc('Session', 'set', 'SessionPost', $SessionPost);
        // get view name by action name.
        $request = new Request();
        $request->setView(CURRENT_REQUEST_URL);
        $application->redirect($request);
    }

    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    /**
     * Corresponding View file.
     */
    var $ViewFilename;

    /**#@-*/
}
?>