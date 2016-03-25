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

class REST_Orders extends RESTResponse
{
    function getOrderInfo($oid)
    {
        $oid = $oid['oid'];
        if (modApiFunc('Checkout', 'isCorrectOrderId', $oid) == false){
            $this->setResponseError('InvalidOrderId', 'Order does not exist.');
            return;
        }

        $order_currency = modApiFunc("Localization", "whichCurrencyToDisplayOrderIn", $oid);
        $currency_iso = modApiFunc('Localization', 'getCurrencyCodeById', $order_currency);

        $order = modApiFunc('Checkout','getOrderInfo',$oid, $order_currency);

        $order['CustomerId'] = $order['PersonId'];
        $order['OrderCurrency'] = $currency_iso;
        $order['ProductNumber'] = count($order['Products']);

        unset($order['PaymentModuleId']);
        unset($order['PaymentMethodDetail']);
        unset($order['PaymentProcessorOrderId']);
        unset($order['TrackId']);
        unset($order['Subtotal']);
        unset($order['Total']);
        unset($order['History']);
        unset($order['CreditCard']);
        unset($order['BankAccount']);
        unset($order['NewType']);
        unset($order['DisplayIncludedTax']);
        unset($order['PersonId']);
        unset($order['Comments']);

        $keys = array('storeProductID', 'name', 'qty', 'SalePrice', 'SKU', 'options');
        reset($order['Products']);
        for($i=0; $i<count($order['Products']); $i++)
        {
            $prd = $order['Products'][$i];
            $order['Products'][$i] = array();
            foreach ($keys as $k) {
                $order['Products'][$i][$k] = $prd[$k];
            }
        }

        $keys = array('Billing', 'Shipping');
        foreach ($keys as $group) {
            $billing = $order[$group];
            $order[$group] = array();
            foreach ($billing['attr'] as $attr_name => $attr_data) {
                $order[$group][$attr_name] = $attr_data['value'];
            }
        }

        $this->setResponseOk($order);
    }
}