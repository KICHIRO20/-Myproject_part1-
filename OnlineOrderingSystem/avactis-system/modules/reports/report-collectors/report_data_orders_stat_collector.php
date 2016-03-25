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
loadModuleFile('reports/reports_api.php');
loadModuleFile('reports/abstract/report_data_collector.php');

/**
 * COrdersStatisticCollector class
 *
 * @author Alexey Florinsky
 * @version $Id$
 * @package Reports
 */
class COrdersStatisticCollector extends CReportDataCollector
{
    function COrdersStatisticCollector()
    {
    }

    function addRecord($order_id, $order_status_id, $order_payment_status_id, $order_deleted, $order_total, $order_tax_total, $order_currency)
    {
        if ($this->isStatisticsEnable() == false)
        {
            return;
        }

        $params = array(
            'order_id'                  => $order_id,
            'order_status_id'           => $order_status_id,
            'order_payment_status_id'   => $order_payment_status_id,
            'order_deleted'             => $order_deleted,
            'order_datetime'            => date('Y-m-d H:i:s', $this->getTimestamp()),
            'order_total'               => $order_total,
            'order_currency'            => $order_currency,
            'order_tax_total'           => $order_tax_total,
        );
        execQuery('INSERT_ORDERS_STAT_RECORD', $params);
    }

    /*
     * This function adds a single record to reports_orders_products_stat table. Where history order products data is collected.
     */
    function addProductRecord($order_id, $product_id, $qty)
    {
    	if ($this->isStatisticsEnable() == false)
        {
            return;
        }

        $params = array(
            'order_id'                  => $order_id,
            'product_id'                => $product_id,
            'amount'                    => $qty
        );
        execQuery('INSERT_ORDERS_PRODUCTS_STAT_RECORD', $params);
    }

    function updateProductRecord($order_id, $product_id, $qty)
    {
    	if ($this->isStatisticsEnable() == false)
        {
            return;
        }

        $params = array(
            'order_id'                  => $order_id,
            'product_id'                => $product_id,
            'amount'                    => $qty
        );
        execQuery('UPDATE_ORDERS_PRODUCTS_STAT_RECORD', $params);
    }

    function updateRecord($order_id, $order_status_id, $order_payment_status_id, $order_deleted = 0, $order_total = null, $order_tax = null)
    {
        if ($this->isStatisticsEnable() == false)
        {
            return;
        }

        $params = array(
            'order_id'                  => $order_id,
            'order_status_id'           => $order_status_id,
            'order_payment_status_id'   => $order_payment_status_id,
            'order_deleted'             => $order_deleted
        );

        if ($order_total == 0 || $order_total != null)
        {
        	$params['order_total'] = $order_total;
        }

        if ($order_tax == 0 || $order_tax != null)
        {
        	$params['order_tax'] = $order_tax;
        }
        execQuery('UPDATE_ORDERS_STAT_RECORD', $params);
    }

    function markRecordAsDeleted($order_id, $order_deleted)
    {
   	    if ($this->isStatisticsEnable() == false)
        {
            return;
        }

        $params = array(
            'order_id'                  => $order_id,
            'order_deleted'             => $order_deleted
        );
        execQuery('UPDATE_ORDERS_STAT_RECORD', $params);
    }
    /*
     * OrderCreated event handler
     */
    function onOrderCreated($oid)
    {
    	$main_store_currency_id = modApiFunc("Localization", "getMainStoreCurrency");
        $orer_total_price = modApiFunc('Checkout','getOrderPrice','Total',$main_store_currency_id);
        $order_tax_total =  modApiFunc('Checkout','getOrderPrice','Tax',$main_store_currency_id);
        $order_tax_total = ($order_tax_total == PRICE_N_A ) ? 0 : $order_tax_total;

        $order_data = modApiFunc("Checkout","getOrderInfo", $oid, $main_store_currency_id);
        $status = $order_data['StatusId'];
        $payment_status = $order_data['PaymentStatusId'];
        $order_deleted = 0;

        $ord_prods = array(); //the array of unique product ids in the order. It is required when there are several products with the same ids (e.g. with different options sets)

        $this->addRecord($oid, $status, $payment_status, $order_deleted, $orer_total_price, $order_tax_total , $main_store_currency_id);

        // Collect order products history data
        foreach ($order_data['Products'] as $id => $product)
        {
        	if (array_key_exists($product['storeProductID'], $ord_prods) == true)
        	{
        		$amount = $ord_prods[$product['storeProductID']] + $product['qty'];
        		$this->updateProductRecord($oid, $product['storeProductID'], $amount);
        		$ord_prods[$product['storeProductID']] = $amount;
        	}
        	else
        	{
        	    $this->addProductRecord($oid,$product['storeProductID'],$product['qty']);
        	    $ord_prods[$product['storeProductID']] = $product['qty'];
            }
        }
    }

    /*
     * OrdersWereUpdated event handler
     */
    function onOrdersUpdated($oids)
    {
    	if (is_array($oids))
        {
    	    # Order statuses
    	    if (is_array($oids['order_status']))
    	    {
                foreach ($oids['order_status'] as $id => $o)
    	        {
                    if (!is_array($o)) continue;
                    $order_data = modApiFunc('Checkout', 'getBaseOrderInfo', $id);

                    $status = $o['new_status'];
                    $payment_status = $order_data['PaymentStatusId'];
                    if (isset($oids['payment_status'][$id]) && is_array($oids['payment_status'][$id]))
                    {
                        $payment_status = $oids['payment_status'][$id]['new_status'];
                        unset($oids['payment_status'][$id]);
                    }
                    $order_deleted = 0;

                    $this->updateRecord($id, $status, $payment_status, $order_deleted);
                }
    	    }
            # Payment statuses
            if (is_array($oids['payment_status']))
            {
                foreach ($oids['payment_status'] as $id => $o)
    	        {
                    if (!is_array($o)) continue;
                    $order_data = modApiFunc('Checkout', 'getBaseOrderInfo', $id);

                    $payment_status = $o['new_status'];

                    $status = $order_data['StatusId'];
    	    	    if (isset($oids['order_status'][$id]) && is_array($oids['order_status'][$id]))
                    {
                        $status = $oids['order_status'][$id]['new_status'];
                        unset($oids['order_status'][$id]);
                    }

                    $this->updateRecord($id, $status, $payment_status, 0);
                }
            }
        }
    }

    /*
     * OrdersWillBeDeleted event handler
     */
    function onOrdersDeleted($oids)
    {
    	    foreach ($oids as $k => $oid)
    	    {
                $order_delete = 1;
                $this->markRecordAsDeleted($oid, $order_delete);
    	   	}
    }

    function onOrderDataEdited($oid)
    {
    	$main_store_currency_id = modApiFunc("Localization", "getMainStoreCurrency");
    	$order_data = modApiFunc("Checkout","getOrderInfo", $oid, $main_store_currency_id);

    	$order_total_price = $order_data['Total'];
        $order_tax_total =  $order_data['Price']['OrderTaxTotal'];

        $te = modApiFunc("TaxExempts", "getOrderFullTaxExempts", intval($oid), false);

        if ($te[0]['exempt_status'] == "true") // customer choices tax exemption
        {
            $order_tax_total = "0.0000";
        }

    	# updating order products.
    	# @ products cannot be added or removed!
    	foreach ($order_data['Products'] as $i => $p)
    	{
    		$this->updateProductRecord($oid, $p['storeProductID'], $p['qty']);
    	}
    	# udating order_total and order_tax. table: reports_orders_stat
    	$this->updateRecord($oid, $order_data['StatusId'], $order_data['PaymentStatusId'], 0, $order_total_price, $order_tax_total);
    }
}


?>