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
loadClass('DataFilterDefault');
loadCoreFile('Tar.php');

/**
 *                                               CSV     .
 *
 * @package OrdersExport
 * @author Alexey Florinsky
 */
class DataFilterOrdersCSV extends DataFilterDefault
{
    function DataFilterOrdersCSV()
    {
        loadCoreFile('aal.class.php');
    }

    function doWork($order_info)
    {
        $result = array();

        $result['Order Id']                         = $order_info['ID'];
	$result['Order Date']                       = $order_info['Date'];
        $result['Order Status']                     = $order_info['Status'];
        $result['Order Payment Status']             = $order_info['PaymentStatus'];
        $result['Order Payment Method']             = $order_info['PaymentMethod'];
        $result['Order Payment Method Detail']      = $order_info['PaymentMethodDetail'];
        $result['Order Payment Processor Order Id'] = $order_info['PaymentProcessorOrderId'];
        $result['Order Shipping Method']            = $order_info['ShippingMethod'];
        $result['Order Track Id']                   = $order_info['TrackId'];
        $result['Order Affiliate Id']               = $order_info['AffiliateId'];
        $result['Order Currency Code']              = $order_info['OrderCurrencyCode'];


        $result['Order Subtotal']                   = $order_info['Price']['OrderSubtotal'];
        $result['Order Global Discount']            = $order_info['Price']['SubtotalGlobalDiscount'];
        $result['Order Promo Code Discount']        = $order_info['Price']['SubtotalPromoCodeDiscount'];
        $result['Order Quantity Discount']          = $order_info['Price']['QuantityDiscount'];
        $result['Order Discounted Subtotal']        = $order_info['Price']['DiscountedSubtotal'];
        $result['Order Total Shipping And Handling Cost'] = $order_info['Price']['TotalShippingAndHandlingCost'];
        $result['Order Tax Total']                  = $order_info['Price']['OrderTaxTotal'];
        $result['Order Total']                      = $order_info['Price']['OrderTotal'];

        $billing = new ArrayAccessLayer($order_info);
        $billing->setAccessMask("Billing", "attr", AAL_CUSTOM_PARAM, "value");
        $result['Order Billing Firstname']   = $billing->getByMask('Firstname');
        $result['Order Billing Lastname']    = $billing->getByMask('Lastname');
        $result['Order Billing Email']       = $billing->getByMask('Email');
        $result['Order Billing Streetline1'] = $billing->getByMask('Streetline1');
        $result['Order Billing Streetline2'] = $billing->getByMask('Streetline2');
        $result['Order Billing City']        = $billing->getByMask('City');
        $result['Order Billing State']       = $billing->getByMask('State');
        $result['Order Billing Postcode']    = $billing->getByMask('Postcode');
        $result['Order Billing Country']     = $billing->getByMask('Country');
        $result['Order Billing Phone']       = $billing->getByMask('Phone');
	$result['Order Billing CommentLine'] = $billing->getByMask('CommentLine');
        $result['Order Billing CommentArea'] = $billing->getByMask('CommentArea');

	$shipping = new ArrayAccessLayer($order_info);
        $shipping->setAccessMask("Shipping", "attr", AAL_CUSTOM_PARAM, "value");
        $result['Order Shipping Firstname']   = $shipping->getByMask('Firstname');
        $result['Order Shipping Lastname']    = $shipping->getByMask('Lastname');
        $result['Order Shipping Email']       = $shipping->getByMask('Email');
        $result['Order Shipping Streetline1'] = $shipping->getByMask('Streetline1');
        $result['Order Shipping Streetline2'] = $shipping->getByMask('Streetline2');
        $result['Order Shipping City']        = $shipping->getByMask('City');
        $result['Order Shipping State']       = $shipping->getByMask('State');
        $result['Order Shipping Postcode']    = $shipping->getByMask('Postcode');
        $result['Order Shipping Country']     = $shipping->getByMask('Country');
        $result['Order Shipping Phone']       = $shipping->getByMask('Phone');
        $result['Order Shipping CommentLine'] = $shipping->getByMask('CommentLine');
        $result['Order Shipping CommentArea'] = $shipping->getByMask('CommentArea');

        for ($i=0; $i<count($order_info['Products']); $i++)
        {
            $j = $i + 1;
            $result['Order Product '.$j.' Name']       = $order_info['Products'][$i]['name'];
            $result['Order Product '.$j.' Quantity']   = $order_info['Products'][$i]['qty'];
            $result['Order Product '.$j.' Sale Price'] = $order_info['Products'][$i]['SalePrice'];
            $result['Order Product '.$j.' SKU']        = (isset($order_info['Products'][$i]['SKU'])) ? $order_info['Products'][$i]['SKU'] : '';
            $result['Order Product '.$j.' Weight']     = $order_info['Products'][$i]['Weight'];
            $result['Order Product '.$j.' Amount']     = $order_info['Products'][$i]['qty'] * $order_info['Products'][$i]['SalePrice'];
            if (!empty($order_info['Products'][$i]['options']))
            {
                for($oi=0; $oi<count($order_info['Products'][$i]['options']); $oi++)
                {
                    $oj = $oi + 1;
                    $result['Order Product '.$j.' Option '.$oj.' Name']  = $order_info['Products'][$i]['options'][$oi]['option_name'];
                    $result['Order Product '.$j.' Option '.$oj.' Value'] = $order_info['Products'][$i]['options'][$oi]['option_value'];
                }
            }
	}
	for ($on=0; $on<sizeof($order_info['Comments']); $on++)
	{
		$result['Date of Note '.($on+1)]		= $order_info['Comments'][$on]['date'];
		$result['Admin Note '.($on+1)]			= $order_info['Comments'][$on]['content'];
	}
	$this->_messages = "Order {$order_info['ID']} exported.";
        return $result;
    }

}

?>