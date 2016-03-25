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
 * Checkout module.
 *
 * @package Checkout
 * @access  public
 */
class OrderInfo
{

    function OrderInfo()
    {
        $this->template_folder = "order-info";
		$this->isInfo=true;
        $this->order_id = modApiFunc('Checkout', 'getCurrentOrderID');
        if($this->order_id !== NULL)
        {
            $this->_order_currencies = $this->asc_array_unique(modApiFunc('Checkout',
                                                                          'getOrderCurrencyList',
                                                                          $this->order_id));
            foreach ($this->_order_currencies as $key)
            {
                if ($key["currency_type"] == CURRENCY_TYPE_MAIN_STORE_CURRENCY)
                {
                    $this->main_store_currency = $key["currency_code"];
                    break;
                }
            }

            $this->order_currency = modApiFunc('Checkout', 'getCurrentOrderCurrencyID');
            if($this->order_currency == NULL)
            {
                $this->order_currency = modApiFunc("Localization", "getCurrencyIdByCode", $this->main_store_currency);
            }
        }

        if(modApiFunc("Session", "is_Set", "EditOrderForm"))
        {
            $this->copyFormData();
            modApiFunc('Session', 'un_Set', 'EditOrderForm');
        }
        else
        {
            $this->initFormData();

            if (modApiFunc("Session", "is_Set", "OrderViewState"))
            {
                $OVS = modApiFunc("Session", "get", "OrderViewState");
                $this->ViewState = $OVS["ViewState"];
                modApiFunc("Session", "un_set", "OrderViewState");
            }
        }
    }

    /**
     * Copies data from the user page to view the form.
     */
    function copyFormData()
    {
        $EditOrderForm = modApiFunc("Session", "get", "EditOrderForm");
        $this->_form_style = $EditOrderForm['style'];
        $this->ViewState = $EditOrderForm["ViewState"];

        $this->_order           = modApiFunc("Checkout", "getOrderInfo", $this->order_id, $this->order_currency, false);
        $this->_order_encrypted = modApiFunc("Checkout", "getOrderInfo", $this->order_id, $this->order_currency, true);
        $this->isOrderEditable = ($this->_order["NewType"] == "1") ? true : false;
        $this->DisplayIncludedTax = ($this->_order["DisplayIncludedTax"] == "1") ? true : false;
        # always updated
        $this->_order['StatusId'] = $EditOrderForm['status_id'];
        $this->_order['PaymentStatusId'] = $EditOrderForm['payment_status_id'];
        $this->_order['TrackId'] = $EditOrderForm['track_id'];
        # updated only for advanced view
        if (array_key_exists('processor_order_id', $EditOrderForm))
        {
            $this->_order['PaymentProcessorOrderId'] = $EditOrderForm['processor_order_id'];
        }
        if (array_key_exists('payment_method', $EditOrderForm))
        {
            $this->_order['PaymentMethod'] = $EditOrderForm['payment_method'];
        }
        if (array_key_exists('shipping_method', $EditOrderForm))
        {
            $this->_order['ShippingMethod'] = $EditOrderForm['shipping_method'];
        }
        $this->_order["Date"] = modApiFunc("Localization", "SQL_date_format", $this->_order["Date"]);

    }

    /**
     * Initializes data for the first form view.
     */
    function initFormData()
    {
        $this->_form_style = ORDERS_INFO_SIMPLE_FORM;
        if ($this->order_id == null)
        {
            $this->_order = null;
            return;
        }
        $this->_order           = modApiFunc("Checkout", "getOrderInfo", $this->order_id, $this->order_currency, false);
        $this->_order_encrypted = modApiFunc("Checkout", "getOrderInfo", $this->order_id, $this->order_currency, true);
        $this->isOrderEditable = ($this->_order["NewType"] == "1") ? true : false;
        $this->DisplayIncludedTax = ($this->_order["DisplayIncludedTax"] == "1") ? true : false;
        $this->_order["Date"] = modApiFunc("Localization", "SQL_date_format", $this->_order["Date"]);
        $this->ViewState = array("hasCloseScript" => "false");
    }

    function outputResultMessage()
    {
        global $application;
        if(modApiFunc("Session","is_set","ResultMessage"))
        {
            $msg=modApiFunc("Session","get","ResultMessage");
            modApiFunc("Session","un_set","ResultMessage");
            $template_contents=array(
                "ResultMessage" => getMsg('SYS',$msg)
            );
            $this->_Template_Contents=$template_contents;
            $application->registerAttributes($this->_Template_Contents);
            $this->mTmplFiller = &$application->getInstance('TmplFiller');
            return $this->mTmplFiller->fill("checkout/order-info/", "result-message.tpl.html",array());
        }
        else
        {
            return "";
        }
    }

    function asc_array_unique($a)
    {
        $res = array();
        foreach($a as $key => $value)
        {
            if(!array_key_exists(md5(serialize($value)), $res))
            {
                $res[md5(serialize($value))] = $value;
            }
        }
        return $res;
    }

    function outputOrderCurrency()
    {
        $currencies = $this->_order_currencies;

        $res = "";
        $order_currency_code = modApiFunc("Localization", "getCurrencyCodeById", $this->order_currency);
        foreach($currencies as $info)
        {
            if($info["currency_code"] == $order_currency_code)
            {
                $selected = " SELECTED ";
            }
            else
            {
                $selected = "";
            }

            switch($info["currency_type"])
            {
                case CURRENCY_TYPE_MAIN_STORE_CURRENCY:
                {
                    $type = "main";
                    break;
                }
                case CURRENCY_TYPE_CUSTOMER_SELECTED:
                {
                    $type = "";// "cs";
                    break;
                }
                case CURRENCY_TYPE_PAYMENT_GATEWAY:
                {
                    $type = "gw";
                    break;
                }
            }

            $res  .= '<option value="'.modApiFunc("Localization", "getCurrencyIdByCode", $info["currency_code"]).'" '.$selected.'>'. $info["currency_code"] . (empty($type) ? '' : ' ('.$type.')' ).'</option>';
        }
        return $res;
    }

    //                                 -                                    .
    function outputOrderCurrencyStyle()
    {
        $currencies = modApiFunc('Checkout', 'getOrderCurrencyList', $this->order_id);
        if(sizeof($this->asc_array_unique($currencies)) == 1)
        {
            return "display: none;";
        }
        else
        {
            return "";
        }
    }

    /**
     * The main function to output the given view.
     */
    function output()
    {
        global $application;
        loadCoreFile('html_form.php');
        $this->HtmlForm = new HtmlForm();

        if($this->_order == null)
        {
            modApiFunc("application", "closeChild_UpdateParent");
            return;
        }

        if(isset($this->ViewState["hasCloseScript"]) && $this->ViewState["hasCloseScript"] == "true")
        {
            modApiFunc("application", "updateParent");
        }

        $this->MessageResources = &$application->getInstance('MessageResources');
        $this->TemplateFiller = $application->getInstance('TmplFiller');
        $application->registerAttributes(array(
            'FormStyle'
           ,"areOrderPricesEditable"
           ,'OrderCurrency'
           ,'OrderCurrencyHREF'
           ,'OrderCurrencyStyle'
           ,'OrderId'
           ,'OrderDate'
           ,'OrderPaymentProcessorOrderId'
           ,'OrderPaymentMethod'
           ,'OrderPaymentMethodDetail'
           ,'OrderShippingMethod'
           ,'OrderShippingMethodLabelOnly'
           ,'OrderTrackId'
           ,'OrderAffiliateId'
           ,'OrderPriceSubtotal'
           ,'OrderPriceSubtotalGlobalDiscount'
           ,'OrderPriceSubtotalPromoCodeDiscount'
           ,'OrderPriceSubtotalPromoCodeDiscountInfo'
           ,'OrderPriceQuantityDiscount'
           ,'OrderPriceDiscountedSubtotal'
           ,'OrderPriceShipping'
           ,'OrderPriceShippingLabelOnly'
           ,'OrderPriceTaxes'
           ,'OrderTaxItemTaxName'
           ,'OrderTaxItemTaxAmount'
           ,"OrderTaxDisOpItemName"
           ,"OrderTaxDisOpItemAmount"
           ,'OrderPriceTotal'
           ,'OrderPriceTotalToPay'
           ,'OrderPriceTotalPrepaidByGC'
           ,'OrderStatusSelector'
           ,'OrderStatusSelectorItems'
           ,'StatusId'
           ,'StatusName'
           ,'StatusSelected'
           ,'OrderPaymentStatusSelector'
           ,'OrderPaymentStatusSelectorItems'
           ,'PaymentStatusId'
           ,'PaymentStatusName'
           ,'PaymentStatusSelected'
           ,'PersonInfo'
           ,'OrderComments'
           ,'OrderHistory'
           ,'GroupName'
           ,'GroupId'
           ,'GroupPersonInfoVariantId'
           ,'GroupTag'
           ,'GroupCVVPurged'
           ,'Counter'
           ,'CommentDate'
           ,'CommentValue'
           ,'InputControlName'
           ,'InputControlValue'
           ,'InputControlSize'
           ,'Products'
           ,'ProductQTY'
           ,'ProductAmount'
           ,'Controls'
           ,'PageTutorialHelpLinks'
           ,'Delete'
           ,'AddCommentVisibilityOpen'
           ,'AddCommentVisibilityClose'
           ,'OrderIvoiceHREF'
           ,'OrderPackingSlipHREF'
           ,'ResultMessage'
           ,'ResultMessageRow'
           ,'AppliedGiftCertificatesList'
        ));

        // ???????  modApiFunc('Checkout', 'getPersonInfo', 1);
        modApiFunc("Localization", "pushDisplayCurrency", $this->order_currency, $this->order_currency);
        $ret = $this->TemplateFiller->fill("checkout/" . $this->template_folder . "/", "container.tpl.html", array());
        modApiFunc("Localization", "popDisplayCurrency");
        return $ret;
    }

    /**
     * Generates a product list for the given order.
     */
    function getProducts()
    {
        global $application;
        $application->registerAttributes(array(
                    "ProductStoreProductID" => ""
                   ,"ProductHandpickedOptions" => ""
                   ,"ProductHotlinks" => ""
                   ,"CycleColor" => ""
                 ));
        $result = "";
        $products = $this->_order['Products'];

        if ($products == null || !is_array($products) || count($products) == 0)
        {
            return $result;
        }
        foreach ($products as $pkey => $product)
        {
            $this->_pkey = $pkey;
            $this->_product = $product;
            foreach ($product["custom_attributes"] as $custom_attr_info)
            {
                $this->_product[$custom_attr_info["tag"]] = $custom_attr_info["value"];
            }

            $result .= $this->TemplateFiller->fill("checkout/" . $this->template_folder . "/", "product.tpl.html", array());
        }
        return $result;
    }

    /**
     * Generates a tax list for the given order.
     */
    function getTaxes()
    {
        // @ tax
        global $application;
        $retval = "";
        $this->_Tax_Item = array();
        $this->areIncludedTaxesPresent = false;

        if ($this->_form_style == ORDERS_INFO_ADVANCED_FORM)
        {
            foreach ($this->_order["Price"]["taxes"] as $key => $value)
            {
                $this->_Tax_Item["TaxId"]       = $key;
                $this->_Tax_Item['TaxName']     = prepareHTMLDisplay($value["name"]);
                $this->_Tax_Item['TaxAmount']   = ($value["value"] == PRICE_N_A) ? 0.0000 : $value["value"];
                $this->_Tax_Item["is_included"] = $value["is_included"];
                $retval .= $this->TemplateFiller->fill("checkout/" . $this->template_folder . "/", "order-tax-item.tpl.html", array());

                if ($value["is_included"] == 1)
                    $this->areIncludedTaxesPresent = true;
            }
        }

        foreach ($this->_order["Price"]["tax_dops"] as $key => $value)
        {
            $this->_Tax_Item["TaxId"]       = $key;
            $this->_Tax_Item['TaxName']     = prepareHTMLDisplay($value["name"]);
            $this->_Tax_Item['TaxAmount']   = $value["value"];

            $formula_array = array();
            $formula = explode(',', $value["formula"]);
            foreach ($formula as $id)
            {
                $formula_array[] = $this->_order["Price"]["taxes"][$id]["name"];
            }
            $this->_Tax_Item["formula"] = implode(' + ', $formula_array);

            $retval .= $this->TemplateFiller->fill("checkout/" . $this->template_folder . "/", "order-tax-dis-op-item.tpl.html", array());
        }

        //                                             -                  .
        $res = modApiFunc("TaxExempts", "getOrderFullTaxExempts", $this->order_id);
        if ($this->_form_style == ORDERS_INFO_SIMPLE_FORM)
        {
            if (sizeof($res) == 1)
            {
                $prices = getKeyIgnoreCase('price', $this->_order);
                $this->_Tax_Item["TaxId"]     = "taxExemption";
                $this->_Tax_Item['TaxName']   = getMsg('SYS', "FULL_TAX_EXEMPT_MSG");
                $this->_Tax_Item['TaxAmount'] = -1 * getKeyIgnoreCase("OrderTaxTotal", $prices);
                $this->_Tax_Item["is_included"] = 0;
                $retval .= $this->TemplateFiller->fill("checkout/" . $this->template_folder . "/", "order-tax-item.tpl.html", array());
            }
        }
        else if ($this->_form_style == ORDERS_INFO_ADVANCED_FORM
            && count($this->_order["Price"]["taxes"]) > 0)
        {
            $taxExemptEnabled = modApiFunc('Settings','getParamValue','TAXES_PARAMS','ALLOW_FULL_TAX_EXEMPTS');
            if (sizeof($res) == 1)
            {
                $this->_Tax_Item["TaxId"]     = "taxExemption";
                $this->_Tax_Item['TaxName']   = getMsg('SYS',"FULL_TAX_EXEMPT_MSG");
                $this->_Tax_Item['TaxAmount'] = "<input type=checkbox name='taxExemption' checked onFocus='setPricesAsEdited();'>";
                $this->_Tax_Item["is_included"] = 0;
                $retval .= $this->TemplateFiller->fill("checkout/" . $this->template_folder . "/", "order-tax-item.tpl.html", array());
            }
            else if ($taxExemptEnabled == "true")
            {
                // is ALLOW_FULL_TAX_EXEMPTS enabled?
                $this->_Tax_Item["TaxId"]     = "taxExemption";
                $this->_Tax_Item['TaxName']   = getMsg('SYS',"FULL_TAX_EXEMPT_MSG");
                $this->_Tax_Item['TaxAmount'] = '<input type="checkbox" name="taxExemption" onFocus="setPricesAsEdited();">';
                $this->_Tax_Item["is_included"] = 0;
                $retval .= $this->TemplateFiller->fill("checkout/" . $this->template_folder . "/", "order-tax-item.tpl.html", array());
            }
        }

        // show warning about included taxes
        if ($this->_form_style == ORDERS_INFO_ADVANCED_FORM
            && $this->areIncludedTaxesPresent == true
            && $this->DisplayIncludedTax == true)
        {
            $this->_Tax_Item["TaxId"]     = 0;
            $this->_Tax_Item['TaxName']   = getMsg('SYS',"INCLUDED_TAX_EDIT_MSG");
            $this->_Tax_Item['TaxAmount'] = '';
            $this->_Tax_Item["is_included"] = 0;
            $retval .= $this->TemplateFiller->fill("checkout/" . $this->template_folder . "/", "order-tax-item.tpl.html", array());
        }

        return $retval;
    }

    /**
     * describe the function OrderInfo->.
     */
    function getPersonInfo($infoType)
    {
        global $application;
        $info = $this->_order[$infoType];
        if(!isset($info['id']))
        {
            return "";
        }
        $result = "";
        $this->_counter = 0;
        $this->_group = array(
            'id' => $info['id']
           ,'name' => $info['name']
           ,'tag' => $info['tag']
           ,'personinfovariantid' => $info['person_info_variant_id']
        );
        $result .= $this->TemplateFiller->fill("checkout/" . $this->template_folder . "/", "group.tpl.html", array());
        foreach ($info['attr'] as $attr)
        {
            if ($attr['id'] === null) continue;
            $this->_counter++;
            $this->_attr = array(
                'tag' => $attr['tag']
               ,'name' => $attr['name']
               ,'value' => $attr['value']
            );
            if ($this->_form_style == ORDERS_INFO_SIMPLE_FORM)
            {
                $result .= $this->TemplateFiller->fill("checkout/" . $this->template_folder . "/", "attr.tpl.html", array());
            }
            else
            {
                $this->_input_control = array(
                    'name' => _ml_strtolower($infoType)."[".$attr['tag']."]"
                   ,'value' => $attr['value']
                   ,'size' => 70
                );

                switch($attr['input_type_id'])
                {
                    case "1": /* <input type="text"> */
                        $result .= $this->TemplateFiller->fill("checkout/" . $this->template_folder . "/", "attr-input.tpl.html", array());
                        break;
                    case "2": /* <textarea> */
                        $result .= $this->TemplateFiller->fill("checkout/" . $this->template_folder . "/", "attr-textarea.tpl.html", array());
                        break;
                    default: /* <input type="text"> */
                        //: investigate this case thoroughly
                        $result .= $this->TemplateFiller->fill("checkout/" . $this->template_folder . "/", "attr-input.tpl.html", array());
                        break;
                }
            }
        }
        if($this->isInfo){
        	$result .='</tbody></table></div></div></div></div></div></td></tr>';
        }
        $result .="<!-- END Panel -->";
        $result .= $this->TemplateFiller->fill("checkout/" . $this->template_folder . "/", "show-state.tpl.html", array());
        return $result;
    }

    /**
     * Differs from getPersonInfo() in outputting the controls for decrypting
     * values: the Private key uppload and the hidden fields with encrypted data.
     */
    function getPersonInfoEncrypted($infoType)
    {
        global $application;

        $info = $this->_order[$infoType];
        if (empty($info['attr']))
        {
            return '';
        }

        $result = "";
        $this->_counter = 0;
        $this->_group = array(
            'id' => $info['id']
           ,'name' => $info['name']
           ,'tag' => $info['tag']
           ,'personinfovariantid' => $info['person_info_variant_id']
        );
        $GroupCVVPurged = 'true';
        $result .= $this->TemplateFiller->fill("checkout/" . $this->template_folder . "/", "group-encrypted.tpl.html", array());


        /**
          * If the encrypted info is credit card info, then output a special
         * message, otherwise - a general message (general is not used now).
         */
        $remove_info_group_msg = strstr(_ml_strtolower($infoType), "creditcard") === FALSE ? getMsg('SYS','CHECKOUT_ORDER_INFO_REMOVE_ENCRYPTED_PERSON_INFO_MSG') : getMsg('SYS','CHECKOUT_ORDER_INFO_REMOVE_CREDIT_CARD_INFO_MSG');
        $remove_info_group_confirm_msg = strstr(_ml_strtolower($infoType), "creditcard") === FALSE ? getMsg('SYS','CHECKOUT_ORDER_INFO_REMOVE_ENCRYPTED_PERSON_INFO_CONFIRM_MSG') : getMsg('SYS','CHECKOUT_ORDER_INFO_REMOVE_CREDIT_CARD_INFO_CONFIRM_MSG');

        $this->_msg['CHECKOUT_ORDER_INFO_REMOVE_ENCRYPTED_PERSON_INFO_MSG'] = $remove_info_group_msg;
        $this->_msg['CHECKOUT_ORDER_INFO_REMOVE_ENCRYPTED_PERSON_INFO_CONFIRM_MSG'] = $remove_info_group_confirm_msg;
        $application->registerAttributes(array('CHECKOUT_ORDER_INFO_REMOVE_ENCRYPTED_PERSON_INFO_MSG'
                                              ,'CHECKOUT_ORDER_INFO_REMOVE_ENCRYPTED_PERSON_INFO_CONFIRM_MSG'));
        $this->_counter++;
        $result .= $this->TemplateFiller->fill("checkout/" . $this->template_folder . "/", "group-decryption-controls-right.tpl.html", array());



        foreach ($info['attr'] as $attr)
        {
            if ($attr['id'] === null) continue;
            $this->_counter++;

            $this->_attr = array(
                'tag' => $attr['tag']
               ,'name' => $attr['name']
               ,'value' => nl2br(htmlspecialchars($attr['value']))
               ,'personattributeid' => $attr['person_attribute_id']
               ,'encryptedsecretkey' => $attr['encrypted_secret_key']
               ,'rsapublickeyascformat' => $attr['rsa_public_key_asc_format']
            );

            //The code is not correct. Output the warning about CVV.
            //It would be better to find the id by the tag in the table person_attributes.
            $cvv_person_attribute_id = 14;
            if ($this->_form_style == ORDERS_INFO_SIMPLE_FORM)
            {
                if($attr['person_attribute_id'] == $cvv_person_attribute_id &&
                   $attr['value'] != getMsg('SYS','CHECKOUT_ORDER_INFO_CVV_PURGED_MSG'))
                {
                    $GroupCVVPurged = 'false';
                    $this->_attr['value'] = $attr['value'] . ' <span style="color: red;">' . getMsg('SYS',"CHECKOUT_ORDER_INFO_CVV_WILL_BE_DELETED_WARNING_MSG") . '</span>';
                }
                $result .= $this->TemplateFiller->fill("checkout/" . $this->template_folder . "/", "attr.tpl.html", array());
            }
            else
            {
                $this->_input_control = array(
                    'name' => _ml_strtolower($infoType)."[".$attr['tag']."]"
                   ,'value' => $attr['value']
                   ,'personattributeid' => $attr['person_attribute_id']
                   ,'size' => 70
                );
                //The code is not correct. Output the warning about CVV.
                //It would be better to find the id by the tag in the table person_attributes.
                if($attr['person_attribute_id'] == $cvv_person_attribute_id &&
                   $attr['value'] != getMsg('SYS','CHECKOUT_ORDER_INFO_CVV_PURGED_MSG'))
                {
                    $GroupCVVPurged = 'false';
                    $this->_input_control['value'] = $attr['value'] . " " . getMsg('SYS',"CHECKOUT_ORDER_INFO_CVV_WILL_BE_DELETED_WARNING_MSG");
                }

                switch($attr['input_type_id'])
                {
                    case "1": /* <input type="text"> */
                        $result .= $this->TemplateFiller->fill("checkout/" . $this->template_folder . "/", "attr-input-encrypted.tpl.html", array());
                        break;
                    case "2": /* <textarea> */
                        $result .= $this->TemplateFiller->fill("checkout/" . $this->template_folder . "/", "attr-textarea-encrypted.tpl.html", array());
                        break;
                    default: /* <input type="text"> */
                        //: investigate this case thoroughly
                        $result .= $this->TemplateFiller->fill("checkout/" . $this->template_folder . "/", "attr-input-encrypted.tpl.html", array());
                        break;
                }
            }
        }

        //Output encrypted data:
        $info = $this->_order_encrypted[$infoType];
        $encrypted_data = array();
        //$this->_counter = 0;
        $this->_group = array(
            'id' => $info['id']
           ,'name' => $info['name']
           ,'tag' => $info['tag']
           ,'personinfovariantid' => $info['person_info_variant_id']
           ,'CVVPurged' => $GroupCVVPurged
        );

        foreach ($info['attr'] as $attr)
        {
            if ($attr['id'] === null) continue;
            $this->_counter++;
            $this->_attr = array(
                'tag' => $attr['tag']
               ,'name' => $attr['name']. "_encrypted"
               ,'value' => nl2br(htmlspecialchars($attr['value']))
               ,'personattributeid' => $attr['person_attribute_id']
               ,'encryptedsecretkey' => $attr['encrypted_secret_key']
               ,'rsapublickeyascformat' => $attr['rsa_public_key_asc_format']
            );
            $encrypted_data[$attr["person_attribute_id"]] = array
            (
                "variable_value__blowfish_encrypted" => $this->_attr["value"]
               ,"blowfish_key__rsa_encrypted" => $this->_attr["encryptedsecretkey"]
               ,"rsa_public_key_asc_format" => $this->_attr["rsapublickeyascformat"]
               //RSA Private Key is one for all.
            );
        }
        //The end of collecting encrypted values.

        $result .= '<input type="hidden" name="encrypted_data['.$this->_group['id'].']" value="'.base64_encode(serialize($encrypted_data)).'">';
        $this->_counter++;
        $result .= $this->TemplateFiller->fill("checkout/" . $this->template_folder . "/", "group-decryption-controls.tpl.html", array());
        $result .="";
        $result .= $this->TemplateFiller->fill("checkout/" . $this->template_folder . "/", "show-state.tpl.html", array());
        return $result;
    }


    /**
     * Ouptputs a list of comments for the order.
     */
    function getComments()
    {
        global $application;

        $notes = $this->_order['Comments'];
        $result = "";
        $this->_counter = 0;
        foreach ($notes as $note)
        {
            $this->_counter++;
            $this->_note = $note;
            $result .= $this->TemplateFiller->fill("checkout/" . $this->template_folder . "/", "comment.tpl.html", array());
        }
        return $result;
    }

    /**
     * Outputs a list of changes for the given order.
     */
    function getHistory()
    {
        global $application;

        $notes = $this->_order['History'];
        $result = "";
        $this->_counter = 0;
        foreach ($notes as $note)
        {
            $this->_counter++;
            $this->_note = $note;
            $result .= $this->TemplateFiller->fill("checkout/" . $this->template_folder . "/", "history.tpl.html", array());
        }
        return $result;
    }

    /**
     * Outputs a message, if the order's prices and quantities are not editable
     *
     */
    function checkAreOrderPricesEditable()
    {
        if ($this->isOrderEditable)
            return "";

        return $this->TemplateFiller->fill("checkout/" . $this->template_folder . "/", "order-cannot-edit-msg.tpl.html", array());
    }

    function getTag($tag)
    {
        global $application;
        $value = null;
        switch ($tag)
        {
            case "areOrderPricesEditable":
                $value = "";
                if ($this->_form_style == ORDERS_INFO_ADVANCED_FORM)
                    $value = $this->checkAreOrderPricesEditable();
                break;

            case 'Products':
                $value = $this->getProducts();
                break;

            case 'CycleColor':
                $value = (($this->_pkey % 2) == 0 ? '#FFFFFF' : '#EEEEEE');
                break;

            case 'PersonInfo':
                $value = '';
                if(modApiFunc('Customer_Account','isPersionInfoGroupActive','Billing'))
                  $value .= $this->getPersonInfo('Billing');
                if(modApiFunc('Customer_Account','isPersionInfoGroupActive','Shipping'))
                  $value .= $this->getPersonInfo('Shipping');
                $value .= $this->getPersonInfoEncrypted('CreditCard');
                $value .= $this->getPersonInfo('BankAccount');
                break;

            case 'OrderComments':
                $value = $this->getComments();
                break;

            case 'OrderHistory':
                $value = $this->getHistory();
                break;


        // @ tax labels here
            case "OrderTaxItemTaxName":
                $value = $this->_Tax_Item['TaxName'];
                if ($this->_form_style == ORDERS_INFO_ADVANCED_FORM
                    && $this->_Tax_Item['is_included'] == 1
                    && $this->DisplayIncludedTax == true)
                {
                    $value = '*' . $value;
                }
                break;

            case "OrderTaxItemTaxAmount":
                if ($this->_form_style == ORDERS_INFO_SIMPLE_FORM
                    || $this->isOrderEditable == false)
                {
                    $value = modApiFunc("Localization", "currency_format", $this->_Tax_Item['TaxAmount']);
                    if ($this->_Tax_Item['TaxId'] == "taxExemption")
                    {
                        $value .= "<input type='hidden' name='taxExemption' value='off'>";
                    }
                    else
                    {
                        $value .= "<input type=hidden name='tax[{$this->_Tax_Item['TaxId']}][value]' value='{$this->_Tax_Item['TaxAmount']}'>"
                                    ."<input type=hidden name='tax[{$this->_Tax_Item['TaxId']}][is_included]' value='{$this->_Tax_Item['is_included']}'>";
                    }
                }
                else //if ($this->_form_style == ORDERS_INFO_ADVANCED_FORM)
                {
                    if ($this->_Tax_Item['TaxId'] == "taxExemption")
                    {
                        $value = $this->_Tax_Item['TaxAmount'];
                    }
                    else
                    {
                        $value = '<input class="input_value"  type="text" '
                                    . $this->HtmlForm->genInputTextField("16", "tax[{$this->_Tax_Item['TaxId']}][value]", "6", prepareHTMLDisplay($this->_Tax_Item['TaxAmount']))
                                    . " onFocus='setPricesAsEdited();' />"
                                    ."<input type=hidden name='tax[{$this->_Tax_Item['TaxId']}][is_included]' value='{$this->_Tax_Item['is_included']}'>";
                    }
                }

                break;

            case "OrderTaxDisOpItemName":
                $value = $this->_Tax_Item['TaxName'];
                if ($this->_form_style == ORDERS_INFO_ADVANCED_FORM)
                {
                    $value .= " (&fnof;: {$this->_Tax_Item['formula']})";
                }
                break;

            case "OrderTaxDisOpItemAmount":
                $value = $value = modApiFunc("Localization", "currency_format", $this->_Tax_Item['TaxAmount']);
                break;

            case 'Controls':
                if (modApiFunc("Checkout", "getDeleteOrdersFlag") == "true")
                {
                    $value = $this->TemplateFiller->fill("checkout/" . $this->template_folder . "/", "controls-style3.tpl.html", array());
                }
                elseif ($this->_form_style == ORDERS_INFO_SIMPLE_FORM)
                {
                    $value = $this->TemplateFiller->fill("checkout/" . $this->template_folder . "/", "controls-style1.tpl.html", array());
                }
                elseif ($this->_form_style == ORDERS_INFO_ADVANCED_FORM)
                {
                    $value = $this->TemplateFiller->fill("checkout/" . $this->template_folder . "/", "controls-style2.tpl.html", array());
                }
                break;

            case 'PageTutorialHelpLinks':
                if (modApiFunc("Checkout", "getDeleteOrdersFlag") == "true")
                {
                    $value = $this->TemplateFiller->fill("checkout/" . $this->template_folder . "/", "pagetutoriallinks-style3.tpl.html", array());
                }
                elseif ($this->_form_style == ORDERS_INFO_SIMPLE_FORM)
                {
                    $value = $this->TemplateFiller->fill("checkout/" . $this->template_folder . "/", "pagetutoriallinks-style1.tpl.html", array());
                }
                elseif ($this->_form_style == ORDERS_INFO_ADVANCED_FORM)
                {
                    $value = $this->TemplateFiller->fill("checkout/" . $this->template_folder . "/", "pagetutoriallinks-style2.tpl.html", array());
                }
                break;

            case 'CommentDate':
                $value = modApiFunc("Localization", "SQL_date_format", $this->_note['date'])." ".modApiFunc("Localization", "SQL_time_format", $this->_note['date']);
                break;

            case 'CommentValue':
                $value = $this->_note['content'];
                break;

            case 'Counter':
                $value = $this->_counter;
                break;

            ### Status selectors
            case 'OrderStatusSelector':
                if (modApiFunc("Checkout", "getDeleteOrdersFlag") == "true")
                {
                    $value = $this->_order['Status'];
                }
                else
                {
                    $value = $this->TemplateFiller->fill("checkout/" . $this->template_folder . "/", "status-selector.tpl.html", array());
                }
                break;

            case 'OrderStatusSelectorItems':
                $status_array = modApiFunc('Checkout', 'getOrderStatusList');
                $value = "";
                foreach ($status_array as $status)
                {
                    $this->_status = $status;
                    $value .= $this->TemplateFiller->fill("checkout/" . $this->template_folder . "/", "status-selector-item.tpl.html", array());
                }
                break;

            case 'StatusId':
                $value = $this->_status['id'];
                break;

            case 'StatusName':
                $value = $this->_status['name'];
                break;

            case 'StatusSelected':
                if ($this->_status['id'] == $this->_order['StatusId'])
                {
                    $value = " selected";
                }
                else
                {
                    $value = "";
                }
                break;

            case 'OrderPaymentStatusSelector':
                if (modApiFunc("Checkout", "getDeleteOrdersFlag") == "true")
                {
                    $value = $this->_order['PaymentStatus'];
                }
                else
                {
                    $value = $this->TemplateFiller->fill("checkout/" . $this->template_folder . "/", "payment-status-selector.tpl.html", array());
                }
                break;

            case 'OrderPaymentStatusSelectorItems':
                $status_array = modApiFunc('Checkout', 'getOrderPaymentStatusList');
                $value = "";
                foreach ($status_array as $status)
                {
                    $this->_payment_status = $status;
                    $value .= $this->TemplateFiller->fill("checkout/orders/", "payment-status-selector-item.tpl.html", array());
                }
                break;

            case 'PaymentStatusId':
                $value = $this->_payment_status['id'];
                break;

            case 'PaymentStatusName':
                $value = $this->_payment_status['name'];
                break;

            case 'PaymentStatusSelected':
                if ($this->_payment_status['id'] == $this->_order['PaymentStatusId'])
                {
                    $value = " selected";
                }
                else
                {
                    $value = "";
                }
                break;

            case 'AppliedGiftCertificatesList':
                $gcs = modApiFunc('GiftCertificateApi', 'getOrderGCs',$this->order_id);
                $value = "";
                if (!empty($gcs) && is_array($gcs))
                {
                    foreach ($gcs as $gc)
                    {

                        $value .= $this->TemplateFiller->fill("checkout/orders/", "gift-certificate-item.tpl.html", array("GiftCertificateCode" => $gc['gc_code']));
                    }
                }
                else
                    $value = "<tr><td colspan=\"3\" class=\"text-center\">".getMsg("CHCKT","LBL_NO_GIFT_CERTIFICATES")."</td></tr>";
                break;


            case 'OrderIvoiceHREF':
                $request = new Request();
                $request->setView  ( 'OrderInvoice' );
                $request->setAction( 'SetCurrentOrder' );
                $request->setKey   ( 'order_id', $this->order_id);
                $request->setKey   ( 'order_currency_id', $this->order_currency);
                $value = $request->getURL();
                break;

            case 'OrderPackingSlipHREF':
                $request = new Request();
                $request->setView  ( 'OrderPackingSlip' );
                $request->setAction( 'SetCurrentOrder' );
                $request->setKey   ( 'order_id', $this->order_id);
                $request->setKey   ( 'order_currency_id', $this->order_currency);
                $value = $request->getURL();
                break;

            case 'OrderTrackId':
                if (modApiFunc("Checkout", "getDeleteOrdersFlag") == "true")
                {
                    $value = $this->_order['TrackId'];
                }
                else
                {
                    $value = "<textarea name=\"track_id\" cols=\"60\" rows=\"5\" class=\"form-control\">".prepareHTMLDisplay($this->_order['TrackId'])."</textarea>";
                }
                break;

            case 'OrderAffiliateId':
                if (!empty($this->_order['AffiliateId']))
                    $value = $this->_order['AffiliateId'];
                else
                    $value = getMsg("CHCKT","LBL_NO_AFFILIATE_ID");
                break;

            case 'FormStyle':
                $value = $this->_form_style;
                break;

            case 'OrderPaymentMethod':
                if ($this->_form_style == ORDERS_INFO_SIMPLE_FORM)
                {
                    $value = getKeyIgnoreCase('PaymentMethod', $this->_order);
                }
                else
                {
                    $payment_method = getKeyIgnoreCase('PaymentMethod', $this->_order);
                    $this->_input_control = array(
                        'name' => 'payment_method'
                       ,'value' => $payment_method
                       ,'size' => 40
                    );
                    $value = $this->TemplateFiller->fill("checkout/" . $this->template_folder . "/", "input-control.tpl.html", array());
                }
                break;

            case 'OrderShippingMethod':
                if ($this->_form_style == ORDERS_INFO_SIMPLE_FORM)
                {
                    $value = getKeyIgnoreCase('ShippingMethod', $this->_order);
                }
                else
                {
                    $shipping_method = getKeyIgnoreCase('ShippingMethod', $this->_order);
                    $this->_input_control = array(
                        'name' => 'shipping_method'
                       ,'value' => $shipping_method
                       ,'size' => 40
                    );
                    $value = $this->TemplateFiller->fill("checkout/" . $this->template_folder . "/", "input-control.tpl.html", array());
                }
                break;

            case 'OrderShippingMethodLabelOnly':
                $value = getKeyIgnoreCase('ShippingMethod', $this->_order);
                break;

            case 'OrderPaymentProcessorOrderId':
                if ($this->_form_style == ORDERS_INFO_SIMPLE_FORM)
                {
                    $value = getKeyIgnoreCase('PaymentProcessorOrderId', $this->_order);
                }
                else
                {
                    $processor_order_id = getKeyIgnoreCase('PaymentProcessorOrderId', $this->_order);
                    $this->_input_control = array(
                        'name' => 'processor_order_id'
                       ,'value' => $processor_order_id
                       ,'size' => 40
                    );
                    $value = $this->TemplateFiller->fill("checkout/" . $this->template_folder . "/", "input-control.tpl.html", array());
                }
                break;

            case 'InputControlName': $value = $this->_input_control['name']; break;
            case 'InputControlValue': $value = $this->_input_control['value']; break;
            case 'InputControlSize': $value = $this->_input_control['size']; break;

            case 'Delete':
                $value = modApiFunc("Checkout", "getDeleteOrdersFlag");
                break;

            case 'AddCommentVisibilityOpen':
                if (modApiFunc("Checkout", "getDeleteOrdersFlag") == "true")
                {
                    $value = "<!--";
                }
                break;

            case 'AddCommentVisibilityClose':
                if (modApiFunc("Checkout", "getDeleteOrdersFlag") == "true")
                {
                    $value = "-->";
                }
                break;

            case 'CHECKOUT_ORDER_INFO_REMOVE_ENCRYPTED_PERSON_INFO_MSG':
            case 'CHECKOUT_ORDER_INFO_REMOVE_ENCRYPTED_PERSON_INFO_CONFIRM_MSG':
                $value = $this->_msg[$tag];
                break;
            case 'ResultMessageRow':
                $value = $this->outputResultMessage();
                break;
            case 'ResultMessage':
                $value = $this->_Template_Contents['ResultMessage'];
                break;
            case 'OrderCurrency':
                $value = $this->outputOrderCurrency();
                break;
            case 'OrderCurrencyHREF':
                $request = new Request();
                $request->setView  ( 'OrderInfo' );
                $request->setAction( 'SetCurrentOrder' );
                $request->setKey   ( 'order_id', $this->order_id);
                $value = $request->getURL();
                break;
            case 'OrderCurrencyStyle':
                //                                 -                                    .
                $value = $this->outputOrderCurrencyStyle();
                break;

            default:
                $tag_copy = $tag;
                list($entity, $tag) = getTagName($tag);
                if ($entity == 'order')
                {
                    if (_ml_strpos($tag, 'price') === 0)
                    {
                        $tag = _ml_strtolower(_ml_substr($tag, _ml_strlen('price')));
                        if ($tag == 'total')
                        {
                            $value = modApiFunc("Localization", "currency_format", $this->_order['Total']);
                        }
                        elseif ($tag == 'subtotal')
                        {
                            $value = modApiFunc("Localization", "currency_format", $this->_order['Subtotal']);
                        }
                        elseif ($tag == 'totaltopay')
                        {
                            $value = modApiFunc("Localization", "currency_format", $this->_order['Price']['OrderTotalToPay']);
                        }
                        elseif ($tag == 'totalprepaidbygc')
                        {
                            $value = modApiFunc("Localization", "currency_format", $this->_order['Price']['OrderTotalPrepaidByGC']);
                        }
                        elseif ($tag == 'taxes')
                        {
                            $value = $this->getTaxes();
                        }
                        elseif ($tag == 'shippinglabelonly')
                        {
                            $prices = getKeyIgnoreCase('price', $this->_order);

                            $shipping = getKeyIgnoreCase("TotalShippingAndHandlingCost", $prices);
                            $shipping = $shipping == PRICE_N_A ? 0.0 : $shipping;
                            $value = modApiFunc("Localization", "currency_format", $shipping);
                        }
                        elseif ($tag == 'shipping')
                        {
                            $prices = getKeyIgnoreCase('price', $this->_order);

                            $shipping = getKeyIgnoreCase("TotalShippingAndHandlingCost", $prices);
                            $shipping = $shipping == PRICE_N_A ? 0.0 : $shipping;

                            if ($this->_form_style == ORDERS_INFO_SIMPLE_FORM
                                || $this->isOrderEditable == false)
                            {
                                $value = modApiFunc("Localization", "currency_format", $shipping);
                                $value .= "<input type=hidden name='shippingHandling' value='{$shipping}'>";
                            }
                            else  //if ($this->_form_style == ORDERS_INFO_ADVANCED_FORM)
                            {
                                $value = '<input class="input_value"  type="text" '
                                    . $this->HtmlForm->genInputTextField("16", "shippingHandling", "6", prepareHTMLDisplay($shipping))
                                    . " onFocus='setPricesAsEdited();' />";
                            }
                        }
                        elseif ($tag == 'subtotalglobaldiscount')
                        {
                            $prices = getKeyIgnoreCase('price', $this->_order);

                            $subtotal_global_discount = getKeyIgnoreCase("SubtotalGlobalDiscount", $prices);
                            $subtotal_global_discount = $subtotal_global_discount == PRICE_N_A ? 0.0 : $subtotal_global_discount;

                            if ($this->_form_style == ORDERS_INFO_SIMPLE_FORM
                                || $this->isOrderEditable == false)
                            {
                                $value = modApiFunc("Localization", "currency_format", $subtotal_global_discount);
                                $value .= "<input type=hidden name='globalDiscount' value='{$subtotal_global_discount}'>";
                            }
                            else  //if ($this->_form_style == ORDERS_INFO_ADVANCED_FORM)
                            {
                                $value = '<input type="text" class="input_value"'
                                    . $this->HtmlForm->genInputTextField("16", "globalDiscount", "6", prepareHTMLDisplay($subtotal_global_discount))
                                    . " onFocus=\"setPricesAsEdited();\" />";
                            }

                        }
                        elseif ($tag == 'subtotalpromocodediscount')
                        {
                            $prices = getKeyIgnoreCase('price', $this->_order);
                            $subtotal_promo_code_discount = getKeyIgnoreCase("SubtotalPromoCodeDiscount", $prices);
                            $subtotal_promo_code_discount = $subtotal_promo_code_discount == PRICE_N_A ? 0.0 : $subtotal_promo_code_discount;

                            if ($this->_form_style == ORDERS_INFO_SIMPLE_FORM
                                || $this->isOrderEditable == false)
                            {
                                $value = modApiFunc("Localization", "currency_format", $subtotal_promo_code_discount);
                                $value .= "<input type=hidden name='promoCodeDiscount' value='{$subtotal_promo_code_discount}'>";
                            }
                            else  //if ($this->_form_style == ORDERS_INFO_ADVANCED_FORM)
                            {
                                $value = '<input  class="input_value" type="text" '
                                    . $this->HtmlForm->genInputTextField("16", "promoCodeDiscount", "6", prepareHTMLDisplay($subtotal_promo_code_discount))
                                    . " onFocus=\"setPricesAsEdited();\" />";
                            }
                        }
                        elseif ($tag == 'subtotalpromocodediscountinfo')
                        {
                            $prices = getKeyIgnoreCase('price', $this->_order);
                            $subtotal_promo_code_discount = getKeyIgnoreCase("SubtotalPromoCodeDiscount", $prices);
                            //                 -                        .
                            $info = modApiFunc("PromoCodes", "getOrderCoupons", $this->order_id);
                            if(!empty($info))
                            {
                                //
                                $coupon_id = $info[0]["coupon_id"];
                                $coupon_promo_code = $info[0]["coupon_promo_code"];

                                //         ,          _                  (            id).
                                //            -                        .                    -
                                //                .

                                $info = modApiFunc("PromoCodes", "getPromoCodeInfo", $coupon_id);
                                if($info === false)
                                {
                                    $coupon_tag = "(" . $coupon_promo_code . ")";
                                }
                                else
                                {
                                    $request = new Request();
                                    $request->setView  ( 'EditPromoCode' );
                                    $request->setAction( 'SetEditablePromoCode' );
                                    $request->setKey   ( 'PromoCode_id', $coupon_id);
                                    $value = $request->getURL();
                                    $coupon_tag = '(<a href="'.$value.'" style="color: blue;">' . $coupon_promo_code . '</a>)';
                                }
                            }
                            else
                            {
                                $coupon_tag = "";
                            }
                            $value = $coupon_tag;
                        }
                        elseif ($tag == 'quantitydiscount')
                        {
                            $prices = getKeyIgnoreCase('price', $this->_order);
                            $quantity_discount = getKeyIgnoreCase("QuantityDiscount", $prices);
                            $quantity_discount = $quantity_discount == PRICE_N_A ? 0.0 : $quantity_discount;

                            if ($this->_form_style == ORDERS_INFO_SIMPLE_FORM
                                || $this->isOrderEditable == false)
                            {
                                $value = modApiFunc("Localization", "currency_format", $quantity_discount);
                                $value .= "<input type=hidden name='qtyDiscount' value='{$quantity_discount}'>";
                            }
                            else  //if ($this->_form_style == ORDERS_INFO_ADVANCED_FORM)
                            {
                                $value = '<input  class="input_value" type="text" '
                                    . $this->HtmlForm->genInputTextField("16", "qtyDiscount", "6", prepareHTMLDisplay($quantity_discount))
                                    . " onFocus=\"setPricesAsEdited();\" />";
                            }
                        }
                        elseif ($tag == 'discountedsubtotal')
                        {
                            $prices = getKeyIgnoreCase('price', $this->_order);
                            $discounted_subtotal = getKeyIgnoreCase("DiscountedSubtotal", $prices);
                            $discounted_subtotal = $discounted_subtotal == PRICE_N_A ? 0.0 : $discounted_subtotal;

                            $value = modApiFunc("Localization", "currency_format", $discounted_subtotal);
                        }
                        else
                        {
                            $prices = getKeyIgnoreCase('price', $this->_order);
                            $value = modApiFunc("Localization", "currency_format", getKeyIgnoreCase($tag, $prices));
                        }
                    }
                    elseif(_ml_strpos($tag, 'taxitem') === 0)
                    {
                        $tag = _ml_strtolower(_ml_substr($tag, _ml_strlen('taxitem')));
                        $value = getKeyIgnoreCase($tag, $this->_Tax_Item);
                    }
                    /*
                    elseif (_ml_strpos($tag, 'customer') === 0)
                    {
                        $tag = _ml_strtolower(_ml_substr($tag, _ml_strlen('customer')));
                        $person = getKeyIgnoreCase('person', $this->_order);
                        if(!array_key_exists('customer', $person) || !array_key_exists($tag, $person['customer']['attr'])) break;
                        $value = $person['customer']['attr'][$tag]['value'];
                    }
                    */
                    else
                    {
                       $value = getKeyIgnoreCase($tag, $this->_order);
                    }
                }
                elseif ($entity == 'product')
                {
                    if ($tag == 'amount')
                    {
                        $qty = getKeyIgnoreCase('qty', $this->_product);
                        $price = getKeyIgnoreCase('SalePrice', $this->_product['attr']);
                        $value = modApiFunc("Localization", "currency_format", $qty * $price['value']);
                    }
                    else if ($tag == "name")
                    {
                        if ($this->_form_style == ORDERS_INFO_SIMPLE_FORM)
                        {
                            $value = $this->_product["name"];
                        }
                        else //if ($this->_form_style == ORDERS_INFO_ADVANCED_FORM)
                        {
                            $value = '<input type="text"  class="input_value" '
                                .$this->HtmlForm->genInputTextField("255", "productName[{$this->_product["id"]}]", "50", prepareHTMLDisplay($this->_product["name"]))
                                . "/>";
                        }
                    }
                    else if ($tag == "qty")
                    {
                        if ($this->_form_style == ORDERS_INFO_SIMPLE_FORM
                            || $this->isOrderEditable == false)
                        {
                            $value = $this->_product["qty"];
                            $value .= "<input type=hidden name='productQty[{$this->_product["id"]}]' value='{$this->_product["qty"]}'>";
                        }
                        else //if ($this->_form_style == ORDERS_INFO_ADVANCED_FORM)
                        {
                            $value = '<input type="text"  class="input_value" '
                                .$this->HtmlForm->genInputTextField("16", "productQty[{$this->_product["id"]}]", "4", prepareHTMLDisplay($this->_product["qty"]))
                                . " onFocus=\"setPricesAsEdited();\" />";
                        }
                    }
                    elseif ($tag == 'saleprice')
                    {
                        if ($this->_form_style == ORDERS_INFO_SIMPLE_FORM
                            || $this->isOrderEditable == false)
                        {
                            $price = getKeyIgnoreCase('SalePrice', $this->_product['attr']);
                            $value = modApiFunc("Localization", "currency_format", $price['value']);
                            $value .= "<input type=hidden name='productPrice[{$this->_product["id"]}]' value='{$price['value']}'>";
                        }
                        else //if ($this->_form_style == ORDERS_INFO_ADVANCED_FORM)
                        {
                            $price = getKeyIgnoreCase('SalePrice', $this->_product['attr']);
                            $value = modApiFunc("Localization", "currency_format", $price['value']);
                            $value = '<input type="text"  class="input_value" '
                                .$this->HtmlForm->genInputTextField("32", "productPrice[{$this->_product["id"]}]", "10", prepareHTMLDisplay($price['value']))
                                . " onFocus=\"setPricesAsEdited();\" />";
                        }
                    }
                    elseif ($tag == 'handpickedoptions')
                    {
                        //: put to the template?
                        $value = "";
                        for($j=0; $j < count($this->_product['options']); $j++)
                        {
                            $_val = $this->_product['options'][$j]['option_value'];

                            if($_val != '')
                            {
                                if ($this->_product['options'][$j]['is_file'] == 'Y')
                                {
                                    $_val = '<a href="orders_info.php?asc_action=get_uploaded_file&fp='.base64_encode($_val).'">'.basename($_val).'</a>';
                                }
                                else if ($this->_form_style == ORDERS_INFO_ADVANCED_FORM)
                                {
                                    $_val = "<br/><textarea name='productOption[{$this->_product['options'][$j]['product_option_id']}]' cols='30' rows='1'>"
                                        . prepareHTMLDisplay($_val)
                                        . "</textarea>";
                                }
                            }

                            $value .= "<b>".$this->_product['options'][$j]['option_name']."</b>: ".$_val."<br>";
                        }
                        if($value!="")
                            $value="<div style='padding-top: 4px;'><div style='padding: 2px; background: #EEF2F8; border-top: solid 1px #A9A9A9; font-weight: bold;'>".getMsg('PO','OPTIONS')."</div>".$value."</div>";
                    }
                    elseif ($tag == 'hotlinks')
                    {
                        $value = "";
                        if(count(modApiFunc('Product_Files','getHotlinksList',$this->_product['id'])))
                        {
                            $value = '<br><a href="javascript: void(0);" onClick="openURLinNewWindow(\'popup_window.php?page_view=PF_OrderHotlinks&opid='.$this->_product['id'].'\',\'OrderHotlinks\')"><b>'.getMsg('PF','HOTLINKS').'</b></a>';
                        };
                    }
                    else
                    {
                        $value = getKeyIgnoreCase($tag, $this->_product);
                    }
                }
                elseif ($entity == 'group')
                {
                    $value = getKeyIgnoreCase($tag, $this->_group);
                }
                elseif ($entity == 'attribute')
                {
                    //The code is not correct. Output the warning about CVV.
                    //It would be better to find the id by the tag in the table person_attributes.
                    $cvv_person_attribute_id = 14;
                    if(isset($this->_attr['personattributeid']) &&
                       $this->_attr['personattributeid'] == $cvv_person_attribute_id)
                    {
                        $value = getKeyIgnoreCase($tag, $this->_attr);
                    }
                    else
                    {
                        $value = nl2br(htmlspecialchars(getKeyIgnoreCase($tag, $this->_attr)));
                    }
                }
                break;
        }
        return $value;
    }

    var $TemplateFiller;
    var $MessageResources;
    var $order_id;
    var $_order;
    var $_product;
    var $_group;
    var $_counter;
    var $_attr;
    var $_note;
    var $_status;
    var $_payment_status;
    var $_form_style;
    var $_input_control;
    var $ViewState;
    var $_order_currencies;

    var $_pkey;
    var $HtmlForm;
    var $isOrderEditable;
    var $DisplayIncludedTax;
    var $areIncludedTaxesPresent;
    var $isInfo;
}
?>