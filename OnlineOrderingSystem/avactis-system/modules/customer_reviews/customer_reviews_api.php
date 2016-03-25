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
loadModuleFile('customer_reviews/abstract/product_review_info.php');

/**
 * Customer_Reviews class
 *
 * Common API class for customer reviews.
 *
 * @author Sergey Kulitsky
 * @version $Id: customer_reviews_api.php xxxx 2009-03-19 13:10:47Z azrael $
 * @package Customer Reviews
 */
class Customer_Reviews
{
    function Customer_Reviews()
    {
    }

    function install()
    {
    	include_once(dirname(__FILE__)."/includes/install.inc");
    }

    function getTables()
    {
        static $tables;

        if (is_array($tables))
        {
            return $tables;
        }

        $tables = array ();

        $tbl_info = 'customer_reviews_rate_list';
        $tables[$tbl_info] = array();
        $tables[$tbl_info]['columns'] = array
            (
                'cr_rl_id'     => $tbl_info.'.cr_rl_id',
                'rate_label'   => $tbl_info.'.rate_label',
                'visible'      => $tbl_info.'.visible',
                'sort_order'   => $tbl_info.'.sort_order'
            );
        $tables[$tbl_info]['types'] = array
            (
                'cr_rl_id'     => DBQUERY_FIELD_TYPE_INT .
                                  ' NOT NULL auto_increment',
                'rate_label'   => DBQUERY_FIELD_TYPE_CHAR255,
                'visible'      => DBQUERY_FIELD_TYPE_CHAR1,
                'sort_order'   => DBQUERY_FIELD_TYPE_INT
            );
        $tables[$tbl_info]['primary'] = array
            (
                'cr_rl_id'
            );
        $tables[$tbl_info]['indexes'] = array
            (
                'visible'    => 'visible',
                'sort_order' => 'sort_order'
            );

        $tbl_info = 'customer_reviews';
        $tables[$tbl_info] = array();
        $tables[$tbl_info]['columns'] = array
            (
                'cr_id'      => $tbl_info.'.cr_id',
                'datetime'   => $tbl_info.'.datetime',
                'author'     => $tbl_info.'.author',
                'ip_address' => $tbl_info.'.ip_address',
                'product_id' => $tbl_info.'.product_id',
                'review'     => $tbl_info.'.review',
                'status'     => $tbl_info.'.status'
            );
        $tables[$tbl_info]['types'] = array
            (
                'cr_id'      => DBQUERY_FIELD_TYPE_INT .
                                ' NOT NULL auto_increment',
                'datetime'   => DBQUERY_FIELD_TYPE_DATETIME,
                'author'     => DBQUERY_FIELD_TYPE_CHAR255,
                'ip_address' => DBQUERY_FIELD_TYPE_CHAR20,
                'product_id' => DBQUERY_FIELD_TYPE_INT,
                'review'     => DBQUERY_FIELD_TYPE_LONGTEXT,
                'status'     => DBQUERY_FIELD_TYPE_CHAR1
            );
        $tables[$tbl_info]['primary'] = array
            (
                'cr_id'
            );
        $tables[$tbl_info]['indexes'] = array
            (
                'datetime'   => 'datetime',
                'author'     => 'author',
                'ip_address' => 'ip_address',
                'product_id' => 'product_id',
                'status'     => 'status'
            );

        $tbl_info = 'customer_reviews_rates';
        $tables[$tbl_info] = array();
        $tables[$tbl_info]['columns'] = array
            (
                'crr_id'   => $tbl_info.'.crr_id',
                'cr_id'    => $tbl_info.'.cr_id',
                'cr_rl_id' => $tbl_info.'.cr_rl_id',
                'rate'     => $tbl_info.'.rate',
            );
        $tables[$tbl_info]['types'] = array
            (
                'crr_id'   => DBQUERY_FIELD_TYPE_INT .
                              ' NOT NULL auto_increment',
                'cr_id'    => DBQUERY_FIELD_TYPE_INT,
                'cr_rl_id' => DBQUERY_FIELD_TYPE_INT,
                'rate'     => DBQUERY_FIELD_TYPE_INT
            );
        $tables[$tbl_info]['primary'] = array
            (
                'crr_id'
            );
        $tables[$tbl_info]['indexes'] = array
            (
                'cr_id'    => 'cr_id',
                'cr_rl_id' => 'cr_rl_id',
                'rate'     => 'rate'
            );

        global $application;
        return $application->addTablePrefix($tables);
    }

    function getSettings()
    {
        return modApiFunc('Settings',
                          'getParamListByGroup', 'CUSTOMER_REVIEWS');
    }

    function getRatesSettings()
    {
        return execQuery('SELECT_CUSTOMER_REVIEWS_RATE_LIST', array());
    }

    function insertNewRate($rate_label, $visible, $sort_order = -1)
    {
        if (!$rate_label)
            return 0;

        // Getting max sort_order to put the new rate to the end
        if ($sort_order == -1)
        {
            $sort_order = execQuery('SELECT_MAX_RATE_SORT_ORDER', array());
            $sort_order = array_pop(array_pop($sort_order)) + 1;
        }

        // Inserting...
        execQuery('INSERT_CUSTOMER_REVIEW_RATE_LIST_RECORD',
                  array('rate_label' => $rate_label,
                        'visible'    => $visible,
                        'sort_order' => $sort_order));
        return 1;
    }

    function updateRates($data)
    {
        if (!is_array($data) || empty($data))
            return;

        foreach($data as $cr_rl_id => $rate)
        {
            if (!$rate['rate_label'])
                continue;

            execQuery('UPDATE_CUSTOMER_REVIEW_RATE_LIST_RECORD',
                      array('rate_label' => $rate['rate_label'],
                            'visible'    => $rate['visible'],
                            'cr_rl_id'   => $cr_rl_id));
        }
    }

    function deleteRates($cr_rl_id_array)
    {
        if (!is_array($cr_rl_id_array) || empty($cr_rl_id_array))
            return;

        foreach($cr_rl_id_array as $cr_rl_id => $value)
        {
            // Deleting all the customer rates
            execQuery('DELETE_CUSTOMER_REVIEW_RATE_RECORD',
                      array('cr_rl_id' => $cr_rl_id));

            // Deleting from Rate List
            execQuery('DELETE_CUSTOMER_REVIEW_RATE_LIST_RECORD',
                      array('cr_rl_id' => $cr_rl_id));
        }
    }

    function updateRateSortOrder($sort_array)
    {
        if (!is_array($sort_array) || empty($sort_array))
            return;

        foreach($sort_array as $k => $cr_rl_id)
            execQuery('UPDATE_CUSTOMER_REVIEW_RATE_LIST_RECORD',
                      array('cr_rl_id' => $cr_rl_id, 'sort_order' => $k + 1));
    }

    function getRateValues()
    {
        return array(
                   array(
                       'value'    => 1,
                       'contents' => getMsg('CR', 'CR_RATING_1')
                   ),
                   array(
                       'value'    => 2,
                       'contents' => getMsg('CR', 'CR_RATING_2')
                   ),
                   array(
                       'value'    => 3,
                       'contents' => getMsg('CR', 'CR_RATING_3')
                   ),
                   array(
                       'value'    => 4,
                       'contents' => getMsg('CR', 'CR_RATING_4')
                   ),
                   array(
                       'value'    => 5,
                       'contents' => getMsg('CR', 'CR_RATING_5')
                   )
               );
    }

    function searchCustomerReviews($search_filter)
    {
        global $application;

        if (!isset($search_filter['sort_order']))
            $search_filter['sort_order'] = modApiFunc(
                'Settings', 'getParamValue',
                'CUSTOMER_REVIEWS', 'CUSTOMER_REVIEWS_SORTORDER'
            );

        $reviews = execQuery('SELECT_CUSTOMER_REVIEWS_BY_FILTER',
                             $search_filter);

	foreach($reviews as $k => $v) {
            // localization...
            $reviews[$k]['date'] = modApiFunc(
                                      'Localization',
                                      'SQL_date_format',
                                      $v['datetime']
                                   );
            $reviews[$k]['time'] = modApiFunc(
                                       'Localization',
                                       'SQL_time_format',
                                       $v['datetime']
                                   );

            // getting rates
            $reviews[$k]['rating'] = execQuery(
                'SELECT_CUSTOMER_REVIEWS_RATES_BY_REVIEW_ID',
                array('cr_id' => $v['cr_id'])
            );

            // getting rates to show
            $reviews[$k]['rating_cz'] = $this -> getCustomerReviewsRates(
                                                     $v['cr_id'],
                                                     @$search_filter['visible']
                                                 );
        }

        if (!is_array($reviews))
            $reviews = array();

        return $reviews;
    }

    function searchPgCustomerReviews($search_filter, $pg_enable)
    {
        if ($pg_enable == PAGINATOR_ENABLE)
            $search_filter['use_paginator'] = true;

        return execQueryPaginator('SELECT_CUSTOMER_REVIEWS_BY_FILTER',
                                  $search_filter);
    }

    function getCustomerReviewsRates($review_id, $visible = '')
    {
        return execQuery('SELECT_CUSTOMER_REVIEWS_RATES_CZ_BY_REVIEW_ID',
                         array('cr_id' => $review_id, 'visible' => $visible));
    }

    function getReviewsCount($type)
    {
        $result = 0;

        switch($type)
        {
            case 'pending':
                $result = execQuery(
                              'SELECT_COUNT_OF_CUSTOMER_REVIEWS_BY_STATUS',
                              array('status' => 'P')
                          );
                $result = $result[0]['count_id'];
                break;

            case 'bad':
                $result = execQuery(
                              'SELECT_COUNT_OF_CUSTOMER_REVIEWS_BY_RATE',
                              array('rate' => '2', 'rate_range' => '-')
                          );
                $result = $result[0]['count_id'];
                break;

            case 'good':
                $result = execQuery(
                              'SELECT_COUNT_OF_CUSTOMER_REVIEWS_BY_RATE',
                              array('rate' => '4', 'rate_range' => '+')
                          );
                $result = $result[0]['count_id'];
                break;

            case 'all':
                $result = execQuery(
                              'SELECT_COUNT_OF_CUSTOMER_REVIEWS_BY_STATUS',
                              array()
                          );
                $result = $result[0]['count_id'];
                break;
        }

        return $result;
    }

    function getReviewsCountForProductByIP($product_id = 0, $ip_address = '')
    {
        $params = array();
        if ($product_id)
            $params['product_id'] = $product_id;
        if ($ip_address)
            $params['ip_address'] = $ip_address;

        $result = execQuery('SELECT_COUNT_OF_CUSTOMER_REVIEWS_BY_IP', $params);
        return ((isset($result[0]['count_id'])) ? $result[0]['count_id'] : 0);
    }

    function updateCustomerReview($cr_id, $data)
    {
        // $data should be an array
        if (!is_array($data))
            return;

        // validating $data fields
        foreach($data as $k => $v)
            if (!in_array($k, array(
                                  'datetime',
                                  'author',
                                  'ip_address',
                                  'product_id',
                                  'review',
                                  'status'
                              )
               ))
                unset($data[$k]);

        // adding key
        $data['cr_id'] = $cr_id;

        // executing query
        execQuery('UPDATE_CUSTOMER_REVIEW_RECORD', $data);
    }

    function getBaseProductInfo($product_data)
    {
        $result = execQuery('SELECT_CUSTOMER_REVIEW_PRODUCT_INFO',
                            $product_data);

        if (is_array($result) && !empty($result))
            $result = $result[0];
        else
            $result = array('product_cr' => 0);

        return $result;
    }

    function getTotalProductRating($product_id)
    {
        $result = execQuery('SELECT_CUSTOMER_REVIEWS_TOTAL_RATING',
                            array('product_id' => $product_id));

        if (is_array($result) && !empty($result))
            $result = $result[0];
        else
            $result = array('total_rate'  => 0,
                            'total_count' => 0,
                            'product_cr'  => 0);

        if ($result['total_count'] > 0)
            $result['total_rating'] = $this -> _scaleRate(
                                          $result['total_rate'] /
                                          $result['total_count']
                                      );
        else
            $result['total_rating'] = 0;

        return $result;
    }

    function getDetailedProductRating($product_id)
    {
        $result = execQuery('SELECT_CUSTOMER_REVIEWS_DETAILED_RATING',
                            array('product_id' => $product_id));

        if (is_array($result))
            foreach($result as $k => $v)
                $result[$k]['total_rating'] = (($v['total_count'] > 0)
                                                  ? $this -> _scaleRate(
                                                      $v['total_rate'] /
                                                      $v['total_count'])
                                                  : 0);

        return $result;
    }

    function getProductCustomerReviewNumber($product_id)
    {
        $result = execQuery('SELECT_COUNT_OF_CUSTOMER_REVIEWS_CZ_BY_PRODUCT',
                            array('product_id' => $product_id));

        return ((isset($result[0]['count_id'])) ? $result[0]['count_id'] : 0);
    }

    function deleteCustomerReview($cr_id)
    {
        execQuery('DELETE_CUSTOMER_REVIEW_RATE_RECORD',
                  array('cr_id' => $cr_id));
        execQuery('DELETE_CUSTOMER_REVIEW_RECORD', array('cr_id' => $cr_id));
    }

    function uninstall()
    {
        $query = new DB_Table_Delete(Customer_Reviews :: getTables());
        global $application;
        $application -> db -> getDB_Result($query);
    }

    function _scaleRate($rate)
    {
        return floor($rate * 20 + 0.5);
    }

    function onProductsDeleted($prod_ids)
    {
        if (!is_array($prod_ids) || empty($prod_ids))
            return;

        $cr_ids = execQuery('SELECT_CUSTOMER_REVIEW_IDS_BY_PRODUCT_IDS',
                            array('ids' => $prod_ids));

        if (is_array($cr_ids) && !empty($cr_ids))
        {
            $_cr_ids = array();
            foreach($cr_ids as $v)
                $_cr_ids[] = $v['cr_id'];

            execQuery('DELETE_CUSTOMER_REVIEW_RATE_RECORDS',
                      array('ids' => $_cr_ids));
            execQuery('DELETE_CUSTOMER_REVIEW_RECORDS',
                      array('ids' => $_cr_ids));
        }
    }

}

?>