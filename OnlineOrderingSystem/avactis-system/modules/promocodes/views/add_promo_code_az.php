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
 * "PromoCodes -> Add Promo Code" View.
 *
 * @package PromoCodes
 * @access  public
 * @author  Vadim Lyalikov
 *
 */


class AddPromoCode
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * The view constructor.
     * About data flow. All data is transferred
     * <p> Action -> View :
     * <p> Through session variable @var $SessionPost (created from POST data), especially it's $SessionPost["ViewState"] array, containing current View state information. State does not include such information like already inputed name, description values. It includes variables, determining the view structure: table or list, image or input field etc. @see @var SessionPost.
     * <p> View -> Action :
     * <p> Through POST data. All form'related session data is removed while processing view output.
     */
    function AddPromoCode()
    {
        global $application;

        $this->MessageResources = &$application->getInstance('MessageResources',"promo-codes-messages", "AdminZone");

        $this->terminator_outed = false;

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


    function copyFormData()
    {
        // eliminate copying on construction
        $SessionPost = modApiFunc("Session", "get", "SessionPost");
        $this->ViewState =
            $SessionPost["ViewState"];

        //Remove some data, that should not be resent to action, from ViewState.
        if(isset($this->ViewState["ErrorsArray"]) &&
           count($this->ViewState["ErrorsArray"]) > 0)
        {
            $this->ErrorsArray = $this->ViewState["ErrorsArray"];
            unset($this->ViewState["ErrorsArray"]);
        }

        $this->POST  =
            array(
                "PromoCodeCampaignNameText" => ($SessionPost["PromoCodeCampaignName"]),
                "PromoCodePromoCodeText" => ($SessionPost["PromoCodePromoCode"]),
                "PromoCodeBIgnoreOtherDiscountsValue" => ($SessionPost["PromoCodeBIgnoreOtherDiscounts"]),
                "PromoCodeStatusValue" => ($SessionPost["PromoCodeStatus"]),
                "PromoCodeMinSubtotalText" => ($SessionPost["PromoCodeMinSubtotal"]),
                "PromoCodeDiscountCostText" => ($SessionPost["PromoCodeDiscountCost"]),
                "PromoCodeDiscountCostTypeIDValue" => ($SessionPost["PromoCodeDiscountCostTypeID"]),

                "PromoCodeStartDateFYearValue" => ($SessionPost["PromoCodeStartDateFYear"]),
                "PromoCodeStartDateMonthValue" => ($SessionPost["PromoCodeStartDateMonth"]),
                "PromoCodeStartDateDayValue" => ($SessionPost["PromoCodeStartDateDay"]),

                "PromoCodeEndDateFYearValue" => ($SessionPost["PromoCodeEndDateFYear"]),
                "PromoCodeEndDateMonthValue" => ($SessionPost["PromoCodeEndDateMonth"]),
                "PromoCodeEndDateDayValue" => ($SessionPost["PromoCodeEndDateDay"]),

                "PromoCodeTimesToUseText" => ($SessionPost["PromoCodeTimesToUse"]),

                "FreeShipping" => ($SessionPost["FreeShipping"]),
                "FreeHandling" => ($SessionPost["FreeHandling"]),
                "StrictCart"   => ($SessionPost["StrictCart"])
            );
    }

    function initFormData()
    {
        $this->ViewState =
            array(
                "hasCloseScript" => "false",
                 );
        $this->POST  =
            array(
                "PromoCodeCampaignNameText" => "",
                "PromoCodePromoCodeText" => "",
                "PromoCodeBIgnoreOtherDiscountsValue" => 2, //NO, apply both
                "PromoCodeStatusValue" => 2, //NO, disabled
                "PromoCodeMinSubtotalText" => "0",
                "PromoCodeDiscountCostText" => "0",
                "PromoCodeDiscountCostTypeIDValue" => 1, //FLAT RATE, not percent

                "PromoCodeStartDateFYearValue" => date("Y"), //current date
                "PromoCodeStartDateMonthValue" => date("m"),
                "PromoCodeStartDateDayValue" => date("d"),

                "PromoCodeEndDateFYearValue" => date("Y"),
                "PromoCodeEndDateMonthValue" => date("m"),
                "PromoCodeEndDateDayValue" => date("d"),

                "PromoCodeTimesToUseText" => "0",

                "FreeShipping" => PROMO_CODE_NO_ATTENTION_TO_FREE_SHIPPING,    // no free shipping
                "FreeHandling" => PROMO_CODE_NO_ATTENTION_TO_FREE_HANDLING,    // no free handling
                "StrictCart"   => PROMO_CODE_STRICT_CART                       // demand strict cart
            );
    }

    /**
     * @return String Return a href link to "Promo Codes Navigator" view.
     */
    function getLinkToPromoCodesNavigator($cid)
    {
        $_request = new Request();
        $_request->setView  ( 'PromoCodesNavigationBar' );
        return $_request->getURL();
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

    function outputSubtitle()
    {
        global $application;
        loadCoreFile('html_form.php');
        $HtmlForm1 = new HtmlForm;
        $this->_Template_Contents = array(
                           "BookmarksBlock"   => $this->outputBookmarksBlock('details', 1),
                           "SubmitSaveScript" => $HtmlForm1->genSubmitScript("AddPromoCodeForm")
                    );
        $application->registerAttributes($this->_Template_Contents);
        return $this->mTmplFiller->fill("promo_codes/add_promo_code/", "subtitle.tpl.html", array());
    }

//==== functions used in EditPromoCode_AZ, stubs here =================
    function outputPromoCodeId()
    {
        return "";
    }

    function outputPromoCodeSummary()
    {
        return "";
    }

    function outputEffectiveAreaLaconic()
    {
        return '';
    }

    function outputPromoCodeEffectiveAreaDetails()
    {
        return '';
    }

    function outputOrderList()
    {
        return "";
    }
//===================================================================

    function outputViewStateConstants()
    {
        loadCoreFile('html_form.php');
        $HtmlForm1 = new HtmlForm();
        $retval = "<input type=\"hidden\"" . $HtmlForm1->genHiddenField("asc_action", "AddPromoCodeInfo") . ">";
        return $retval;
    }

    /**
     * @return String Return html code representing @var $this->ErrorsArray array.
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
    		$result .= $this->mTmplFiller->fill("promo_codes/add_promo_code/", "error.tpl.html", array());
    	}
    	return $result;
    }

    function outputBIgnoreOtherDiscounts()
    {
    	$selected_index = $this->POST["PromoCodeBIgnoreOtherDiscountsValue"];
    	$options = array
    	(
    	    array("value" => 1, "contents" => getMsg('PROMOCODES', "PROMOCODES_MODULE_IGNORE_OTHER_DISCOUNTS_TEXT_YES")),
    	    array("value" => 2, "contents" => getMsg('PROMOCODES', "PROMOCODES_MODULE_IGNORE_OTHER_DISCOUNTS_TEXT_NO"))
    	);

        $HtmlForm1 = new HtmlForm();
        $value = $HtmlForm1->genDropdownSingleChoice(array
            (
                "onChange"=> "",
                "select_name"=> "PromoCodeBIgnoreOtherDiscounts",
                "values" => $options,
                "selected_value" => $selected_index
            )
        );

        return $value;
    }

    function outputStatus()
    {
    	$selected_index = $this->POST["PromoCodeStatusValue"];
    	$options = array
    	(
    	    array("value" => 1, "contents" => getMsg('PROMOCODES', "PROMOCODES_MODULE_STATUS_ACTIVE")),
    	    array("value" => 2, "contents" => getMsg('PROMOCODES', "PROMOCODES_MODULE_STATUS_INACTIVE"))
    	);

        $HtmlForm1 = new HtmlForm();
        $value = $HtmlForm1->genDropdownSingleChoice(array
            (
                "onChange"=> "",
                "select_name"=> "PromoCodeStatus",
                "values" => $options,
                "selected_value" => $selected_index
            )
        );

        return $value;
    }

    function outputDiscountCostTypeID()
    {
    	$selected_index = $this->POST["PromoCodeDiscountCostTypeIDValue"];
    	$options = array
    	(
    	    array("value" => 1, "contents" => modApiFunc("Localization", "getCurrencySign")),
    	    array("value" => 2, "contents" => getMsg('PROMOCODES', "PROMOCODES_MODULE_PERCENT_SIGN"))
    	);

        $HtmlForm1 = new HtmlForm();
        $value = $HtmlForm1->genDropdownSingleChoice(array
            (
                "onChange"=> "",
                "select_name"=> "PromoCodeDiscountCostTypeID",
                "values" => $options,
                "selected_value" => $selected_index
            )
        );

        return $value;
    }

    function outputFYear($prefix)
    {
    	$selected_index = $this->POST["PromoCode".$prefix."FYearValue"];
        $num_of_years = 10;
    	$options = array();
        $current_year = date("Y");
        for($i=0; $i< $num_of_years; $i++)
        {
            $options[] = array("value" => $current_year + $i, "contents" => $current_year + $i);
        }

        $HtmlForm1 = new HtmlForm();
        $value = $HtmlForm1->genDropdownSingleChoice(array
            (
                "onChange"=> "",
                "select_name"=> "PromoCode".$prefix."FYear",
                "values" => $options,
                "selected_value" => $selected_index
            )
        );

        return $value;
    }

    function outputMonth($prefix)
    {
    	$selected_index = $this->POST["PromoCode".$prefix."MonthValue"];

    	$options = array();
        $moths = modApiFunc("Checkout", "getMonthNames");
        foreach($moths as $id => $name)
        {
            $options[] = array("value" => $id, "contents" => $name);
        }

        $HtmlForm1 = new HtmlForm();
        $value = $HtmlForm1->genDropdownSingleChoice(array
            (
                "onChange"=> "",
                "select_name"=> "PromoCode".$prefix."Month",
                "values" => $options,
                "selected_value" => $selected_index
            )
        );

        return $value;
    }

    function outputDay($prefix)
    {
    	$selected_index = $this->POST["PromoCode".$prefix."DayValue"];
        $num_of_days = 31;
    	$options = array();
        for($i=1; $i<= $num_of_days; $i++)
        {
            $options[] = array("value" => $i, "contents" => $i);
        }

        $HtmlForm1 = new HtmlForm();
        $value = $HtmlForm1->genDropdownSingleChoice(array
            (
                "onChange"=> "",
                "select_name"=> "PromoCode".$prefix."Day",
                "values" => $options,
                "selected_value" => $selected_index
            )
        );

        return $value;
    }

    function outputOrders()
    {
        return "";
    }

    /**
     * returns Strict Cart select options
     *
     */
    function outputFreeShippingOptions()
    {
        $options = array(
             PROMO_CODE_NO_ATTENTION_TO_FREE_SHIPPING   => getMsg('PROMOCODES', 'PROMO_CODE_FREE_SHIPPING_OPTION_0')
            ,PROMO_CODE_GRANTS_FREE_SHIPPING            => getMsg('PROMOCODES', 'PROMO_CODE_FREE_SHIPPING_OPTION_1')
            ,PROMO_CODE_FORBIDS_FREE_SHIPPING           => getMsg('PROMOCODES', 'PROMO_CODE_FREE_SHIPPING_OPTION_2')
        );

        $output = '';
        foreach ($options as $id => $opt)
        {
            $checked = '';
            if ($this->POST['FreeShipping'] == $id)
            {
                $checked = ' selected';
            }
            $output .= "<option value='$id'$checked>$opt</option>";
        }

        return $output;
    }

    /**
     * returns Strict Cart select options
     *
     */
    function outputFreeHandlingOptions()
    {
        $options = array(
             PROMO_CODE_NO_ATTENTION_TO_FREE_HANDLING   => getMsg('PROMOCODES', 'PROMO_CODE_FREE_HANDLING_OPTION_0')
            ,PROMO_CODE_GRANTS_FREE_HANDLING            => getMsg('PROMOCODES', 'PROMO_CODE_FREE_HANDLING_OPTION_1')
            ,PROMO_CODE_FORBIDS_FREE_HANDLING           => getMsg('PROMOCODES', 'PROMO_CODE_FREE_HANDLING_OPTION_2')
        );

        $output = '';
        foreach ($options as $id => $opt)
        {
            $checked = '';
            if ($this->POST['FreeHandling'] == $id)
            {
                $checked = ' selected';
            }
            $output .= "<option value='$id'$checked>$opt</option>";
        }

        return $output;
    }

    /**
     * returns Strict Cart select options
     *
     */
    function outputStrictCartOptions()
    {
        $checked_1 = ' selected';
        $checked_0 = '';

        if ($this->POST['StrictCart'] == PROMO_CODE_DIRTY_CART)
        {
            $checked_0 = ' selected';
            $checked_1 = '';
        }

        $output = "<option value='".PROMO_CODE_STRICT_CART."'$checked_1>".getMsg('PROMOCODES', 'PROMO_CODE_STRICT_CART_OPTION_1')."</option>";
        $output .= "<option value='".PROMO_CODE_DIRTY_CART."'$checked_0>".getMsg('PROMOCODES', 'PROMO_CODE_STRICT_CART_OPTION_0')."</option>";
        return $output;
    }

    /**
     * Return the "PromoCodes -> Add Promo Code" view html code.
     *
     */
    function output()
    {
        global $application;
        loadCoreFile('html_form.php');
        $this->Hints = &$application->getInstance('Hint');
        $HtmlForm1 = new HtmlForm();

        $this->MessageResources = &$application->getInstance('MessageResources',"promo-codes-messages", "AdminZone");
        if($this->ViewState["hasCloseScript"] == "true")
        {
            modApiFunc("application", "closeChild_UpdateParent");
        }

//        $promo_code_summary = ;
        $promo_code_order_list = $this->outputOrderList();
        $this->_Template_Contents = array
        (
            "OrderList" => $promo_code_order_list
        );
        $application->registerAttributes($this->_Template_Contents);

        $template_contents= array(
                           "Subtitle"            => $this->outputSubtitle(),
                           "Errors"              => $this->outputErrors(),
                           "PromoCodeId"         => $this->outputPromoCodeId(),
                           "PromoCodeSummary"    => $this->outputPromoCodeSummary(),
                           "PromoCodeEffectiveAreaLaconic" => $this->outputEffectiveAreaLaconic(),
                           "OrderList"           => $promo_code_order_list,

                           "PromoCodeCampaignNameError"  => isset($this->ErrorMessages['ERR_AZ_PROMOCODES_ADD_PROMO_CODE_001']) ? $this->ErrorMessages['ERR_AZ_PROMOCODES_ADD_PROMO_CODE_001'] : "",
                           "PromoCodeCampaignNameInputStyleClass" => isset($this->ErrorMessages['ERR_AZ_PROMOCODES_ADD_PROMO_CODE_001']) ? "error" : "",
                           "PromoCodeCampaignName"         => $HtmlForm1->genInputTextField("128", "PromoCodeCampaignName", "75", prepareHTMLDisplay($this->POST["PromoCodeCampaignNameText"])),
                           "PromoCodeCampaignFieldHint" => $this->Hints->getHintLink(array('PROMO_CODE_CAMPAIGN_NAME_NAME', 'promo-codes-messages')),

                           "PromoCodePromoCodeError"  => isset($this->ErrorMessages['ERR_AZ_PROMOCODES_ADD_PROMO_CODE_002']) ? $this->ErrorMessages['ERR_AZ_PROMOCODES_ADD_PROMO_CODE_002'] : "",
                           "PromoCodePromoCodeInputStyleClass" => isset($this->ErrorMessages['ERR_AZ_PROMOCODES_ADD_PROMO_CODE_002']) ? "error" : "",
                           "PromoCodePromoCode"       => $HtmlForm1->genInputTextField("128", "PromoCodePromoCode", "75", prepareHTMLDisplay($this->POST["PromoCodePromoCodeText"])),
                           "PromoCodePromoCodeFieldHint" => $this->Hints->getHintLink(array('PROMO_CODE_PROMO_CODE_NAME', 'promo-codes-messages')),

                           "PromoCodeStatusError"  => isset($this->ErrorMessages['ERR_AZ_PROMOCODES_ADD_PROMO_CODE_004']) ? $this->ErrorMessages['ERR_AZ_PROMOCODES_ADD_PROMO_CODE_004'] : "",
                           "PromoCodeStatusInputStyleClass" => isset($this->ErrorMessages['ERR_AZ_PROMOCODES_ADD_PROMO_CODE_004']) ? "error" : "",
                           "PromoCodeStatus"       => $this->outputStatus(),
                           "PromoCodeStatusFieldHint" => $this->Hints->getHintLink(array('PROMO_CODE_STATUS_NAME', 'promo-codes-messages')),

                           "PromoCodeMinSubtotalError"  => isset($this->ErrorMessages['ERR_AZ_PROMOCODES_ADD_PROMO_CODE_005']) ? $this->ErrorMessages['ERR_AZ_PROMOCODES_ADD_PROMO_CODE_005'] : "",
                           "PromoCodeMinSubtotalInputStyleClass" => isset($this->ErrorMessages['ERR_AZ_PROMOCODES_ADD_PROMO_CODE_005']) ? "error" : "",
                           "PromoCodeMinSubtotal"         => $HtmlForm1->genInputTextField("10", "PromoCodeMinSubtotal", "10", prepareHTMLDisplay($this->POST["PromoCodeMinSubtotalText"])),
                           "PromoCodeMinSubtotalFormat" => modApiFunc("Localization", "format_settings_for_js", "currency"),
                           "PromoCodeMinSubtotalSign" => modApiFunc("Localization", "getCurrencySign"),
                           "PromoCodeMinSubtotalFieldHint" => $this->Hints->getHintLink(array('PROMO_CODE_MIN_SUBTOTAL_NAME', 'promo-codes-messages')),

                           "PromoCodeDiscountCostError"  => isset($this->ErrorMessages['ERR_AZ_PROMOCODES_ADD_PROMO_CODE_006']) ? $this->ErrorMessages['ERR_AZ_PROMOCODES_ADD_PROMO_CODE_006'] : "",
                           "PromoCodeDiscountCostInputStyleClass" => isset($this->ErrorMessages['ERR_AZ_PROMOCODES_ADD_PROMO_CODE_006']) ? "error" : "",
                           "PromoCodeDiscountCost"         => $HtmlForm1->genInputTextField("10", "PromoCodeDiscountCost", "10", prepareHTMLDisplay($this->POST["PromoCodeDiscountCostText"])),
                           "PromoCodeDiscountCostFormat" => modApiFunc("Localization", "format_settings_for_js", "currency"),
                           "PromoCodeDiscountCostFieldHint" => $this->Hints->getHintLink(array('PROMO_CODE_DISCOUNT_COST_NAME', 'promo-codes-messages')),

                           "PromoCodeDiscountCostTypeIDError"  => isset($this->ErrorMessages['ERR_AZ_PROMOCODES_ADD_PROMO_CODE_007']) ? $this->ErrorMessages['ERR_AZ_PROMOCODES_ADD_PROMO_CODE_007'] : "",
                           "PromoCodeDiscountCostTypeIDInputStyleClass" => isset($this->ErrorMessages['ERR_AZ_PROMOCODES_ADD_PROMO_CODE_007']) ? "error" : "",
                           "PromoCodeDiscountCostTypeID"       => $this->outputDiscountCostTypeID(),

                           "PromoCodeStartDateFYearError"  => isset($this->ErrorMessages['ERR_AZ_PROMOCODES_ADD_PROMO_CODE_008']) ? $this->ErrorMessages['ERR_AZ_PROMOCODES_ADD_PROMO_CODE_008'] : "",
                           "PromoCodeStartDateFYearInputStyleClass" => isset($this->ErrorMessages['ERR_AZ_PROMOCODES_ADD_PROMO_CODE_008']) ? "error" : "",
                           "PromoCodeStartDateFYear"       => $this->outputFYear("StartDate"),
                           "PromoCodeStartDateFieldHint" => $this->Hints->getHintLink(array('PROMO_CODE_START_DATE_NAME', 'promo-codes-messages')),

                           "PromoCodeStartDateMonthError"  => isset($this->ErrorMessages['ERR_AZ_PROMOCODES_ADD_PROMO_CODE_009']) ? $this->ErrorMessages['ERR_AZ_PROMOCODES_ADD_PROMO_CODE_009'] : "",
                           "PromoCodeStartDateMonthInputStyleClass" => isset($this->ErrorMessages['ERR_AZ_PROMOCODES_ADD_PROMO_CODE_009']) ? "error" : "",
                           "PromoCodeStartDateMonth"       => $this->outputMonth("StartDate"),

                           "PromoCodeStartDateDayError"  => isset($this->ErrorMessages['ERR_AZ_PROMOCODES_ADD_PROMO_CODE_010']) ? $this->ErrorMessages['ERR_AZ_PROMOCODES_ADD_PROMO_CODE_010'] : "",
                           "PromoCodeStartDateDayInputStyleClass" => isset($this->ErrorMessages['ERR_AZ_PROMOCODES_ADD_PROMO_CODE_010']) ? "error" : "",
                           "PromoCodeStartDateDay"       => $this->outputDay("StartDate"),

                           "PromoCodeEndDateFYearError"  => isset($this->ErrorMessages['ERR_AZ_PROMOCODES_ADD_PROMO_CODE_011']) ? $this->ErrorMessages['ERR_AZ_PROMOCODES_ADD_PROMO_CODE_011'] : "",
                           "PromoCodeEndDateFYearInputStyleClass" => isset($this->ErrorMessages['ERR_AZ_PROMOCODES_ADD_PROMO_CODE_011']) ? "error" : "",
                           "PromoCodeEndDateFYear"       => $this->outputFYear("EndDate"),
                           "PromoCodeEndDateFieldHint" => $this->Hints->getHintLink(array('PROMO_CODE_END_DATE_NAME', 'promo-codes-messages')),

                           "PromoCodeEndDateMonthError"  => isset($this->ErrorMessages['ERR_AZ_PROMOCODES_ADD_PROMO_CODE_012']) ? $this->ErrorMessages['ERR_AZ_PROMOCODES_ADD_PROMO_CODE_012'] : "",
                           "PromoCodeEndDateMonthInputStyleClass" => isset($this->ErrorMessages['ERR_AZ_PROMOCODES_ADD_PROMO_CODE_012']) ? "error" : "",
                           "PromoCodeEndDateMonth"       => $this->outputMonth("EndDate"),

                           "PromoCodeEndDateDayError"  => isset($this->ErrorMessages['ERR_AZ_PROMOCODES_ADD_PROMO_CODE_013']) ? $this->ErrorMessages['ERR_AZ_PROMOCODES_ADD_PROMO_CODE_013'] : "",
                           "PromoCodeEndDateDayInputStyleClass" => isset($this->ErrorMessages['ERR_AZ_PROMOCODES_ADD_PROMO_CODE_013']) ? "error" : "",
                           "PromoCodeEndDateDay"       => $this->outputDay("EndDate"),

                           "PromoCodeTimesToUseError"  => isset($this->ErrorMessages['ERR_AZ_PROMOCODES_ADD_PROMO_CODE_014']) ? $this->ErrorMessages['ERR_AZ_PROMOCODES_ADD_PROMO_CODE_014'] : "",
                           "PromoCodeTimesToUseInputStyleClass" => isset($this->ErrorMessages['ERR_AZ_PROMOCODES_ADD_PROMO_CODE_014']) ? "error" : "",
                           "PromoCodeTimesToUse"         => $HtmlForm1->genInputTextField("10", "PromoCodeTimesToUse", "10", prepareHTMLDisplay($this->POST["PromoCodeTimesToUseText"])),
                           "PromoCodeTimesToUseFormat" => modApiFunc("Localization", "format_settings_for_js", "item"),
                           "PromoCodeTimesToUseFieldHint" => $this->Hints->getHintLink(array('PROMO_CODE_TIMES_TO_USE_NAME', 'promo-codes-messages')),

                           "PromoCodeBIgnoreOtherDiscountsError"  => isset($this->ErrorMessages['ERR_AZ_PROMOCODES_ADD_PROMO_CODE_003']) ? $this->ErrorMessages['ERR_AZ_PROMOCODES_ADD_PROMO_CODE_003'] : "",
                           "PromoCodeBIgnoreOtherDiscountsInputStyleClass" => isset($this->ErrorMessages['ERR_AZ_PROMOCODES_ADD_PROMO_CODE_003']) ? "error" : "",
                           "PromoCodeBIgnoreOtherDiscounts"       => $this->outputBIgnoreOtherDiscounts(),
                           "PromoCodeBIgnoreOtherDiscountsFieldHint" => $this->Hints->getHintLink(array('PROMO_CODE_B_IGNORE_OTHER_DISCOUNTS_NAME', 'promo-codes-messages')),

                           "PromoCodeOffersFreeShippingFieldHint" => $this->Hints->getHintLink(array('PROMO_CODE_OFFERS_FREE_SHIPPING_NAME', 'promo-codes-messages')),
                           "PromoCodeOffersFreeShippingOptions"   => $this->outputFreeShippingOptions(),

                           "PromoCodeOffersFreeHandlingFieldHint" => $this->Hints->getHintLink(array('PROMO_CODE_OFFERS_FREE_HANDLING_NAME', 'promo-codes-messages')),
                           "PromoCodeOffersFreeHandlingOptions"   => $this->outputFreeHandlingOptions(),

                           "PromoCodeStrictCartFieldHint" => $this->Hints->getHintLink(array('PROMO_CODE_STRICT_CART_NAME', 'promo-codes-messages')),
                           "PromoCodeStrictCartOptions"   => $this->outputStrictCartOptions(),

                           "AddPromoCodeForm"     => $HtmlForm1->genForm(modApiFunc("application", "getPagenameByViewname","PromoCodesNavigationBar",-1,-1,'AdminZone'), "POST", "AddPromoCodeForm"),
                           "HiddenFormSubmitValue"=> $HtmlForm1->genHiddenField("FormSubmitValue", "Save"),
                           "HiddenArrayViewStateConstants"=> $this->outputViewStateConstants(),
                           "HiddenArrayViewState"=> $this->outputViewState(),

                           "SubmitSaveScript" => $HtmlForm1->genSubmitScript("AddPromoCodeForm")
                    );

        $this->_Template_Contents = $template_contents;
        $application->registerAttributes($this->_Template_Contents);

        $SpecMessageResources = &$application->getInstance('MessageResources');
        //: correct error codes
        $output = modApiFunc('TmplFiller', 'fill',
                              './../../js/','validate.msgs.js.tpl',
                              array(
                                    "STRING1024"=> $SpecMessageResources->getMessage( new ActionMessage(array('CATADD_001')) ),
                                    "STRING128"=> $SpecMessageResources->getMessage( new ActionMessage(array('CATADD_002')) ),
                                    "STRING256"=> $SpecMessageResources->getMessage( new ActionMessage(array('CATADD_003')) ),
                                    "STRING512"=> $SpecMessageResources->getMessage( new ActionMessage(array('CATADD_004')) ),
                                    "INTEGER" => $SpecMessageResources->getMessage( new ActionMessage(array('PRDADD_001')))
                                   ,"FLOAT"   => $SpecMessageResources->getMessage( new ActionMessage(array('PRDADD_002')))
                                   ,"CURRENCY"=> addslashes($SpecMessageResources->getMessage( new ActionMessage(array('CURRENCY_FIELD',
                                                         modApiFunc("Localization", "FloatToFormatStr", 12.35, "currency"),
                                                         modApiFunc("Localization", "FloatToFormatStr", 34, "currency"),
                                                         modApiFunc("Localization", "FloatToFormatStr", 99.00, "currency")))))
                                   ,"WEIGHT"   => addslashes($SpecMessageResources->getMessage( new ActionMessage(array('WEIGHT_FIELD',
                                                         modApiFunc("Localization", "FloatToFormatStr", 23.325, "weight"),
                                                         modApiFunc("Localization", "FloatToFormatStr", 34, "weight"),
                                                         modApiFunc("Localization", "FloatToFormatStr", 99.2, "weight")))))
                                   ,"ITEM"     => $SpecMessageResources->getMessage( new ActionMessage(array('ITEM_FIELD')))
                                   )
                            );

        return $output.$this->mTmplFiller->fill("promo_codes/add_promo_code/", "list.tpl.html",array());
    }

    /**
     * @                      AddPromoCode->getTag.
     */
    function getTag($tag)
    {
        global $application;
        $value = getKeyIgnoreCase($tag, $this->_Template_Contents);
        if ($value == null)
        {
            switch ($tag)
        	{
        	    case 'ErrorIndex':
        	        $value = $this->_error_index;
        	        break;

        	    case 'Error':
        	        $value = $this->_error;
        	        break;
        	};
        }

        return $value;
    }
    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    function gen_bmOnClick($bm_location)
    {
        $code = "window.location='";
        $request = new Request();
        $request->setView($bm_location['view']);
        if(isset($bm_location['action']))
            $request->setAction($bm_location['action']);
        if(isset($bm_location['keys']))
            foreach($bm_location['keys'] as $key => $value)
                $request->setKey($key,$value);
        $code .= $request->getURL();
        $code .= "';";
        return $code;
    }

    function outputBookmarks()
    {
        global $application;

        $html_code = "";

        foreach($this->bms as $page => $bm)
        {
            $tpl_content = array(
                "bmClass" => ($page == $this->page) ? 'active' : ((strstr($this->status_depends[$this->page.'_'.$this->page_status],$page.'_notavail')) ? 'notavail disabled' : 'inactive')
               ,"bmIcon" => $bm['icon'] . ((@strstr($this->status_depends[$this->page.'_'.$this->page_status],$page.'_notavail')) ? '-na' : '')
               ,"bmText" => $this->MessageResources->getMessage($bm['title'])
               ,"bmOnClick" => ($page == $this->page or @strstr($this->status_depends[$this->page.'_'.$this->page_status],$page.'_notavail')) ? '' : $this->gen_bmOnClick($bm['location'])
               ,"bmName" => $page
            );
            $tpl_file = 'bookmark';

            $this->_Template_Contents=$tpl_content;
            $application->registerAttributes($this->_Template_Contents);
            $this->mTmplFiller = &$application->getInstance('TmplFiller');
            $html_code .= $this->mTmplFiller->fill("promo_codes/bookmarks/", "{$tpl_file}.tpl.html",array());
        }

        return $html_code;
    }

    function outputBookmarksBlock()
    {
        global $application;

        $this->page=func_get_arg(0);
        $this->entity_id=func_get_arg(1);
        if(func_num_args()==3)
            $this->page_status=func_get_arg(2);
        else
            $this->page_status='add';

        $this->_initBookmarks();

        $tpl_content = array(
            "bmBGColor" => 'transparent'
           ,"Bookmarks" => $this->outputBookmarks()
           ,"RightSpace" => ($this->_need_right_space) ? '<td width="100%" class="bookmarks_space">&nbsp;</td>' : ''
        );

        $this->_Template_Contents=$tpl_content;
        $application->registerAttributes($this->_Template_Contents);
        $this->mTmplFiller = &$application->getInstance('TmplFiller');
        return $this->mTmplFiller->fill("promo_codes/bookmarks/", "container.tpl.html",array());
    }

    function _initBookmarks()
    {
        $pcid = 0;
        if (isset($this->POST["PromoCodeID"]))
            $pcid = $this->POST["PromoCodeID"];

        $this->bms = array('details' => array(
                                'title' => 'PROMO_DETAILS'
                               ,'icon' => 'p-details'
                               ,'location' => array(
                                    'view' => 'EditPromoCode'
                                   ,'action' => 'SetEditablePromoCode'
                                   ,'keys' => array(
                                                'PromoCode_id' => $pcid
                                              )
                                )
                           ),
                           'area' => array(
                                 'title' => 'PROMO_AREA'
                                ,'icon' => 'p-categories'
                                ,'location' => array(
                                     'view' => 'EditPromoCodeArea'
                                    ,'action' => 'SetEditablePromoCode'
                                    ,'keys' => array(
                                               'PromoCode_id' => $pcid
                                              )
                                )
                           )
                     );

        $this->status_depends = array(
            'details_edit' => ''
           ,'details_add' => 'area_notavail'
        );

        $this->_need_right_space = true;
    }

    var $MR;
    var $bms;
    var $m_bms;
    var $page;
    var $entity_id;
    var $page_status;
    var $status_depends;
    var $_need_right_space;

    /**#@+
     * @access private
     */

    /**
     * Pointer to the template filler object.
     * Needs to track sequences of identical templates, like lists.
     */
    var $mTmplFiller;
    /**#@-*/

    /**
     * Pointer to the received from action or prepared FORM data.
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

    var $MessageResources;
    var $_error_index;
    var $_error;
}
?>