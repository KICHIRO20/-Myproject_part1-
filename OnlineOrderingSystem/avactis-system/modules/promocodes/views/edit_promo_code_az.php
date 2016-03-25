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

_use(dirname(__FILE__).'/add_promo_code_az.php');

/**
 * PromoCodes module.
 * "PromoCodes -> Edit Promo Code" View.
 *
 * @package PromoCodes
 * @access  public
 *
 */
class EditPromoCode extends AddPromoCode
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * The view constructor.
     */
    function EditPromoCode()
    {
        global $application;

        $this->mTmplFiller = &$application->getInstance('TmplFiller');

        if(modApiFunc("Session", "is_Set", "SessionPost"))
        {
            $this->copyFormData();
            modApiFunc('Session', 'un_Set', 'SessionPost');
        }
        else
        {
            $this->initDBFormData();
        }
    }

    /**
     *
     *
     * @return
     */
    function initDBFormData()
    {
            $this->pcid = modApiFunc('PromoCodes', 'getEditablePromoCodeID');

            $promoCodeInfo = modApiFunc('PromoCodes', 'getPromoCodeInfo', $this->pcid);
            $this->ViewState =
                array(
                    "hasCloseScript"   => "false"
                     );

            $this->POST  =
                array(
                    "PromoCodeID"            => $this->pcid,
	                "PromoCodeCampaignNameText" => $promoCodeInfo["campaign_name"],
	                "PromoCodePromoCodeText" => $promoCodeInfo["promo_code"],
	                "PromoCodeBIgnoreOtherDiscountsValue" => $promoCodeInfo["b_ignore_other_discounts"], //YES, ignore
	                "PromoCodeStatusValue" => $promoCodeInfo["status"], //YES, active
	                "PromoCodeMinSubtotalText" => $promoCodeInfo["min_subtotal"],
	                "PromoCodeDiscountCostText" => $promoCodeInfo["discount_cost"],
	                "PromoCodeDiscountCostTypeIDValue" => $promoCodeInfo["discount_cost_type_id"], //FLAT RATE, not percent

	                "PromoCodeStartDateFYearValue" => date("Y", strtotime($promoCodeInfo["start_date"])), //current date
	                "PromoCodeStartDateMonthValue" => date("m", strtotime($promoCodeInfo["start_date"])),
	                "PromoCodeStartDateDayValue" => date("d", strtotime($promoCodeInfo["start_date"])),

	                "PromoCodeEndDateFYearValue" => date("Y", strtotime($promoCodeInfo["end_date"])), //current date
	                "PromoCodeEndDateMonthValue" => date("m", strtotime($promoCodeInfo["end_date"])),
	                "PromoCodeEndDateDayValue" => date("d", strtotime($promoCodeInfo["end_date"])),

	                "PromoCodeTimesToUseText" => $promoCodeInfo["times_to_use"],

                    "FreeShipping" => $promoCodeInfo["free_shipping"],
                    "FreeHandling" => $promoCodeInfo["free_handling"],
                    "StrictCart"   => $promoCodeInfo["strict_cart"]
                );
    }

    /**
     *                 PromoCodes_AddPromoCode::copyFormData() -                     'PromoCodeID'          $this->POST
     */
    function copyFormData()
    {
        AddPromoCode::copyFormData();
        $SessionPost = modApiFunc("Session", "get", "SessionPost");
        if (isset($SessionPost['PromoCodeID']))
        {
            $this->POST['PromoCodeID'] = $SessionPost['PromoCodeID'];
            $this->pcid = $this->POST['PromoCodeID'];
        }
        else
        {
            global $application;
            $request = $application->getInstance('Request');
            $this->POST['PromoCodeID'] = $request->getValueByKey('PromoCode_id');
            $this->pcid = $this->POST['PromoCodeID'];
        }
    }

    /**
     *
     */
    function outputSubtitle()
    {
        global $application;
        loadCoreFile('html_form.php');
        $HtmlForm1 = new HtmlForm;
        $this->_Template_Contents = array(
                           "BookmarksBlock"   => $this->outputBookmarksBlock("details", 1, "edit"),
                           "SubmitSaveScript" => $HtmlForm1->genSubmitScript("AddPromoCodeForm")
                    );
        $application->registerAttributes($this->_Template_Contents);
        return $this->mTmplFiller->fill("promo_codes/edit_promo_code/", "subtitle.tpl.html",
                      array(
//                      "SubmitSaveScript" => $HtmlForm1->genSubmitScript("AddPromoCodeForm")
                           )
                      );
    }

    function outputPromoCodeId()
    {
        global $application;
        $obj = &$application->getInstance('MessageResources',"promo-codes-messages", "AdminZone");
        $PromoCodeID = $obj->getMessage( new ActionMessage('PROMO_CODE_ID_NAME'));

        $this->Hints = &$application->getInstance('Hint');
        $PromoCodeIdFieldHint = $this->Hints->getHintLink(array('PROMO_CODE_ID_NAME', 'promo-codes-messages'));

        $retval = "<label class='col-md-3 control-label'>".$PromoCodeID."*</label>";
        $retval.= "<div class='col-md-5'><div class='input-icon left'><i class='fa fa-life-ring' onclick=\"" . $PromoCodeIdFieldHint . "\" style='cursor:pointer;display:inline-block;'></i>";
        $retval.= "<span style='display:inline-block;width:65px;' class='form-control'>".$this->POST['PromoCodeID']."</span></div></div>";
        return $retval;
    }

    function outputPromoCodeSummary()
    {
        //TimesUsed, OrderIds, active/inactive text.
//        $pcid = modApiFunc('PromoCodes', 'getEditablePromoCodeID');
        $promoCodeInfo = modApiFunc('PromoCodes', 'getPromoCodeInfo', $this->pcid);

        //                       .
        $used = getMsg('PROMOCODES', 'PROMO_CODE_USED_NAME');
        $used = str_replace("{n}", $promoCodeInfo["times_to_use"] - $promoCodeInfo["times_used"], $used);

        //
        $inactive_because = "";
        $applicable = modApiFunc("PromoCodes", "isPromoCodeApplicableWithoutMinSubtotal", $this->pcid);
        $area_not_empty = modApiFunc("PromoCodes", "isPromoCodeEffectiveAreaNotEmpty", $this->pcid);
        if ($area_not_empty === false && $applicable === true)
            $applicable = PROMO_CODE_NOT_APPLICABLE_AREA;

        if($applicable !== true)
        {
            switch($applicable)
            {
                case PROMO_CODE_NOT_APPLICABLE_DATE:
                {
                    $inactive_because = getMsg('PROMOCODES', 'PROMO_CODE_NOT_APPLICABLE_DATE_MSG');
                    break;
                }
                case PROMO_CODE_NOT_APPLICABLE_TIMES_USED:
                {
                    $inactive_because = getMsg('PROMOCODES', 'PROMO_CODE_NOT_APPLICABLE_TIMES_USED_MSG');
                    break;
                }
                case PROMO_CODE_NOT_APPLICABLE_STATUS:
                {
                    $inactive_because = getMsg('PROMOCODES', 'PROMO_CODE_NOT_APPLICABLE_STATUS_MSG');
                    break;
                }
                case PROMO_CODE_NOT_APPLICABLE_AREA:
                {
                    $inactive_because = getMsg('PROMOCODES', 'PROMO_CODE_NOT_APPLICABLE_AREA_MSG');
                    break;
                }
                default:
                {
                    $inactive_because = getMsg('PROMOCODES', 'PROMO_CODE_NOT_APPLICABLE_DEFAULT_MSG');
                    break;
                }
             }
        }

        global $application;
        $this->_Template_Contents = array
        (
           "PromoCodeSummary" => $used . (empty($inactive_because) ? "" :  "<BR>" . $inactive_because)
        );
        $application->registerAttributes($this->_Template_Contents);
        return $this->mTmplFiller->fill("promo_codes/edit_promo_code/", "promo_code_summary.tpl.html",array());
    }

    function outputEffectiveAreaLaconic()
    {
        global $application;

        $area = getMsg('PROMOCODES', 'PROMO_CODE_EFFECTIVE_AREA_MSG');
        $affected = modApiFunc('PromoCodes', "getCatsProductsAffected", $this->pcid);
        $prod_num = (empty($affected['prods'])) ? 0 : count($affected['prods']);
        $cat_num = (empty($affected['cats'])) ? 0 : count($affected['cats']);
        $area = str_replace('{NCAT}', $cat_num, $area);
        $area = str_replace('{NPROD}', $prod_num, $area);

        if ($cat_num == 1)
            $area = str_replace('{CAT_LABEL}', getMsg('PROMOCODES', 'PROMO_CODE_CATEGORY_LABEL'), $area);
        else
            $area = str_replace('{CAT_LABEL}', getMsg('PROMOCODES', 'PROMO_CODE_CATEGORIES_LABEL'), $area);

        if ($prod_num == 1)
            $area = str_replace('{PRODUCT_LABEL}', getMsg('PROMOCODES', 'PROMO_CODE_PRODUCT_LABEL'), $area);
        else
            $area = str_replace('{PRODUCT_LABEL}', getMsg('PROMOCODES', 'PROMO_CODE_PRODUCTS_LABEL'), $area);

        $this->_Template_Contents = array
        (
           "PromoCodeEffectiveArea" => $area,
           "PromoCodeEffectiveAreaDetails" => $this->outputPromoCodeEffectiveAreaDetails($affected, $cat_num, $prod_num)
        );
        $application->registerAttributes($this->_Template_Contents);
        return $this->mTmplFiller->fill("promo_codes/edit_promo_code/", "promo_code_effective_area.tpl.html",array());
    }

    function outputPromoCodeEffectiveAreaDetails($affected, $cat_num, $prod_num)
    {
        global $application;

        if ($cat_num == 0 && $prod_num == 0)
            return '';

        $cat_list = array();
        $cat_str = '';
        if ($cat_num != 0)
        {
            loadClass("CCategoryInfo");
            foreach ($affected['cats'] as $cat)
            {
                $obj = new CCategoryInfo($cat);

                // CCategoryInfo::isCategoryIdCorrect() does not exist
                if ($obj->_fCategoryIDIsIncorrect !== true)
                {
                    $cat_list[] = $obj->getCategoryTagValue('Name');
                }
            }
            $cat_str = implode('<br />', $cat_list);
        }

        $prod_list = array();
        $prod_str = '';
        if ($prod_num != 0)
        {
            loadClass("CProductInfo");
            foreach ($affected['prods'] as $prod)
            {
                $obj = new CProductInfo($prod);

                if ($obj->isProductIdCorrect())
                {
                    $prod_list[] = $obj->getProductTagValue('Name');
                }
            }
            $prod_str = implode('<br />', $prod_list);
        }

        $this->_Template_Contents = array
        (
           "CategoriesAffectedList" => $cat_str,
           "ProductsAffectedList" => $prod_str
        );
        $application->registerAttributes($this->_Template_Contents);
        return $this->mTmplFiller->fill("promo_codes/edit_promo_code/", "promo_code_effective_area_details.tpl.html",array());
    }

    function outputOrders()
    {
        global $application;
        //                           .
//        $pcid = modApiFunc('PromoCodes', 'getEditablePromoCodeID');
        $orders = modApiFunc("PromoCodes", "getOrderCoupons", NULL, $this->pcid);
        $order_list = "";
        if(empty($orders))
        {
            $order_list = "<tr><td colspan='6' align='center' style='color: #AAAAAA;'>".getMsg('PROMOCODES', 'PROMO_CODE_NO_ORDERS_MSG')."</td></tr>";
        }
        else
        {
            foreach($orders as $order)
            {
                $order_info = modApiFunc("Checkout", "getOrderInfo", $order['order_id'], modApiFunc("Localization", "whichCurrencyToDisplayOrderIn", $order['order_id']));
                $this->_Template_Contents = array
                (
                    "OrderId" => $order_info['ID']
                   ,"OrderCustomerName" => $order_info["Billing"]["attr"]["Firstname"]["value"] . " " . $order_info["Billing"]["attr"]["Lastname"]["value"]
                   ,"OrderDate" => modApiFunc("Localization", "SQL_date_format", $order_info["Date"])
                   ,"OrderPriceTotal" => modApiFunc("Localization", "currency_format", $order_info["Total"])
                   ,"OrderStatus" => $order_info["Status"]
                   ,"OrderPaymentStatus" => $order_info["PaymentStatus"]
                );
                $application->registerAttributes($this->_Template_Contents);
                $order_list .= $this->mTmplFiller->fill("promo_codes/edit_promo_code/", "order.tpl.html",array());
            }
        }

        return $order_list;
    }

    function outputOrderList()
    {
        global $application;
        $promo_code_orders = $this->outputOrders();
        $this->_Template_Contents = array("Orders" => $promo_code_orders);
        $application->registerAttributes($this->_Template_Contents);
        $order_list = $this->mTmplFiller->fill("promo_codes/edit_promo_code/", "order_list.tpl.html",array());
        return $order_list;
    }

    /**
     *                     ViewState
     */
    function outputViewStateConstants()
    {
        //$retval = Catalog_AddPromoCode::outputViewStateConstants();
        loadCoreFile('html_form.php');
        $HtmlForm1 = new HtmlForm();
        $retval = "<input type=\"hidden\"" . $HtmlForm1->genHiddenField("asc_action", "UpdatePromoCodeInfo") . ">";
        $retval.= "<input type=\"hidden\"" . $HtmlForm1->genHiddenField("PromoCodeID", $this->POST["PromoCodeID"]) . ">";
        return $retval;
    }


    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    // currently viewed promocode id
    var $pcid;

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
     * array
     * (
     *     "hasCloseScript"  = "false"           //true/false
     *     "ErrorsArray"     =  array()          //true/false
     *     "LargeImage"      = "image.jpg"       //
     *     "SmallImage"      = "image_small.jpg" //
     * )
     */
    var $ViewState;

    /**
     * List of error ids. Comes from action.
     */
    var $ErrorsArray;
}
?>