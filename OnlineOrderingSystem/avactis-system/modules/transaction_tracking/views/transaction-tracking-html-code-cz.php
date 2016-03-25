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
 * TransactionTrackingHtmlCode view.
 *
 * @package TransactionTracking
 * @author VadimLyalikov
 */

class TransactionTrackingHtmlCode
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     *  TransactionTracking constructor.
     */
    function TransactionTrackingHtmlCode()
    {
    }

    /**
     * Converts the value of the monetary sum to be used _out_ ASC.
     * If the price equals PRICE_N_A, then it is changed to 0.0.
     */
    function export_PRICE_N_A($price)
    {
        return ($price == PRICE_N_A) ? 0.0 : $price;
    }


    /**
     * Otputs the view.
     *
     * @ $request->setView  ( '' ) - define the view name
     */
    function outputGA()
    {
        global $application;

        $last_placed_order_id = modApiFunc("Checkout", "getLastPlacedOrderID");
        if($last_placed_order_id !== NULL)
        {
	        $settings = TransactionTracking::getModulesSettings();
	        $GA_ACCOUNT_NUMBER = $settings[MODULE_GOOGLE_ANALYTICS_UID]['GA_ACCOUNT_NUMBER'];

	        $container_template = TransactionTracking::getIncludedFileContents("google_analytics_container.tpl.html");
	        $item_template = TransactionTracking::getIncludedFileContents("google_analytics_item.tpl.html");

                $orderInfo = modApiFunc("Checkout", "getOrderInfo", $last_placed_order_id, modApiFunc("Localization", "whichCurrencySendOrderToPaymentShippingGatewayIn", $last_placed_order_id, GET_PAYMENT_MODULE_FROM_ORDER));
	        $ITEMS = "";
	        foreach($orderInfo['Products'] as $product_info)
	        {
	        	$handpicked_options = "";
	        	for($j=0;$j<count($product_info['options']);$j++)
                {
                    $handpicked_options .= $product_info['options'][$j]['option_name'].": ".$product_info['options'][$j]['option_value'].";";
                }
	        	$handpicked_options = htmlspecialchars($handpicked_options);

	        	$SKU = getKeyIgnoreCase('SKU', $product_info['attr']);
	        	$SKU = $SKU['value'];

	        	$SalePrice = getKeyIgnoreCase('SalePrice', $product_info['attr']);
	        	$SalePrice = $SalePrice['value'];

	        	$item_data = array
	        	(
                    "ORDER_ID"     => $last_placed_order_id                                // Order ID
                   ,"SKU"          => $SKU                                                 // SKU
                   ,"PRODUCT_NAME" => $product_info['name']                                // Product Name
                   ,"CATEGORY"     => $handpicked_options                                  // Category
                   ,"PRICE"        => $SalePrice // Price
                   ,"QUANTITY"     => $product_info['qty']                                 // Quantity
	        	);
	        	$encoded_item_data = array();
	        	foreach($item_data as $key => $value)
	        	{
	        		$encoded_item_data[$key] = htmlspecialchars($value);
	        	}
	        	$ITEMS .= strtr($item_template, $encoded_item_data)
	        	         ."\n";
	        }

	        //                               CITY, STATE, COUNTRY -        .           Shipping -             ,
	        //       ,           Billing -             .
            if(!empty($orderInfo['Shipping']['attr']))
            {
            	$person_info = $orderInfo['Shipping']['attr'];
            }
            else
            {
            	$person_info = $orderInfo['Billing']['attr'];
            }

            $CITY = getKeyIgnoreCase("city", $person_info);
            $CITY = $CITY['value'];

            $STATE = getKeyIgnoreCase("state", $person_info);
            $STATE = $STATE['value'];

            $COUNTRY = getKeyIgnoreCase("country", $person_info);
            $COUNTRY = $COUNTRY['value'];

            $currency_id = modApiFunc("Localization", "whichCurrencySendOrderToPaymentShippingGatewayIn", $last_placed_order_id, GET_PAYMENT_MODULE_FROM_ORDER);
	        $container_data = array
	        (
	            "UA-XXXXX-1"  => $GA_ACCOUNT_NUMBER
               ,"ORDER_ID"    => $last_placed_order_id
               ,"AFFILIATION" => modApiFunc("Configuration", "getValue", SYSCONFIG_STORE_OWNER_NAME)
               ,"TOTAL"       => modApiFunc("Checkout", "getOrderPrice", "Total", $currency_id)
               ,"TAX"         => $this->export_PRICE_N_A(modApiFunc("Checkout", "getOrderPrice", "Tax", $currency_id))
               ,"SHIPPING"    => $this->export_PRICE_N_A(modApiFunc("Checkout", "getOrderPrice", "TotalShippingAndHandlingCost", $currency_id))
               ,"CITY"        => $CITY
               ,"STATE"       => $STATE
               ,"COUNTRY"     => $COUNTRY
	        );

            $encoded_container_data = array();
            foreach($container_data as $key => $value)
            {
                $encoded_container_data[$key] = htmlspecialchars($value);
            }
	        $encoded_container_data["ITEMS"] = $ITEMS;
            $value = strtr($container_template, $encoded_container_data)
                         ."\n";

	        return $value;
        }
        else
        {
        	return "";
        }
    }

    function outputClixGalore()
    {
        global $application;

        $last_placed_order_id = modApiFunc("Checkout", "getLastPlacedOrderID");
        $currency_id = modApiFunc("Localization", "whichCurrencySendOrderToPaymentShippingGatewayIn", $last_placed_order_id, GET_PAYMENT_MODULE_FROM_ORDER);
        if($last_placed_order_id !== NULL)
        {
	        $settings = TransactionTracking::getModulesSettings();
	        $CLIXGALORE_AD_ID = $settings[MODULE_CLIXGALORE_UID]['CLIXGALORE_AD_ID'];

	        $container_template = TransactionTracking::getIncludedFileContents("clixgalore_container.tpl.html");

	        $container_data = array
	        (
	            "ADID"             => $CLIXGALORE_AD_ID
	           ,"AN_ORDER_ID"      => $last_placed_order_id
	           ,"SALE_AMOUNT_HERE" => modApiFunc("Checkout", "getOrderPrice", "Total", $currency_id)
	        );

            $value = strtr($container_template, $container_data)
                         ."\n";

	        return $value;
        }
        else
        {
        	return "";
        }
    }

    function outputMethodHtmlCode($method_uid)
    {
    	switch($method_uid)
    	{
    		case MODULE_GOOGLE_ANALYTICS_UID:
    			$value = $this->outputGA();
    			break;
    		case MODULE_CLIXGALORE_UID:
    			$value = $this->outputClixGalore();
                break;
    		default:
    			$value = "";
                break;
    	}
    	return $value;
    }

    function output()
    {
        global $application;

        #Define whether to output the view or not
        if (isset($this->NoView) && $this->NoView)
        {
            $application->outputTagErrors(true, "TransactionTracking", "Errors");
            return "";
        }

        $res = "";
        $InstalledModules = modApiStaticFunc("TransactionTracking", "getInstalledModules");
        foreach($InstalledModules as $method_uid => $info)
        {
        	if($info['status_active'] == DB_TRUE)
        	{
        		$res .= $this->outputMethodHtmlCode($method_uid)
        		       ."\n";
        	}
        }
        return $res;
    }

    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    /**
     * A reference to the object TemplateFiller.
     *
     * @var TemplateFiller
     */
    var $templateFiller;

    /**
     * The current selected template.
     *
     * @var array
     */
    var $template;
    /**#@-*/
}
?>