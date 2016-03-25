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
 * @package ShippingModuleDSR
 * @access  public
 * @author Egor V. Derevyankin
 */
class update_dsr_settings extends update_pm_sm
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
    function update_dsr_settings()
    {
    }

    /**
     * @param $SessionPost array of the posted params
     */
    function saveDataToDB(&$SessionPost)
    {
        if($SessionPost["status"]=="active")
        {
            if(!isset($SessionPost["SMethodsAvailable"]) or empty($SessionPost["SMethodsAvailable"]))
                $SessionPost["ViewState"]["ErrorsArray"][]="ERROR_002";

        };

        if(count($SessionPost["ViewState"]["ErrorsArray"])==0
            and count(modApiFunc("Shipping_Module_DSR","checkRequirments"))==0)
        {
            unset($SessionPost["ViewState"]["ErrorsArray"]);
            //--
            modApiFunc("Checkout", "setModuleActive", (modApiFunc("Shipping_Module_DSR", "getUid")), ($SessionPost["status"]=="active")? true:false);

            $Settings = array(
                             "RATE_UNIT" => $SessionPost["RateUnit"]
                             );
            modApiFunc("Shipping_Module_DSR", "updateSettings", $Settings);

            $Methods = array();

            if(isset($SessionPost["SMethodsAvailable"])
                && is_array($SessionPost["SMethodsAvailable"]))
            {
                $Methods=$SessionPost["SMethodsAvailable"];
                foreach ($Methods as $k => $m)
                    if ($m != "on") unset($Methods[$k]);
            }

            modApiFunc("Shipping_Module_DSR", "_updateShippingMethods", $Methods);
            //--
            $SessionPost["ViewState"]["hasCloseScript"] = "true";
        }
    }

    /**
     * Action processor.
     */
    function onAction()
    {
        global $application;
        $request = $application->getInstance('Request');

        if(modApiFunc('Session', 'is_Set', 'SessionPost'))
        {
            _fatal(array( "CODE" => "CORE_050"), __CLASS__, __FUNCTION__);
        }

        $SessionPost = $_POST;

        $SessionPost["ViewState"]["ErrorsArray"] = array();

        if(isset($SessionPost["status"]) &&
            $SessionPost["status"] == "active")
        {
            //         ,       CheckoutFormEditor'
            //         PersonInfo     : ShippingInfo.
            if(modApiFunc("Checkout", "arePersonInfoTypesActive", array("shippingInfo")) === true)
            {
            }
            else
            {
                $SessionPost["status"]= "inactive";
                $SessionPost["ViewState"]["ErrorsArray"][] = "MODULE_ERROR_NO_PERSON_INFO_TYPES";
            }
        }

        switch ($SessionPost["ViewState"]["FormSubmitValue"])
        {
            case "Save":
                $this->saveDataToDB($SessionPost);
                break;

            case "AddMethod":
                if($SessionPost["NewMethodName"]=="")
                    $SessionPost["ViewState"]["ErrorsArray"][]="ERROR_001";
                else
                    $rates_method_id=modApiFunc("Shipping_Module_DSR","_addShippingMethod",$SessionPost["NewMethodName"]);
                break;

            case "DeleteMethods":
                $methods_ids=array_keys($SessionPost["SMethodsDelete"]);
                for($i=0;$i<count($methods_ids);$i++)
                    modApiFunc("Shipping_Module_DSR","_deleteShippingMethod",$methods_ids[$i]);
                if(!modApiFunc("Shipping_Module_DSR","isActive"))
                    $SessionPost["status"]="";
                break;

            case "CloneMethods":
                $methods_ids = array_keys($SessionPost["SMethodsDelete"]);
                for($i=0; $i < count($methods_ids); $i++)
                {
                    modApiFunc("Shipping_Module_DSR", "_cloneShippingMethod", $methods_ids[$i]);
                }
                break;

            case "RenameMethods":
                $method_names = $request->getValueByKey("method_name");
                foreach ($method_names as $key => $value)
                {
                    modApiFunc("Shipping_Module_DSR", "_renameShippingMethod", $key, $value);
                }
                $this->saveDataToDB($SessionPost);
                $SessionPost["ViewState"]["hasCloseScript"] = "false";
                break;

            case "AddRate":
                $rates_method_id=$SessionPost["rates_method_id"];
                unset($SessionPost["rates_method_id"]);

                $new_rate_data=array(
                    "country_id" => $SessionPost["DstCountry"],
                    "state_id" => $SessionPost["DstState_menu_select"],
                    "rate_data" => $SessionPost["NewRate"]
                );

                $add_result = modApiFunc("Shipping_Module_DSR","_addShippingRate",$rates_method_id,$new_rate_data);

                if(!empty($add_result))
                    $SessionPost["ViewState"]["ErrorsArray"]=$add_result;
                else
                    unset($SessionPost["DstCountry"],$SessionPost["DstState_menu_select"],$SessionPost["DstState_text_div"],$SessionPost["NewRate"]);

                break;

            case "DeleteRates":
                $rates_method_id=$SessionPost["rates_method_id"];
                unset($SessionPost["rates_method_id"]);

                $rates_ids=array_keys($SessionPost["RatesDelete"]);
                for($i=0;$i<count($rates_ids);$i++)
                    modApiFunc("Shipping_Module_DSR","_deleteShippingRate",$rates_ids[$i]);

                break;

            case "ChangeRateUnit":
                $nErrors = sizeof($SessionPost["ViewState"]["ErrorsArray"]);
                if($nErrors == 0)
                {
                    unset($SessionPost["ViewState"]["ErrorsArray"]);
                    $Settings = array(
                         "RATE_UNIT" => $SessionPost["RateUnit"]
                         );
                    modApiFunc("Shipping_Module_DSR", "updateSettings", $Settings);
                }

                break;

            default :
                _fatal(array( "CODE" => "CORE_051"), __CLASS__, __FUNCTION__, $SessionPost["ViewState"]["FormSubmitValue"]);
                break;
        }

        modApiFunc('Session', 'set', 'SessionPost', $SessionPost);
        // get view name by action name.
        $request = new Request();
        $request->setView(CURRENT_REQUEST_URL);
        if(isset($rates_method_id))
            $request->setKey("rates_method_id",$rates_method_id);
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