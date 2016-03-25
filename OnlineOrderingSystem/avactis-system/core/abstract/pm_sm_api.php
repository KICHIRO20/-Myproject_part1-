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
 * @package core
 * @author Vadim Lyalikov
 */
class pm_sm_api
{
    function pm_sm_api()
    {
    }

    /* static */ function getInitialCurrencySettings()
    {
        $res = array
        (
            'currency_acceptance_rules' => array
            (
                array
                (
                    'rule_name'     =>  DEFAULT_CURRENCY_ACCEPTANCE_RULE_NAME
                   ,'rule_selected' =>  DB_TRUE
                )
            )

           ,'accepted_currencies' => array()
        );
        return $res;
    }

    function addRequestLog($io, $tl_type, $tl_header, $tl_body)
    {
        if (modApiFunc('Settings', 'getParamValue', 'TIMELINE', $io) === 'NO')
            return;

        if (is_array($tl_body))
            $tl_body = prepareArrayDisplay($tl_body);

        modApiFunc('Timeline', 'addLog', $tl_type, $tl_header, $tl_body);
    }

    function getPresentOrderTotalAndCurrency($order_id)
    {
        $currency_id = modApiFunc("Localization", "whichCurrencySendOrderToPaymentShippingGatewayIn", $order_id, $this->getUid());
        $currency = modApiFunc("Localization", "getCurrencyCodeById", $currency_id);
        $order = modApiFunc('Checkout', 'getOrderInfo', $order_id, $currency_id);
        return array('total' => floatval($order['Total']), 'curr' => $currency, 'msg' => getMsg('CHCKT', 'PAYMENT_STATUS_SUSPICIOUS'));
    }
}
?>