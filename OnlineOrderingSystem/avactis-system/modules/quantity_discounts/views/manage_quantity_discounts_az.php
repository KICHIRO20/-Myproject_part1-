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
 * manage_quantity_discounts_az view
 *
 * @package
 * @author Vadim Lyalikov
 */
class manage_quantity_discounts_az
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Modules_Manager constructor
     */
    function manage_quantity_discounts_az()
    {
        global $application;
        $this->MessageResources = &$application->getInstance('MessageResources',"quantity-discounts-messages", "AdminZone");

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
     *                         POST
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

        $this->POST =
            array(
                "product_id" => $SessionPost["product_id"]
               ,"discount_or_new_price" => $SessionPost["discount_or_new_price"]
               ,"rv_from" => $SessionPost["rv_from"]
               ,"cost" => $SessionPost["cost"]
               ,"CostTypeId" => $SessionPost["CostTypeId"]
            );
    }

    /**
     *                         DB
     */
    function initFormData()
    {
        global $application;
        $this->POST = array();
        $settings = modApiFunc("Quantity_Discounts", "getSettings");
//        $this->POST["status"] = modApiFunc("Shipping_Module_Flat_Shipping_Rates", "isActive");
        $request = &$application->getInstance('Request');
        $product_id = $request->getValueByKey('product_id');

        $this->POST["product_id"] = $product_id;
        $this->POST["discount_or_new_price"] = 1;
        $this->POST["rv_from"] = "";
        $this->POST["cost"] = "";
        $this->POST["CostTypeId"] = "1"; //CURRENCY, not PERCENT
        $this->ViewState =
            array(
                "hasCloseScript"  => "false"
               ,"FormSubmitValue" => "save"
                 );
    }


    /**
     * @return String Return html code for hidden form fields representing @var $this->ViewState array.
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
     *
     */
    function outputErrors()
    {
        //:                                     .    ,                               .

        //:                                                     Action update_flat_shipping_rates
        //           View. (manage_quantity_discounts_az)
        global $application;
        if (!is_array($this->ErrorsArray) || sizeof($this->ErrorsArray) == 0)
        {
            return;
        }
        $result = "";
        $application->registerAttributes(array('ErrorIndex', 'Error'));
        $this->_error_index = 0;
        foreach ($this->ErrorsArray as $key => $value)
        {
            $vars = array();
            //                                                     .
            if($key == "INTERSECTION_COORD")
            {
                continue;
            }
            if($key == "INTERSECTION_RATE_ID")
            {
               if(isset($this->ErrorsArray["INTERSECTION_COORD"]) &&
                 !empty($this->ErrorsArray["INTERSECTION_COORD"]))
               {
                   $error = "INTERSECTION_IN_SINGLE_POINT";
               }
               else
               {
                   $error = "INTERSECTION_IN_MORE_THAN_ONE_POINT";
               }
            }
            else
            {
                $error = $key;
            }

            $this->_error_index++;
            $this->_error = $this->MessageResources->getMessage("DISCOUNTS_" . $error);
            $result .= $this->mTmplFiller->fill("quantity_discounts/list_quantity_discounts/", "quantity_discount_error.tpl.html", array());
        }
        return $result;
    }

    /**
     *
     */
    function outputStatus()
    {
        global $application;
        $retval = "";
        $status = $this->POST["status"];
        $this->_Template_Contents = array(
                                          "Active"          => ($status)? "checked":""
                                         ,"ActiveMessage"   => $this->MessageResources->getMessage("DISCOUNTS_MODULE_STATUS_ACTIVE")
                                         ,"Inactive"        => ($status)? "":"checked"
                                         ,"InactiveMessage" => $this->MessageResources->getMessage("DISCOUNTS_MODULE_STATUS_INACTIVE")
                                         );
        $application->registerAttributes($this->_Template_Contents);
        $retval.= $this->mTmplFiller->fill("quantity_discounts/list_quantity_discounts/", "quantity_discount_status.tpl.html", array());
        return $retval;
    }

    /**
     *                  :           /
     */
    function outputCostType()
    {
        $USD = "";
        $PROC = "";
        $FIXED = "";
        switch($this->POST["CostTypeId"])
        {
            case 1: $USD = "SELECTED"; break;
            case 2: $PROC = "SELECTED"; break;
            case 3: $FIXED = "SELECTED"; break;
            default: break;
        }

        $retval = "<option value=\"1\" ".$USD. ">".modApiFunc("Localization", "getCurrencySign")." ".getMsg('QUANTITY_DISCOUNTS', 'DISCOUNTS_LABEL_007')."</option>";
        $retval.= "<option value=\"2\" ".$PROC.">% ".getMsg('QUANTITY_DISCOUNTS', 'DISCOUNTS_LABEL_007')."</option>";
        $retval.= "<option value=\"3\" ".$FIXED.">".getMsg('QUANTITY_DISCOUNTS', 'DISCOUNTS_LABEL_FIXED_PRICE')."</option>";
        return $retval;
    }

    /**
     *                :                /
     */
    function outputNewPriceOrDiscount()
    {
        $DISCOUNT = "";
        $NEW_PRICE = "";
        if ($this->POST["discount_or_new_price"] == 1)
        {
            $DISCOUNT = "SELECTED";
        }
        else
        {
            $NEW_PRICE = "SELECTED";
        }
        $retval = "<option value=\"1\" ".$DISCOUNT. ">".getMsg('QUANTITY_DISCOUNTS', 'DISCOUNTS_LABEL_007')."</option>";
        $retval.= "<option value=\"2\" ".$NEW_PRICE.">".getMsg('QUANTITY_DISCOUNTS', 'MNG_PRODUCT_QUANTITY_DISCOUNTS_SALE_PRICE_LABEL')."</option>";
        return $retval;
    }

    /**
     *
     */
    function outputQuantityDiscountRates()
    {
        global $application;
        $ratesTable = modApiFunc("Quantity_Discounts", "getQuantityDiscountRates", false);
        $customerGroups = modApiFunc("Customer_Account", "getGroups");

        $product_id = $this->POST["product_id"];
        if(!isset($ratesTable[$product_id]))
        {
            $ratesTable = array();
        }
        else
        {
            $ratesTable = $ratesTable[$product_id];
        }

        $retval = "";

        $precision = modApiFunc("Localization", "getPrecision", "currency" /*           */);
        $EPS = $precision;
        $cost_type_id = 1; /* :                                   */

        $RateN = 1;

        $product = &$application->getInstance('CProductInfo',$product_id);
        $product_sale_price = $product->getProductTagValue("SalePrice", PRODUCTINFO_NOT_LOCALIZED_DATA);

        foreach($ratesTable as $rate)
        {
            $RateCssClass ="";
            $FromCssClass = "";
            $ToCssClass = "";

            if(isset($this->ErrorsArray["INTERSECTION_RATE_ID"]))
            {
                if($rate["id"] == $this->ErrorsArray["INTERSECTION_RATE_ID"])
                {
                    //                                                     .
                    $RateCssClass = "rate_intervals_intersection_error";
                    if(isset($this->ErrorsArray["INTERSECTION_COORD"]) &&
                             $this->ErrorsArray["INTERSECTION_COORD"] != NULL)
                    {
                        //                                          .
                        $val = $this->ErrorsArray["INTERSECTION_COORD"];
                        if($rate["rv_from"] == $val)
                        {
                            $FromCssClass = "rate_value_single_point_intersecion_coord_class";
                        }
                    }
                }
            }

            $this->CurrentRateUnitUnitValue = modApiFunc("Localization", "getUnitTypeValue", "item");

            $Cost = "";
            if($rate["cost"] === NULL)
            {
                $Cost = $this->MessageResources->getMessage("DISCOUNTS_COST_N_A_TEXT");
            }
            else
            {
                 $Cost = getMsg('QUANTITY_DISCOUNTS', 'QUANTITY_DISCOUNTS_NEW_PRICE_LABEL') . " - ";
                 switch($rate["cost_type_id"])
                 {
                     case "1" /* CURRENCY */:
                     {
                         $Cost .= modApiFunc("Localization", "currency_format",
                                    (float)($product_sale_price - $rate["cost"]))
                                  . "<br>(" . getMsg('QUANTITY_DISCOUNTS', 'QUANTITY_DISCOUNTS_DISCOUNT_LABEL')
                                  . " - " . modApiFunc("Localization", "currency_format", $rate["cost"]) . ")";
                         break;
                     }
                     case "2" /* PERCENT */:
                     {
                         $Cost .= modApiFunc("Localization", "currency_format",
                                    (float)($product_sale_price - $product_sale_price * $rate["cost"] / 100))
                                  . "<br>(" . getMsg('QUANTITY_DISCOUNTS', 'QUANTITY_DISCOUNTS_DISCOUNT_LABEL')
                                  . " - " . modApiFunc("Localization", "num_format", $rate["cost"]) . "%)";
                         break;
                     }
                     case "3" /* FIXED PRICE */:
                     {
                         $Cost .= modApiFunc("Localization", "currency_format", $rate["cost"]);
                         break;
                     }
                     default:
                     {
                         $Cost = "";
                         //: report error.
                     }
                 }
            }

            $rv_to = modApiFunc("Quantity_Discounts", "getNextFromValue", $product_id, $rate["rv_from"], $rate["customer_group_id"]);
            $rv_to = (isset($rv_to) ? (($rv_to==1 || $rv_to==-PRICE_N_A) ? 'infinity' : $rv_to-1) : 'infinity');
            $this->_Rate = array
            (
                "RateID" => ($rate["id"] === NULL) ? "" : $rate["id"]
               ,"RateN" => $RateN++
               ,"StatusActive" => ($rate["b_active"] == "1") ? "SELECTED" : ""
               ,"StatusDisabled" => ($rate["b_active"] == "1") ? "" : "SELECTED"
               ,"b_RateActiveChecked" => ($rate["b_active"] == "1") ? "CHECKED" : ""
               ,"RateCssClass" => ($rate["id"] === NULL) ? "virtual_rate_class" : $RateCssClass
               ,"FromCssClass" => $FromCssClass

               ,"RateValueFrom" =>  modApiFunc
                (
                    "Localization",
                    "FloatToFormatStr",
                    $rate["rv_from"],
                    $this->CurrentRateUnitValue
                ) . " " . $this->CurrentRateUnitUnitValue

               ,"ToCssClass" => $ToCssClass
               ,"RateValueTo" =>  modApiFunc
                (
                    "Localization",
                    "FloatToFormatStr",
                    $rv_to,
                    $this->CurrentRateUnitValue
                ) . ($rv_to != 'infinity' ? " ".$this->CurrentRateUnitUnitValue : "")
                //:            PERCENT
               ,"Cost" => $Cost
               ,"DelCheckboxCssClass" => ($rate["id"] === NULL) ? "display_none" : ""
               ,"CustomerGroup" => $customerGroups[$rate["customer_group_id"]]
            );
            $application->registerAttributes($this->_Rate);
            $retval .= $this->mTmplFiller->fill("quantity_discounts/list_quantity_discounts/", "quantity_discount_list_item.tpl.html", array());
        }

        $min_n = 5;
        if(sizeof($ratesTable) < $min_n)
        {
            $n = sizeof($ratesTable) == 0 ? $min_n - 1 : $min_n - sizeof($ratesTable);
            for($i = 0 ; $i < $n; $i++)
            {
                $retval .= $this->mTmplFiller->fill("quantity_discounts/list_quantity_discounts/", "quantity_discount_list_item_empty.tpl.html", array());
            }
        }

        return $retval;
    }

    function outputResultMessage()
    {
        global $application;
        if(modApiFunc("Session","is_set","ResultMessage"))
        {
            $msg=modApiFunc("Session","get","ResultMessage");
            modApiFunc("Session","un_set","ResultMessage");
            $template_contents=array(
                "ResultMessage" => getMsg('QUANTITY_DISCOUNTS',$msg)
            );
            $this->_Template_Contents=$template_contents;
            $application->registerAttributes($this->_Template_Contents);
            $this->mTmplFiller = &$application->getInstance('TmplFiller');
            return $this->mTmplFiller->fill("quantity_discounts/list_quantity_discounts/", "result-message.tpl.html",array());
        }
        else
        {
            return "";
        }
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

        $this->CurrentRateUnitValue = "item";
        //             ?
        $this->CurrentRateUnit = "Quantity";

        $request = new Request();
        $request->setView('manage_quantity_discounts_az');
        $request->setAction("update_quantity_discounts");
        $form_action = $request->getURL();
        $settings = modApiFunc("Discounts", "getSettings");

        $product = &$application->getInstance('CProductInfo',$this->POST["product_id"]);

        $template_contents = array(
                                    "EditQuantityDiscountForm"    => $HtmlForm1->genForm($form_action, "POST", "EditQuantityDiscountForm")
                                   ,"HiddenArrayViewState"  => $this->outputViewState()
                                   ,"Errors"                => $this->outputErrors()
//                                   ,"ModuleStatusFieldName" => $this->MessageResources->getMessage('MODULE_STATUS_FIELD_NAME')
//                                   ,"ModuleStatusField"     => $this->outputStatus()

                                   ,"Items"                 => $this->outputQuantityDiscountRates()

//                                      "ElementValue"=> modApiFunc("Localization", "FloatToFormatStr", $rate_unit_value["value"], $this->CurrentRateUnitValue)
//                                      "PatternType" => $this->CurrentRateUnitValue
//                                      "Format" => modApiFunc("Localization", "format_settings_for_js", $this->CurrentRateUnitValue)
                                   ,"RvPatternType"         => $this->CurrentRateUnitValue
                                   ,"RvFormat"              => modApiFunc("Localization", "format_settings_for_js", $this->CurrentRateUnitValue)
                                   ,"RvFromValue"           => modApiFunc("Localization", "FloatToFormatStr", $this->POST["rv_from"], $this->CurrentRateUnitValue)

                                    //:       ,                PERCENT
                                   ,"CostPatternType"         => "currency"
                                    //:       ,                PERCENT
                                   ,"CostFormat"              => modApiFunc("Localization", "format_settings_for_js", "currency")
                                    //:       ,                PERCENT
                                   ,"CostValue"             => modApiFunc("Localization", "FloatToFormatStr", $this->POST["cost"], "currency")
                                   ,"CostTypeOptions"       => $this->outputCostType()
                                   ,"Local_ProductBookmarks" => getProductBookmarks('quantity_discounts', $this->POST["product_id"])
                                   ,"Local_ProductId"       => $this->POST["product_id"]
                                   ,"Local_ProductSalePrice"=> $product->getProductTagValue("SalePrice")
                                   ,"ProductName"           => $product->getProductTagValue("Name")
                                   ,"NewPriceOrDiscountOptions" => $this->outputNewPriceOrDiscount()
                                   ,"ResultMessage"         => $this->outputResultMessage()
                                   ,"MembershipDropDown"    => modApiFunc('Customer_Account','getGroupsDropDown')
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
        return $output.$this->mTmplFiller->fill("quantity_discounts/list_quantity_discounts/", "quantity_discount_list.tpl.html",array());
    }

    function getTag($tag)
    {
        global $application;
        switch($tag)
        {
            case 'ProductInfoLink':
                $cz_layouts = LayoutConfigurationManager::static_get_cz_layouts_list();
                LayoutConfigurationManager::static_activate_cz_layout(array_shift(array_keys($cz_layouts)));
                $request = new CZRequest();
                $request->setView  ( 'ProductInfo' );
                $request->setAction( 'SetCurrentProduct' );
                $request->setKey   ( 'prod_id', $this->POST["product_id"]);
                $request->setProductID($this->POST["product_id"]);
                $value = $request->getURL();
                break;
            case "ErrorIndex":
                $value = $this->_error_index;
                break;
            case "Error":
                $value = $this->_error;
                break;
            default:
                $value = getKeyIgnoreCase($tag, $this->_Template_Contents);
                if($value === NULL)
                {
                    $value = getKeyIgnoreCase($tag, $this->_Rate);
                }
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
     * View state structure. Comes from action.
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
     * List of error ids. Comes from action.
     */
    var $ErrorsArray;
    var $ErrorMessages;

    var $_Template_Contents;
    var $_Rate;

    var $MessageResources;
    var $_error_index;
    var $_error;

    /**#@-*/
}
?>