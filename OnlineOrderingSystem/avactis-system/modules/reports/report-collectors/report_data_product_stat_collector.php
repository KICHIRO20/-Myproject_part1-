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
 * CProductStatisticCollector class
 *
 * @author Alexey Florinsky
 * @version $Id: report_data_product_stat_collector.php 5038 2008-04-16 11:46:20Z af $
 * @package Reports
 */
class CProductStatisticCollector extends CReportDataCollector
{
    function CProductStatisticCollector()
    {
        loadClass('CProductInfo');
    }

    function getTimestamp()
    {
        $f = $this->get_timestamp_function_name;
        return $f();
    }

    function addRecord($product_id, $field, $value)
    {
        if ($this->isStatisticsEnable() == false)
        {
            return;
        }

        $params = array(
            'datetime'                  => date('Y-m-d H:00:00', $this->getTimestamp()),
            'product_id'                => $product_id,
            'views'                     => 0,
            'sale_items'                => 0,
            'added_to_cart_times'       => 0,
            'deleted_from_cart_times'   => 0,
            'added_to_cart_qty'         => 0,
            'deleted_from_cart_qty'     => 0,
        );
        $params[$field] = $value;

        $record = execQuery('PRODUCT_STAT_SELECT_RECORD_BY_PK', $params);
        if (!empty($record) and isset($record[0]))
        {
            $record = $record[0];
            $params['views']                    += $record['views'];
            $params['sale_items']               += $record['sale_items'];
            $params['added_to_cart_times']      += $record['added_to_cart_times'];
            $params['deleted_from_cart_times']  += $record['deleted_from_cart_times'];
            $params['added_to_cart_qty']        += $record['added_to_cart_qty'];
            $params['deleted_from_cart_qty']    += $record['deleted_from_cart_qty'];
        }

        execQuery('PRODUCT_STAT_REPLACE_RECORD_BY_PK', $params);

        //                                                      ,                            reports_product_info.
        $params = array($product_id);
        $record = execQuery('PRODUCT_INFO_SELECT_RECORD_BY_PK', $params);
        if (empty($record) or !isset($record[0]))
        {
            $this->updateProductName($product_id);
        }
    }

    function addMultipleRecords($product_id_list, $field, $value)
    {
        if ($this->isStatisticsEnable() == false)
        {
            return;
        }

        $empty_record = array(
            'datetime'                  => date('Y-m-d H:00:00', $this->getTimestamp()),
            'product_id'                => 0,
            'views'                     => 0,
            'sale_items'                => 0,
            'added_to_cart_times'       => 0,
            'deleted_from_cart_times'   => 0,
            'added_to_cart_qty'         => 0,
            'deleted_from_cart_qty'     => 0,
        );

        $records = array();
        foreach ($product_id_list as $pid)
        {
            $tmp = $empty_record;
            $tmp[$field] = $value;
            $tmp['product_id'] = $pid;
            $records[] = $tmp;
        }

        $existing_stat = execQuery('PRODUCT_STAT_SELECT_MULTIPLE_RECORDS_BY_PK', $records);
        if (!empty($existing_stat) and isset($existing_stat[0]))
        {
            foreach ($records as $k=>$r)
            {
                foreach ($existing_stat as $existing_record)
                {
                    if ($r['datetime'] == $existing_record['datetime'] && $r['product_id'] == $existing_record['product_id'])
                    {
                        $records[$k]['views']                    += $existing_record['views'];
                        $records[$k]['sale_items']               += $existing_record['sale_items'];
                        $records[$k]['added_to_cart_times']      += $existing_record['added_to_cart_times'];
                        $records[$k]['deleted_from_cart_times']  += $existing_record['deleted_from_cart_times'];
                        $records[$k]['added_to_cart_qty']        += $existing_record['added_to_cart_qty'];
                        $records[$k]['deleted_from_cart_qty']    += $existing_record['deleted_from_cart_qty'];
                    }
                }
            }
        }

        execQuery('PRODUCT_STAT_REPLACE_MULTIPLE_RECORDS_BY_PK', $records);

        //                                                      ,                            reports_product_info.
        //              ,
        $stat_records = execQuery('PRODUCT_INFO_SELECT_RECORD_BY_PK', $product_id_list);
        //                     product id,
        $product_id_list = array_flip($product_id_list);
        foreach ($stat_records as $rec)
        {
            unset($product_id_list[$rec['product_id']]);
        }
        //                   id,
        $product_id_list = array_flip($product_id_list);

        if (count($product_id_list)>0)
        {
            $this->updateProductName($product_id_list);
        }
    }

    function updateProductName($product_id_list)
    {
        if ($this->isStatisticsEnable() == false)
        {
            return;
        }
        global $application;
        if (!is_array($product_id_list))
        {
            $product_id_list = array($product_id_list);
        }

        $params = array();
        foreach($product_id_list as $pid)
        {
            $product_obj = &$application->getInstance('CProductInfo', $pid);
            $params[] = array(
                'product_id' => $pid,
                'product_name' => $product_obj->getProductTagValue('name'),
            );
        }

        execQuery('PRODUCT_INFO_REPLACE_MULTIPLE_RECORDS', $params);
    }

    function onProductInfoChanged($product_id)
    {
        $this->updateProductName($product_id);
    }

    function onProductInfoDisplayed($pid)
    {
    	if ($this->isStatisticsEnable() == false)
        {
            return;
        }

    	if (modApiFunc('Settings','getParamValue','REPORTS_AND_STATISTICS','COLLECT_VIEWS_FROM_PRODUCT_INFO') === 'YES')
        {
            $this->addRecord($pid, 'views', 1);
        }
    }

    function onProductListDisplayed($list)
    {
    	if ($this->isStatisticsEnable() == false)
        {
            return;
        }

    	if (modApiFunc('Settings','getParamValue','REPORTS_AND_STATISTICS','COLLECT_VIEWS_FROM_PRODUCT_LIST') === 'YES')
        {
            $this->addMultipleRecords($list, 'views', 1);
        }
    }

    function onProductAddedToCart($pid, $qty)
    {
        if ($this->isStatisticsEnable() == false)
        {
            return;
        }

    	if (modApiFunc('Settings','getParamValue','REPORTS_AND_STATISTICS','COLLECT_CART_ADD_REMOVE_TIMES') === 'YES')
        {
            $this->addRecord($pid, 'added_to_cart_times', 1);
        }

        if (modApiFunc('Settings','getParamValue','REPORTS_AND_STATISTICS','COLLECT_CART_QTY_CHANGE') === 'YES')
        {
            $this->addRecord($pid, 'added_to_cart_qty', $qty);
        }
    }

    function onProductQuantityInCartUpdated($data)
    {
        if ($this->isStatisticsEnable() == false)
        {
            return;
        }

    	$pid = $data['PRODUCT_ID'];
        $prev_qty = $data['PREV_QTY'];
        $new_qty = $data['NEW_QTY'];
        $diff = $new_qty - $prev_qty;

        if (modApiFunc('Settings','getParamValue','REPORTS_AND_STATISTICS','COLLECT_CART_QTY_CHANGE') === 'YES')
        {
            if ($diff > 0)
            {
                $this->addRecord($pid, 'added_to_cart_qty', $diff);
            }
            if ($diff < 0)
            {
                $this->addRecord($pid, 'deleted_from_cart_qty', abs($diff));
            }
        }
    }

    function onProductRemovedFromCart($data)
    {
        if ($data != null && count($data) > 0)
        {
    	    foreach($data as $item)
            {
                $qty = $item['QUANTITY'];
                $pid = $item['PRODUCT_ID'];

                if (modApiFunc('Settings','getParamValue','REPORTS_AND_STATISTICS','COLLECT_CART_QTY_CHANGE') === 'YES')
                {
                    $this->addRecord($pid, 'deleted_from_cart_qty', $qty);
                }

                if ( modApiFunc('Settings','getParamValue','REPORTS_AND_STATISTICS','COLLECT_CART_ADD_REMOVE_TIMES') === 'YES' )
                {
                    $this->addRecord($pid, 'deleted_from_cart_times', 1);
                }
            }
        }
    }

    function onProductsDeleted()
    {
    }

    function onProductsWasSold($list)
    {
        foreach ($list as $product)
        {
            $pid = $product['PRODUCT_ID'];
            $qty = $product['PRODUCT_QUANTITY'];

            if ( modApiFunc('Settings','getParamValue','REPORTS_AND_STATISTICS','COLLECT_PRODUCT_SALES_ITEMS') === 'YES' )
            {
                $this->addRecord($pid, 'sale_items', $qty);
            }
        }
    }

}


?>