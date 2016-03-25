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
 * discounts_manage_global_discounts_az view
 *
 * @package
 * @author Vadim Lyalikov
 */
class discounts_manage_global_discounts_az
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
    function discounts_manage_global_discounts_az()
    {
        global $application;
        $this->MessageResources = &$application->getInstance('MessageResources',"discounts-messages", "AdminZone");

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
                "rv_from" => $SessionPost["rv_from"]
               ,"rv_to" => $SessionPost["rv_to"]
               ,"cost" => $SessionPost["cost"]
               ,"CostTypeId" => $SessionPost["CostTypeId"]
            );
    }

    /**
     *                         DB
     */
    function initFormData()
    {
        $this->POST = array();
        $settings = modApiFunc("Discounts", "getSettings");
//        $this->POST["status"] = modApiFunc("Shipping_Module_Flat_Shipping_Rates", "isActive");
        $this->POST["rv_from"] = "";
        $this->POST["rv_to"] = "";
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
        //           View. (discounts_manage_global_discounts_az)
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
            $result .= $this->mTmplFiller->fill("discounts/list_global_discounts/", "global_discount_error.tpl.html", array());
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
        $retval.= $this->mTmplFiller->fill("discounts/list_global_discounts/", "global_discount_status.tpl.html", array());
        return $retval;
    }

    /**
     *                :           /
     */
    function outputCostType()
    {
        $USD = "";
        $PROC = "";
        if ($this->POST["CostTypeId"] == 1)
        {
            $USD = "SELECTED";
        }
        else
        {
            $PROC = "SELECTED";
        }
        $retval = "<option value=\"1\" ".$USD.">".modApiFunc("Localization", "getCurrencySign")."</option>";
        $retval.= "<option value=\"2\" ".$PROC.">%</option>";
        return $retval;
    }

    /**
     *
     */
    function outputGlobalDiscountRates()
    {
        global$application;
        $ratesTable = modApiFunc("Discounts", "getGlobalDiscountRates", false);

        $retval = "";

        $precision = modApiFunc("Localization", "getPrecision", "currency" /*           */);
        $EPS = $precision;
        $cost_type_id = 1; /* :                                   */

        if(sizeof($ratesTable) == 0)
        {
            $retval .= $this->mTmplFiller->fill("discounts/list_global_discounts/", "global_discount_list_item_no_items.tpl.html", array());
        }

        $RateN = 1;
        foreach($ratesTable as  $rate)
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
                        if($rate["rv_to"] == $val)
                        {
                            $ToCssClass = "rate_value_single_point_intersecion_coord_class";
                        }
                    }
                }
            }

            $this->CurrentRateUnitUnitValue = modApiFunc("Localization", "getUnitTypeValue", "currency");

            $Cost = "";
            if($rate["cost"] === NULL)
            {
                $Cost = $this->MessageResources->getMessage("DISCOUNTS_COST_N_A_TEXT");
            }
            else
            {
                 switch($rate["cost_type_id"])
                 {
                     case "1" /* CURRENCY */:
                     {
                         $Cost = modApiFunc("Localization", "currency_format", $rate["cost"]);
                         break;
                     }
                     case "2" /* PERCENT */:
                     {
                         $Cost = modApiFunc("Localization", "num_format", $rate["cost"]) . "%";
                         break;
                     }
                     default:
                     {
                         $Cost = "";
                         //: report error.
                     }
                 }
            }

            $this->_Rate = array
            (
                "RateID" => ($rate["id"] === NULL) ? "" : $rate["id"]
               ,"RateN" => $RateN++
               ,"StatusActive" => ($rate["b_active"] == "1") ? "SELECTED" : ""
               ,"StatusDisabled" => ($rate["b_active"] == "1") ? "" : "SELECTED"
               ,"b_RateActiveChecked" => ($rate["b_active"] == "1") ? "CHECKED" : ""
               ,"RateCssClass" => ($rate["id"] === NULL) ? "virtual_rate_class" : $RateCssClass
               ,"FromCssClass" => $FromCssClass

               ,"RateValueFrom" => $this->CurrentRateUnitUnitValue . modApiFunc
                (
                    "Localization",
                    "FloatToFormatStr",
                    $rate["rv_from"],
                    $this->CurrentRateUnitValue
                )

               ,"ToCssClass" => $ToCssClass
               ,"RateValueTo" => $this->CurrentRateUnitUnitValue . modApiFunc
                (
                    "Localization",
                    "FloatToFormatStr",
                    $rate["rv_to"],
                    $this->CurrentRateUnitValue
                )
                //:            PERCENT
               ,"Cost" => $Cost
               ,"DelCheckboxCssClass" => ($rate["id"] === NULL) ? "display_none" : ""
            );
            $application->registerAttributes($this->_Rate);
            $retval .= $this->mTmplFiller->fill("discounts/list_global_discounts/", "global_discount_list_item.tpl.html", array());
        }

        $min_n = 5;
        if(sizeof($ratesTable) < $min_n)
        {
            $n = sizeof($ratesTable) == 0 ? $min_n - 1 : $min_n - sizeof($ratesTable);
            for($i = 0 ; $i < $n; $i++)
            {
                $retval .= $this->mTmplFiller->fill("discounts/list_global_discounts/", "global_discount_list_item_empty.tpl.html", array());
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
                "ResultMessage" => getMsg('DISCOUNTS',$msg)
            );
            $this->_Template_Contents=$template_contents;
            $application->registerAttributes($this->_Template_Contents);
            $this->mTmplFiller = &$application->getInstance('TmplFiller');
            return $this->mTmplFiller->fill("discounts/list_global_discounts/", "result-message.tpl.html",array());
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

        $this->CurrentRateUnitValue = "currency";
        $this->CurrentRateUnit = "Price";

        $request = new Request();
        $request->setView('discounts_manage_global_discounts_az');
        $request->setAction("update_global_discounts");
        $form_action = $request->getURL();
        $settings = modApiFunc("Discounts", "getSettings");
        $template_contents = array(
                                    "EditGlobalDiscountForm"    => $HtmlForm1->genForm($form_action, "POST", "EditGlobalDiscountForm")
                                   ,"HiddenArrayViewState"  => $this->outputViewState()
                                   ,"Errors"                => $this->outputErrors()
//                                   ,"ModuleStatusFieldName" => $this->MessageResources->getMessage('MODULE_STATUS_FIELD_NAME')
//                                   ,"ModuleStatusField"     => $this->outputStatus()

                                   ,"Items"                 => $this->outputGlobalDiscountRates()

//                                      "ElementValue"=> modApiFunc("Localization", "FloatToFormatStr", $rate_unit_value["value"], $this->CurrentRateUnitValue)
//                                      "PatternType" => $this->CurrentRateUnitValue
//                                      "Format" => modApiFunc("Localization", "format_settings_for_js", $this->CurrentRateUnitValue)
                                   ,"RvPatternType"         => $this->CurrentRateUnitValue
                                   ,"RvFormat"              => modApiFunc("Localization", "format_settings_for_js", $this->CurrentRateUnitValue)

                                   ,"RvFromValue"           => modApiFunc("Localization", "FloatToFormatStr", $this->POST["rv_from"], $this->CurrentRateUnitValue)
                                   ,"RvToValue"             => modApiFunc("Localization", "FloatToFormatStr", $this->POST["rv_to"], $this->CurrentRateUnitValue)

                                    //:       ,                PERCENT
                                   ,"CostPatternType"         => "currency"
                                    //:       ,                PERCENT
                                   ,"CostFormat"              => modApiFunc("Localization", "format_settings_for_js", "currency")
                                    //:       ,                PERCENT
                                   ,"CostValue"             => modApiFunc("Localization", "FloatToFormatStr", $this->POST["cost"], "currency")
                                   ,"CostTypeOptions"       => $this->outputCostType()
                                   ,"ResultMessage"         => $this->outputResultMessage()
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
        return $output.$this->mTmplFiller->fill("discounts/list_global_discounts/", "global_discount_list.tpl.html",array());
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