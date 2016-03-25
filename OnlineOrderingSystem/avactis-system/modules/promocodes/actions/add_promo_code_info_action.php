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
 * PromoCodes module.
 * This action is responsible for adding new promo code.
 *
 * @package PromoCodes
 * @access  public
 * @author  Vadim Lyalikov
 */
class AddPromoCodeInfo extends AjaxAction
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
    function AddPromoCodeInfo()
    {
        $this->pcid = false;
    }

    /**
     * Get Action name
     * @return string Action name
     */
    function ACT_NM()
    {
        return 'AddPromoCodeInfo';
    }

    /**
     * Validate user input. Check "New Promo Code Campaign Name".
     */
    function isValidPromoCodeCampaignName($data)//, &$error_message_text)
    {
        $retval = (is_string($data) &&
                _ml_strlen(trim($data)) > 0 &&
                _ml_strlen(trim($data)) < 129);
        return $retval;
    }

    /**
     * Validate user input. Check "New Promo Code PROMO CODE".
     */
    function isValidPromoCodePromoCode($data)//, &$error_message_text)
    {
        $retval = (is_string($data) &&
                _ml_strlen(trim($data)) > 0 &&
                _ml_strlen(trim($data)) < 129);
        return $retval;
    }

    function isPromoCodePromoCodeUnique($data)
    {
        $promo_code_list = modApiFunc("PromoCodes", "getPromoCodesListFullAZ");
        foreach($promo_code_list as $pc_info)
        {
        	if(_ml_strtolower($pc_info["promo_code"]) == _ml_strtolower($data))
        	{
        		return $pc_info["id"];
        	}
        }
        return true;
    }

    /**
     * Validate user input. Check "New Promo Code 'Ignore Other Discounts'".
     */
    function isValidPromoCodeBIgnoreOtherDiscounts($data)
    {
        $retval = ($data == "2" || $data == "1");
        return $retval;
    }

    /**
     * Validate user input. Check "New Promo Code 'Status'".
     */
    function isValidPromoCodeStatus($data)
    {
        $retval = ($data == "2" || $data == "1");
        return $retval;
    }

    function isValidPromoCodeMinSubtotal($data)
    {
    	$retval = ($data);
    	return $retval;
    }

    /**
     * Validate user input. Check "Image Description".
     */
    function isValidImageDescription($data)//, &$error_message_text)
    {
        $retval = (is_string($data) &&
                _ml_strlen(trim($data)) < 257);
        return $retval;
    }

    /**
     * Validate user input. Check "Meta Keywords" and "Meta Description".
     */
    function isValidMetaField($data)
    {
        $retval = (
                _ml_strlen(trim($data)) <= 1024
                  );
        return $retval;
    }

    function saveDataToDB($data)
    {
        return modApiFunc("PromoCodes", "insertPromoCode",
                   $data["PromoCodeCampaignName"],
                   $data["PromoCodePromoCode"],
                   $data["PromoCodeBIgnoreOtherDiscounts"],
                   $data["PromoCodeStatus"],
                   $data["PromoCodeMinSubtotal"],
                   $data["PromoCodeDiscountCost"],
                   $data["PromoCodeDiscountCostTypeID"],
                   $data["PromoCodeStartDateFYear"],
                   $data["PromoCodeStartDateMonth"],
                   $data["PromoCodeStartDateDay"],
                   $data["PromoCodeEndDateFYear"],
                   $data["PromoCodeEndDateMonth"],
                   $data["PromoCodeEndDateDay"],
                   $data["PromoCodeTimesToUse"],
                   $data["FreeShipping"],
                   $data["FreeHandling"],
                   $data["StrictCart"]
               );
    }

    /**
     * Action: AddCat
     *
     * Add new category record to db, or save current form state parameters, like uploaded images names etc.
     * <p> Subactions are
     * <ul>
     *     <li>"Save"</li>
     * </ul>
     * The main action is "Save". Any other subaction may occur 0 or any number of times. Subactions change the "View State".
     */
    function onAction()
    {
        global $application;
        $request = $application->getInstance('Request');

        $SessionPost = array();
        if(modApiFunc('Session', 'is_Set', 'SessionPost'))
        {
            _fatal(array( "CODE" => "CORE_050"), __CLASS__, __FUNCTION__);
        }

        $pcid = false;
        $SessionPost = $_POST;

        switch($request->getValueByKey('FormSubmitValue'))
        {
            case "Save" :
            {
                $nErrors = 0;
                $SessionPost["ViewState"]["ErrorsArray"] = array();

                loadCoreFile('html_form.php');
                $HtmlForm1 = new HtmlForm();

                $error_message_text = "";

                if(!$this->isValidPromoCodeCampaignName($SessionPost["PromoCodeCampaignName"], $error_message_text))
                {
                    $nErrors++;
                    $SessionPost["ViewState"]["ErrorsArray"]["PromoCodeCampaignName"] = new ActionMessage(array("ERR_AZ_PROMOCODES_ADD_PROMO_CODE_001"));
                }

                if(!$this->isValidPromoCodePromoCode($SessionPost["PromoCodePromoCode"], $error_message_text))
                {
                    $nErrors++;
                    $SessionPost["ViewState"]["ErrorsArray"]["PromoCodePromoCode"] = new ActionMessage(array("ERR_AZ_PROMOCODES_ADD_PROMO_CODE_002"));
                }

                $present_pcid = $this->isPromoCodePromoCodeUnique($SessionPost["PromoCodePromoCode"]);
                if ($present_pcid !== true && $this->pcid != $present_pcid)
                {
                    $nErrors++;
                    $SessionPost["ViewState"]["ErrorsArray"]["PromoCodePromoCode"] = new ActionMessage(array("ERR_AZ_PROMOCODES_ADD_PROMO_CODE_015"));
                }

                if(!$this->isValidPromoCodeBIgnoreOtherDiscounts($SessionPost["PromoCodeBIgnoreOtherDiscounts"], $error_message_text))
                {
                    $nErrors++;
                    $SessionPost["ViewState"]["ErrorsArray"]["PromoCodeBIgnoreOtherDiscounts"] = new ActionMessage(array("ERR_AZ_PROMOCODES_ADD_PROMO_CODE_003"));
                }

                if(!$this->isValidPromoCodeStatus($SessionPost["PromoCodeStatus"], $error_message_text))
                {
                    $nErrors++;
                    $SessionPost["ViewState"]["ErrorsArray"]["PromoCodeStatus"] = new ActionMessage(array("ERR_AZ_PROMOCODES_ADD_PROMO_CODE_004"));
                }

                $SessionPost["PromoCodeMinSubtotal"] = modApiFunc("Localization", "FormatStrToFloat", $SessionPost["PromoCodeMinSubtotal"], "currency");
                $SessionPost["PromoCodeDiscountCost"] = modApiFunc("Localization", "FormatStrToFloat", $SessionPost["PromoCodeDiscountCost"], "weight");

                //          ,                            .                                                  -
                //                 ,                 .
                $discount_cost_type_id = $SessionPost["PromoCodeDiscountCostTypeID"];
                if($discount_cost_type_id != "1" &&
                   $discount_cost_type_id != "2")
                {
                   	$SessionPost["PromoCodeDiscountCostTypeID"] = 1; //FLAT RATE, not PERCENT.
                }

                //                                                             ,
                //                      .                  ,                                  .
                //                 ,                 .
                $year_num = 10;
                $start_date_fyear = $SessionPost["PromoCodeStartDateFYear"];
                if($start_date_fyear < date("Y") &&
                   $start_date_fyear > date("Y") + $year_num - 1)
                {
                   	$SessionPost["PromoCodeStartDateFYear"] = date("Y");
                }

                $start_date_month = $SessionPost["PromoCodeStartDateMonth"];
                if($start_date_month < 1 &&
                   $start_date_month > 12)
                {
                   	$SessionPost["PromoCodeStartDateMonth"] = 1;
                }

                if($SessionPost["PromoCodeStartDateDay"] < 1 &&
                   $SessionPost["PromoCodeStartDateDay"] > 31)
                {
                   	$SessionPost["PromoCodeStartDateDay"] = 1;
                }
                //                                                       .                 ,
                //                        -                                      .
                $daysinmonth = date("t", mktime(0, 0, 0, $start_date_month, 1, $start_date_fyear));
                if($SessionPost["PromoCodeStartDateDay"] > $daysinmonth)
                {
                	$SessionPost["PromoCodeStartDateDay"] = $daysinmonth;
                }


                $end_date_fyear = $SessionPost["PromoCodeEndDateFYear"];
                if($end_date_fyear < date("Y") &&
                   $end_date_fyear > date("Y") + $year_num - 1)
                {
                   	$SessionPost["PromoCodeEndDateFYear"] = date("Y");
                }

                $end_date_month = $SessionPost["PromoCodeEndDateMonth"];
                if($end_date_month < 1 &&
                   $end_date_month > 12)
                {
                   	$SessionPost["PromoCodeEndDateMonth"] = 1;
                }

                if($SessionPost["PromoCodeEndDateDay"] < 1 &&
                   $SessionPost["PromoCodeEndDateDay"] > 31)
                {
                   	$SessionPost["PromoCodeEndDateDay"] = 1;
                }
                //                                                       .                 ,
                //                        -                                      .
                $daysinmonth = date("t", mktime(0, 0, 0, $end_date_month, 1, $end_date_fyear));
                if($SessionPost["PromoCodeEndDateDay"] > $daysinmonth)
                {
                	$SessionPost["PromoCodeEndDateDay"] = $daysinmonth;
                }

                if(mktime(0, 0, 0, $SessionPost["PromoCodeEndDateMonth"],   $SessionPost["PromoCodeEndDateDay"],   $SessionPost["PromoCodeEndDateFYear"]) <
                   mktime(0, 0, 0, $SessionPost["PromoCodeStartDateMonth"], $SessionPost["PromoCodeStartDateDay"], $SessionPost["PromoCodeStartDateFYear"])
                  )
                {
                	//      .                                                                    .
                    $nErrors++;
                    $SessionPost["ViewState"]["ErrorsArray"]["PromoCodeEndDateFYear"] = new ActionMessage(array("ERR_AZ_PROMOCODES_ADD_PROMO_CODE_016"));
                }

				if(!ctype_digit($SessionPost["PromoCodeTimesToUse"]))
				{
                    $nErrors++;
                    $SessionPost["ViewState"]["ErrorsArray"]["PromoCodeTimesToUse"] = new ActionMessage(array("ERR_AZ_PROMOCODES_ADD_PROMO_CODE_014"));
				}

                $SessionPost["FreeShipping"] = number_format($SessionPost["FreeShipping"], 0, '', '');
                $SessionPost["FreeHandling"] = number_format($SessionPost["FreeHandling"], 0, '', '');
                $SessionPost["StrictCart"] = number_format($SessionPost["StrictCart"], 0, '', '');

                $nErrors = sizeof($SessionPost["ViewState"]["ErrorsArray"]);

                if ($nErrors == 0)
                {
                    unset($SessionPost["ViewState"]["ErrorsArray"]);
                    $pcid = $this->saveDataToDB($SessionPost);
                    $SessionPost["ViewState"]["hasCloseScript"] = "false";
                }
                break;
            }
            default : _fatal(array( "CODE" => "CORE_051"), __CLASS__, __FUNCTION__, $request->getValueByKey('FormSubmitValue'));
        }

        if ($pcid !== false)
            $SessionPost['PromoCodeID'] = $pcid;

        modApiFunc('Session', 'set', 'PromoCodeAdded', true);
        modApiFunc('Session', 'set', 'SessionPost', $SessionPost);
        // get view name by action name.
        $this->redirect($pcid);
    }

    /**
     * Redirect after action
     */
    function redirect($pcid)
    {
        global $application;

        $request = new Request();

        if ($pcid === false)
        {
            $request->setView('AddPromoCode');
        }
        else
        {
            $request->setView('EditPromoCode');
            $request->setAction('SetEditablePromoCode');
            $request->setKey('PromoCode_id', $pcid);
        }
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