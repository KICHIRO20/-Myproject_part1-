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
 * Quantity_Discounts module.
 * This action is responsible for updating Quantity Discounts.
 *
 * @package Quantity_Discounts
 * @access  public
 * @author Vadim Lyalikov
 */
class update_quantity_discounts extends AjaxAction
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Action constructor
     */
    function update_quantity_discounts()
    {
    }

    function saveDataToDB($SessionPost)
    {
//        modApiFunc("Checkout", "setModuleActive", (modApiFunc("Shipping_Module_Flat_Shipping_Rates", "getUid")), ($SessionPost["status"]=="active")? true:false);

        $Settings = array(
//                          "MODULE_NAME"  => $SessionPost["ModuleName"]
////                         ,"MODULE_DESCR" => $SessionPost["ModuleDescr"]
//                         ,"MODULE_RATE_UNIT_ID" => $SessionPost["ModuleRateUnitId"]
////                         ,"MODULE_COST_TYPE_ID" => $SessionPost["ModuleCostTypeId"]
                         );
        modApiFunc("Quantity_Discounts", "updateSettings", $Settings);
    }

    /**
     *
     */
    function onAction()
    {
        global $application;
        $request = $application->getInstance('Request');

        if(modApiFunc('Session', 'is_Set', 'SessionPost'))
        {
            _fatal(array( "CODE" => "CORE_050"), __CLASS__, __FUNCTION__);
        }

//        $SessionPost = array();
        $SessionPost = $_POST;

        $SessionPost["ViewState"]["ErrorsArray"] = array();
        $SessionPost["discount_or_new_price"] = 1;

        $product_id = $SessionPost["product_id"];
        if(modApiFunc("Catalog", "isCorrectProductId", $product_id) === false)
        {
            $SessionPost["ViewState"]["ErrorsArray"]["INCORRECT_PRODUCT_ID"] = "INCORRECT_PRODUCT_ID";
        }
        else
        {
            switch($request->getValueByKey('FormSubmitValue'))
            {
                case "AddRow" :
                {
                    $rate_unit_id = 1;
                    //1. Price.
                    //2. Weight.
                    //3. Items.

                    $UnitType = "currency";
                    $membership = $SessionPost["membership"][0];
                    //                         .
                    $rate_value_from = modApiFunc("Localization", "FormatStrToFloat", $SessionPost["rv_from"], $UnitType);

                    if(!is_numeric($rate_value_from) || ($rate_value_from < 1) || !is_numeric($SessionPost["cost"]))
                    {
                        break;
                    }

                    if($SessionPost["discount_or_new_price"] == 2)
                    {
                        //"New Price" absolute value.
                        $SessionPost["CostTypeId"] = 3 /*"CURRENCY" new price */;
                    }

                    $cost_type_id = $SessionPost["CostTypeId"];

                    if($cost_type_id == 1 /*"CURRENCY" absolute discount value */)
                    {
                        $rate_cost = modApiFunc("Localization", "FormatStrToFloat", $SessionPost["cost"], $UnitType);
                    }
                    else if($cost_type_id == 2 /*"PERCENT" percent discount value */)
                    {
                        //:                                 PERCENT,                         Float.
                        //$rate_cost = $SessionPost["cost"];
                        $rate_cost = modApiFunc("Localization", "FormatStrToFloat", $SessionPost["cost"], "currency");
                    }
                    else if($cost_type_id == 3 /*"CURRENCY"*/)
                    {
                        $rate_cost = modApiFunc("Localization", "FormatStrToFloat", $SessionPost["cost"], $UnitType);
                    }
                    else
                    {
                        //: report error
                    }

                    $ret_struct = modApiFunc("Quantity_Discounts", "doesAddingRateExist", $product_id, $rate_value_from, $membership);
                    if($ret_struct["ret_val"] == false)
                    {
                        //               .
//                        if(modApiFunc("Shipping_Module_Flat_Shipping_Rates", "areShippingRatesIntervalsContiguous"))
//                        {
//                        }
                    }
                    else
                    {
                        $SessionPost["ViewState"]["ErrorsArray"]["INTERSECTION_RATE_ID"] = $ret_struct["id"];
                        $SessionPost["ViewState"]["ErrorsArray"]["INTERSECTION_COORD"] = $ret_struct["intersection_coord"];
                            //if($ret_struct["intersection_coord"] !== NULL)
                            ////                               .
                            //else
                            ////                   ,                .
                    }

                    $nErrors = sizeof($SessionPost["ViewState"]["ErrorsArray"]);

                    if($nErrors == 0)
                    {
                        unset($SessionPost["ViewState"]["ErrorsArray"]);
                        modApiFunc("Quantity_Discounts", "insertQuantityDiscountRates", $product_id, $rate_value_from, $cost_type_id, $rate_cost, $membership);
                        modApiFunc('Session','set','ResultMessage','MSG_QUANTITY_DISCOUNTS_UPDATED');
                        //$SessionPost["ViewState"]["hasCloseScript"] = "true";
                    }
                    break;
                }

                case "DelRows" :
                {
                    if(isset($SessionPost["selected_rates"]))
                    {
                        $selected_rates_array = $SessionPost["selected_rates"];

                        $nErrors = sizeof($SessionPost["ViewState"]["ErrorsArray"]);
                        if($nErrors == 0)
                        {
                            unset($SessionPost["ViewState"]["ErrorsArray"]);
                            modApiFunc("Quantity_Discounts", "deleteRowsFromQuantityDiscount", $selected_rates_array);
                            modApiFunc('Session','set','ResultMessage','MSG_QUANTITY_DISCOUNTS_UPDATED');
                            //$SessionPost["ViewState"]["hasCloseScript"] = "true";
                        }
                    }
                    break;
                }

                case "UpdateRows" :
                {
                    if(isset($SessionPost["active_rates"]))
                    {
                        $active_rates = $SessionPost["active_rates"];

                        $nErrors = sizeof($SessionPost["ViewState"]["ErrorsArray"]);
                        if($nErrors == 0)
                        {
                            unset($SessionPost["ViewState"]["ErrorsArray"]);
                            modApiFunc("Quantity_Discounts", "updateRowsFromQuantityDiscount", $active_rates);
                            modApiFunc('Session','set','ResultMessage','MSG_QUANTITY_DISCOUNTS_UPDATED');
                            //$SessionPost["ViewState"]["hasCloseScript"] = "true";
                        }
                    }
                    break;
                }

                case "SetRowActive":
                {
                    global $_RESULT;

                    $request = $application->getInstance('Request');
                    $rate_id = $request->getValueByKey("rate_id");
                    $rate_status = $request->getValueByKey("rate_status");
                    if(!empty($rate_id) &&
                       is_numeric($rate_id) &&
                       !empty($rate_status) &&
                       is_numeric($rate_status))
                    {
                        modApiFunc("Quantity_Discounts", "setQuantityDiscountRowActive", $rate_id, $rate_status);
                        modApiFunc('Session','set','ResultMessage','MSG_QUANTITY_DISCOUNTS_UPDATED');
                        $_RESULT['ERR_CODE'] = "";
                        $_RESULT['ERR_MSG'] = "";
                    }
                    else
                    {
                        $_RESULT['ERR_CODE'] = "DISCOUNTS_DISCOUNT_ERR_001";
                        $_RESULT['ERR_MSG'] = "Error while changing current rate status.";
                    }
                    exit(0);
                    break;
                }

                case "Save" :
                {
                    $nErrors = sizeof($SessionPost["ViewState"]["ErrorsArray"]);
                    if($nErrors == 0)
                    {
                        unset($SessionPost["ViewState"]["ErrorsArray"]);
                        $this->saveDataToDB($SessionPost);
                        modApiFunc('Session','set','ResultMessage','MSG_QUANTITY_DISCOUNTS_UPDATED');
                        $SessionPost["ViewState"]["hasCloseScript"] = "true";
                    }
                    break;
                }
                default :
                    _fatal(array( "CODE" => "CORE_051"), __CLASS__, __FUNCTION__, $request->getValueByKey('FormSubmitValue'));
                    break;
            }
        }

        modApiFunc('Session', 'set', 'SessionPost', $SessionPost);
        // get view name by action name.
        $request = new Request();
        $request->setView(CURRENT_REQUEST_URL);
        $request->setKey("product_id", $product_id);
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