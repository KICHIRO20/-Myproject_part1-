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
 *
 * @package Notifications
 * @author Alexander Girin
 */
class NotificationContent
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * A constructor.
     */
    function NotificationContent($condition = array())
    {
        global $application;

        $this->notificationId = $condition['notification_id'];
        $this->orderId = isset($condition['order_id'])? $condition['order_id']:NULL;
        $this->actionId = $condition['action_id'];
        $this->gift_cert = isset($condition['gc_obj']) ? $condition['gc_obj']:NULL;
        $this->MessageResources = &$application->getInstance('MessageResources', "notifications-messages", "AZ");
        $this->customerAccount = null;

        $this->haveToSend = $this->getNotificationInfo($this->notificationId);
        if (!$this->haveToSend)
        {
            return;
        }
        if (isset($condition['status']))
        {
            if ($condition['status'] == 'order')
            {
                $orderNewStatus = modApiFunc("Checkout", "getOrderStatusList", $condition['statuses']['new_status']);
                $orderNewStatus = $orderNewStatus[$condition['statuses']['new_status']]['id'];

                $tables = Notifications::getTables();
                $naov = $tables['notification_action_option_values']['columns'];
                $ov2n = $tables['option_values_to_notification']['columns'];

                $query = new DB_Select();
                $query->addSelectField($ov2n['value'], 'value');
                $query->WhereValue($naov['nao_id'], DB_EQ, 1);
                $query->WhereAnd();
                $query->WhereValue($ov2n['n_id'], DB_EQ, $this->notificationId);
                $query->WhereAnd();
                $query->WhereField($ov2n['naov_id'], DB_EQ, $naov['id']);
                $query->WhereAnd();
                $query->WhereValue($naov['key'], DB_EQ, $orderNewStatus);
                $result = $application->db->getDB_Result($query);

                if (isset($result[0]['value']) && $result[0]['value'] == 'true')
                {
                    $this->haveToSend = true;
                    $orderOldStatus = modApiFunc("Checkout", "getOrderStatusList", $condition['statuses']['old_status']);
                    $this->OrderOldStatus = $orderOldStatus[$condition['statuses']['old_status']]['name'];
                }
                else
                {
                    $this->haveToSend = false;
                }
            }
            if ($condition['status'] == 'payment')
            {
                $orderNewPaymentStatus = modApiFunc("Checkout", "getOrderPaymentStatusList", $condition['statuses']['new_status']);
                $orderNewPaymentStatus = $orderNewPaymentStatus[$condition['statuses']['new_status']]['id'];

                $tables = Notifications::getTables();
                $naov = $tables['notification_action_option_values']['columns'];
                $ov2n = $tables['option_values_to_notification']['columns'];

                $query = new DB_Select();
                $query->addSelectField($ov2n['value'], 'value');
                $query->WhereValue($naov['nao_id'], DB_EQ, 2);
                $query->WhereAnd();
                $query->WhereValue($ov2n['n_id'], DB_EQ, $this->notificationId);
                $query->WhereAnd();
                $query->WhereField($ov2n['naov_id'], DB_EQ, $naov['id']);
                $query->WhereAnd();
                $query->WhereValue($naov['key'], DB_EQ, $orderNewPaymentStatus);
                $result = $application->db->getDB_Result($query);

                if (isset($result[0]['value']) && $result[0]['value'] == 'true')
                {
                    $this->haveToSend = true;
                    $orderOldStatus = modApiFunc("Checkout", "getOrderPaymentStatusList", $condition['statuses']['old_status']);
                    $this->OrderOldPaymentStatus = $orderOldStatus[$condition['statuses']['old_status']]['name'];
                }
                else
                {
                    $this->haveToSend = false;
                }
            }
        }
        if ($this->actionId == 4) //ProductLowLevelInStock
        {
            /*
                                                              :                                    .
                       ,                              ,
                          ,                                      ,      $this->haveToSend
                            false,                       .

                                           ,  . .                                                   ,
                         ,                    ,                              ,
                                       .                                   prepareEmailTextAndSubject.


                        : QuantityInStock   LowStockLevel.

                                                              :
                -
                -

                                    ,                           :
                -                     (           )
                -                    (                                                 )

                                                             :
                -             ProductLowLevelInStock                                        ,

                                     .
             */

            $this->haveToSend = false;
            $orderInfo = modApiFunc("Checkout", "getOrderInfo", $this->orderId, modApiFunc("Localization", "whichCurrencyToDisplayOrderIn", $this->orderId));
            foreach ($orderInfo['Products'] as $productInfo)
            {
                //check if the attributes of the product QuantityInStock and LowStockLevel is visible
                if (!modApiFunc("Catalog", "isProductAttributeVisible", $productInfo['storeProductID'], 3)
                    or
                    !modApiFunc("Catalog", "isProductAttributeVisible", $productInfo['storeProductID'], 6))
                {
                    //                        ,
                    continue;
                }

                $_productInfo = new CProductInfo($productInfo['storeProductID']);
                if ($_productInfo->whichStockControlMethod() == PRODUCT_OPTIONS_INVENTORY_TRACKING)
                {
                    $productQuantityInStock = modApiFunc('Product_Options','getQuantityInStockByInventoryTable','product', $productInfo['storeProductID']);
                }
                else // PRODUCT_QUANTITY_IN_STOCK_ATTRIBUTE
                {
                    $productQuantityInStock = $_productInfo->getProductTagValue('QuantityInStock', PRODUCTINFO_NOT_LOCALIZED_DATA);
                }

                $productLowLevelInStock = $_productInfo->getProductTagValue('LowStockLevel', PRODUCTINFO_NOT_LOCALIZED_DATA);

                //
                //
                if (is_numeric($productLowLevelInStock) and is_numeric($productQuantityInStock))
                {
                    if ($productQuantityInStock <= $productLowLevelInStock)
                    {
                        $this->haveToSend = true;
                        $this->_LowLevelProducts[] = $productInfo['storeProductID'];
                    }
                }
            }
        }
        if ($this->actionId == 5) //DigitalProductsOrdered
        {
            $this->haveToSend = false;

            //                            downloadable products,                              $this->haveToSend = true;
            // id        - $this->orderId

            if(count(modApiFunc('Product_Files','getHotlinksListForOrder',$this->orderId)) > 0)
                $this->haveToSend = true;
        }
        if($this->actionId == 6)
        {
            $this->haveToSend = true;
            $this->customerRegData = $condition['reg_data'];
            $this->customerAccount = $this->customerRegData['account'];
        }
        if($this->actionId >= 7 and $this->actionId <= 13)
        {
            $this->haveToSend = true;
            $this->customerAccount = $condition['account_name'];
        }
        if($this->actionId == 14) //Inventory Low Level (refer to Product Options Inventory)
        {
            $this->haveToSend = true;
            $this->inventory_info = $condition['inventory_info'];
        }
        if ($this -> actionId == 15) // Customer review posted
        {
            $this -> haveToSend = true;
            $this -> review_data = modApiFunc(
                'Customer_Reviews',
                'searchCustomerReviews',
                array('cr_id' => $condition['cr_id'])
            );
            if (!$this -> review_data)
                $this -> haveToSend = false;
            else
                $this -> review_data = $this -> review_data[0];
        }
        if ($this->actionId == 16) //GiftCertificateOrdered
        {
            $this->haveToSend = false;

            if(count(modApiFunc('GiftCertificateApi','getGiftCertificatesForOrderId',$this->orderId)) > 0) // receive a list of GCs purchased in specified order_id
                $this->haveToSend = true;
        }
        if ($this->actionId == 17) //GiftCertificateCreated
        {
            $this->haveToSend = true;

            //check conditions
            //$this->haveToSend = false;
        }
        if ($this->actionId == 18) //Notify-Me
        {
            $this->haveToSend = true;

            //check conditions
            //$this->haveToSend = false;
        }
    }

    /**
     * Sends a notification to each of the receiver.
     */
    function send()
    {
        loadCoreFile('ascHtmlMimeMail.php');
        if (!$this->haveToSend)
        {
            return;
        }

        if (Configuration::getSupportMode(ASC_S_NOTIFICATIONS))
        {
            return;
        }

        // getting the recipients and languages
        $to = $this -> getMLSendTo();

        // adding third party emails to "To" array
        if (isset($this->thirdparty_emails) && is_array($this->thirdparty_emails) && !empty($this->thirdparty_emails))
            foreach ($this->thirdparty_emails as $i => $email)
                $to[] = $email;

        // saving the current languages
        $cur_lng = modApiFunc('MultiLang', 'getLanguage');
        $cur_res_lng = modApiFunc('MultiLang', 'getResourceLanguage');

        // processing the recipients
        foreach($to as $address)
        {
            // skipping invalid records (paranoidal check)
            if (!isset($address[0]) || !$address[0])
                continue;

            // setting the language for the current notification
            modApiFunc('MultiLang', 'setLanguage', @$address[1]);
            modApiFunc('MultiLang', 'setResourceLanguage', @$address[1]);

            $format = modApiFunc('Settings','getParamValue','EMAIL_NOTIFICATION_SETTINGS','EMAIL_NOTIFICATION_FORMAT');
            $this->prepareEmailTextAndSubject();

            // there were some problems with Unix <-> Windows linefeeds
            // so we make it all Windows style
            $this->EmailText = str_replace("\n", "\r\n", str_replace("\r\n", "\n", $this->EmailText));

            $mail = new ascHtmlMimeMail();
	    if($format == "HTML"){
		$mail->setHtml($this->EmailText);
	    }else{
            	$mail->setText($this->EmailText);
	    }
            $mail->setSubject($this->EmailSubject);
            $from = $this->getSendFrom();
            $mail->setFrom($from);
//          $mail->setCc($from);

            $this->addEmailToTimeline($address[0], $mail->send(array($address[0])), @$address[1]);
        }

        // restoting the languages
        modApiFunc('MultiLang', 'setLanguage', $cur_lng);
        modApiFunc('MultiLang', 'setResourceLanguage', $cur_res_lng);
    }

    function addEmailToTimeline($to, $result, $lng)
    {
        if (modApiFunc('Settings','getParamValue','TIMELINE','LOG_EMAIL_SEND') === 'NO')
        {
            return;
        }

        $tl_type = getMsg('NTFCTN','NTFCTN_TL_TYPE');

        $to = prepareHTMLDisplay($to);
        $subj = prepareHTMLDisplay($this->EmailSubject);
        $tl_header = str_replace(
                                    array('{SUBJ}',     '{TO}'),
                                    array( $subj,        $to),
                                    getMsg('NTFCTN','NTFCTN_TL_HEADER')
                                );

        if ($result == true)
        {
            $tl_header .= getMsg('NTFCTN','NTFCTN_TL_SUCCESS');
        }
        else
        {
            $tl_header .= getMsg('NTFCTN','NTFCTN_TL_FAILED');
        }

        //        timeline                         "        ".
        //                       HTML    .
        $body = prepareHTMLDisplay($this->EmailText);
        $tl_body = str_replace('{BODY}', $body, getMsg('NTFCTN','NTFCTN_TL_BODY'));
        $tl_body = str_replace('{LNG}', $lng, $tl_body);
        $tl_body = str_replace("\n", "<br>", $tl_body );

        modApiFunc('Timeline', 'addLog', $tl_type, $tl_header, $tl_body);
    }

    /**
     * Prepares an email text and the subject to send.
     */
    function prepareEmailTextAndSubject()
    {
        global $application;
        $this->customerEmail = null;
        $this->product_obj = null;
        $pushedCurrency = false;

        switch($this->actionId)
        {
            case '1':
            case '2':
            case '3':
            case '4':
            case '5':
            case '16':
                $pushedCurrency = true;
                $currencyId = modApiFunc("Localization", "whichCurrencyToDisplayOrderIn", $this->orderId);
                $orderInfo = modApiFunc("Checkout", "getOrderInfo", $this->orderId, $currencyId);
                modApiFunc("Localization", "pushDisplayCurrency", $currencyId, $currencyId);

                $customer_id = $orderInfo['PersonId'];
                $account_name = modApiFunc('Customer_Account','getCustomerAccountNameByCustomerID',$customer_id);
                $this->customer_obj = &$application->getInstance('CCustomerInfo',$account_name);
//                $this->customerEmail = $this->customer_obj->getPersonInfo('Email','Customer');
                $taxExemptEnabled = modApiFunc('Settings','getParamValue','TAXES_PARAMS','ALLOW_FULL_TAX_EXEMPTS');
                $taxExemptInfo = modApiFunc("TaxExempts", "getOrderFullTaxExempts", $this->orderId, false);
                break;

            case '6':
//                if(array_key_exists('Email',$this->customerRegData['info']))
//                    $this->customerEmail = $this->customerRegData['info']['Email'];
                $this->customer_obj = &$application->getInstance('CCustomerInfo',$this->customerRegData['account']);
                break;

            case '7':
            case '8':
            case '9':
            case '10':
            case '11':
            case '12':
            case '13':
                $this->customer_obj = &$application->getInstance('CCustomerInfo',$this->customerAccount);
//                $this->customerEmail = $this->customer_obj->getPersonInfo('Email','Customer');
                break;

            case '14':
                $this->product_obj = &$application->getInstance('CProductInfo',$this->inventory_info['entity_id']);
                break;

            case '15':
                $this -> product_obj = &$application -> getInstance('CProductInfo', $this -> review_data['product_id']);
                break;
            case '18':
                $this->customer_obj = &$application->getInstance('CCustomerInfo',$this->customerAccount);
                $this -> product_obj = &$application -> getInstance('CProductInfo', $this -> review_data['product_id']);
                break;
        };

        // reloading the notification info since it may be different for each language
        $this -> getNotificationInfo($this -> notificationId);

        $tagsList = modApiFunc("Notifications", "getAvailableTagsList", array('0' => array('Id' => $this->actionId)));

        //initialize infotags
        $infotags = array();
        foreach ($tagsList[$this->actionId]['InfoTags'] as $infotag)
        {
            if (_ml_strpos($infotag, "Order") == 1)
            {
                $tag = _ml_substr($infotag, 6, _ml_strlen($infotag)-7);
                switch ($tag)
                {
                    case 'Date':
                        $infotags[$infotag] = modApiFunc("Localization", "date_format", $orderInfo[$tag]);
                        break;
                    case 'GlobalDiscount':
                        $infotags[$infotag] = modApiFunc("Localization", "currency_format", $orderInfo['Price']['SubtotalGlobalDiscount']);
                        break;
                    case 'PromoCodeDiscount':
                        $infotags[$infotag] = modApiFunc("Localization", "currency_format", $orderInfo['Price']['SubtotalPromoCodeDiscount']);
                        break;
                    case 'QuantityDiscount':
                        $infotags[$infotag] = modApiFunc("Localization", "currency_format", $orderInfo['Price']['QuantityDiscount']);
                        break;
                    case 'DiscountedSubtotal':
                        $infotags[$infotag] = modApiFunc("Localization", "currency_format", $orderInfo['Price']['DiscountedSubtotal']);
                        break;
                    case 'TotalShippingAndHandlingCost':
                        $ShippingCost = ($orderInfo['Price']['TotalShippingAndHandlingCost'] == PRICE_N_A) ? 0.0 : $orderInfo['Price']['TotalShippingAndHandlingCost'];
                        $infotags[$infotag] = modApiFunc("Localization", "currency_format", ($ShippingCost));
                        break;
                    case 'Tax':
                        $infotags[$infotag] = "";
                        $taxes = array();
                        foreach ($orderInfo['Price'] as $key => $value)
                        {
                            $tax_name_patern = "/^Taxes\['(.+)'\]$/";
                            $matches = array();
                            if(preg_match($tax_name_patern, $key, $matches))
                            {
                                $taxes[$matches[1]] = $value;
                            }
                        }

                        foreach ($taxes as $name => $value)
                        {
                            $infotags[$infotag].= $name." ".modApiFunc("Localization", "currency_format", $value)."\n";
                        }
                        break;
                    case 'OldStatus':
                        if ($this->actionId == '2')
                        {
                            $infotags[$infotag] = $this->OrderOldStatus;
                        }
                        break;
                    case 'OldPaymentStatus':
                        if ($this->actionId == '3')
                        {
                            $infotags[$infotag] = $this->OrderOldPaymentStatus;
                        }
                        break;
                    case 'Total':
                    case 'Subtotal':
                        $infotags[$infotag] = modApiFunc("Localization", "currency_format", $orderInfo[$tag]);
                        break;
                    case 'PaymentMethodText':
                    {
                        $pm_uid = $orderInfo['PaymentModuleId'];
                        $infotags[$infotag] = modApiFunc("Checkout", "getPaymentMethodText", $pm_uid);
                        break;
                    }

                    case "PromoCode":
                        $couponInfo = modApiFunc("PromoCodes", "getOrderCoupons", $this->orderId);
                        if (isset($couponInfo[0]["coupon_promo_code"])) {
                            $infotags[$infotag] = $couponInfo[0]["coupon_promo_code"];
                        }
                        else {
                            $infotags[$infotag] = "";
                        }
                        break;

                    case "TaxExptMark":
                        if ($taxExemptEnabled == "true"
                            && isset($taxExemptInfo[0]["exempt_status"])
                            && $taxExemptInfo[0]["exempt_status"] == "true")
                            {
                                $infotags[$infotag] = getMsg("SYS","FULL_TAX_EXEMPT_YES_MSG");
                            }
                            else
                            {
                                $infotags[$infotag] = getMsg("SYS","FULL_TAX_EXEMPT_NO_MSG");
                            }
                        break;

                    case "TaxExptAmount":
                        if ($taxExemptEnabled == "true"
                            && isset($taxExemptInfo[0]["exempt_status"])
                            && $taxExemptInfo[0]["exempt_status"] == "true")
                            {
                                $infotags[$infotag] = modApiFunc("Localization", "currency_format", $orderInfo["Price"]["OrderTaxTotal"]);
                            }
                            else
                            {
                                $infotags[$infotag] = modApiFunc("Localization", "currency_format", "0.0000");
                            }
                        break;

                    case "TaxExptInput":
                        if ($taxExemptEnabled == "true"
                            && isset($taxExemptInfo[0]["exempt_status"])
                            && $taxExemptInfo[0]["exempt_status"] == "true")
                            {
                                $infotags[$infotag] = $taxExemptInfo[0]["exempt_reason_customer_input"];
                            }
                            else
                            {
                                $infotags[$infotag] = '';
                            }
                        break;
                    case 'OrderTotalToPay':
                        $infotags[$infotag] = modApiFunc("Localization", "currency_format", $orderInfo['Price']['OrderTotalToPay']);
                        break;
                    case 'OrderTotalPrepaidByGC':
                        $infotags[$infotag] = modApiFunc("Localization", "currency_format", $orderInfo['Price']['OrderTotalPrepaidByGC']);
                        break;

                    default:
                        $infotags[$infotag] = $orderInfo[$tag];
                        break;
                }
            }
            elseif (_ml_strpos($infotag, 'CustomerReview') == 1)
            {
                $tag = _ml_substr($infotag, 15, _ml_strlen($infotag) - 16);

                $tag_value = '';
                if ($this -> review_data)
                    switch($tag)
                    {
                        case 'Date':
                            $tag_value = $this -> review_data['date'];
                            break;

                        case 'Time':
                            $tag_value = $this -> review_data['time'];
                            break;

                        case 'Author':
                            $tag_value = _ml_substr($this -> review_data['author'], 0, 56) .
                                         ((_ml_strlen($this -> review_data['author']) > 56)
                                         ? '...' : '');
                            break;

                        case 'IP':
                            $tag_value = $this -> review_data['ip_address'];
                            break;

                        case 'Status':
                            if ($this -> review_data['status'] == 'A')
                                $tag_value = getMsg('CR', 'CR_STATUS_APPROVED');
                            elseif ($this -> review_data['status'] == 'P')
                                $tag_value = getMsg('CR', 'CR_STATUS_PENDING');
                            else
                                $tag_value = getMsg('CR', 'CR_STATUS_NOTAPPROVED');
                            break;

                        case 'Text':
                            $tag_value = $this -> review_data['review'];
                            break;

                        case 'OverallRating':
                            $tag_value = getMsg('CR', 'CR_RECORD_NO_RATE');
                            if (is_array($this -> review_data['rating'])
                                && !empty($this -> review_data['rating']))
                            {
                                $sum = 0;
                                foreach($this -> review_data['rating'] as $v)
                                    $sum += $v['rate'];

                                $tag_value = sprintf("%.2f",
                                                     $sum / count($this -> review_data['rating']));
                            }
                            break;
                    }

                $infotags[$infotag] = $tag_value;
            }
            elseif (_ml_strpos($infotag, "Customer") == 1)
            {
                $tag = _ml_substr($infotag, 9, _ml_strlen($infotag)-10);
                $infotags[$infotag] = $this->customer_obj->getPersonInfo($tag,'Customer');

                switch(_ml_strtolower($tag))
                {
                    case 'country':
                        $infotags[$infotag] = modApiFunc('Location','getCountry',$infotags[$infotag]);
                        break;
                    case 'state':
                        if(modApiFunc('Location','getStateCode',$infotags[$infotag]) != '')
                            $infotags[$infotag] = modApiFunc('Location','getState',$infotags[$infotag]);
                        else
                            $infotags[$infotag] = $infotags[$infotag];
                        break;
                };
            }
            elseif (_ml_strpos($infotag, "Shipping") == 1)
            {
                $tag = _ml_substr($infotag, 9, _ml_strlen($infotag)-10);
                $infotags[$infotag] = isset($orderInfo['Shipping']['attr'][$tag]['value'])? $orderInfo['Shipping']['attr'][$tag]['value']:(isset($orderInfo['Shipping']['attr'][$tag."NULL"]['value'])? $orderInfo['Shipping']['attr'][$tag."NULL"]['value']:"");
            }
            elseif (_ml_strpos($infotag, "Billing") == 1)
            {
                $tag = _ml_substr($infotag, 8, _ml_strlen($infotag)-9);
                $infotags[$infotag] = isset($orderInfo['Billing']['attr'][$tag]['value'])? $orderInfo['Billing']['attr'][$tag]['value']:"";
            }
            elseif (_ml_strpos($infotag, "StoreOwner") == 1)
            {
                $tag = _ml_substr($infotag, 11, _ml_strlen($infotag)-12);
                switch ($tag)
                {
                    case "StreetLine1":
                        $infotags[$infotag] = modApiFunc("Configuration", "getValue", "store_owner_street_line_1");
                        break;
                    case "StreetLine2":
                        $infotags[$infotag] = modApiFunc("Configuration", "getValue", "store_owner_street_line_2");
                        break;
                    case "SiteAdministratorEmail":
                        $infotags[$infotag] = modApiFunc("Configuration", "getValue", "store_owner_site_administrator_email");
                        break;
                    case "OrdersDepartmentEmail":
                        $infotags[$infotag] = modApiFunc("Configuration", "getValue", "store_owner_orders_department_email");
                        break;
                    case "Country":
                        $infotags[$infotag] = modApiFunc("Location", "getCountry", modApiFunc("Configuration", "getValue", "store_owner_country"));
                        break;
                    case "State":
                        $state = modApiFunc("Configuration", "getValue", "store_owner_state");
                        $states_in_country = modApiFunc("Location", "getStates", modApiFunc("Configuration", "getValue", "store_owner_country"));
                        if (array_key_exists($state, $states_in_country))
                        {
                            $infotags[$infotag] = modApiFunc("Location", "getState", $state);
                        }
                        else
                        {
                            $infotags[$infotag] = $state;
                        }
                        break;
                    default:
                        $infotags[$infotag] = modApiFunc("Configuration", "getValue", "store_owner_"._ml_strtolower($tag));
                        break;
                }
            }
            elseif (_ml_strpos($infotag, "TrackingNumber") == 1)
            {
                $infotags[$infotag] = $orderInfo["TrackId"];
            }
            elseif(_ml_strpos($infotag, 'AccountActivationLink') == 1)
            {
                $r = new Request();
                $r->setView('AccountActivation');
                $r->setAction('activate_account');
                $r->setKey('key',modApiFunc('Customer_Account','getActivationKey',$this->customerAccount));
                $infotags[$infotag] = $r->getURL();
            }
            elseif(_ml_strpos($infotag, 'AccountName') == 1)
            {
                $infotags[$infotag] = $this->customerAccount;
            }
            elseif(_ml_strpos($infotag, 'AccountNewPasswordLink') == 1)
            {
                $cz_layouts = LayoutConfigurationManager::static_get_cz_layouts_list();
                LayoutConfigurationManager::static_activate_cz_layout(array_shift(array_keys($cz_layouts)));
                $r = new CZRequest();
                $r->setView('CustomerNewPassword');
                $r->setKey('key',modApiFunc('Customer_Account','getActivationKey',$this->customerAccount));
                $infotags[$infotag] = $r->getURL();
            }
            elseif(preg_match('/^\{combination(.+)\}$/i',$infotag,$matches))
            {
                $tag_value = '';

                switch(_ml_strtolower($matches[1]))
                {
                    case 'sku':
                        $tag_value = $this->inventory_info['sku'];
                        break;
                    case 'quantityinstock':
                        $tag_value = $this->inventory_info['quantity'];
                        break;
                    case 'description':
                        $tag_value = modApiFunc('Product_Options','convertCombinationToString',modApiFunc('Product_Options','_unserialize_combination',$this->inventory_info['combination']),'; ');
                        break;
                    default:
                        $tag_value = '';
                        break;
                };

                $infotags[$infotag] = $tag_value;
            }
            elseif(preg_match('/^\{product(.+)\}$/i',$infotag,$matches) and $this->product_obj != null)
            {
                $infotags[$infotag] = $this->product_obj->getProductTagValue($matches[1],PRODUCTINFO_LOCALIZED_DATA);
            }
            elseif(_ml_strpos($infotag, 'GiftCertificateRecipient') == 1)
            {
                if ($this->gift_cert != null)
                {
                    $infotags[$infotag] = $this->gift_cert->to;
                }
            }
        }

        //initialize blocktags
        foreach ($tagsList[$this->actionId]['BlockTags'] as $blocktagId => $blocktagInfo)
        {
            $infotags[$blocktagInfo['BlockTag']] = "";
            switch ($blocktagInfo['BlockTag'])
            {
                case '{OrderContentBlock}':
                    foreach ($orderInfo['Products'] as $productInfo)
                    {
                        # settings default values for all infotags
                        foreach ($blocktagInfo['BlockInfoTags'] as $t)
                        {
                            $t = strtr($t, array('{'=>'\{','}'=>'\}'));
                            $init_productInfoTags[$t] = "";
                        }

                        $productInfoTags = array(
                            "{ProductID}" => $productInfo['storeProductID'],
                            "{ProductName}" => $productInfo['name'],
                            "{ProductQuantity}" => $productInfo['qty'],
                            "{ProductPrice}" => modApiFunc("Localization", "currency_format", $productInfo['SalePrice']),
                            "{ProductAmount}" => modApiFunc("Localization", "currency_format", ($productInfo['qty']*$productInfo['SalePrice'])),
                            "{ProductOptions}" => modApiFunc("Product_Options","prepareTextForNotificaton",$productInfo['options'])
                        );

                        $productInfoTags = array_merge($init_productInfoTags, $productInfoTags);

                        foreach ($productInfo['attr'] as $attr => $attr_value)
                        {
                            if (
                                $attr == "SalePrice" ||
                                $attr == "ListPrice" ||
                                $attr == "PerItemShippingCost" ||
                                $attr == "PerItemHandlingCost"
                               )
                            {
                                $productInfoTags["{Product".$attr."}"] = modApiFunc("Localization", "currency_format", $attr_value["value"]);
                            }
                            elseif (
                                    $attr == "FreeShipping" ||
                                    $attr == "NeedShipping"
                                   )
                            {
                                $productInfoTags["{Product".$attr."}"] = ($attr_value["value"] == PRODUCT_FREESHIPPING_YES)? $this->MessageResources->getMessage("NTFCTN_INFO_YES_LABEL"):$this->MessageResources->getMessage("NTFCTN_INFO_NO_LABEL");
                            }
                            else
                            {
                                $productInfoTags["{Product".$attr."}"] = $attr_value["value"];
                            }
                        }
                        foreach ($productInfo['custom_attributes'] as $custom_attr_info)
                        {
                            $productInfoTags["{Product".$custom_attr_info["tag"]."Custom}"] = $custom_attr_info["value"];
                        }

                        $blocktag_body = $this->getNotificationBlockBody($blocktagId);
                        foreach ($productInfoTags as $tag => $val)
                        {
                            $blocktag_body = str_ireplace($tag, $val, $blocktag_body);
                        }
                        $infotags[$blocktagInfo['BlockTag']].= $blocktag_body."\n";
                    }
                    break;
                case '{LowLevelProductsBlock}':
                    foreach ($orderInfo['Products'] as $productInfo)
                    {
                        if (!in_array($productInfo['storeProductID'], $this->_LowLevelProducts))
                        {
                            continue;
                        }

                        $_productInfo = new CProductInfo($productInfo['storeProductID']);

                        if ($_productInfo->whichStockControlMethod() == PRODUCT_OPTIONS_INVENTORY_TRACKING)
                        {
                            $productQuantityInStock = modApiFunc('Product_Options','getQuantityInStockByInventoryTable','product', $productInfo['storeProductID']);
                        }
                        else // PRODUCT_QUANTITY_IN_STOCK_ATTRIBUTE
                        {
                            $productQuantityInStock = $_productInfo->getProductTagValue('QuantityInStock', PRODUCTINFO_NOT_LOCALIZED_DATA);
                        }
                        $productLowLevelInStock = $_productInfo->getProductTagValue('LowStockLevel', PRODUCTINFO_NOT_LOCALIZED_DATA);

                        $productInfoTags = array(
                            "{ProductID}" => $productInfo['storeProductID'],
                            "{ProductName}" => $productInfo['name'],
//                                    "{ProductQuantity}" => $productInfo['qty'],
                            "{ProductPrice}" => modApiFunc("Localization", "currency_format", $productInfo['SalePrice']),
//                                    "{ProductAmount}" => modApiFunc("Localization", "currency_format", ($productInfo['qty']*$productInfo['SalePrice'])),
                            "{ProductQuantityInStock}" => (string)$productQuantityInStock
                        );

                        foreach ($productInfo['attr'] as $attr => $attr_value)
                        {
                            if (
                                $attr == "SalePrice" ||
                                $attr == "ListPrice" ||
                                $attr == "PerItemShippingCost" ||
                                $attr == "PerItemHandlingCost"
                               )
                            {
                                $productInfoTags["{Product".$attr."}"] = modApiFunc("Localization", "currency_format", $attr_value["value"]);
                            }
                            elseif (
                                    $attr == "FreeShipping" ||
                                    $attr == "NeedShipping"
                                   )
                            {
                                $productInfoTags["{Product".$attr."}"] = ($attr_value["value"] == PRODUCT_FREESHIPPING_YES)? $this->MessageResources->getMessage("NTFCTN_INFO_YES_LABEL"):$this->MessageResources->getMessage("NTFCTN_INFO_NO_LABEL");
                            }
                            else if ($attr != "QuantityInStock")
                            {
                                $productInfoTags["{Product".$attr."}"] = $attr_value["value"];
                            }
                        }
                        foreach ($productInfo['custom_attributes'] as $custom_attr_info)
                        {
                            $productInfoTags["{Product".$custom_attr_info["tag"]."Custom}"] = $custom_attr_info["value"];
                        }

                        $blocktag_body = $this->getNotificationBlockBody($blocktagId);
                        foreach ($productInfoTags as $tag => $val)
                        {
                            $blocktag_body = str_ireplace($tag, $val, $blocktag_body);
                        }
                        $infotags[$blocktagInfo['BlockTag']].= $blocktag_body."\n";
                    }
                    break;

                case '{OrderDiscountsBlock}':
                    $promoInfo = modApiFunc("PromoCodes", "getOrderCoupons", $this->orderId);
                    $promoCode = (isset($promoInfo[0]["coupon_promo_code"])) ? $promoInfo[0]["coupon_promo_code"] : "";

                    $discountsInfoTags = array(
                            "{OrderGlobalDiscount}"     => modApiFunc("Localization", "currency_format", $orderInfo['Price']['SubtotalGlobalDiscount']),
                            "{OrderPromoCode}"          => $promoCode,
                            "{OrderPromoCodeDiscount}"  => modApiFunc("Localization", "currency_format", $orderInfo['Price']['SubtotalPromoCodeDiscount']),
                            "{OrderQuantityDiscount}"   => modApiFunc("Localization", "currency_format", $orderInfo['Price']['QuantityDiscount']),
                            "{OrderDiscountedSubtotal}" => modApiFunc("Localization", "currency_format", $orderInfo['Price']['DiscountedSubtotal']),
                            "{OrderSubtotal}"           => modApiFunc("Localization", "currency_format", $orderInfo['Price']['OrderSubtotal']),
                            "{OrderTotalToPay}"         => modApiFunc("Localization", "currency_format", $orderInfo['Price']['OrderTotalToPay']),
                            "{OrderTotalPrepaidByGC}"   => modApiFunc("Localization", "currency_format", $orderInfo['Price']['OrderTotalPrepaidByGC'])
                    );
                    $blocktag_body = $this->getNotificationBlockBody($blocktagId);

                    foreach ($discountsInfoTags as $tag => $val)
                    {
                        $blocktag_body = str_ireplace($tag, $val, $blocktag_body);
                    }
                    $infotags[$blocktagInfo['BlockTag']].= $blocktag_body;
                    break;

                case '{OrderDownloadLinksBlock}':
                    $hotlinks = modApiFunc('Product_Files','getHotlinksListForOrder',$this->orderId);
                    foreach($hotlinks as $k => $hotlink_info)
                    {
                        $file_info = modApiFunc('Product_Files','getPFileInfo',$hotlink_info['file_id']);
                        $HotlinkInfoTags = array(
                                "{DownloadLink}" => $hotlink_info['hotlink_value']
                               ,"{DownloadLinkExpiryDate}" => date("d M Y, H:i", $hotlink_info['expire_date'])
                               ,"{DownloadLinkAttempts}" => $hotlink_info['max_try']
                               ,"{DownloadFilename}" => $file_info['file_name']
                               ,"{DownloadFileDescription}" => $file_info['file_descr']
                        );
                        $blocktag_body = $this->getNotificationBlockBody($blocktagId);
                        foreach ($HotlinkInfoTags as $tag => $val)
                        {
                            $blocktag_body = str_ireplace($tag, $val, $blocktag_body);
                        }
                        $infotags[$blocktagInfo['BlockTag']].= $blocktag_body;
                    }
                    break;

                case '{OrderedGiftCertificateBlock}':
                    $gcs = array();
                    if ($this->gift_cert)
                    {
                        $gcs = array(modApiFunc("GiftCertificateApi", "getGiftCertificate", $this->gift_cert->code));
                    }
                    else if ($this->orderId)
                    {
                        $gcs = modApiFunc("GiftCertificateApi","getGiftCertificatesForOrderId",$this->orderId);
                    }
                    if (sizeof($gcs) > 0)
                    {
                        foreach($gcs as $gc)
                        {
                            $GCInfoTags = array(
                                    "{PurchasedGiftCertificateCode}"    => $gc["gc_code"]
                                   ,"{PurchasedGiftCertificateMessage}" => $gc["gc_message"]
                                   ,"{PurchasedGiftCertificateAmount}"  => modApiFunc("Localization", "currency_format", $gc["gc_amount"])
                                   ,"{PurchasedGiftCertificateFrom}"    => $gc["gc_from"]
                                   ,"{PurchasedGiftCertificateTo}"      => $gc["gc_to"]
                                   ,"{PurchasedGiftCertificateType}"    => $gc["gc_sendtype"]
                            );
                            $blocktag_body = $this->getNotificationBlockBody($blocktagId);
                            foreach ($GCInfoTags as $tag => $val)
                            {
                                $blocktag_body = str_replace($tag, $val, $blocktag_body);
                            }
                            $infotags[$blocktagInfo['BlockTag']].= $blocktag_body;
                        }
                    }
                    break;

                case '{AppliedGiftCertificateBlock}':
                    $gcs = modApiFunc("GiftCertificateApi","getOrderGCs",$this->orderId);
                    if (!empty($gcs) && is_array($gcs))
                    {
                        foreach($gcs as $i => $gc_data)
                        {
                            $gc = new GiftCertificate($gc_data['gc_code']);

                            $GCInfoTags = array(
                                "{AppliedGiftCertificateCode}"    => $gc->code
                               ,"{AppliedGiftCertificateMessage}" => $gc->message
                               ,"{AppliedGiftCertificateAmount}"  => modApiFunc("Localization", "currency_format", $gc->amount)//$this->gift_cert->amount
                               ,"{AppliedGiftCertificateFrom}"    => $gc->from
                               ,"{AppliedGiftCertificateTo}"      => $gc->to
                               ,"{AppliedGiftCertificateType}"    => $gc->sendtype
                            );
                            $blocktag_body = $this->getNotificationBlockBody($blocktagId);
                            foreach ($GCInfoTags as $tag => $val)
                            {
                                $blocktag_body = str_replace($tag, $val, $blocktag_body);
                            }
                            $infotags[$blocktagInfo['BlockTag']].= $blocktag_body;
                        }
                    }
                    break;


            }
        };

        $this->EmailText = $this->getNotificationBody();
        $this->EmailSubject = $this->subject;

        foreach ($infotags as $tag => $val)
        {
            $this->EmailText = str_ireplace($tag, $val, $this->EmailText);
            $this->EmailSubject = str_ireplace($tag, $val, $this->EmailSubject);
        };

        $this->EmailText = $this->html_replace($this->EmailText);
        $this->EmailSubject = $this->html_replace($this->EmailSubject);

        if ($pushedCurrency)
            modApiFunc("Localization", "popDisplayCurrency");
    }

    /**
     * Gets notification info.
     *
     * @param integer $n_id - notification id
     */
    function getNotificationInfo($n_id)
    {
        global $application;

        $tables = Notifications::getTables();
        $n  = $tables['notifications']['columns'];

        $query = new DB_Select();

        $query->setMultiLangAlias('_ml_ntfctn_subject', 'notifications', $n['subject'], $n['id'], 'Notifications');
        $query->setMultiLangAlias('_ml_ntfctn_body', 'notifications', $n['body'], $n['id'], 'Notifications');
        $query->setMultiLangAlias('_ml_ntfctn_from_email_custom_address', 'notifications', $n['from_email_custom_address'], $n['id'], 'Notifications');

        $query->addSelectField($query->getMultiLangAlias('_ml_ntfctn_subject'), 'Subject');
        $query->addSelectField($query->getMultiLangAlias('_ml_ntfctn_body'), 'Body');
        $query->addSelectField($query->getMultiLangAlias('_ml_ntfctn_from_email_custom_address'), 'From_addr');

        $query->addSelectField($n['from_email_admin_id'], 'Admin_ID');
        $query->addSelectField($n['from_email_code'], 'Email_Code');
        $query->WhereValue($n['id'], DB_EQ, $n_id);
        $query->WhereAnd();
        $query->WhereValue($n['active'], DB_EQ, 'checked');
        $result = $application->db->getDB_Result($query);
        if (sizeof($result))
        {
            $this->subject = $result[0]['Subject'];
            $this->body = $result[0]['Body'];
            $this->from = $result[0]['From_addr'];
            $this->from_code = $result[0]['Email_Code'];
            $this->from_admin_id = $result[0]['Admin_ID'];
            return true;
        }
        else
        {
            return false;
        }
    }


    /**
     * Gets a notification body.
     *
     */
    function getNotificationBody()
    {
        return $this->body;
    }

    /**
     * Gets a blocktag body.
     *
     *@param integer $b_id - id blocktag-a
     */
    function getNotificationBlockBody($b_id)
    {
        $body = "";
        global $application;

        $tables = Notifications::getTables();
        $nbb = $tables['notification_blocktag_bodies']['columns'];

        $query = new DB_Select();

        $query->setMultiLangAlias('_ml_ntfctn_body', 'notification_blocktag_bodies', $nbb['body'], $nbb['id'], 'Notifications');

        $query->addSelectField($query->getMultiLangAlias('_ml_ntfctn_body'), 'Body');

        $query->WhereValue($nbb['n_id'], DB_EQ, $this->notificationId);
        $query->WhereAnd();
        $query->WhereValue($nbb['nb_id'], DB_EQ, $b_id);
        $result = $application->db->getDB_Result($query);

        if (sizeof($result) != 0)
        {
            $body = $result[0]['Body'];
        }

        return $body;
    }

    /**
     * Gets a sender address.
     *
     */
    function getSendFrom()
    {
        return modApiFunc("Notifications", "getExtendedEmail", $this->from, $this->from_code, true, (empty($this->from_admin_id) ? NULL : $this->from_admin_id));
    }

    /**
     * Gets a list of receiver addesses.
     *
     */
    function getSendTo()
    {
        global $application;

        $tables = Notifications::getTables();
        $nst = $tables['notification_send_to']['columns'];

        $query = new DB_Select();
        $query->addSelectField($nst['email'], 'Email');
        $query->addSelectField($nst['code'], 'Email_Code');
        $query->WhereValue($nst['n_id'], DB_EQ, $this->notificationId);
        $result = $application->db->getDB_Result($query);

        $to = array();
        foreach ($result as $ToEmail)
        {
            if ($ToEmail['Email_Code'] == 'EMAIL_CUSTOMER' and $this->customerEmail != null)
            {
                $to[] = $this->customerEmail;
            }
            else
            {
                $email = modApiFunc("Notifications", "getExtendedEmail", $ToEmail['Email'], $ToEmail['Email_Code'], true);
                if ($email)
                {
                    $to[] = $email;
                }
            }
        }
        return $to;
    }

    /**
     * Gets a list of receiver addesses together with their languages.
     *
     */
    function getMLSendTo()
    {
        global $application;

        $tables = Notifications::getTables();
        $nst = $tables['notification_send_to']['columns'];

        $query = new DB_Select();
        $query->addSelectField($nst['email'], 'Email');
        $query->addSelectField($nst['code'], 'Email_Code');
        $query->WhereValue($nst['n_id'], DB_EQ, $this->notificationId);
        $result = $application->db->getDB_Result($query);

        $to = array();
        foreach ($result as $ToEmail)
        {
            if ($ToEmail['Email_Code'] == 'EMAIL_CUSTOMER')
            {
                $customerEmail = null;
                switch($this->actionId)
                {
                    case '1':
                    case '2':
                    case '3':
                    case '4':
                    case '5':
                        $pushedCurrency = true;
                        $currencyId = modApiFunc('Localization', 'whichCurrencyToDisplayOrderIn', $this->orderId);
                        $orderInfo = modApiFunc('Checkout', 'getOrderInfo', $this->orderId, $currencyId);
                        modApiFunc('Localization', 'pushDisplayCurrency', $currencyId, $currencyId);

                        $customer_id = $orderInfo['PersonId'];
                        $account_name = modApiFunc('Customer_Account','getCustomerAccountNameByCustomerID',$customer_id);
                        $customer_obj = &$application -> getInstance('CCustomerInfo',$account_name);
                        /* download links should be sent to shipping email, other info to main email */
                        $destination = $this->actionId == 5 ? 'Shipping' : 'Customer';
                        $customerEmail = $customer_obj -> getPersonInfo('Email', $destination);
                        if (!$customerEmail)
                            $customerEmail = $customer_obj -> getPersonInfo('Email', 'Customer');
                        $customerLng = $customer_obj -> getPersonInfo('Notification_Lng');
                        break;

                    case '6':
                        if (array_key_exists('Email', $this -> customerRegData['info']))
                        {
                            $customerEmail = $this -> customerRegData['info']['Email'];
                            $customerLng = modApiFunc('MultiLang', 'getLanguage');
                        }
                        break;

                    case '7':
                    case '8':
                    case '9':
                    case '10':
                    case '11':
                    case '12':
                    case '13':
                    case '15':
                        $customer_obj = &$application -> getInstance('CCustomerInfo', $this -> customerAccount);
                        $customerEmail = $customer_obj -> getPersonInfo('Email', 'Customer');
                        $customerLng = $customer_obj -> getPersonInfo('Notification_Lng');
                        break;
                };

                if ($customerEmail)
                {
                    if (!modApiFunc('MultiLang', 'checkLanguage', $customerLng))
                        $customerLng = modApiFunc('MultiLang', 'getDefaultLanguage');

                    if (!modApiFunc('MultiLang', 'checkLanguage', $customerLng))
                        $customerLng = modApiFunc('MultiLang', '_getAnyLanguage');

                    $to[] = array($customerEmail, $customerLng);
                }
            }
            else
            {
                $email = modApiFunc("Notifications", "getExtendedEmail", $ToEmail['Email'], $ToEmail['Email_Code'], true, NULL, true);
                if ($email[0])
                {
                    $to[] = $email;
                }
            }
        }

        return $to;
    }

    /**
     *
     */
    function html_replace($text)
    {
        $search = array ("'<script[^>]*?>.*?</script>'si",  //          javaScript
//                         "'([\r\n])[\s]+'",                //
                         "'&(amp|#38);'i",
                         "'&(lt|#60);'i",
                         "'&(gt|#62);'i",
                         "'&(nbsp|#160);'i",
                         "'&(iexcl|#161);'i",
                         "'&(cent|#162);'i",
                         "'&(pound|#163);'i",
                         "'&(copy|#169);'i",
                         "'&#(\d+);'e");                    //                      php-

	$format = modApiFunc('Settings','getParamValue','EMAIL_NOTIFICATION_SETTINGS','EMAIL_NOTIFICATION_FORMAT');
	if($format != 'HTML')
	{
		$search = array_merge( $search, array (
					"'<[\/\!]*?[^<>]*?>'si",          //          HTML-
					"'&(quot|#34);'i",                //          HTML-
					));
	}

        $replace = array ("",
                         "",
//                         "\\1",
                         "\"",
                         "&",
                         "<",
                         ">",
                         " ",
                         _byte_chr(161),
                         _byte_chr(162),
                         _byte_chr(163),
                         _byte_chr(169),
                         "chr(\\1)");

        return preg_replace($search, $replace, $text);
    }

    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    var $_LowLevelProducts = array();
    var $review_data;

    /**#@-*/

}
?>