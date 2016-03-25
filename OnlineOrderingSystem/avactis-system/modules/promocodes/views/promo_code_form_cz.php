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
 * "PromoCodes -> "PromoCodeForm" View.
 *
 * @package PromoCodes
 * @access  public
 *
 */
class PromoCodeForm
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     *                                                     .
     */
    function getTemplateFormat()
    {
        $format = array(
            'layout-file'        => 'promo-code-form-config.ini'
           ,'files' => array(
                'AddPromoCode'      => TEMPLATE_FILE_SIMPLE
               ,'RemovePromoCode'   => TEMPLATE_FILE_SIMPLE
            )
           ,'options' => array(
            )
        );
        return $format;
    }

    /**
     * Cart_Content constructor
     */
    function PromoCodeForm()
    {
        global $application;

        #
        $this->NoView = false;
        if ($application->issetBlockTagFatalErrors("PromoCodeForm"))
        {
            $this->NoView = true;
        }

        $this->pc_info = false;
        $this->applicable = false;
        if (modApiFunc("PromoCodes", "isPromoCodeIdSet") === true)
        {
            $this->pc_info = modApiFunc("PromoCodes", "getPromoCodeInfo", modApiFunc("PromoCodes", "getPromoCodeId"));
            $this->applicable = modApiFunc("PromoCodes", "isPromoCodeApplicableWithoutMinSubtotal", modApiFunc("PromoCodes", "getPromoCodeId"));
        }
    }

/**
 *                      /         promo code' .
 */
    function outputPromoCode()
    {
        global $application;

        //       AZ                       -                      .
        $promo_code_list = modApiFunc("PromoCodes", "getPromoCodesListFullCZ");
        $promo_always_show_param = modApiFunc('Settings','getParamValue','PROMO_CODE_PARAMS','ALWAYS_SHOW_PROMO_CODES');

        if(sizeof($promo_code_list) == 0            // no active coupons
            && $promo_always_show_param == "NO")    // option "show always"
        {
            $retval  = "";
        }
        else
        {
            //if(modApiFunc("Cart", "getPromoCodeId") == NULL)
            if(modApiFunc("PromoCodes", "isPromoCodeIdSet") === false)
            {
                //                         Promo Code
                $_template_tags = array('Local_AddPromoCode_URL' => "",
                                        'Local_AddPromoCode_Error' => "");

                $application->registerAttributes($_template_tags);
                $this->templateFiller = new TemplateFiller();
                $this->template = $application->getBlockTemplate('PromoCodeForm');
                $this->templateFiller->setTemplate($this->template);

                $retval = $this->templateFiller->fill("AddPromoCode");
            }
            else
            {
                //                    Promo Code
                $_template_tags = array('Local_PromoCode' => "",
                                        'Local_RemovePromoCode_URL' => "",
                                        'Local_PromoCodeDiscountedItemsListForCZ' => '',
                                        'Local_PromoCodeMinSubtotal' => "",
                                        'Local_PromoCodeDiscount' => "",
                                        'Local_PromoCodeDiscountText' => "",
                                        'Local_PromoCodeStrictCartAttitude' => '',
                                        'Local_PromoCodeShippingAttitude' => '',
                                        'Local_PromoCodeHandlingAttitude' => '',
                                        'Local_PromoCodeGlobalDiscountIgnored' => ''
                                );

                $application->registerAttributes($_template_tags);
                $this->templateFiller = new TemplateFiller();
                $this->template = $application->getBlockTemplate('PromoCodeForm');
                $this->templateFiller->setTemplate($this->template);

                $retval = $this->templateFiller->fill("RemovePromoCode");
            }
        }
        return $retval;
    }

    /**
     *
     *
     * @ $request->setView  ( '' ) -                     view
     */
    function output()
    {
        global $application;

        #
        if ($this->NoView)
        {
            $application->outputTagErrors(true, "PromoCodeForm", "Errors");
            return "";
        }
        else
        {
            $application->outputTagErrors(true, "PromoCodeForm", "Warnings");
        }

        $retval = $this->outputPromoCode();
        return $retval;
    }

    function getTag($tag)
    {
        global $application;
        $value = null;
        switch ($tag)
        {
            case 'Local_AddPromoCode_URL':
                $request = new Request();
                $request->setView  ( CURRENT_REQUEST_URL );
                $request->setAction( 'AddPromoCode' );
                #$request->setKey( 'promo_code', '' );
                $value = $request->getURL();
                break;

            case 'Local_AddPromoCode_Error':
                $AddPromoCodeError = modApiFunc("PromoCodes", "getAddPromoCodeError");
                modApiFunc("PromoCodes", "setAddPromoCodeError", "");
                if(!empty($AddPromoCodeError))
                {
                    $value = getMsg('SYS',$AddPromoCodeError);
                }
                else
                {
                    $value = "";
                }
                break;

            case 'Local_RemovePromoCode_URL':
                $request = new Request();
                $request->setView  ( CURRENT_REQUEST_URL );
                $request->setAction( 'RemovePromoCode' );
                $value = $request->getURL();
                break;

            case 'Local_PromoCode':
                $value = '';
                if($this->pc_info !== false)
                {
                    $value = prepareHTMLDisplay($this->pc_info["promo_code"]);
                }
                break;

            case 'Local_PromoCodeDiscountedItemsListForCZ':
                $value = '';
                if ($this->pc_info !== false
                    && $this->applicable === true)
                {
                    $home_present = false;
                    $cat_names = array();
                    $prod_names = array();

                    if ($this->pc_info['cats'] != null)
                    {
                        $cats = explode('|', $this->pc_info['cats']);
                        foreach ($cats as $cid)
                        {
                            $cat_obj = new CCategoryInfo($cid);
                            if ($cat_obj->_fCategoryIDIsIncorrect === true)
                                continue;

                            // Home category present, we do not need anything else
                            if ($cid == 1)
                            {
                                $home_present = true;
                                $cat_names = array();
                                $cat_names[] = cz_getMsg('PROMOCODE_HOME_CATEGORY_NAME_SUBST');
                                break;
                            }
                            // else just get the category name
                            else
                            {
                                $cat_names[] = $cat_obj->getCategoryTagValue('Name');
                            }
                        }
                    }

                    if ($this->pc_info['prods'] != null
                        && !$home_present
                    )
                    {
                        $prods = explode('|', $this->pc_info['prods']);
                        foreach ($prods as $pid)
                        {
                            $obj = new CProductInfo($pid);
                            if (!$obj->isProductIdCorrect())
                                continue;

                            $prod_names[] = $obj->getProductTagValue('Name');
                        }
                    }

                    if ($cat_names)
                    {
                        $cat_names_str = implode(', ', $cat_names);

                        if ($home_present)
                            $value .= $cat_names_str;
                        else if (count($cat_names) == 1)
                            $value .= cz_getMsg("PROMOCODE_CATEGORY_LABEL") . $cat_names_str;
                        else
                            $value .= cz_getMsg("PROMOCODE_CATEGORIES_LABEL") . $cat_names_str;

                        if ($prod_names)
                            $value .= cz_getMsg("PROMOCODE_AND_LABEL");
                    }
                    if ($prod_names)
                    {
                        $prod_names_str = implode(', ', $prod_names);

                        if (count($prod_names) == 1)
                            $value .= cz_getMsg("PROMOCODE_PRODUCT_LABEL") . $prod_names_str;
                        else
                            $value .= cz_getMsg("PROMOCODE_PRODUCTS_LABEL") . $prod_names_str;
                    }
                }
                break;

            case 'Local_PromoCodeMinSubtotal':
                $value = '';
                if ($this->pc_info !== false
                    && $this->applicable === true
                    && $this->pc_info["min_subtotal"] != 0)
                {
                    $min_subtotal = modApiFunc("Localization", "currency_format", $this->pc_info["min_subtotal"]);
                    $value = str_replace('{MINSUBTOTAL}', $min_subtotal, cz_getMsg('PROMOCODE_MINSUBTOTAL_NEEDED'));
                }
                break;

            case 'Local_PromoCodeDiscount':
                $value = '';
                if ($this->pc_info !== false
                    && $this->applicable === true)
                {
                    $o_subtotal = modApiFunc("Checkout", "getOrderPrice", "Subtotal", modApiFunc("Localization", "getMainStoreCurrency"));
                    $value = modApiFunc("Localization", "currency_format", modApiFunc("PromoCodes", "getPromoCodeDiscount", $o_subtotal, modApiFunc("PromoCodes", "getPromoCodeId"), array()));
                }

                if($value == "")
                {
                    $value = modApiFunc("Localization", "currency_format", PRICE_N_A);
                }
                break;

            case 'Local_PromoCodeDiscountText':
                //        "10%"     "$2"          ,
                //               .
                $value = "";
                if ($this->pc_info !== false
                    && $this->applicable === true)
                {
                    switch($this->pc_info["discount_cost_type_id"])
                    {
                        case 1 /* FLAT RATE */:
                        {
                            $value = modApiFunc("Localization", "currency_format", $this->pc_info["discount_cost"]);
                            break;
                        }
                        case 2 /* PERCENT */:
                        {
                            $value = modApiFunc("Localization", "num_format", $this->pc_info["discount_cost"]) . "%";
                            break;
                        }
                        default:
                        {
                            //: report error.
                            exit(1);
                        }
                    }
                }
                break;

            case 'Local_PromoCodeStrictCartAttitude':
                $value = '';
                if ($this->pc_info
                    && $this->pc_info['strict_cart'] == PROMO_CODE_STRICT_CART)
                {
                    // get product IDs and their categories' IDs
                    $prod = $coupon_cart = array();
                    $promo_codes = $application->getInstance('PromoCodes');
                    $order_cart = modApiFunc("Cart","getCartContentExt");
                    foreach ($order_cart as $product)
                    {
                        $coupon_cart[] = array(
                          'id' => $product["ID"],
                          'cat' => $product['CategoryID'],
                          'total' => $product['TotalExcludingTaxes']
                        );
                    }

                    // coupon not applicable due to not meeting coupon conditions
                    if (false == $promo_codes->isPromoCodeAreaApplicable($this->pc_info, $coupon_cart))
                    {
                        $value = cz_getMsg('PROMOCODE_STRICT_CART_NEEDED');
                    }
                }
                break;

            case 'Local_PromoCodeShippingAttitude':
                $value = '';
                if ($this->pc_info !== false
                    && $this->applicable === true)
                {
                    if ($this->pc_info['free_shipping'] == PROMO_CODE_FORBIDS_FREE_SHIPPING)
                        $value = cz_getMsg('PROMOCODE_FREE_SHIPPING_FORBIDDEN');
                    else if ($this->pc_info['free_shipping'] == PROMO_CODE_GRANTS_FREE_SHIPPING)
                        $value = cz_getMsg('PROMOCODE_FREE_SHIPPING_GRANTED');
                }
                break;

            case 'Local_PromoCodeHandlingAttitude':
                $value = '';
                if ($this->pc_info !== false
                    && $this->applicable === true)
                {
                    if ($this->pc_info['free_handling'] == PROMO_CODE_FORBIDS_FREE_HANDLING)
                        $value = cz_getMsg('PROMOCODE_FREE_HANDLING_FORBIDDEN');
                    else if ($this->pc_info['free_handling'] == PROMO_CODE_GRANTS_FREE_HANDLING)
                        $value = cz_getMsg('PROMOCODE_FREE_HANDLING_GRANTED');
                }
                break;

            case 'Local_PromoCodeGlobalDiscountIgnored':
                $value = '';
                if ($this->pc_info !== false
                    && $this->applicable === true)
                {
                    if ($this->pc_info['b_ignore_other_discounts'] == 1) // ignore global discount
                        $value = cz_getMsg('PROMOCODE_GLOBAL_DISCOUNT_IGNORED');
                }
                break;

            default:
                $value = NULL;
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

    var $pc_info;
    var $applicable;

    /**
     *                  TemplateFiller.
     * @var TemplateFiller
     */
    var $templateFiller;

    /**
     *                         .
     * @var array
     */
    var $template;

    /**#@-*/

}
?>