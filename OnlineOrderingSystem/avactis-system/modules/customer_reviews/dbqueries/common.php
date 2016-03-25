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
loadModuleFile('customer_reviews/customer_reviews_api.php');
loadModuleFile('catalog/catalog_api.php');

// ---------------------------
// Select queries
// ---------------------------

class SELECT_MAX_RATE_SORT_ORDER extends DB_Select
{
    function initQuery($params)
    {
        $tables = Customer_Reviews::getTables();
        $rltable = $tables['customer_reviews_rate_list']['columns'];

        $this -> addSelectField('MAX(' . $rltable['sort_order'] . ')');
    }
}

class SELECT_COUNT_OF_CUSTOMER_REVIEWS_CZ_BY_PRODUCT extends DB_Select
{
    function initQuery($params)
    {
        $tables = Customer_Reviews::getTables();
        $crtable = $tables['customer_reviews']['columns'];

        $this -> addSelectField($this -> fCount($crtable['cr_id']), 'count_id');

        $this -> WhereValue($crtable['status'], DB_EQ, 'A');

        if (isset($params['product_id']))
        {
            $this -> WhereAND();
            $this -> WhereValue($crtable['product_id'], DB_EQ, $params['product_id']);
        }
    }
}

class SELECT_COUNT_OF_CUSTOMER_REVIEWS_BY_STATUS extends DB_Select
{
    function initQuery($params)
    {
        $tables = Customer_Reviews::getTables();
        $crtable = $tables['customer_reviews']['columns'];

        $this -> addSelectField($this -> fCount($crtable['cr_id']), 'count_id');
        if (isset($params['status']))
            $this -> WhereValue($crtable['status'], DB_EQ, $params['status']);
    }
}

class SELECT_COUNT_OF_CUSTOMER_REVIEWS_BY_RATE extends DB_Select
{
    function initQuery($params)
    {
        $tables = Customer_Reviews::getTables();
        $crtable = $tables['customer_reviews']['columns'];
        $rtable = $tables['customer_reviews_rates']['columns'];

        $this -> addSelectTable('customer_reviews');
        $this -> addSelectField(
                     $this -> fCount('DISTINCT(' . $crtable['cr_id'] . ')'),
                     'count_id'
                 );
        $this -> addLeftJoin('customer_reviews_rates', $rtable['cr_id'], DB_EQ, $crtable['cr_id']);
        if (isset($params['rate']))
        {
            $rate_range = DB_EQ;
            if ($params['rate_range'] == '+')
               $rate_range = DB_GTE;
            if ($params['rate_range'] == '-')
               $rate_range = DB_LTE;
            $this -> WhereValue($rtable['rate'], $rate_range, $params['rate']);
        }
    }
}

class SELECT_COUNT_OF_CUSTOMER_REVIEWS_BY_IP extends DB_Select
{
    function initQuery($params)
    {
        $tables = Customer_Reviews::getTables();
        $crtable = $tables['customer_reviews']['columns'];

        $this -> addSelectTable('customer_reviews');
        $this -> addSelectField(
                     $this -> fCount('DISTINCT(' . $crtable['cr_id'] . ')'),
                     'count_id'
                 );

        if (isset($params['product_id']))
            $this -> WhereValue($crtable['product_id'], DB_EQ,
                                $params['product_id']);

        if (isset($params['ip_address']))
        {
            if (isset($params['product_id']))
                $this -> WhereAND();
            $this -> WhereValue($crtable['ip_address'], DB_EQ,
                                $params['ip_address']);
        }
    }
}

class SELECT_CUSTOMER_REVIEWS_RATE_LIST extends DB_Select
{
    function initQuery($params)
    {
        $tables = Customer_Reviews::getTables();
        $rltable = $tables['customer_reviews_rate_list']['columns'];

        $this -> addSelectTable('customer_reviews_rate_list');
        $this -> addSelectField('*');
        $this -> SelectOrder($rltable['sort_order']);
    }
}

class SELECT_CUSTOMER_REVIEWS_BY_FILTER extends DB_Select
{
    function initQuery($params)
    {
        $tables = Customer_Reviews::getTables();
        $crtable = $tables['customer_reviews']['columns'];
        $rtable = $tables['customer_reviews_rates']['columns'];

        $product_tables = Catalog::getTables();
        $ptable = $product_tables['products']['columns'];
        $patable = $product_tables['product_attributes']['columns'];
        $ptatable = $product_tables['product_type_attributes']['columns'];

        $this -> addSelectTable('customer_reviews');

        foreach($crtable as $column)
            $this -> addSelectField($column);

        // getting product_name
        $this -> addLeftJoin('products',
                             $crtable['product_id'],
                             DB_EQ,
                             $ptable['id']);

        $this -> setMultiLangAlias('_ml_name', 'products', $ptable['name'],
                                   $ptable['id'], 'Catalog');
        $this -> addSelectValue($this -> getMultiLangAlias('_ml_name'),
                                'product_name');

        // getting customer_review value for selected product
        // attribute_id = CUSTOMER_REVIEWS_PRODUCT_ATTRIBUTE_ID
        $attribute_id = modApiFunc('Catalog', 'getCustomerReviewsAttrId');
        $this -> addLeftJoinOnConditions(
                     'product_attributes',
                     array($crtable['product_id'], DB_EQ, $patable['p_id'],
                           DB_AND, $patable['a_id'], DB_EQ,
                           $attribute_id)
                 );
        $this -> addLeftJoinOnConditions(
                     'product_type_attributes',
                     array($ptable['pt_id'], DB_EQ, $ptatable['pt_id'],
                           DB_AND, $ptatable['type_attr_visible'], DB_EQ,
                           '1', DB_AND, $ptatable['a_id'], DB_EQ,
                           $attribute_id)
                 );
        $this -> addSelectField('IF(' .
                                $patable['attr_value'] .
                                ' IS NULL OR ' . $ptatable['a_id'] .
                                ' IS NULL, 0, ' .
                                $patable['attr_value'] . ')',
                                'product_cr');

        $where = array();
        if (isset($params['from']))
            $where[] = array(
                           'DATE_ADD(' . $crtable['datetime'] . ', ' .
                           modApiFunc('Localization', 'getSQLInterval') . ')',
                           DB_GTE,
                           $params['from']['year'] . '-' .
                           $params['from']['month'] . '-' .
                           $params['from']['day'] . ' 00:00:00'
                       );

        if (isset($params['to']))
            $where[] = array(
                           'DATE_ADD(' . $crtable['datetime'] . ', ' .
                           modApiFunc('Localization', 'getSQLInterval') . ')',
                           DB_LTE,
                           $params['to']['year'] . '-' .
                           $params['to']['month'] . '-' .
                           $params['to']['day'] . ' 23:59:59'
                       );

        if (isset($params['author']))
            $where[] = array(
                           $crtable['author'],
                           ((@$params['author']['exactly'] == 'Y')
                               ? DB_EQ
                               : DB_LIKE
                           ),
                           ((@$params['author']['exactly'] == 'Y')
                               ? ''
                               : '%'
                           ) .
                           $params['author']['name'] .
                           ((@$params['author']['exactly'] == 'Y')
                               ? ''
                               : '%'
                           )
                       );

        if (isset($params['ip_address']))
            $where[] = array(
                           $crtable['ip_address'],
                           DB_EQ,
                           $params['ip_address']
                       );

        if (isset($params['product']) &&
            isset($params['product']['id']) &&
            $params['product']['id'] > 0)
            $where[] = array(
                           $crtable['product_id'],
                           DB_EQ,
                           $params['product']['id']
                       );
        elseif (isset($params['product']) &&
            $params['product']['name'] != '')
            $where[] = array(
                           $this -> getMultiLangAlias('_ml_name'),
                           ((@$params['product']['exactly'] == 'Y')
                               ? DB_EQ
                               : DB_LIKE
                           ),
                           ((@$params['product']['exactly'] == 'Y')
                               ? ''
                               : '%'
                           ) .
                           $params['product']['name'] .
                           ((@$params['product']['exactly'] == 'Y')
                               ? ''
                               : '%'
                           )
                       );

        if (isset($params['rating']))
        {
            $this -> addLeftJoin('customer_reviews_rates',
                                 $crtable['cr_id'],
                                 DB_EQ,
                                 $rtable['cr_id']);

            $rate_range = DB_EQ;
            if ($params['rating']['range'] == '+')
               $rate_range = DB_GTE;
            if ($params['rating']['range'] == '-')
               $rate_range = DB_LTE;

            $where[] = array(
                           $rtable['rate'], $rate_range,
                           $params['rating']['rate']
                       );
        }

        if (isset($params['status']))
            $where[] = array(
                           $crtable['status'],
                           DB_EQ,
                           $params['status']
                       );

        if (isset($params['cr_id']))
            $where[] = array(
                           $crtable['cr_id'],
                           DB_EQ,
                           $params['cr_id']
                       );

        if (!empty($where))
            foreach($where as $k => $v)
            {
                if ($k > 0)
                    $this -> WhereAND();
                $this -> WhereValue($v[0], $v[1], $v[2]);
            }

        $this -> SelectGroup($crtable['cr_id']);

        if (@$params['sort_order'] == 'DATE_ASC')
            $this -> SelectOrder($crtable['datetime'], 'ASC');
        else
            $this -> SelectOrder($crtable['datetime'], 'DESC');

        if (isset($params['paginator']) && is_array($params['paginator']))
        {
            list($offset, $count) = $params['paginator'];
            $this -> SelectLimit($offset, $count);
        }
        elseif (isset($params['limit']) && is_array($params['limit']))
        {
            list($offset, $count) = $params['limit'];
            $this -> SelectLimit($offset, $count);
        }
    }
}

class SELECT_CUSTOMER_REVIEWS_RATES_BY_REVIEW_ID extends DB_Select
{
    function initQuery($params)
    {
        $tables = Customer_Reviews::getTables();
        $rtable = $tables['customer_reviews_rates']['columns'];
        $rltable = $tables['customer_reviews_rate_list']['columns'];

        $this -> addSelectTable('customer_reviews_rates');
        $this -> addSelectField($rtable['cr_rl_id']);
        $this -> addSelectField($rtable['rate']);
        $this -> addSelectField($rltable['rate_label']);
        $this -> addSelectField($rltable['visible']);
        $this -> addLeftJoin('customer_reviews_rate_list',
                             $rtable['cr_rl_id'],
                             DB_EQ,
                             $rltable['cr_rl_id']);
        $this -> WhereValue($rtable['cr_id'], DB_EQ, @$params['cr_id']);
        $this -> SelectOrder($rltable['sort_order']);
    }
}

class SELECT_CUSTOMER_REVIEWS_RATES_CZ_BY_REVIEW_ID extends DB_Select
{
    function initQuery($params)
    {
        $tables = Customer_Reviews::getTables();
        $rltable = $tables['customer_reviews_rate_list']['columns'];
        $rtable = $tables['customer_reviews_rates']['columns'];

        $this -> addSelectTable('customer_reviews_rate_list');
        $this -> addSelectField($rltable['cr_rl_id']);
        $this -> addSelectField($rltable['rate_label']);
        $this -> addSelectField($rltable['visible']);

        $this -> addLeftJoinOnConditions(
                     'customer_reviews_rates',
                     array($rltable['cr_rl_id'], DB_EQ, $rtable['cr_rl_id'],
                           DB_AND, $rtable['cr_id'], DB_EQ,
                           '\'' . $params['cr_id']  . '\'')
                 );
        $this -> addSelectField('IF(' . $rtable['rate'] . ' IS NULL, 0, ' .
                                $rtable['rate'] . ')', 'rate');

        if (isset($params['visible']) && $params['visible'] != '')
            $this -> WhereValue($rltable['visible'], DB_EQ, $params['visible']);

        $this -> SelectOrder($rltable['sort_order']);
    }
}

class SELECT_CUSTOMER_REVIEW_PRODUCT_INFO extends DB_Select
{
    function initQuery($params)
    {
        $product_tables = Catalog::getTables();
        $ptable = $product_tables['products']['columns'];
        $patable = $product_tables['product_attributes']['columns'];
        $ptatable = $product_tables['product_type_attributes']['columns'];

        $this -> addSelectField($ptable['id']);
        $this -> setMultiLangAlias('_ml_name', 'products', $ptable['name'],
                                   $ptable['id'], 'Catalog');
        $this -> addSelectValue($this -> getMultiLangAlias('_ml_name'),
                                'product_name');

        // getting customer_review value for selected product
        // attribute_id = CUSTOMER_REVIEWS_PRODUCT_ATTRIBUTE_ID
        $attribute_id = modApiFunc('Catalog', 'getCustomerReviewsAttrId');
        $this -> addLeftJoinOnConditions(
                     'product_attributes',
                     array($ptable['id'], DB_EQ, $patable['p_id'],
                           DB_AND, $patable['a_id'], DB_EQ,
                           $attribute_id)
                 );
        $this -> addLeftJoinOnConditions(
                     'product_type_attributes',
                     array($ptable['pt_id'], DB_EQ, $ptatable['pt_id'],
                           DB_AND, $ptatable['type_attr_visible'], DB_EQ,
                           '1', DB_AND, $ptatable['a_id'], DB_EQ,
                           $attribute_id)
                 );
        $this -> addSelectField('IF(' .
                                $patable['attr_value'] .
                                ' IS NULL OR ' . $ptatable['a_id'] .
                                ' IS NULL, 0, ' .
                                $patable['attr_value'] . ')',
                                'product_cr');

        if (isset($params['product_name']))
            $this -> WhereValue($this -> getMultiLangAlias('_ml_name'),
                                DB_EQ, $params['product_name']);
        else
            $this -> WhereValue($ptable['id'],
                                DB_EQ, @$params['product_id']);
    }
}

class SELECT_CUSTOMER_REVIEWS_TOTAL_RATING extends DB_Select
{
    function initQuery($params)
    {
        $tables = Customer_Reviews::getTables();
        $crtable = $tables['customer_reviews']['columns'];
        $rtable = $tables['customer_reviews_rates']['columns'];
        $rltable = $tables['customer_reviews_rate_list']['columns'];

        $product_tables = Catalog::getTables();
        $ptable = $product_tables['products']['columns'];
        $patable = $product_tables['product_attributes']['columns'];
        $ptatable = $product_tables['product_type_attributes']['columns'];

        $this -> addSelectTable('customer_reviews');
        $this -> addSelectTable('customer_reviews_rates');
        $this -> addSelectTable('customer_reviews_rate_list');

        $this -> addSelectField($crtable['product_id']);
        $this -> addSelectField('SUM(' . $rtable['rate'] . ')', 'total_rate');
        $this -> addSelectField('COUNT(' . $rtable['rate'] . ')',
                                'total_count');

        // getting customer_review value for selected product
        // attribute_id = CUSTOMER_REVIEWS_PRODUCT_ATTRIBUTE_ID
        $attribute_id = modApiFunc('Catalog', 'getCustomerReviewsAttrId');
        $this -> addLeftJoinOnConditions(
                     'product_attributes',
                     array($crtable['product_id'], DB_EQ, $patable['p_id'],
                           DB_AND, $patable['a_id'], DB_EQ,
                           $attribute_id)
                 );
        $this -> addLeftJoin('products', $crtable['product_id'], DB_EQ,
                             $ptable['id']);
        $this -> addLeftJoinOnConditions(
                     'product_type_attributes',
                     array($ptable['pt_id'], DB_EQ, $ptatable['pt_id'],
                           DB_AND, $ptatable['type_attr_visible'], DB_EQ,
                           '1', DB_AND, $ptatable['a_id'], DB_EQ,
                           $attribute_id)
                 );
        $this -> addSelectField('IF(' .
                                $patable['attr_value'] .
                                ' IS NULL OR ' . $ptatable['a_id'] .
                                ' IS NULL, 0, ' .
                                $patable['attr_value'] . ')',
                                'product_cr');

        $this -> WhereField($crtable['cr_id'], DB_EQ, $rtable['cr_id']);
        $this -> WhereAND();
        $this -> WhereField($rtable['cr_rl_id'], DB_EQ, $rltable['cr_rl_id']);
        $this -> WhereAND();
        // show only visible rates
        $this -> WhereValue($rltable['visible'], DB_EQ, 'Y');
        $this -> WhereAND();
        // show only approved reviews
        $this -> WhereValue($crtable['status'], DB_EQ, 'A');

        if (isset($params['product_id']))
        {
            $this -> WhereAND();
            $this -> WhereValue($crtable['product_id'], DB_EQ,
                                $params['product_id']);
        }

        $this -> SelectGroup($crtable['product_id']);
    }
}

class SELECT_CUSTOMER_REVIEWS_DETAILED_RATING extends DB_Select
{
    function initQuery($params)
    {
        $tables = Customer_Reviews::getTables();
        $crtable = $tables['customer_reviews']['columns'];
        $rtable = $tables['customer_reviews_rates']['columns'];
        $rltable = $tables['customer_reviews_rate_list']['columns'];

        $this -> addSelectTable('customer_reviews');
        $this -> addSelectTable('customer_reviews_rates');
        $this -> addSelectTable('customer_reviews_rate_list');

        $this -> addSelectField($crtable['product_id']);
        $this -> addSelectField($rltable['rate_label']);
        $this -> addSelectField('SUM(' . $rtable['rate'] . ')', 'total_rate');
        $this -> addSelectField('COUNT(' . $rtable['rate'] . ')',
                                'total_count');

        $this -> WhereField($crtable['cr_id'], DB_EQ, $rtable['cr_id']);
        $this -> WhereAND();
        $this -> WhereField($rtable['cr_rl_id'], DB_EQ, $rltable['cr_rl_id']);
        $this -> WhereAND();
        // show only visible rates
        $this -> WhereValue($rltable['visible'], DB_EQ, 'Y');
        $this -> WhereAND();
        // show only approved reviews
        $this -> WhereValue($crtable['status'], DB_EQ, 'A');
        if (isset($params['product_id']))
        {
            $this -> WhereAND();
            $this -> WhereValue($crtable['product_id'], DB_EQ,
                                $params['product_id']);
        }

        $this -> SelectGroup($crtable['product_id']);
        $this -> SelectGroup($rltable['cr_rl_id']);

        $this -> SelectOrder($crtable['product_id']);
        $this -> SelectOrder($rltable['sort_order']);
    }
}

class SELECT_CUSTOMER_REVIEW_IDS_BY_PRODUCT_IDS extends DB_Select
{
    function initQuery($params)
    {
        $tables = Customer_Reviews::getTables();
        $crtable = $tables['customer_reviews']['columns'];

        $this -> addSelectField($crtable['cr_id']);
        $this -> WhereField($crtable['product_id'], DB_IN, '(\'' . implode('\',\'', $params['ids']) . '\')');
    }
}

// ---------------------------
// Update queries
// ---------------------------

class UPDATE_CUSTOMER_REVIEW_RECORD extends DB_Update
{
    function UPDATE_CUSTOMER_REVIEW_RECORD()
    {
        parent :: DB_Update('customer_reviews');
    }

    function initQuery($params)
    {
        if (!is_array($params))
            return;

        $tables = Customer_Reviews::getTables();
        $crtable = $tables['customer_reviews']['columns'];

        foreach($params as $k => $v)
            if (in_array($k, array('author', 'ip_address',
                         'product_id', 'review', 'status')))
                $this -> addUpdateValue($crtable[$k], $v);
            elseif ($k == 'datetime')
                $this -> addUpdateExpression($crtable[$k], $v);

        $this -> WhereValue($crtable['cr_id'], DB_EQ, @$params['cr_id']);
    }
}

class UPDATE_CUSTOMER_REVIEW_RATE_LIST_RECORD extends DB_Update
{
    function UPDATE_CUSTOMER_REVIEW_RATE_LIST_RECORD()
    {
        parent :: DB_Update('customer_reviews_rate_list');
    }

    function initQuery($params)
    {
        if (!is_array($params))
            return;

        $tables = Customer_Reviews::getTables();
        $rltable = $tables['customer_reviews_rate_list']['columns'];

        foreach($params as $k => $v)
            if (in_array($k, array('rate_label', 'visible', 'sort_order')))
                $this -> addUpdateValue($rltable[$k], $v);

        $this -> WhereValue($rltable['cr_rl_id'], DB_EQ, @$params['cr_rl_id']);
    }
}

// ---------------------------
// Insert queries
// ---------------------------

class INSERT_CUSTOMER_REVIEW_RATE_LIST_RECORD extends DB_Insert
{
    function INSERT_CUSTOMER_REVIEW_RATE_LIST_RECORD()
    {
        parent :: DB_Insert('customer_reviews_rate_list');
    }

    function initQuery($params)
    {
        $tables = Customer_Reviews::getTables();
        $rltable = $tables['customer_reviews_rate_list']['columns'];

        $this -> addInsertValue($params['sort_order'], $rltable['sort_order']);
        $this -> addInsertValue($params['rate_label'], $rltable['rate_label']);
        $this -> addInsertValue($params['visible'], $rltable['visible']);
    }
}

class INSERT_FAKE_CUSTOMER_REVIEW extends DB_Insert
{
    function INSERT_FAKE_CUSTOMER_REVIEW()
    {
        parent :: DB_Insert('customer_reviews');
    }

    function initQuery($params)
    {
        $tables = Customer_Reviews::getTables();
        $crtable = $tables['customer_reviews']['columns'];

        $this -> addInsertValue('Fake review', $crtable['review']);
    }
}

class INSERT_CUSTOMER_REVIEW_RATE_RECORD extends DB_Insert
{
    function INSERT_CUSTOMER_REVIEW_RATE_RECORD()
    {
        parent :: DB_Insert('customer_reviews_rates');
    }

    function initQuery($params)
    {
        $tables = Customer_Reviews::getTables();
        $rtable = $tables['customer_reviews_rates']['columns'];

        $this -> addInsertValue($params['cr_id'], $rtable['cr_id']);
        $this -> addInsertValue($params['cr_rl_id'], $rtable['cr_rl_id']);
        $this -> addInsertValue($params['rate'], $rtable['rate']);
    }
}

// ---------------------------
// Delete queries
// ---------------------------

class DELETE_CUSTOMER_REVIEW_RATE_LIST_RECORD extends DB_Delete
{
    function DELETE_CUSTOMER_REVIEW_RATE_LIST_RECORD()
    {
        parent :: DB_Delete('customer_reviews_rate_list');
    }

    function initQuery($params)
    {
        $tables = Customer_Reviews::getTables();
        $rltable = $tables['customer_reviews_rate_list']['columns'];

        $this -> WhereValue($rltable['cr_rl_id'], DB_EQ, $params['cr_rl_id']);
    }
}

class DELETE_CUSTOMER_REVIEW_RATE_RECORD extends DB_Delete
{
    function DELETE_CUSTOMER_REVIEW_RATE_RECORD()
    {
        parent :: DB_Delete('customer_reviews_rates');
    }

    function initQuery($params)
    {
        $tables = Customer_Reviews::getTables();
        $rtable = $tables['customer_reviews_rates']['columns'];

        if (isset($params['cr_id']))
            $this -> WhereValue($rtable['cr_id'], DB_EQ, $params['cr_id']);
        elseif (isset($params['cr_rl_id']))
            $this -> WhereValue($rtable['cr_rl_id'], DB_EQ,
                                $params['cr_rl_id']);
        else // force to delete nothing...
            $this -> WhereValue($rtable['crr_id'], DB_EQ, 0);
    }
}

class DELETE_CUSTOMER_REVIEW_RATE_RECORDS extends DB_Delete
{
    function DELETE_CUSTOMER_REVIEW_RATE_RECORDS()
    {
        parent :: DB_Delete('customer_reviews_rates');
    }

    function initQuery($params)
    {
        $tables = Customer_Reviews::getTables();
        $rtable = $tables['customer_reviews_rates']['columns'];

        $this -> WhereField($rtable['cr_id'], DB_IN, '(\'' . implode('\',\'', $params['ids']) . '\')');
    }
}

class DELETE_CUSTOMER_REVIEW_RECORD extends DB_Delete
{
    function DELETE_CUSTOMER_REVIEW_RECORD()
    {
        parent :: DB_Delete('customer_reviews');
    }

    function initQuery($params)
    {
        $tables = Customer_Reviews::getTables();
        $crtable = $tables['customer_reviews']['columns'];

        $this -> WhereValue($crtable['cr_id'], DB_EQ, @$params['cr_id']);
    }
}

class DELETE_CUSTOMER_REVIEW_RECORDS extends DB_Delete
{
    function DELETE_CUSTOMER_REVIEW_RECORDS()
    {
        parent :: DB_Delete('customer_reviews');
    }

    function initQuery($params)
    {
        $tables = Customer_Reviews::getTables();
        $crtable = $tables['customer_reviews']['columns'];

        $this -> WhereField($crtable['cr_id'], DB_IN, '(\'' . implode('\',\'', $params['ids']) . '\')');
    }
}

?>