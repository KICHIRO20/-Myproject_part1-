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

class SELECT_ATTR_ID_BY_VIEW_TAG_AND_PRODUCT_TYPE_ID extends DB_Select
{
    function initQuery($params)
    {
        $tables = Catalog::getTables();
        $a = $tables['attributes']['columns'];
        $pta = $tables['product_type_attributes']['columns'];

        $this->addSelectField($a['id'], 'attr_id');

        $this->WhereValue($pta['pt_id'], DB_EQ, $params['ptid']);
        $this->WhereAnd();
        $this->WhereField($pta['a_id'], DB_EQ, $a['id']);
        $this->WhereAnd();
        $this->WhereValue($a['view_tag'], DB_EQ, $params['view_tag']);
    }
}

class SELECT_COUNT_OF_PRODUCT_ID extends DB_Select
{
    function initQuery($params)
    {
        $tables = Catalog::getTables();
        $p = $tables['products']['columns'];

        $this->addSelectField($this->fCount($p['id']), 'count_p_id');
        $this->WhereValue($p['id'], DB_EQ, $params['product_id']);
    }
}

class SELECT_COUNT_OF_PRODUCT_TYPE_ID extends DB_Select
{
    function initQuery($params)
    {
        $tables = Catalog::getTables();
        $pt = $tables['product_types']['columns'];

        $this->addSelectField($this->fCount($pt['id']), 'count_pt_id');
        $this->WhereValue($pt['id'], DB_EQ, $params['product_type_id']);
    }
}

class SELECT_ALL_CATEGORIES_BASE_INFO extends DB_Select
{
    function initQuery($params)
    {
        $tables = Catalog::getTables();
        $c     = $tables['categories']['columns'];
        $cd    = $tables['categories_descr']['columns'];

        $this->addSelectField($c['id'], 'id');
        $this->addSelectField($c['left'], 'left1');
        $this->addSelectField($c['right'], 'right1');
        $this->addSelectField($c['level'], 'level');
        $this->addSelectField($c['status'], 'status');
        $this->setMultiLangAlias('_ml_category_name', 'categories_descr', $cd['name'], $cd['id'], 'Catalog');
        $this->addSelectField($this->getMultiLangAlias('_ml_category_name'), 'name');

        $this->setMultiLangAlias('_ml_category_descr', 'categories_descr', $cd['descr'], $cd['id'], 'Catalog');
        $this->addSelectField($this->getMultiLangAlias('_ml_category_descr'), 'descr');

        $this->addSelectField($cd['image_file'], 'largeimage_file');
        $this->addSelectField($cd['image_small_file'], 'smallimage_file');
        $this->setMultiLangAlias('_ml_category_image_descr', 'categories_descr', $cd['image_descr'], $cd['id'], 'Catalog');
        $this->addSelectField($this->getMultiLangAlias('_ml_category_image_descr'), 'image_descr');

        $this->setMultiLangAlias('_ml_category_page_title', 'categories_descr', $cd['page_title'], $cd['id'], 'Catalog');
        $this->addSelectField($this->getMultiLangAlias('_ml_category_page_title'), 'page_title');

        $this->setMultiLangAlias('_ml_category_meta_keywords', 'categories_descr', $cd['meta_keywords'], $cd['id'], 'Catalog');
        $this->addSelectField($this->getMultiLangAlias('_ml_category_meta_keywords'), 'meta_keywords');

        $this->setMultiLangAlias('_ml_category_meta_descr', 'categories_descr', $cd['meta_descr'], $cd['id'], 'Catalog');
        $this->addSelectField($this->getMultiLangAlias('_ml_category_meta_descr'), 'meta_descr');

        $this->addSelectField($cd['show_prod_recurs'], 'show_prod_recurs');

        $this->setMultiLangAlias('_ml_category_seo_url_prefix', 'categories_descr', $cd['seo_url_prefix'], $cd['id'], 'Catalog');
        $this->addSelectField($this->getMultiLangAlias('_ml_category_seo_url_prefix'), 'seo_url_prefix');

        $this->WhereField($c['id'], DB_EQ, $cd['id']);
    }
}

class SELECT_ALL_CATEGORIES_BASIC_INFO extends DB_Select
{
    function initQuery($params)
    {
        $tables = Catalog::getTables();
        $c     = $tables['categories']['columns'];
        $cd    = $tables['categories_descr']['columns'];

        $this->addSelectField($c['id'], 'id');
        $this->addSelectField($c['left'], 'left1');
        $this->addSelectField($c['right'], 'right1');
        $this->addSelectField($c['level'], 'level');
        $this->addSelectField($c['status'], 'status');
        $this->addSelectField($cd['image_file'], 'largeimage_file');
        $this->addSelectField($cd['image_small_file'], 'smallimage_file');
        $this->addSelectField($cd['show_prod_recurs'], 'show_prod_recurs');

        $this->addSelectField($cd['name'], 'name');
        $this->addSelectField($cd['descr'], 'descr');
        $this->addSelectField($cd['image_descr'], 'image_descr');
        $this->addSelectField($cd['page_title'], 'page_title');
        $this->addSelectField($cd['meta_keywords'], 'meta_keywords');
        $this->addSelectField($cd['meta_descr'], 'meta_descr');
        $this->addSelectField($cd['seo_url_prefix'], 'seo_url_prefix');

        $this->WhereField($c['id'], DB_EQ, $cd['id']);
    }
}

class IS_CORRECT_CATEGORY_ID extends DB_Select
{
    function initQuery($params)
    {
        $tables = Catalog::getTables();
        $c     = $tables['categories']['columns'];
        $this->addSelectField($c['id'], 'id');
        $this->WhereField($c['id'], DB_EQ, $params['category_id']);
    }
}

class SELECT_PRODUCT_IMAGES_NUMBER extends DB_Select
{
    function initQuery($params)
    {
        $tables = Catalog::getTables();
        $pa = $tables['product_attributes']['columns'];
        $a = $tables['attributes']['columns'];

        $this->addSelectField("COUNT(*)", 'NUM');
        $this->WhereField($pa['a_id'], DB_EQ, $a['id']);
        $this->WhereAND();
        $this->WhereValue($pa['attr_value'], DB_NEQ, '');
        $this->WhereAND();
        $this->WhereField($a['view_tag'], DB_IN, "('LargeImage','SmallImage')");
    }
}

class SELECT_BASE_PRODUCT_INFO extends DB_Select
{
    function initQuery($params)
    {
        $tables = Catalog::getTables();
        $t_products = $tables['products']['columns'];
        $t_product_types = $tables['product_types']['columns'];

        $this->addSelectField($t_products['id'],           'p_id');
        $this->setMultiLangAlias('_ml_product_name', 'products', $t_products['name'], $t_products['id'], 'Catalog');
        $this->addSelectField($this->getMultiLangAlias('_ml_product_name'), 'p_name');
        //$this->addSelectField($t_products['name'],         'p_name');
        $this->addSelectField($t_products['date_updated'], 'p_date_updated');
        $this->addSelectField($t_products['date_added'],   'p_date_added');
        $this->addSelectField($t_product_types['id'],      'p_type_id');
        $this->setMultiLangAlias('_ml_product_type_name', 'product_types', $t_product_types['name'], $t_product_types['id'], 'Catalog');
        $this->addSelectField($this->getMultiLangAlias('_ml_product_type_name'), 'p_type_name');
        //$this->addSelectField($t_product_types['name'],    'p_type_name');

        $this->WhereValue($t_products['id'], DB_EQ, $params['product_id']);
        $this->WhereAnd();
        $this->WhereField($t_product_types['id'], DB_EQ, $t_products['pt_id']);
    }
}

class SELECT_ALL_PRODUCT_CATEGORIES extends DB_Select
{
    function initQuery($params)
    {
        $tables = Catalog::getTables();
        $p2c = $tables['products_to_categories']['columns'];
        $this->addSelectField($p2c['category_id'],'category_id');
        $this->WhereValue($p2c['product_id'], DB_EQ, $params['product_id']);
    }
}

class SELECT_INPUT_TYPE_VALUES_BY_ATTRIBUTE extends DB_Select
{
    function initQuery($params)
    {
        $tables = Catalog::getTables();
        $t_input_type_values = $tables['input_type_values']['columns'];
        $this->setMultiLangAlias('_ml_input_type_value', 'input_type_values', $t_input_type_values['value'], $t_input_type_values['id'], 'Catalog');
        $this->addSelectValue($this->getMultiLangAlias('_ml_input_type_value'), 'value');
        //$this->addSelectField($t_input_type_values['value'], 'value');
        $this->WhereValue($t_input_type_values['it_id'], DB_EQ, $params['a_input_type_id']);
        $this->WhereAND();
        $this->WhereValue($t_input_type_values['id'], DB_EQ, $params['pa_value']);
    }
}

class SELECT_CUSTOM_PRODUCT_TAGS extends DB_Select
{
    function initQuery($params)
    {
        $tables = Catalog::getTables();
        $a     = $tables['attributes']['columns'];

        $this->addSelectField($a['view_tag'], 'view_tag');
        $this->WhereValue($a['type'], DB_EQ, 'custom');
    }
}


class SELECT_MAX_PRODUCT_SORT_ORDER_IN_CATEGORY extends DB_Select
{
    function initQuery($params)
    {
        $cid = $params['category_id'];

        $tables = Catalog::getTables();
        $columns = $tables['products']['columns'];

        $this->addSelectField($this->fMax($columns['sort_order']), 'max');
        $this->WhereValue($columns['c_id'], DB_EQ, $cid);
    }
}

/**
 *                                                                SELECT_PRODUCT_LIST.
 *                                                                                            .
 *
 *         !                                             .
 *
 */
class PRODUCT_LIST_PARAMS
{
    /**
     * (int) Category ID
     */
    var $category_id = null;

    /**
     * (array)        ID          ,
     */
    var $product_id_list_to_ignore = array();

    /**
     * (array)                       ID          .                                 ,
     *               .
     */
    var $product_id_list_to_select = array();

    /**
     * (const) Select Mode: IN_CATEGORY_ONLY or IN_CATEGORY_RECURSIVELY or IN_CATEGORY_RECURSIVELY_WITHOUT_CURRENT
     */
    var $select_mode_recursiveness = IN_CATEGORY_ONLY;

    /**
     * (const) Select Mode: UNIQUE_PRODUCTS or ALL_PRODUCT_LINKS
     */
    var $select_mode_uniqueness = UNIQUE_PRODUCTS;

    /**
     * (bool) Select online products only or both offline and online
     */
    var $select_online_products_only = false;

    /**
     * (bool) Use or not paginator. If true - will be used current paginator.
     */
    var $use_paginator = false;

    var $membership_filter = false;

    /**
     * (int)                        ,
     */
    var $filter_stock_level_min = null;

    /**
     * (int)                        ,
     */
    var $filter_stock_level_max = null;

    /**
     * (array) The list of manufacturer IDs
     */
    var $filter_manufacturer_id_list = null;

    /**
     * (array) The list of product type IDs
     */
    var $filter_product_type_id_list = null;

    /**
     * (array)            ,                                                     .
     */
    var $filter_product_name_like_values = null;

    /**
     * (const) SEARCH_ALL_VALUES or SEARCH_ANY_VALUES.
     */
    var $filter_product_name_search_condition = SEARCH_ALL_VALUES;

    /**
     * (int)                        ,
     */
    var $filter_sale_price_min = null;

    /**
     * (int)                        ,
     */
    var $filter_sale_price_max = null;

    /**
     * (int)                        , list-
     */
    var $filter_list_price_min = null;

    /**
     * (int)                        , list-
     */
    var $filter_list_price_max = null;

    /**
     * (int)                        , PerItemShippingCost
     */
    var $filter_per_item_shipping_cost_min = null;

    /**
     * (int)                        , PerItemShippingCost
     */
    var $filter_per_item_shipping_cost_max = null;

    /**
     * (int)                        , PerItemHandlingCost
     */
    var $filter_per_item_handling_cost_min = null;

    /**
     * (int)                        , PerItemHandlingCost
     */
    var $filter_per_item_handling_cost_max = null;

    /**
     * (int)                        ,
     */
    var $filter_weight_min = null;

    /**
     * (int)                        ,
     */
    var $filter_weight_max = null;

    /**
     * (array)            ,                                         SKU         .
     */
    var $filter_sku_like_values = null;

    /**
     * (const) SEARCH_ALL_VALUES or SEARCH_ANY_VALUES.
     */
    var $filter_sku_search_condition = SEARCH_ALL_VALUES;

    /**
     * (array)            ,                                         Short Description         .
     */
    var $filter_short_description_like_values = null;

    /**
     * (const) SEARCH_ALL_VALUES or SEARCH_ANY_VALUES.
     */
    var $filter_short_description_search_condition = SEARCH_ALL_VALUES;

    /**
     * (array)            ,                                         Detailed Description          .
     */
    var $filter_detailed_description_like_values = null;

    /**
     * (const) SEARCH_ALL_VALUES or SEARCH_ANY_VALUES.
     */
    var $filter_detailed_description_search_condition = SEARCH_ALL_VALUES;

    /**
     * (array)            ,                                         Page Title          .
     */
    var $filter_page_title_like_values = null;

    /**
     * (const) SEARCH_ALL_VALUES or SEARCH_ANY_VALUES.
     */
    var $filter_page_title_search_condition = SEARCH_ALL_VALUES;

    /**
     * (array)            ,                                         Meta Keywords          .
     */
    var $filter_meta_keywords_like_values = null;

    /**
     * (const) SEARCH_ALL_VALUES or SEARCH_ANY_VALUES.
     */
    var $filter_meta_keywords_search_condition = SEARCH_ALL_VALUES;

    /**
     * (array)            ,                                         Meta Description          .
     */
    var $filter_meta_description_like_values = null;

    /**
     * (const) SEARCH_ALL_VALUES or SEARCH_ANY_VALUES.
     */
    var $filter_meta_description_search_condition = SEARCH_ALL_VALUES;

    /**
     * (const)                 SORT_BY_PRODUCT_*
     */
    var $sort_by = SORT_BY_PRODUCT_SORT_ORDER;

    /**
     * (const) SORT_DIRECTION_ASC or SORT_DIRECTION_DESC
     *
     * @var unknown_type
     */
    var $sort_direction = 'ASC';


    /**
     * Raw format of params array
     */
    var $params = array();

    /**
     *                             default                            :                                 .
     *
     *             ,                                 ,
     *                   -                                      ,               ,                   . .
     *
     *                                                       ,                                             ,
     *          default                              ,                                             .
     *
     * @return PRODUCT_LIST_PARAMS
     */
    function PRODUCT_LIST_PARAMS()
    {
        $this->params['category_id'] = 1;
        $this->params['select_mode_recursiveness'] = IN_CATEGORY_RECURSIVELY;
        $this->params['select_mode_uniqueness'] = UNIQUE_PRODUCTS;
        $this->params['product_id_list_to_ignore'] = array();
        $this->params['product_id_list_to_select'] = array();

        $this->params['filter']['type']['exact_values'] = null;

        $this->params['filter']['name']['like_values'] = null;
        $this->params['filter']['name']['search_condition'] = SEARCH_ALL_VALUES;

        $this->params['filter']['status']['online_only'] = false;
        $this->params['filter']['status']['select_undefined'] = true;
        $this->params['filter']['status']['select_invisible'] = true;
        $this->params['filter']['status']['online_subcat_ids'] = null;
        $this->params['filter']['status']['min_value'] = null;
        $this->params['filter']['status']['max_value'] = null;
        $this->params['filter']['status']['exact_values'] = null;
        $this->params['filter']['status']['filter_type'] = 'DIGITAL';
        $this->params['filter']['status']['attribute_id'] = AVAILABLE_PRODUCT_ATTRIBUTE_ID;
        $this->params['filter']['status']['sort_flag'] = false;

        $this->params['filter']['manufacturer']['exact_values'] = null;
        $this->params['filter']['manufacturer']['select_undefined'] = null;
        $this->params['filter']['manufacturer']['select_invisible'] = true;
        $this->params['filter']['manufacturer']['filter_type'] = 'IN_ARRAY';
        $this->params['filter']['manufacturer']['attribute_id'] = modApiFunc('Catalog', 'getManufacturerAttrId');
        $this->params['filter']['manufacturer']['sort_flag'] = false;

        $this->params['filter']['stock_level']['min_value'] = null;
        $this->params['filter']['stock_level']['max_value'] = null;
        $this->params['filter']['stock_level']['exact_values'] = null;
        $this->params['filter']['stock_level']['product_to_skip'] = array();
        $this->params['filter']['stock_level']['select_undefined'] = true;
        $this->params['filter']['stock_level']['select_invisible'] = true;
        $this->params['filter']['stock_level']['filter_type'] = 'DIGITAL';
        $this->params['filter']['stock_level']['attribute_id'] = QUANTITY_IN_STOCK_PRICE_PRODUCT_ATTRIBUTE_ID;
        $this->params['filter']['stock_level']['sort_flag'] = ($this->sort_by == SORT_BY_PRODUCT_QUANTITY_IN_STOCK);

        $this->params['filter']['price']['min_value'] = null;
        $this->params['filter']['price']['max_value'] = null;
        $this->params['filter']['price']['exact_values'] = null;
        $this->params['filter']['price']['select_undefined'] = true;
        $this->params['filter']['price']['select_invisible'] = true;
        $this->params['filter']['price']['filter_type'] = 'DIGITAL';
        $this->params['filter']['price']['attribute_id'] = SALE_PRICE_PRODUCT_ATTRIBUTE_ID;
        $this->params['filter']['price']['sort_flag'] = ($this->sort_by == SORT_BY_PRODUCT_SALE_PRICE);

        $this->params['filter']['list_price']['min_value'] = null;
        $this->params['filter']['list_price']['max_value'] = null;
        $this->params['filter']['list_price']['exact_values'] = null;
        $this->params['filter']['list_price']['select_undefined'] = true;
        $this->params['filter']['list_price']['select_invisible'] = true;
        $this->params['filter']['list_price']['filter_type'] = 'DIGITAL';
        $this->params['filter']['list_price']['attribute_id'] = LIST_PRICE_PRODUCT_ATTRIBUTE_ID;
        $this->params['filter']['list_price']['sort_flag'] = ($this->sort_by == SORT_BY_PRODUCT_LIST_PRICE);

        $this->params['filter']['per_item_shipping_cost']['min_value'] = null;
        $this->params['filter']['per_item_shipping_cost']['max_value'] = null;
        $this->params['filter']['per_item_shipping_cost']['exact_values'] = null;
        $this->params['filter']['per_item_shipping_cost']['select_undefined'] = true;
        $this->params['filter']['per_item_shipping_cost']['select_invisible'] = true;
        $this->params['filter']['per_item_shipping_cost']['filter_type'] = 'DIGITAL';
        $this->params['filter']['per_item_shipping_cost']['attribute_id'] = PER_ITEM_SHIPPING_COST_PRODUCT_ATTRIBUTE_ID;
        $this->params['filter']['per_item_shipping_cost']['sort_flag'] = ($this->sort_by == SORT_BY_PRODUCT_PER_ITEM_SHIPPING_COST);

        $this->params['filter']['per_item_handling_cost']['min_value'] = null;
        $this->params['filter']['per_item_handling_cost']['max_value'] = null;
        $this->params['filter']['per_item_handling_cost']['exact_values'] = null;
        $this->params['filter']['per_item_handling_cost']['select_undefined'] = true;
        $this->params['filter']['per_item_handling_cost']['select_invisible'] = true;
        $this->params['filter']['per_item_handling_cost']['filter_type'] = 'DIGITAL';
        $this->params['filter']['per_item_handling_cost']['attribute_id'] = PER_ITEM_HANDLING_COST_PRODUCT_ATTRIBUTE_ID;
        $this->params['filter']['per_item_handling_cost']['sort_flag'] = ($this->sort_by == SORT_BY_PRODUCT_PER_ITEM_HANDLING_COST);

        $this->params['filter']['weight']['min_value'] = null;
        $this->params['filter']['weight']['max_value'] = null;
        $this->params['filter']['weight']['exact_values'] = null;
        $this->params['filter']['weight']['select_undefined'] = true;
        $this->params['filter']['weight']['select_invisible'] = true;
        $this->params['filter']['weight']['filter_type'] = 'DIGITAL';
        $this->params['filter']['weight']['attribute_id'] = WEIGHT_PRODUCT_ATTRIBUTE_ID;
        $this->params['filter']['weight']['sort_flag'] = ($this->sort_by == SORT_BY_PRODUCT_WEIGHT);

        $this->params['filter']['sku']['like_values'] = null;
        $this->params['filter']['sku']['search_condition'] = SEARCH_ALL_VALUES;
        $this->params['filter']['sku']['select_undefined'] = true;
        $this->params['filter']['sku']['select_invisible'] = true;
        $this->params['filter']['sku']['filter_type'] = 'TEXT';
        $this->params['filter']['sku']['attribute_id'] = SKU_PRODUCT_ATTRIBUTE_ID;
        $this->params['filter']['sku']['sort_flag'] = ($this->sort_by == SORT_BY_PRODUCT_SKU);

        $this->params['filter']['short_description']['like_values'] = null;
        $this->params['filter']['short_description']['search_condition'] = SEARCH_ALL_VALUES;
        $this->params['filter']['short_description']['select_undefined'] = true;
        $this->params['filter']['short_description']['select_invisible'] = true;
        $this->params['filter']['short_description']['filter_type'] = 'TEXT';
        $this->params['filter']['short_description']['attribute_id'] = SHORT_DESCRIPTION_PRODUCT_ATTRIBUTE_ID;
        $this->params['filter']['short_description']['sort_flag'] = false;

        $this->params['filter']['detailed_description']['like_values'] = null;
        $this->params['filter']['detailed_description']['search_condition'] = SEARCH_ALL_VALUES;
        $this->params['filter']['detailed_description']['select_undefined'] = true;
        $this->params['filter']['detailed_description']['select_invisible'] = true;
        $this->params['filter']['detailed_description']['filter_type'] = 'TEXT';
        $this->params['filter']['detailed_description']['attribute_id'] = DETAILED_DESCRIPTION_PRODUCT_ATTRIBUTE_ID;
        $this->params['filter']['detailed_description']['sort_flag'] = false;

        $this->params['filter']['page_title']['like_values'] = null;
        $this->params['filter']['page_title']['search_condition'] = SEARCH_ALL_VALUES;
        $this->params['filter']['page_title']['select_undefined'] = true;
        $this->params['filter']['page_title']['select_invisible'] = true;
        $this->params['filter']['page_title']['filter_type'] = 'TEXT';
        $this->params['filter']['page_title']['attribute_id'] = PAGE_TITLE_PRODUCT_ATTRIBUTE_ID;
        $this->params['filter']['page_title']['sort_flag'] = false;

        $this->params['filter']['meta_keywords']['like_values'] = null;
        $this->params['filter']['meta_keywords']['search_condition'] = SEARCH_ALL_VALUES;
        $this->params['filter']['meta_keywords']['select_undefined'] = true;
        $this->params['filter']['meta_keywords']['select_invisible'] = true;
        $this->params['filter']['meta_keywords']['filter_type'] = 'TEXT';
        $this->params['filter']['meta_keywords']['attribute_id'] = META_KEYWORDS_PRODUCT_ATTRIBUTE_ID;
        $this->params['filter']['meta_keywords']['sort_flag'] = false;

        $this->params['filter']['meta_description']['like_values'] = null;
        $this->params['filter']['meta_description']['search_condition'] = SEARCH_ALL_VALUES;
        $this->params['filter']['meta_description']['select_undefined'] = true;
        $this->params['filter']['meta_description']['select_invisible'] = true;
        $this->params['filter']['meta_description']['filter_type'] = 'TEXT';
        $this->params['filter']['meta_description']['attribute_id'] = META_DESCRIPTION_PRODUCT_ATTRIBUTE_ID;
        $this->params['filter']['meta_description']['sort_flag'] = false;

        $this->params['filter']['membership_visibility']['search_condition'] = SEARCH_ALL_VALUES;
        $this->params['filter']['membership_visibility']['select_undefined'] = true;
        $this->params['filter']['membership_visibility']['select_invisible'] = true;
        $this->params['filter']['membership_visibility']['filter_type'] = 'REGEXP';
        $this->params['filter']['membership_visibility']['attribute_id'] = modApiFunc('Catalog', 'getMembershipVisibilityAttrId');
        $this->params['filter']['membership_visibility']['sort_flag'] = false;
        $this->params['filter']['membership_visibility']['active'] = false;

        $this->params['paginator'] = null;
        $this->params['sort_by'] = SORT_BY_PRODUCT_SORT_ORDER;
        $this->params['sort_direction'] = 'ASC';
    }

    function getParams()
    {
        global $application;
        $this->params['category_id'] = $this->category_id;
        $this->params['product_id_list_to_ignore'] = $this->product_id_list_to_ignore;
        $this->params['product_id_list_to_select'] = $this->product_id_list_to_select;

        $this->params['select_mode_recursiveness'] = $this->select_mode_recursiveness;
        $this->params['select_mode_uniqueness'] = $this->select_mode_uniqueness;

        $this->params['filter']['status']['online_only'] = $this->select_online_products_only;
        $this->params['filter']['status']['exact_values'] = $this->select_online_products_only == true ? PRODUCT_STATUS_ONLINE : null;

        $this->params['filter']['membership_visibility']['active'] = $this->membership_filter;
        if($this->sort_by === SORT_BY_RAND) $this->params['rand'] = rand();

        //                          -                                              .
        if (IN_CATEGORY_RECURSIVELY === $this->select_mode_recursiveness or IN_CATEGORY_RECURSIVELY_WITHOUT_CURRENT === $this->select_mode_recursiveness)
        {
            $c = &$application->getInstance('CCategoryInfo', $this->category_id);
            if ($this->select_online_products_only === true )
            {
                //             online    -
                $this->params['filter']['status']['online_subcat_ids'] = $c->_getOnlineSubCategories();
            }
            else
            {
                $this->params['category_left'] = $c->getCategoryTagValue('left');
                $this->params['category_right'] = $c->getCategoryTagValue('right');;
            }
        }

        $this->params['filter']['stock_level']['min_value'] = $this->filter_stock_level_min;
        $this->params['filter']['stock_level']['max_value'] = $this->filter_stock_level_max;
        $this->params['filter']['stock_level']['sort_flag'] = ($this->sort_by == SORT_BY_PRODUCT_QUANTITY_IN_STOCK);
        $this->params['filter']['stock_level']['product_to_skip'] = modApiFunc('Product_Options','getEntitiesWhichUseInventoryTracking','product');

        $this->params['filter']['type']['exact_values'] = $this->filter_product_type_id_list;

        $this->params['filter']['name']['like_values'] = $this->filter_product_name_like_values;
        $this->params['filter']['name']['search_condition'] = $this->filter_product_name_search_condition;

        $this->params['filter']['manufacturer']['exact_values'] = $this->filter_manufacturer_id_list;

        $this->params['filter']['price']['min_value'] = $this->filter_sale_price_min;
        $this->params['filter']['price']['max_value'] = $this->filter_sale_price_max;
        $this->params['filter']['price']['sort_flag'] = ($this->sort_by == SORT_BY_PRODUCT_SALE_PRICE);

        $this->params['filter']['list_price']['min_value'] = $this->filter_list_price_min;
        $this->params['filter']['list_price']['max_value'] = $this->filter_list_price_max;
        $this->params['filter']['list_price']['sort_flag'] = ($this->sort_by == SORT_BY_PRODUCT_LIST_PRICE);

        $this->params['filter']['per_item_shipping_cost']['min_value'] = $this->filter_per_item_shipping_cost_min;
        $this->params['filter']['per_item_shipping_cost']['max_value'] = $this->filter_per_item_shipping_cost_max;
        $this->params['filter']['per_item_shipping_cost']['sort_flag'] = ($this->sort_by == SORT_BY_PRODUCT_PER_ITEM_SHIPPING_COST);

        $this->params['filter']['per_item_handling_cost']['min_value'] = $this->filter_per_item_handling_cost_min;
        $this->params['filter']['per_item_handling_cost']['max_value'] = $this->filter_per_item_handling_cost_max;
        $this->params['filter']['per_item_handling_cost']['sort_flag'] = ($this->sort_by == SORT_BY_PRODUCT_PER_ITEM_HANDLING_COST);

        $this->params['filter']['weight']['min_value'] = $this->filter_weight_min;
        $this->params['filter']['weight']['max_value'] = $this->filter_weight_max;
        $this->params['filter']['weight']['sort_flag'] = ($this->sort_by == SORT_BY_PRODUCT_WEIGHT);

        $this->params['filter']['sku']['like_values'] = $this->filter_sku_like_values;
        $this->params['filter']['sku']['search_condition'] = $this->filter_sku_search_condition;
        $this->params['filter']['sku']['sort_flag'] = ($this->sort_by == SORT_BY_PRODUCT_SKU);

        $this->params['filter']['short_description']['like_values'] = $this->filter_short_description_like_values;
        $this->params['filter']['short_description']['search_condition'] = $this->filter_short_description_search_condition;

        $this->params['filter']['detailed_description']['like_values'] = $this->filter_detailed_description_like_values;
        $this->params['filter']['detailed_description']['search_condition'] = $this->filter_detailed_description_search_condition;

        $this->params['filter']['page_title']['like_values'] = $this->filter_page_title_like_values;
        $this->params['filter']['page_title']['search_condition'] = $this->filter_page_title_search_condition;

        $this->params['filter']['meta_keywords']['like_values'] = $this->filter_meta_keywords_like_values;
        $this->params['filter']['meta_keywords']['search_condition'] = $this->filter_meta_keywords_search_condition;

        $this->params['filter']['meta_description']['like_values'] = $this->filter_meta_description_like_values;
        $this->params['filter']['meta_description']['search_condition'] = $this->filter_meta_description_search_condition;

        $this->params['sort_by'] = $this->sort_by;
        $this->params['sort_direction'] = $this->sort_direction;
        $this->params['cur_membership'] = modApiFunc('Customer_Account','getCurrentSignedCustomerGroupID');

        //
        if ($this->use_paginator === true)
        {
            $this->params['paginator'] = null;
            $this->params['paginator'] = execQueryPaginator('SELECT_PRODUCT_LIST', $this->params);
        }

        return $this->params;
    }

    function setSelectLimits($offset, $count)
    {
        $this->use_paginator = false;
        $this->params['paginator'] = array($offset, $count);
    }
}

class SELECT_PRODUCT_LIST extends DB_Select
{
    function initQuery($params)
    {
        $tables = Catalog::getTables();
        $t_c    = $tables['categories']['columns'];
        $t_p2c  = $tables['products_to_categories']['columns'];
        $t_p    = $tables['products']['columns'];
        $t_pa   = $tables['product_attributes']['columns'];
        $t_pta  = $tables['product_type_attributes']['columns'];

        $this->_table_products = $t_p;
        $this->_table_product_attributes = $t_pa;
        $this->_table_product_type_attributes = $t_pta;

        if ($params['select_mode_uniqueness'] == UNIQUE_PRODUCTS
            && $params['select_mode_recursiveness'] !== IN_CATEGORY_ONLY)
        {
            $this->addSelectField('DISTINCT ('.$t_p['id'].')', 'product_id');
        }
        else
        {
            $this->addSelectField($t_p['id'], 'product_id');
        }

        //                     product type IDs
        if (isset($params['filter']['type']['exact_values']))
        {
            $this->WhereField($t_p['pt_id'], DB_IN, ' ('.implode(',',$params['filter']['type']['exact_values']).') ');
            $this->WhereAND();
        }

        //                     ID          ,
        if (isset($params['product_id_list_to_ignore']) and is_array($params['product_id_list_to_ignore']) and !empty($params['product_id_list_to_ignore']))
        {
            $this->WhereField($t_p['id'], DB_NIN, '('.implode(',', $params['product_id_list_to_ignore']).')');
            $this->WhereAND();
        }

        //                           -                                 ,
        if (isset($params['product_id_list_to_select']) and is_array($params['product_id_list_to_select']) and !empty($params['product_id_list_to_select']))
        {
            $this->WhereField($t_p['id'], DB_IN, '('.implode(',', $params['product_id_list_to_select']).')');
            $this->WhereAND();
        }

        //
        if (isset($params['filter']['name']['like_values']))
        {
            $product_name_words = $params['filter']['name']['like_values'];
            // MultiLang
            $this -> setMultiLangAlias('_ml_name', 'products', $t_p['name'], $t_p['id'], 'Catalog');
            $this->addWhereOpenSection();
            for($i=0; $i<count($product_name_words); $i++)
            {
                // MultiLang
                $this -> WhereValue($this -> getMultiLangAlias('_ml_name'), DB_LIKE, '%'.$product_name_words[$i].'%');
                //$this->WhereValue($t_p['name'], DB_LIKE, '%'.$product_name_words[$i].'%');

                if ($i < count($product_name_words)-1)
                {
                    if (SEARCH_ALL_VALUES == $params['filter']['name']['search_condition'])
                    {
                        $this->WhereAnd();
                    }
                    else
                    {
                        $this->WhereOr();
                    }
                }
            }
            $this->addWhereCloseSection();
            $this->WhereAND();
        }

        //
        if ($params['select_mode_recursiveness'] === IN_CATEGORY_ONLY)
        {
            $this->WhereField($t_p2c['product_id'], DB_EQ, $t_p['id']);
            $this->WhereAND();
            $this->WhereValue($t_p2c['category_id'], DB_EQ, $params['category_id']);
        }
        else
        {
            if ($params['filter']['status']['online_only'] === true)
            {
                $products_status_online_subcat_ids = $params['filter']['status']['online_subcat_ids'];
                if ($params['select_mode_recursiveness'] == IN_CATEGORY_RECURSIVELY_WITHOUT_CURRENT)
                {
                    $key = array_search($category_id, $products_status_online_subcat_ids);
                    if($key)
                    {
                        unset($products_status_online_subcat_ids[$key]);
                    }
                }
                $this->WhereField($t_p2c['product_id'], DB_EQ, $t_p['id']);
                $this->WhereAND();
                $this->WhereField($t_p2c['category_id'], DB_IN,"('" . implode("','", $products_status_online_subcat_ids) . "')");
            }
            else
            {
                $this->WhereField($t_p2c['product_id'], DB_EQ, $t_p['id']);
                $this->WhereAND();
                $this->WhereField($t_c['id'], DB_EQ, $t_p2c['category_id'] );
                $this->WhereAND();
                $this->WhereValue($t_c['left'], DB_GTE, $params['category_left'] );
                $this->WhereAND();
                if ( $params['select_mode_recursiveness'] == IN_CATEGORY_RECURSIVELY )
                {
                    $this->WhereValue($t_c['right'], DB_LTE, $params['category_right']);
                }
                else # IN_CATEGORY_RECURSIVELY_WITHOUT_CURRENT
                {
                    $this->WhereValue($t_c['right'], DB_LT,  $params['category_right'] );
                }
            }
        }

        $filter_list = $params['filter'];
        foreach($filter_list as $filter_name => $filter_data)
        {
            if (!isset($filter_data['filter_type']))
            {
                continue;
            }

            if (!isset($filter_data['product_to_skip']) || !is_array($filter_data['product_to_skip']))
            {
                $filter_data['product_to_skip'] = array();
            }

            switch($filter_data['filter_type'])
            {
                case 'REGEXP':
                    $this->_addRegexpAttributeFilter($filter_data['attribute_id'],
                                                     $filter_data['search_condition'],
                                                     $filter_data['sort_flag'],
                                                     $params['sort_direction'],
                                                     $filter_data['select_undefined'],
                                                     $filter_data['select_invisible'],
                                                     $params['cur_membership'],
                                                     (isset($filter_data['active'])?$filter_data['active']:'false'));
                break;
                case 'TEXT':
                    $this->_addTextAttributeFilter($filter_data['attribute_id'],
                                                   $filter_data['like_values'],
                                                   $filter_data['search_condition'],
                                                   $filter_data['sort_flag'],
                                                   $params['sort_direction'],
                                                   $filter_data['select_undefined'],
                                                   $filter_data['select_invisible']);
                break;

                case 'DIGITAL':
                    $this->_addDigitalAttributeFilter($filter_data['attribute_id'],
                                                      $filter_data['min_value'],
                                                      $filter_data['max_value'],
                                                      $filter_data['exact_values'],
                                                      $filter_data['sort_flag'],
                                                      $params['sort_direction'],
                                                      $filter_data['select_undefined'],
                                                      $filter_data['select_invisible'],
                                                      $filter_data['product_to_skip']);
                break;

                case 'IN_ARRAY':
                    $this->_addInArrayAttributeFilter($filter_data['attribute_id'],
                                                      $filter_data['exact_values'],
                                                      $filter_data['select_invisible']);
                break;

            }
        }

        $sort_field = $params['sort_by'];
        if ($sort_field === SORT_BY_PRODUCT_ID)
        {
            $this->SelectOrder($t_p['id'], $params['sort_direction']);
        }
        else if ($sort_field === SORT_BY_PRODUCT_NAME)
        {
            $this->SelectOrder($t_p['name'], $params['sort_direction']);
        }
        else if ($sort_field === SORT_BY_PRODUCT_DATE_UPDATED)
        {
            $this->SelectOrder($t_p['date_updated'], $params['sort_direction']);
        }
        else if ($sort_field === SORT_BY_PRODUCT_DATE_ADDED)
        {
            $this->SelectOrder($t_p['date_added'], $params['sort_direction']);
        }
        else if ($sort_field === SORT_BY_PRODUCT_SORT_ORDER)
        {
            $this->SelectOrder($t_p2c['sort_order'], $params['sort_direction']);
        }
        else if ($sort_field === SORT_BY_RAND)
        {
            $this->SelectOrder('RAND()');
        }

        $paginator_limits = $params['paginator'];
        if ($paginator_limits !== null && is_array($paginator_limits) === true)
        {
            list($offset,$count) = $paginator_limits;
            $this->SelectLimit($offset,$count);
        }

        #if ($params['select_mode_uniqueness'] == UNIQUE_PRODUCTS)
        #{
        #    $this->SelectGroup($t_p['id']);
        #}
    }

    function _addDigitalAttributeFilter($i_attr_id, $i_min_value, $i_max_value, $i_value, $b_sort, $s_sort_direction, $b_sel_undefined, $b_sel_invisible, $product_to_skip)
    {
        //
        if ($i_min_value !== null or
            $i_max_value !== null or
            $i_value !== null or
            $b_sort === true)
        {
            $this->_table_alias_counter++;
            $t_pa = $this->_table_product_attributes;
            $t_p = $this->_table_products;
            $t_pta = $this->_table_product_type_attributes;

            //                  ,
            $_t_pa = $this->addTableAlias($t_pa, 'pa'.$this->_table_alias_counter, 'product_attributes');
            $this->addInnerJoin(
                                'product_attributes pa'.$this->_table_alias_counter, //
                                $t_p['id'],
                                DB_EQ,
                                $_t_pa['p_id'].
                                ' AND '.
                                $_t_pa['a_id'].' = "'.$this->DBAddSlashes($i_attr_id).'"'   // id
                                );

            //                                          -
            if ($b_sort === true)
            {
                //$this->SelectOrder('CONVERT('.$_t_pa['attr_value'].', SIGNED) ', ife(isset($s_sort_direction), $s_sort_direction, 'ASC'));
                $this->SelectOrder($_t_pa['attr_value'].'*1', ife(isset($s_sort_direction), $s_sort_direction, 'ASC'));
            }

            //
            if ($i_min_value !== null or
                $i_max_value !== null or
                $i_value !== null )
            {
                $_t_pta = $this->addTableAlias($t_pta, 'pta'.$this->_table_alias_counter, 'product_type_attributes');

                //                    INNER JOIN
                //                ,                         ,
                $_filter_condition = ' ( ( '.
                                     $_t_pta['type_attr_visible'] .' = 1 '. //
                                     ' AND '.
                                     ' ( ';

                //
                if ($i_min_value !== null and $i_max_value !== null)
                {
                    $_filter_condition .= ' ( '.
                                          $_t_pa['attr_value'] . ' >= '.$i_min_value.' '.
                                          ' AND '.
                                          $_t_pa['attr_value'] . ' <= '.$i_max_value.' '.
                                          ' ) ';
                }
                else if ($i_min_value !== null)
                {
                    $_filter_condition .= ' ( '.
                                          $_t_pa['attr_value'] . ' >= '.$i_min_value.' '.
                                          ' ) ';
                }
                else if ($i_max_value !== null)
                {
                    $_filter_condition .= ' ( '.
                                          $_t_pa['attr_value'] . ' <= '.$i_max_value.' '.
                                          ' ) ';
                }
                else if ($i_value !== null)
                {
                    $_filter_condition .= ' ( '.
                                          $_t_pa['attr_value'] . ' = '.$i_value.' '.
                                          ' ) ';
                }

                //                      :
                if ($b_sel_undefined === true)
                {
                    //              OR -  . .                                   ,
                    //
                    $_filter_condition .= ' OR '.
                                          $_t_pa['attr_value'] . ' = "" ';
                }
                else
                {
                    //                                                                ,
                    //                     AND -
                    //
                    $_filter_condition .= ' AND '.
                                          $_t_pa['attr_value'] . ' != "" ';
                }

                //
                $_filter_condition .= ' ) ) ';

                //                      :
                if ($b_sel_invisible === true)
                {
                    //              OR -           ,                                   ,
                    //
                    $_filter_condition .=   ' OR '.
                                            ' ( '.
                                                $_t_pta['type_attr_visible'] .' != 1 '. //
                                            ' ) ';
                }

                //                            ,                                 .
                if (!empty($product_to_skip))
                {
                    $_filter_condition .=   ' OR '.
                                            ' ( '.
                                                $t_p['id'] .' IN  ('.implode(',',$product_to_skip).') '.
                                            ' ) ';
                }


                $_filter_condition .= ' ) ';

                $this->addInnerJoin(
                                    'product_type_attributes pta'.$this->_table_alias_counter, //
                                    $_t_pa['a_id'],
                                    DB_EQ,
                                    $_t_pta['a_id'].
                                    ' AND '.
                                    $t_p['pt_id'].' = '.$_t_pta['pt_id'].
                                    ' AND '.
                                    $_filter_condition
                                    );
            }
        }
    }

    function _addRegexpAttributeFilter($i_attr_id, $c_condition, $b_sort, $s_sort_direction, $b_sel_undefined, $b_sel_invisible, $cur_gr, $b_active)
    {
        if($b_active)
        {
            $this->_table_alias_counter++;
            $t_pa = $this->_table_product_attributes;
            $t_p = $this->_table_products;
            $t_pta = $this->_table_product_type_attributes;

            $_t_pa = $this->addTableAlias($t_pa,'pa'.$this->_table_alias_counter, 'product_attributes');
            $this->addInnerJoin(
                                'product_attributes pa'.$this->_table_alias_counter,
                                $t_p['id'],
                                DB_EQ,
                                $_t_pa['p_id'].
                                ' AND '.
                                $_t_pa['a_id'].' = "'.$this->DBAddSlashes($i_attr_id).'"'
                                );

            if($b_sort==true)
            {
                $this->SelectOrder($_t_pa['attr_value'], ife(isset($s_sort_direction), $s_sort_direction, 'ASC'));
            }
            $_t_pta = $this->addTableAlias($t_pta, 'pta'.$this->_table_alias_counter, 'product_type_attributes');
            $_filter_condition = ' ( '.
                                     $_t_pta['type_attr_visible'] .' = 1 ';

            if ($b_sel_undefined === true)
            {
                $_filter_condition .= ' OR ' . $_t_pa['attr_value'] . ' = "" ';
            }
            else
            {
                $_filter_condition .= ' AND ' . $_t_pa['attr_value'] . ' != "" ';
            }

            if ($b_sel_invisible === true)
                {
                    $_filter_condition .=   ' OR '.
                                            ' ( '.
                                                $_t_pta['type_attr_visible'] .' != 1 '.
                                            ' ) ';
                }
            $_filter_condition .= ' ) ';

            $_filter_condition .= ' AND ( '.
                                    '( ' . $_t_pa['attr_value'] .  ' = "-1" ) OR ' .
                                    '( ' . $_t_pa['attr_value'] .  ' = "" ) OR ' .
                                    '( ' . '"'.$cur_gr.'"' . ' RLIKE concat("^(",'.$_t_pa['attr_value'].',")$") ) )';
            $this->addInnerJoin(
                                    'product_type_attributes pta'.$this->_table_alias_counter,
                                    $_t_pa['a_id'],
                                    DB_EQ,
                                    $_t_pta['a_id'].
                                    ' AND '.
                                    $t_p['pt_id'].' = '.$_t_pta['pt_id'].
                                    ' AND '.
                                    $_filter_condition
                               );
        }
    }

    function _addTextAttributeFilter($i_attr_id, $a_words, $c_condition, $b_sort, $s_sort_direction, $b_sel_undefined, $b_sel_invisible)
    {
        //
        if ($a_words !== null or
            $b_sort === true)
        {
            $this->_table_alias_counter++;
            $t_pa = $this->_table_product_attributes;
            $t_p = $this->_table_products;
            $t_pta = $this->_table_product_type_attributes;

            //                  ,
            $_t_pa = $this->addTableAlias($t_pa, 'pa'.$this->_table_alias_counter, 'product_attributes');
            $this->addInnerJoin(
                                'product_attributes pa'.$this->_table_alias_counter, //
                                $t_p['id'],
                                DB_EQ,
                                $_t_pa['p_id'].
                                ' AND '.
                                $_t_pa['a_id'].' = "'.$this->DBAddSlashes($i_attr_id).'"'   // id
                                );

            // MultiLang
            if (modApiFunc('Catalog', 'isMLAttribute', $i_attr_id))
                $this -> setMultiLangAlias('_ml_attr_' . $i_attr_id, 'product_attributes', $_t_pa['attr_value'], $_t_pa['id'], 'Catalog');

            //                                          -
            if ($b_sort === true)
            {
                $this->SelectOrder(((modApiFunc('Catalog', 'isMLAttribute', $i_attr_id))
                                       ? $this -> getMultiLangAlias('_ml_attr_' . $i_attr_id)
                                       : $_t_pa['attr_value']), ife(isset($s_sort_direction), $s_sort_direction, 'ASC'));
            }

            //
            if ($a_words !== null and is_array($a_words))
            {
                $_t_pta = $this->addTableAlias($t_pta, 'pta'.$this->_table_alias_counter, 'product_type_attributes');

                //                    INNER JOIN
                //                ,                         ,
                $_filter_condition = ' ( ( '.
                                     $_t_pta['type_attr_visible'] .' = 1 '. //
                                     ' AND '.
                                     ' ( ';


                for($i=0; $i<count($a_words); $i++)
                {
                    $_filter_condition .= ((modApiFunc('Catalog', 'isMLAttribute', $i_attr_id))
                                               ? $this -> getMultiLangAlias('_ml_attr_' . $i_attr_id)
                                               : $_t_pa['attr_value']) .
                                          ' ' . DB_LIKE .
                                          ' "%' . $this -> DBAddSlashes($a_words[$i]) . '%" ';

                    if ($i < count($a_words)-1)
                    {
                        if ($c_condition == SEARCH_ALL_VALUES)
                        {
                            $_filter_condition .= ' AND ';
                        }
                        else
                        {
                            $_filter_condition .= ' OR ';
                        }
                    }
                }

                //                      :
                if ($b_sel_undefined === true)
                {
                    //              OR -  . .                                   ,
                    //
                    $_filter_condition .= ' OR ' . ((modApiFunc('Catalog', 'isMLAttribute', $i_attr_id))
                                                   ? $this -> getMultiLangAlias('_ml_attr_' . $i_attr_id)
                                                   : $_t_pa['attr_value']) . ' = "" ';
                }
                else
                {
                    //                                                                ,
                    //                     AND -
                    //
                    $_filter_condition .= ' AND ' . ((modApiFunc('Catalog', 'isMLAttribute', $i_attr_id))
                                                    ? $this -> getMultiLangAlias('_ml_attr_' . $i_attr_id)
                                                    : $_t_pa['attr_value']) . ' != "" ';
                }

                //
                $_filter_condition .= ' ) ) ';

                //                      :
                if ($b_sel_invisible === true)
                {
                    //              OR -           ,                                    ,
                    //
                    $_filter_condition .=   ' OR '.
                                            ' ( '.
                                                $_t_pta['type_attr_visible'] .' != 1 '. //
                                            ' ) ';
                }
                $_filter_condition .= ' ) ';

                $this->addInnerJoin(
                                    'product_type_attributes pta'.$this->_table_alias_counter, //
                                    $_t_pa['a_id'],
                                    DB_EQ,
                                    $_t_pta['a_id'].
                                    ' AND '.
                                    $t_p['pt_id'].' = '.$_t_pta['pt_id'].
                                    ' AND '.
                                    $_filter_condition
                                    );
            }
        }
    }

    function _addInArrayAttributeFilter($i_attr_id, $a_values, $b_sel_invisible)
    {
        //
        if ($a_values !== null)
        {
            $this->_table_alias_counter++;
            $t_pa = $this->_table_product_attributes;
            $t_p = $this->_table_products;
            $t_pta = $this->_table_product_type_attributes;

            //                  ,
            $_t_pa = $this->addTableAlias($t_pa, 'pa'.$this->_table_alias_counter, 'product_attributes');
            $this->addInnerJoin(
                                'product_attributes pa'.$this->_table_alias_counter, //
                                $t_p['id'],
                                DB_EQ,
                                $_t_pa['p_id'].
                                ' AND '.
                                $_t_pa['a_id'].' = "'.$this->DBAddSlashes($i_attr_id).'"'   // id
                                );

                $_t_pta = $this->addTableAlias($t_pta, 'pta'.$this->_table_alias_counter, 'product_type_attributes');

                //                    INNER JOIN
                //                ,                         ,
                $_filter_condition = ' ( ( '.
                                     $_t_pta['type_attr_visible'] .' = 1 '. //
                                     ' AND '.
                                     ' ( ';

                $_filter_condition .= $_t_pa['attr_value'] . ' IN ('.implode(',',$a_values).') ';

                //
                $_filter_condition .= ' ) ) ';

                //                      :
                if ($b_sel_invisible === true)
                {
                    //              OR -           ,                                    ,
                    //
                    $_filter_condition .=   ' OR '.
                                            ' ( '.
                                                $_t_pta['type_attr_visible'] .' != 1 '. //
                                            ' ) ';
                }
                $_filter_condition .= ' ) ';

                $this->addInnerJoin(
                                    'product_type_attributes pta'.$this->_table_alias_counter, //
                                    $_t_pa['a_id'],
                                    DB_EQ,
                                    $_t_pta['a_id'].
                                    ' AND '.
                                    $t_p['pt_id'].' = '.$_t_pta['pt_id'].
                                    ' AND '.
                                    $_filter_condition
                                    );
        }
    }


    var $_table_alias_counter = 0;
    var $_table_products = '';
    var $_table_product_attributes = '';
    var $_table_product_type_attributes = '';

}

class SELECT_PRODUCT_ATTRIBUTES_INFO extends DB_Select
{
    function initQuery($params)
    {
        $product_type_id = $params['product_type_id'];
        $product_id = $params['product_id'];

        $tables = Catalog::getTables();
        $t_ptype_attributes   = $tables['product_type_attributes']['columns'];
        $t_attributes         = $tables['attributes']['columns'];
        $t_input_types        = $tables['input_types']['columns'];
        $t_product_attributes = $tables['product_attributes']['columns'];
        $t_product_images     = $tables['product_images']['columns'];

        $this->addSelectField( $t_product_attributes['id'],               'pa_id');

        $this->addLeftJoin('product_attributes', $t_ptype_attributes['a_id'], DB_EQ, $t_product_attributes['a_id'].' AND '.$t_product_attributes['p_id'].' = '.$product_id.' ');

        $this->setMultiLangAlias('_ml_prod_attr', 'product_attributes', $t_product_attributes['attr_value'], $t_product_attributes['id'], 'Catalog');
        $this->addSelectField( $this->getMultiLangAlias('_ml_prod_attr'), 'pa_value');

        $this->addSelectField( $t_attributes['id'],                       'a_id');
        $this->addSelectField( $t_attributes['view_tag'],                 'a_view_tag');
        $this->addSelectField( $t_attributes['allow_html'],               'a_allow_html');
        $this->addSelectField( $t_attributes['it_id'],                    'a_input_type_id');
        $this->addSelectField( $t_input_types['name'],                    'a_input_type_name');
        $this->addSelectField( $t_attributes['type'],                     'a_type');
        $this->addSelectField( $t_attributes['ut'],                       'a_unit_type');
        $this->addSelectField( $t_attributes['name'],                     'a_name');
        $this->addSelectField( $t_attributes['descr'],                    'a_descr');
        $this->addSelectField( $t_attributes['min'],                      'a_min_value');
        $this->addSelectField( $t_attributes['max'],                      'a_max_value');
        $this->addSelectField( $t_attributes['size'],                     'a_html_size');
        $this->addSelectField( $t_ptype_attributes['type_attr_visible'],  'a_visibility');
        $this->addSelectField( $t_ptype_attributes['type_attr_required'], 'a_required');
        $this->addSelectField( $t_ptype_attributes['type_attr_def_val'],  'a_default_value');
        $this->addSelectField( $t_product_images['id'],                   'image_id');
        $this->addSelectField( $t_product_images['name'],                 'image_name');
        $this->addSelectField( $t_product_images['width'],                'image_width');
        $this->addSelectField( $t_product_images['height'],               'image_height');

        $this->addLeftJoin('product_images', $t_product_attributes['id'], DB_EQ, $t_product_images['pa_id']);

        $this->WhereField($t_attributes['id'], DB_EQ, $t_ptype_attributes['a_id']);
        $this->WhereAnd();
        $this->WhereField($t_attributes['it_id'], DB_EQ, $t_input_types['id']);
        $this->WhereAnd();
        $this->WhereValue($t_ptype_attributes['pt_id'], DB_EQ, $product_type_id);
    }
}

class SELECT_PRODUCT_TYPE_LIST extends DB_Select
{
    function initQuery($params)
    {
        $tables = Catalog::getTables();
        $pt = $tables['product_types']['columns'];

        $this->addSelectField($pt['id'], 'id');
        $this->setMultiLangAlias('_ml_prod_type_name', 'product_types', $pt['name'], $pt['id'], 'Catalog');
        $this->addSelectField($this->getMultiLangAlias('_ml_prod_type_name'), 'name');
        $this->setMultiLangAlias('_ml_prod_type_descr', 'product_types', $pt['descr'], $pt['id'], 'Catalog');
        $this->addSelectField($this->getMultiLangAlias('_ml_prod_type_descr'), 'description');
    }
}

class SELECT_PRODUCT_TYPE_BY_ID extends DB_Select
{
    function initQuery($params)
    {
        $product_type_id = $params['product_type_id'];

        $tables = Catalog::getTables();
        $pt = $tables['product_types']['columns'];

        $this->addSelectField($pt['id'], 'id');
        $this->setMultiLangAlias('_ml_prod_type_name', 'product_types', $pt['name'], $pt['id'], 'Catalog');
        $this->addSelectField($this->getMultiLangAlias('_ml_prod_type_name'), 'name');
        $this->setMultiLangAlias('_ml_prod_type_descr', 'product_types', $pt['descr'], $pt['id'], 'Catalog');
        $this->addSelectField($this->getMultiLangAlias('_ml_prod_type_descr'), 'description');
        $this->WhereValue($pt['id'], DB_EQ, $product_type_id);
    }
}

class SELECT_SEARCH_INDEX_WORDS_IN_SEARCH_RESULT extends DB_Select
{
    function initQuery($params)
    {
        $search_id  = $params['search_id'];

        $tables = Catalog::getTables();
        $t_products_search = $tables['products_search']['columns'];

        //$this->setMultiLangAlias('_ml_search', 'products_search', $t_products_search['words'], $t_products_search['id'], 'Catalog');
        //$this->addSelectField($this->getMultiLangAlias('_ml_search'), 'words');
        $this->addSelectField($t_products_search['words'], 'words');
        $this->WhereValue($t_products_search['id'], DB_EQ, $search_id );
    }
}

class UPDATE_PURGE_PRODUCT_MANUFACTURERS extends DB_Update
{
    function UPDATE_PURGE_PRODUCT_MANUFACTURERS()
    {
        parent::DB_Update('product_attributes');
    }

    function initQuery($params)
    {
        $tables = Catalog::getTables();
        $pa = $tables['product_attributes']['columns'];

        $this->addUpdateValue($pa['attr_value'], MANUFACTURER_NOT_DEFINED);
        $this->WhereValue($pa["a_id"], DB_EQ, modApiFunc('Catalog', 'getManufacturerAttrId'));
        $this->WhereAnd();
        $this->WhereField($pa["attr_value"], DB_IN, ' ('.implode(',',$params).') ');
    }
}

class SELECT_ALL_INPUT_TYPE_VALUES extends DB_Select
{
    function initQuery($params)
    {
        $tables = Catalog::getTables();
        $p = $tables['input_type_values']['columns'];

        $this->addSelectField($p['id'], 'id');
        $this->addSelectField($p['it_id'], 'it_id');
        $this->setMultiLangAlias('_ml_it_value', 'input_type_values', $p['value'], $p['id'], 'Catalog');
        $this->addSelectField($this->getMultiLangAlias('_ml_it_value'), 'value');
    }
}

//class SELECT_ extends DB_Select
//{
//    function initQuery($params)
//    {
//        $tables = Catalog::getTables();
//    }
//}

class SELECT_PARENT_CATEGORY_IDS_FOR_CATEGORIES extends DB_Select
{
    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $a = $tables['categories']['columns'];

        $this -> addSelectTable('categories', 'a');
        $b = $this -> addTableAlias($a, 'b', 'categories');
        $this -> addSelectField($b['id'], 'parent_id');
        $this -> Where($a['level'], DB_EQ, $b['level'] . ' + 1');
        $this -> WhereAND();
        $this -> WhereField($a['left'], DB_GT, $b['left']);
        $this -> WhereAND();
        $this -> WhereField($a['right'], DB_LT, $b['right']);

        if (isset($params['cat_ids']) && is_array($params['cat_ids']))
        {
            $this -> WhereAND();
            $this -> Where($a['id'], DB_IN, '(' . join(',', $params['cat_ids']) . ')');
        }

        $this -> SelectGroup($b['id']);
    }
}

class SELECT_OTHER_SUBCATEGORIES_FOR_A_CATEGORY extends DB_Select
{
    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $a = $tables['categories']['columns'];

        $this -> addSelectTable('categories', 'a');
        $b = $this -> addTableAlias($a, 'b', 'categories');
        $this -> addSelectField($a['id'], 'category_id');
        $this -> Where($a['level'], DB_EQ, $b['level'] . ' + 1');
        $this -> WhereAND();
        $this -> WhereField($a['left'], DB_GT, $b['left']);
        $this -> WhereAND();
        $this -> WhereField($a['right'], DB_LT, $b['right']);
        $this -> WhereAND();
        $this -> WhereValue($b['id'], DB_EQ, @$params['id']);

        if (isset($params['cat_ids']) && is_array($params['cat_ids']))
        {
            $this -> WhereAND();
            $this -> Where($a['id'], DB_NIN, '(' . join(',', $params['cat_ids']) . ')');
        }
    }
}

class SELECT_CATEGORY_FIELD extends DB_Select
{
    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $cd = $tables['categories_descr']['columns'];

        $field_name = $params['field_name'];
        $cat_id = $params['cat_id'];

        $this->addSelectTable('categories_descr');
        $this->setMultiLangAlias('_ml_category_name', 'categories_descr', $cd[$field_name], $cd['id'], 'Catalog');
        $this->addSelectField($this->getMultiLangAlias('_ml_category_name'), 'name');
        $this->WhereValue($cd['id'], DB_EQ, $cat_id);
    }
}

class SELECT_CATEGORY_IMAGES extends DB_Select
{
    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $cd = $tables['categories_descr']['columns'];

        $this->addSelectTable('categories_descr');
        $this->addSelectField($cd['image_file'], 'image_large');
        $this->addSelectField($cd['image_small_file'], 'image_small');
        $this->WhereField($cd['id'], DB_IN,"('" . implode("','", $params['ids']) . "')");
    }
}

class UPDATE_SET_SORT_CATEGORY_FIELD extends DB_Update
{
    function UPDATE_SET_SORT_CATEGORY_FIELD()
    {
        parent :: DB_Update('categories');
    }

    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $a = $tables['categories']['columns'];

        $this -> addUpdateValue($a['sort_order'], $params['sort_order']);
        $this -> WhereValue($a['left'], DB_GTE, $params['left']);
        $this -> WhereAND();
        $this -> WhereValue($a['left'], DB_LTE, $params['right']);
    }
}

class UPDATE_CLEAR_SORT_CATEGORY_FIELD extends DB_Update
{
    function UPDATE_CLEAR_SORT_CATEGORY_FIELD()
    {
        parent :: DB_Update('categories');
    }

    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $a = $tables['categories']['columns'];

        $this -> addUpdateValue($a['sort_order'], 0);
    }
}

class UPDATE_SORT_CATEGORIES extends DB_Update
{
    function UPDATE_SORT_CATEGORIES()
    {
        parent :: DB_Update('categories');
    }

    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $a = $tables['categories']['columns'];

        $this -> addUpdateExpression($a['left'], $a['left'] . ' + ' . $a['sort_order']);
        $this -> addUpdateExpression($a['right'], $a['right'] . ' + ' . $a['sort_order']);
        $this -> addUpdateValue($a['sort_order'], 0);

        $this -> WhereValue($a['sort_order'], DB_NEQ, 0);
    }
}

class UPDATE_CATEGORY extends DB_Update
{
    function UPDATE_CATEGORY()
    {
        parent :: DB_Update('categories_descr');
    }

    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $cd = $tables['categories_descr']['columns'];

        $image_files_array = $params['image_files_array'];

        $this->addMultiLangUpdateValue($cd['name'], $params["cat_name"], $cd['id'], @$params["cat_id"], 'Catalog');
        $this->addMultiLangUpdateValue($cd['descr'], $params["description"], $cd['id'], @$params["cat_id"], 'Catalog');
        $this->addUpdateValue($cd['image_file'],       $image_files_array['image_file']);
        $this->addUpdateValue($cd['image_small_file'], $image_files_array['image_small_file']);
        $this->addMultiLangUpdateValue($cd['image_descr'], $params["image_description"], $cd['id'], @$params["cat_id"], 'Catalog');
        $this->addMultiLangUpdateValue($cd['page_title'], $params["page_title"], $cd['id'], @$params["cat_id"], 'Catalog');
        $this->addMultiLangUpdateValue($cd['meta_keywords'], $params["meta_keywords"], $cd['id'], @$params["cat_id"], 'Catalog');
        $this->addMultiLangUpdateValue($cd['meta_descr'], $params["meta_description"], $cd['id'], @$params["cat_id"], 'Catalog');
        $this->addUpdateValue($cd['show_prod_recurs'], $params["show_products_recursively"]);
        $this->addMultiLangUpdateValue($cd['seo_url_prefix'], $params["seo_url"], $cd['id'], @$params["cat_id"], 'Catalog');

        $this->WhereValue($cd['id'], DB_EQ, $params["cat_id"]);
    }
}

class UPDATE_CATEGORY_STATUS extends DB_Update
{
    function UPDATE_CATEGORY_STATUS()
    {
        parent :: DB_Update('categories');
    }

    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $c = $tables['categories']['columns'];

        $this->addUpdateValue($c['status'],             $params["cat_status"]);
        $this->WhereValue($c['id'], DB_EQ, $params["cat_id"]);
    }
}

class UPDATE_EXISTING_CATEGORY_TREE extends DB_Update
{
    function UPDATE_EXISTING_CATEGORY_TREE()
    {
        parent :: DB_Update('categories');
    }

    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $c = $tables['categories']['columns'];

        $this->addUpdateValue($c['left'], $params['left'] );
        $this->addUpdateValue($c['right'], $params['right'] );
        $this->addUpdateValue($c['level'], $params['level'] );

        $this->WhereValue($c['id'], DB_EQ, $params['id']);
    }
}

class UPDATE_CATEGORY_DESCR extends DB_Update
{
    function UPDATE_CATEGORY_DESCR()
    {
        parent :: DB_Update('categories_descr');
    }

    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $cd = $tables['categories_descr']['columns'];

        $this->addMultiLangUpdateValue($cd['name'], $params['name'], $cd['id'], @$params['id'], 'Catalog');

        $this->WhereValue($cd['id'], DB_EQ, $params['id']);
    }
}

// -----------------------------------
// Additional Select queries
// -----------------------------------

class SELECT_CATEGORY_PRODUCTS extends DB_Select
{
    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $p2c = $tables['products_to_categories']['columns'];

        $this -> addSelectField($p2c['product_id'], 'id');
        $this -> WhereValue($p2c['category_id'], DB_EQ, $params['cat_id']);
    }
}

class SELECT_CATEGORIES_PRODUCTS extends DB_Select
{
    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $p2c = $tables['products_to_categories']['columns'];

        $this -> addSelectField($p2c['product_id'], 'id');
        $this -> Where($p2c['category_id'], DB_IN, "('".implode("', '", $params['cats_ids'])."')");
    }
}

class SELECT_LINKED_OTHER_CATEGORIES_PRODUCTS extends DB_Select
{
    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $p2c = $tables['products_to_categories']['columns'];

        $this -> addSelectField($p2c['product_id'], 'id');
        $this -> Where($p2c['product_id'], DB_IN, "('".implode("', '", $params['pids'])."')");
        $this -> WhereAND();
        $this -> Where($p2c['category_id'], DB_NIN, "('".implode("', '", $params['cats_ids'])."')");
    }
}

class SELECT_SORTED_CATEGORY_PRODUCTS extends DB_Select
{
    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $p = $tables['products']['columns'];
        $p2c = $tables['products_to_categories']['columns'];

        $this->addSelectTable('products_to_categories');
        $this->addSelectField($p['id'], 'p_id');

        $this->setMultiLangAlias('_ml_prod_name', 'products', $p['name'], $p['id'], 'Catalog');
        $this->addSelectField($this->getMultiLangAlias('_ml_prod_name'), 'p_name');

        $this->WhereValue($p2c['category_id'], DB_EQ, $params['cat_id']);
        $this->WhereAND();
        $this->Where($p['id'], DB_EQ, $p2c['product_id']);
        $this->SelectOrder($p2c['sort_order']);
    }
}

class SELECT_PRODUCT_SEARCH_RESULTS extends DB_Select
{
    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $psr = $tables['products_search_result']['columns'];

        $this -> addSelectField($psr['p_id'], 'product_id');
        $this -> WhereValue($psr['id'], DB_EQ, $params['search_id']);
        $this -> SelectOrder($psr['relevance']);
        if (isset($params['paginator']) && is_array($params['paginator']))
        {
            list($offset, $count) = $params['paginator'];
            $this -> SelectLimit($offset, $count);
        }
    }
}

class SELECT_PRODUCT_SEARCH_PATTERN extends DB_Select
{
    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $psr = $tables['products_search']['columns'];

        $this->addSelectField($psr['pattern'], 'pattern');
        $this->WhereValue($psr['id'], DB_EQ, $params['search_id']);
    }
}

class SELECT_OLD_PRODUCT_SEARCH_RECORDS extends DB_Select
{
    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $ps = $tables['products_search']['columns'];

        $this -> addSelectField($ps['id'], 'search_id');
        $this -> WhereValue($ps['time'], DB_LTE, date('Y-m-d H:i:s', $params['time_stamp']));
    }
}

class SELECT_PRODUCT_TYPES_BY_PRODUCT_IDS extends DB_Select
{
    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $p = $tables['products']['columns'];

        $this -> addSelectField($p['pt_id'], 'pt_id');

        if (isset($params['pid']) && is_array($params['pid']))
        {
            $this -> Where($p['id'], DB_IN, "('" . implode("','", $params['pid']) . "')");
            $this -> SelectGroup('pt_id');
        }
        else
        {
            $this -> WhereValue($p['id'], DB_EQ, @$params['pid']);
        }
    }
}

class SELECT_PRODUCT_IMAGES_BY_PRODUCT_IDS extends DB_Select
{
    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $pa = $tables['product_attributes']['columns'];
        $pi = $tables['product_images']['columns'];

        $this -> addSelectField($pi['id'], 'id');
        $this -> addSelectField($pi['name'], 'name');
        $this -> addLeftJoin('product_attributes', $pa['id'], DB_EQ, $pi['pa_id']);
        $this -> WhereField($pa['p_id'], DB_IN, "('" . implode("','", $params['pids']) . "')");
        $this -> WhereAnd();
        $this -> addWhereOpenSection();
        $this -> WhereField($pa['a_id'], DB_EQ, "9");
        $this -> WhereOr();
        $this -> WhereField($pa['a_id'], DB_EQ, "10");
        $this -> addWhereCloseSection();
    }
}

class SELECT_CATEGORY_LIST_BY_PRODUCT_IDS extends DB_Select
{
    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $ptc = $tables['products_to_categories']['columns'];

        $this->addSelectTable('products_to_categories');
        $this->addSelectField($ptc['category_id'], 'c_id');
        $this->WhereField($ptc['product_id'], DB_IN, "('" . implode("','", $params['pids']) . "')");
    }
}

class SELECT_CATEGORY_IMAGES_BY_CATEGORY_IDS extends DB_Select
{
    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $cd = $tables['categories_descr']['columns'];

        $this -> addSelectField($cd['image_file'], 'image_large');
        $this -> addSelectField($cd['image_small_file'], 'image_small');
        $this -> WhereField($cd['id'], DB_IN, "('" . implode("','", $params['cids']) . "')");
    }
}

class SELECT_PRODUCT_NAME_BY_ID extends DB_Select
{
    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $p = $tables['products']['columns'];

        $this -> addSelectField($p['id'], 'id');
        $this->setMultiLangAlias('_ml_prod_name', 'products', $p['name'], $p['id'], 'Catalog');
        $this->addSelectField($this->getMultiLangAlias('_ml_prod_name'), 'name');
        if (is_array($params['pid']))
        {
            $this -> WhereField($p['id'], DB_IN, "('" . implode("','", $params['pid']) . "')");
            if (isset($params['paginator']) && is_array($params['paginator']))
            {
                list($offset, $count) = $params['paginator'];
                $this -> SelectLimit($offset, $count);
            }
        }
        else
        {
            $this -> WhereValue($p['id'], DB_EQ, $params['pid']);
        }
    }
}

class SELECT_GENERAL_PRODUCT_ATTRIBUTES extends DB_Select
{
    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $p = $tables['products']['columns'];
        $pt  = $tables['product_types']['columns'];

        $this -> addSelectField($p['id'], 'id');
        $this->setMultiLangAlias('_ml_prod_name', 'products', $p['name'], $p['id'], 'Catalog');
        $this->addSelectField($this->getMultiLangAlias('_ml_prod_name'), 'name');
        $this -> addSelectField($p['date_updated'], 'date_updated');
        $this -> addSelectField($p['date_added'], 'date_added');
        $this -> addSelectField($pt['id'], 'type_id');
        $this->setMultiLangAlias('_ml_prod_type_name', 'product_types', $pt['name'], $pt['id'], 'Catalog');
        $this->addSelectField($this->getMultiLangAlias('_ml_prod_type_name'), 'type_name');
        $this -> Where($p['id'], DB_EQ, $params['prod_id']);
        $this -> WhereAND();
        $this -> WhereField($pt['id'], DB_EQ, $p['pt_id']);
    }
}

class SELECT_REAL_PRODUCT_ATTRIBUTES extends DB_Select
{
    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $pa  = $tables['product_attributes']['columns'];
        $pi  = $tables['product_images']['columns'];
        $a   = $tables['attributes']['columns'];

        $this -> addSelectField($pa['id'], 'id');
        $this->setMultiLangAlias('_ml_prod_attr_value', 'product_attributes', $pa['attr_value'], $pa['id'], 'Catalog');
        $this->addSelectField($this->getMultiLangAlias('_ml_prod_attr_value'), 'value');
        $this -> addSelectField($a['view_tag'], 'view_tag');
        $this -> addSelectField($a['name'], 'name');
        $this -> addSelectField($a['type'], 'attr_type');
        $this -> addSelectField($pi['id'], 'image_id');
        $this -> addSelectField($pi['name'], 'image_name');
        $this -> addSelectField($pi['width'], 'image_width');
        $this -> addSelectField($pi['height'], 'image_height');
        $this -> addLeftJoin('product_images', $pa['id'], DB_EQ, $pi['pa_id']);
        $this -> WhereValue($pa['p_id'], DB_EQ, $params['prod_id']);
        $this -> WhereAND();
        $this -> WhereField($a['id'], DB_EQ, $pa['a_id']);
    }
}

class SELECT_BASIC_PRODUCT_ATTRIBUTE_DATA extends DB_Select
{
    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $pa = $tables['product_attributes']['columns'];

        $this -> addSelectField($pa['id'], 'id');
        if (modAPIFunc('Catalog','isMLAttribute',$params['aid']))
        {
            $this->setMultiLangAlias('_ml_prod_attr_value', 'product_attributes', $pa['attr_value'], $pa['id'], 'Catalog');
            $this->addSelectField($this->getMultiLangAlias('_ml_prod_attr_value'), 'value');
        }
        else
            $this->addSelectField($pa['attr_value'], 'attr_value');

        $this -> WhereValue($pa['a_id'], DB_EQ, $params['aid']);
        $this -> WhereAND();
        $this -> WhereValue($pa['p_id'], DB_EQ, $params['pid']);
    }
}

class SELECT_PRODUCT_IMAGE_NAME_BY_ATTR_ID extends DB_Select
{
    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $pi = $tables['product_images']['columns'];

        $this -> addSelectField($pi['name'], 'name');
        $this -> WhereField($pi['pa_id'], DB_EQ, $params['pa_id']);
    }
}

class SELECT_ATTRIBUTE_INFO_BY_TAG extends DB_Select
{
    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $a = $tables['attributes']['columns'];

        $this -> addSelectTable('attributes');
        $this -> addSelectField('*');
        $this -> WhereValue($a['view_tag'], DB_EQ, $params['tag']);
    }
}

class SELECT_INPUT_TYPE_NAME_BY_ID extends DB_Select
{
    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $it = $tables['input_types']['columns'];

        $this -> addSelectField($it['name'], 'input_type_name');
        $this -> WhereValue($it['id'], DB_EQ, $params['id']);
    }
}

class SELECT_CUSTOM_ATTRIBUTE_ID_BY_TAG extends DB_Select
{
    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $a = $tables['attributes']['columns'];

        $this -> addSelectField($a['id'], 'id');
        $this -> WhereValue($a['type'], DB_EQ, 'custom');
        $this -> WhereAND();
        $this -> WhereValue($a['view_tag'], DB_EQ, $params['tag']);
    }
}

class SELECT_CUSTOM_ATTRIBUTES_BY_PRODUCT_TYPE extends DB_Select
{
    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $a = $tables['attributes']['columns'];
        $pta = $tables['product_type_attributes']['columns'];

        $this -> addSelectField($a['id'], 'a_id');
        $this -> addSelectField($pta['id'], 'pta_id');
        $this -> WhereValue($a['type'], DB_EQ, 'custom');
        $this -> WhereAND();
        $this -> WhereField($pta['a_id'], DB_EQ, $a['id']);
        $this -> WhereAND();
        $this -> WhereValue($pta['pt_id'], DB_EQ, $params['pt_id']);
    }
}

class SELECT_PRODUCT_TYPE_ATTRIBUTES extends DB_Select
{
    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $pta = $tables['product_type_attributes']['columns'];
        $a = $tables['attributes']['columns'];
        $ag = $tables['attribute_groups']['columns'];
        $it = $tables['input_types']['columns'];

        $this -> addSelectField($pta['id'], 'pta_id');
        $this -> addSelectField($pta['type_attr_required'], 'required');
        $this -> addSelectField($pta['type_attr_visible'], 'visible');

        $this -> setMultiLangAlias('_ml_type_attr_def_value', 'product_type_attributes', $pta['type_attr_def_val'], $pta['id'], 'Catalog');
        $this -> addSelectValue($this -> getMultiLangAlias('_ml_type_attr_def_value'), 'default_value');
        // attribute info.
        $this -> addSelectField($a['id'], 'id');
        // new field
        $this -> addSelectField($a['ut'], 'ut');
        $this -> addSelectField($a['view_tag'], 'view_tag');
        $this -> addSelectField($a['name'], 'name');
        $this -> addSelectField($a['descr'], 'descr');
        $this -> addSelectField($a['size'], 'size');
        $this -> addSelectField($a['min'], 'min');
        $this -> addSelectField($a['max'], 'max');
        $this -> addSelectField($a['sort_order'], 'sort');
        $this -> addSelectField($a['type'], 'type');
        $this -> addSelectField($a['allow_html'], 'allow_html');
        $this -> addSelectField($a['multilang'], 'multilang');
        // info about the group containing this attribute.
        $this -> addSelectField($ag['id'], 'group_id');
        $this -> addSelectField($ag['name'], 'group_name');
        $this -> addSelectField($ag['sort_order'], 'group_sort');
        // info about control type for inputting the attribute.
        $this -> addSelectField($it['id'], 'input_type_id');
        $this -> addSelectField($it['name'], 'input_type_name');

        $this -> addLeftJoin('input_types', $it['id'], DB_EQ, $a['it_id']);

        $this -> WhereValue($pta['pt_id'], DB_EQ, $params['pt_id']);
        $this -> WhereAnd();
        $this -> WhereField($a['id'], DB_EQ, $pta['a_id']);
        $this -> WhereAnd();
        $this -> WhereField($ag['id'], DB_EQ, $a['ag_id']);
    }
}

class SELECT_CATALOG_TEMP_BY_FORM_ID extends DB_Select
{
    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $ct = $tables['catalog_temp']['columns'];

        $this -> addSelectField($ct['value'], 'value');
        $this -> WhereValue($ct['form_id'], DB_EQ, $params['form_id']);
    }
}

class SELECT_PRODUCT_COUNT_BY_PRODUCT_TYPE extends DB_Select
{
    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $p = $tables['products']['columns'];

        $this -> addSelectField($this -> fCount($p['id']), 'count_p_id');
        $this -> WhereValue($p['pt_id'], DB_EQ, $params['pt_id']);
    }
}

class SELECT_PRODUCT_COUNT_BY_CATEGORY extends DB_Select
{
    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $ptc = $tables['products_to_categories']['columns'];

        $this -> addSelectField($this -> fCount($ptc['product_id']), 'count_p_id');
        $this -> addSelectField($ptc['category_id'], 'c_id');
        $this -> SelectGroup($ptc['category_id']);
    }
}

class SELECT_PRODUCT_TYPE_ATTRIBUTE_VISIBLE_VALUE extends DB_Select
{
    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $p = $tables['products']['columns'];
        $pta = $tables['product_type_attributes']['columns'];

        $this -> addSelectField($pta['type_attr_visible'], 'visibility');
        $this -> WhereField($p['pt_id'], DB_EQ, $pta['pt_id']);
        $this -> WhereAnd();
        $this -> WhereValue($pta['a_id'], DB_EQ, $params['aid']);
        $this -> WhereAnd();
        $this -> WhereValue($p['id'], DB_EQ, $params['pid']);
    }
}

class SELECT_COUNT_OF_CATEGORIES extends DB_Select
{
    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $cat = $tables['categories']['columns'];

		$this -> addSelectField($this->fCount($cat['id']), 'category_count');
    }
}

class SELECT_COUNT_OF_PRODUCT_LINKS extends DB_Select
{
    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $ptc = $tables['products_to_categories']['columns'];

        $this -> addSelectField($this -> fCount($ptc['record_id']), 'count_rids');
        $this -> WhereValue($ptc['product_id'], DB_EQ, $params['pid']);
    }
}

class SELECT_COUNT_OF_LINKED_PRODUCTS extends DB_Select
{
    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $ptc = $tables['products_to_categories']['columns'];

        $this -> addSelectField($ptc['product_id'], 'product_id');
        $this -> SelectGroup($ptc['product_id']);
    }
}

class SELECT_COUNT_OF_UNIQUE_PRODUCTS extends DB_Select
{
    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $p = $tables['products']['columns'];

        $this -> addSelectField($this->fCount($p['id']), 'product_count');
        $this -> WhereValue($p['pt_id'], DB_NEQ, '-1');
    }
}


class SELECT_MULTILANG_ATTRIBUTES extends DB_Select
{
    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $a = $tables['attributes']['columns'];

        $this -> addSelectField($a['id'], 'id');
        $this -> addSelectField($a['view_tag'], 'view_tag');
        $this -> WhereValue($a['multilang'], DB_EQ, 'Y');
    }
}

class SELECT_PRODUCT_ATTRIBUTES_BY_ATTRIBUTE_ID extends DB_Select
{
    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $pa = $tables['product_attributes']['columns'];

        $this -> addSelectField($pa['id'], 'id');
        $this -> addSelectField($pa['p_id'], 'pid');
        $this -> addSelectField($pa['attr_value'], 'attr_value');
        $this -> WhereValue($pa['a_id'], DB_EQ, $params['attribute_id']);
        if (isset($params['product_id']))
        {
            $this -> WhereAND();
            $this -> WhereValue($pa['p_id'], DB_EQ, $params['product_id']);
        }
    }
}

// -----------------------------------
// Additional Update queries
// -----------------------------------

class UPDATE_PRODUCT_SEARCH_TIME extends DB_Update
{
    function UPDATE_PRODUCT_SEARCH_TIME()
    {
        parent :: DB_Update('products_search');
    }

    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $ps = $tables['products_search']['columns'];

        $this -> addUpdateValue($ps['time'], date('Y-m-d H:i:s', time()));
        $this -> WhereValue($ps['id'], DB_EQ, $params['search_id']);
    }
}

class UPDATE_PRODUCT_SORT_ORDER extends DB_Update
{
    function UPDATE_PRODUCT_SORT_ORDER()
    {
        parent :: DB_Update('products_to_categories');
    }

    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $ptc = $tables['products_to_categories']['columns'];

        $this -> addUpdateValue($ptc['sort_order'], $params['p_sort']);
        $this -> WhereValue($ptc['product_id'], DB_EQ, $params['p_id']);
        $this -> WhereAND();
        $this -> WhereValue($ptc['category_id'], DB_EQ, $params['c_id']);
    }
}

class UPDATE_CATEGORY_RANGES_BY_PARENT_CATEGORY_RANGE_AND_DELTA extends DB_Update
{
    function UPDATE_CATEGORY_RANGES_BY_PARENT_CATEGORY_RANGE_AND_DELTA()
    {
        parent :: DB_Update('categories');
    }

    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $c = $tables['categories']['columns'];

        $this -> addUpdateExpression($c['left'], $this -> fIf($c['left'] . DB_GT . $params['left'], $c['left'] . "- " . $params['delta'], $c['left']));
        $this -> addUpdateExpression($c['right'], $this -> fIf($c['right'] . DB_GT . $params['left'], $c['right'] . "- " . $params['delta'], $c['right']));
        $this -> WhereValue($c['right'], DB_GT, $params['right']);
    }
}

class UPDATE_CATEGORIES_STRUCTURE extends DB_Update
{
    function UPDATE_CATEGORIES_STRUCTURE()
    {
        parent:: DB_Update('categories');
    }

    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $columns = $tables['categories']['columns'];

        $this->addUpdateExpression($columns["left"], $this->fIf($columns["left"] . DB_GT . $params["right"], $columns["left"] . "+ 2" , $columns["left"]));
        $this->addUpdateExpression($columns["right"], $this->fIf($columns["right"] . DB_GTE . $params["right"], $columns["right"] . "+ 2" , $columns["right"]));
        $this->WhereValue($columns["right"], DB_GTE, $params["right"]);
    }
}

class UPDATE_GENERAL_PRODUCT_INFO extends DB_Update
{
    function UPDATE_GENERAL_PRODUCT_INFO()
    {
        parent :: DB_Update('products');
    }

    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $p = $tables['products']['columns'];

        if (isset($params['Name']))
            $this->addMultiLangUpdateValue($p['name'], $params['Name'], $p['id'], @$params['product_id'], 'Catalog');

        $this->addUpdateValue($p['date_updated'], date('Y-m-d H:i:s', time()));
        $this->WhereValue($p['id'], DB_EQ, $params['product_id']);
    }
}

class UPDATE_PRODUCT_ATTRIBUTE_VALUE extends DB_Update
{
    function UPDATE_PRODUCT_ATTRIBUTE_VALUE()
    {
        parent :: DB_Update('product_attributes');
    }

    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $pa = $tables['product_attributes']['columns'];

	    if (modAPIFunc('Catalog','isMLAttribute',$params['aid']))
            $this->addMultiLangUpdateValue($pa['attr_value'], $params['value'], $pa['id'],'', 'Catalog');
        else
            $this->addUpdateValue($pa['attr_value'], $params['value']);

        $this->WhereValue($pa['p_id'], DB_EQ, $params['pid']);
        $this->WhereAND();
        $this->WhereValue($pa['a_id'], DB_EQ, $params['aid']);
    }
}

class UPDATE_PRODUCT_ATTRIBUTE_VALUE_BY_OLD_VALUE extends DB_Update
{
    function UPDATE_PRODUCT_ATTRIBUTE_VALUE_BY_OLD_VALUE()
    {
        parent :: DB_Update('product_attributes');
    }

    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $pa = $tables['product_attributes']['columns'];

        $this->addMultiLangUpdateValue($pa['attr_value'], $params['value'], $pa['id'],'', 'Catalog');
        //$this -> addUpdateValue($pa['attr_value'], $params['value']);
        $this -> WhereValue($pa['a_id'], DB_EQ, $params['aid']);
        $this -> WhereAnd();
        $this -> WhereValue($pa['attr_value'], DB_EQ, $params['old_value']);
    }
}

class UPDATE_PRODUCT_QUANTITY_ATTRIBUTE extends DB_Update
{
    function UPDATE_PRODUCT_QUANTITY_ATTRIBUTE()
    {
        parent :: DB_Update('product_attributes');
    }

    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $pa = $tables['product_attributes']['columns'];

        // Quantity is not multilangual...
        $this -> addUpdateExpression($pa['attr_value'], $pa['attr_value'] . ($params['mult'] == -1 ? " - " : " + ") . $params['qty']);
        $this->WhereValue($pa['a_id'], DB_EQ, 3);
        $this->WhereAnd();
        $this->WhereValue($pa['p_id'], DB_EQ, $params['p_id']);
    }
}

class UPDATE_PRODUCT_TYPE_RECORD extends DB_Update
{
    function UPDATE_PRODUCT_TYPE_RECORD()
    {
        parent :: DB_Update('product_types');
    }

    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $pt = $tables['product_types']['columns'];

        $this->addMultiLangUpdateValue($pt['name'], $params['name'], $pt['id'],@$params['pt_id'], 'Catalog');
        $this->addMultiLangUpdateValue($pt['descr'], $params['descr'], $pt['id'],@$params['pt_id'], 'Catalog');
        $this->WhereValue($pt['id'], DB_EQ, $params['pt_id']);
    }
}

class UPDATE_ATTRIBUTE_RECORD extends DB_Update
{
    function UPDATE_ATTRIBUTE_RECORD()
    {
        parent :: DB_Update('attributes');
    }

    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $a = $tables['attributes']['columns'];

        $this -> addUpdateValue($a['it_id'], $params['input_type_id']);
        $this -> addUpdateValue($a['ut'], $params['unit_type_value']);
        $this -> addUpdateValue($a['view_tag'], $params['view_tag']);
        $this -> addUpdateValue($a['name'], $params['name']);
        $this -> addUpdateValue($a['descr'], $params['descr']);
        $this -> addUpdateValue($a['type'], $params['type']);
        $this -> addUpdateValue($a['min'], $params['min']);
        $this -> addUpdateValue($a['max'], $params['max']);
        $this -> addUpdateValue($a['size'], $params['size']);
        if (isset($params['multilang']))
            $this -> addUpdateValue($a['multilang'], $params['multilang']);
        $this -> addUpdateValue($a['sort_order'], $params['sort']);
        $this -> WhereValue($a['id'], DB_EQ, $params['id']);
    }
}

class UPDATE_PRODUCT_TYPE_ATTRIBUTE_RECORD extends DB_Update
{
    function UPDATE_PRODUCT_TYPE_ATTRIBUTE_RECORD()
    {
        parent :: DB_Update('product_type_attributes');
    }

    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $pta = $tables['product_type_attributes']['columns'];

        $this->addUpdateValue($pta['type_attr_visible'], $params['visible']);
        if (isset($params['multilang']) && $params['multilang'] == 'Y')
            $this->addMultiLangUpdateValue($pta['type_attr_def_val'], $params['default'], $pta['id'], $params['pta_id'], 'Catalog');
        else
            $this->addUpdateValue($pta['type_attr_def_val'], $params['default']);
        $this->WhereValue($pta['id'], DB_EQ, $params['pta_id']);
    }
}

class UPDATE_CATALOG_TEMP_RECORD extends DB_Update
{
    function UPDATE_CATALOG_TEMP_RECORD()
    {
        parent :: DB_Update('catalog_temp');
    }

    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $ct = $tables['catalog_temp']['columns'];

        $this -> addUpdateValue($ct['value'], $params['value']);
        $this -> WhereValue($ct['form_id'], DB_EQ, $params['form_id']);
    }
}

class UPDATE_PRODUCTS_TO_CATEGORIES_SORT_ORDER extends DB_Update
{
    function UPDATE_PRODUCTS_TO_CATEGORIES_SORT_ORDER()
    {
        parent :: DB_Update('products_to_categories');
    }

    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $ptc = $tables['products_to_categories']['columns'];

        $this -> addUpdateValue($ptc['sort_order'], $params['sort_order']);
        $this -> WhereValue($ptc['record_id'], DB_EQ, $params['record_id']);
    }
}

// -----------------------------------
// Additional Insert queries
// -----------------------------------

class INSERT_PRODUCT_SEARCH_RECORD extends DB_Insert
{
    function INSERT_PRODUCT_SEARCH_RECORD()
    {
        parent::DB_Insert('products_search');
    }

    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $ps = $tables['products_search']['columns'];

        $this -> addInsertValue($params['pattern'], $ps['pattern']);
        $this -> addInsertValue(date('Y-m-d H:i:s', time()), $ps['time']);
        $this -> addInsertValue(serialize($params['words']), $ps['words']);
    }
}

class INSERT_PRODUCT_SEARCH_RESULT_RECORD extends DB_Insert
{
    function INSERT_PRODUCT_SEARCH_RESULT_RECORD()
    {
        parent::DB_Insert('products_search_result');
    }

    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $psr = $tables['products_search_result']['columns'];

        $this->addInsertValue($params['search_id'], $psr['id']);
        $this->addInsertValue($params['pid'], $psr['p_id']);
        $this->addInsertValue($params['relevance'], $psr['relevance']);
    }
}

class INSERT_NEW_CATEGORY extends DB_Insert
{
    function INSERT_NEW_CATEGORY()
    {
        parent :: DB_Insert('categories_descr');
    }

    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $columns = $tables['categories_descr']['columns'];

        $image_large_info = $params["image_large_info"];
        $image_small_info = $params["image_small_info"];

        $this->addInsertValue($params["inserted_id"],               $columns['id']);
        $this->addMultiLangInsertValue($params["cat_name"],         $columns['name'], $columns['id'], 'Catalog');
        $this->addMultiLangInsertValue($params["description"],      $columns['descr'], $columns['id'], 'Catalog');
        $this->addInsertValue($image_large_info["name"],            $columns['image_file']);
        $this->addInsertValue($image_small_info["name"],            $columns['image_small_file']);
        $this->addMultiLangInsertValue($params["image_description"], $columns['image_descr'], $columns['id'], 'Catalog');
        $this->addMultiLangInsertValue($params["page_title"],       $columns['page_title'], $columns['id'], 'Catalog');
        $this->addMultiLangInsertValue($params["meta_keywords"],    $columns['meta_keywords'], $columns['id'], 'Catalog');
        $this->addMultiLangInsertValue($params["meta_description"], $columns['meta_descr'], $columns['id'], 'Catalog');
        $this->addInsertValue($params["show_products_recursively"],  $columns['show_prod_recurs']);
        $this->addMultiLangInsertValue($params["seo_url"], $columns['seo_url_prefix'], $columns['id'], 'Catalog');
    }
}

//:
class INSERT_CATEGORY_TO_TREE extends DB_Insert
{
    function INSERT_CATEGORY_TO_TREE()
    {
        parent :: DB_Insert('categories');
    }

    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $c = $tables['categories']['columns'];

        $this->addInsertValue($params['left'],  $c['left']);
        $this->addInsertValue($params['right'], $c['right']);
        $this->addInsertValue($params['level'], $c['level']);
        if (isset($params["cat_status"]) && $params["cat_status"] != null)
            $this->addInsertValue($params["cat_status"],$c['status']);
    }
}

class INSERT_CATEGORY_DESCR extends DB_Insert
{
    function INSERT_CATEGORY_DESCR()
    {
        parent :: DB_Insert('categories_descr');
    }

    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $cd = $tables['categories_descr']['columns'];

        $this->addInsertValue($params["inserted_id"], $cd['id']);
        $this->addMultiLangInsertValue($params["cat_name"], $cd['name'], $cd['id'], 'Catalog');
        $this->addInsertValue('', $cd['descr']);
        $this->addInsertValue('', $cd['image_file']);
        $this->addInsertValue('', $cd['image_small_file']);
        $this->addInsertValue('', $cd['image_descr']);
        $this->addInsertValue('', $cd['page_title']);
        $this->addInsertValue('', $cd['meta_keywords']);
        $this->addInsertValue('', $cd['meta_descr']);
        $this->addInsertValue($params["recursion"], $cd['show_prod_recurs']);
        $this->addInsertValue('', $cd['seo_url_prefix']);
    }
}

class INSERT_NEW_PRODUCT extends DB_Insert
{
    function INSERT_NEW_PRODUCT()
    {
        parent :: DB_Insert('products');
    }

    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $p = $tables['products']['columns'];

        $this->addInsertValue($params['p_type_id'], $p['pt_id'] );
        $this->addMultiLangInsertValue($params['p_name'], $p['name'], $p['id'], 'Catalog');
        $this->addInsertValue(date('Y-m-d H:i:s', time()), $p['date_added']);
        $this->addInsertValue(date('Y-m-d H:i:s', time()), $p['date_updated']);
    }
}

class INSERT_NEW_PRODUCT_ATTRIBUTE extends DB_Insert
{
    function INSERT_NEW_PRODUCT_ATTRIBUTE()
    {
        parent :: DB_Insert('product_attributes');
    }

    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $pa = $tables['product_attributes']['columns'];

        $this->addInsertValue($params['p_id'], $pa['p_id']);
        $this->addInsertValue($params['a_id'], $pa['a_id']);
        if (modApiFunc('Catalog', 'isMLAttribute', $params['a_id']))
            $this->addMultiLangInsertValue($params['pa_value'], $pa['attr_value'], $pa['id'], 'Catalog');
        else
            $this->addInsertValue($params['pa_value'], $pa['attr_value']);
    }
}

class INSERT_NEW_PRODUCT_IMAGE extends DB_Insert
{
    function INSERT_NEW_PRODUCT_IMAGE()
    {
        parent :: DB_Insert('product_images');
    }

    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $pi = $tables['product_images']['columns'];

        $this -> addInsertValue($params['pa_id'], $pi['pa_id']);
        $this -> addInsertValue($params['name'], $pi['name']);
        $this -> addInsertValue($params['width'], $pi['width']);
        $this -> addInsertValue($params['height'], $pi['height']);
    }
}

class INSERT_NEW_PRODUCT_TYPE extends DB_Insert
{
    function INSERT_NEW_PRODUCT_TYPE()
    {
        parent :: DB_Insert('product_types');
    }

    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $pt = $tables['product_types']['columns'];

        $this->addMultiLangInsertValue($params['name'], $pt['name'], $pt['id'], 'Catalog');
        $this->addMultiLangInsertValue($params['descr'], $pt['descr'], $pt['id'], 'Catalog');
    }
}

class INSERT_NEW_ATTRIBUTE extends DB_Insert
{
    function INSERT_NEW_ATTRIBUTE()
    {
        parent :: DB_Insert('attributes');
    }

    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $a = $tables['attributes']['columns'];

        $this -> addInsertValue($params['group_id'], $a['ag_id']);
        $this -> addInsertValue($params['input_type_id'], $a['it_id']);
        $this -> addInsertValue($params['unit_type_value'], $a['ut']);
        $this -> addInsertValue($params['view_tag'], $a['view_tag']);
        $this -> addInsertValue($params['name'], $a['name']);
        $this -> addInsertValue($params['descr'], $a['descr']);
        $this -> addInsertValue($params['type'], $a['type']);
        $this -> addInsertValue($params['min'], $a['min']);
        $this -> addInsertValue($params['max'], $a['max']);
        $this -> addInsertValue($params['size'], $a['size']);
        $this -> addInsertValue($params['sort'], $a['sort_order']);
        $this -> addInsertValue($params['allow_html'], $a['allow_html']);
        if (isset($params['multilang']))
            $this -> addInsertValue($params['multilang'], $a['multilang']);
    }
}

class INSERT_NEW_PRODUCT_TYPE_ATTRIBUTE extends DB_Insert
{
    function INSERT_NEW_PRODUCT_TYPE_ATTRIBUTE()
    {
        parent :: DB_Insert('product_type_attributes');
    }

    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $ptr = $tables['product_type_attributes']['columns'];

        $this->addInsertValue($params['pt_id'], $ptr['pt_id']);
        $this->addInsertValue($params['id'], $ptr['a_id']);
        $this->addInsertValue($params['visible'], $ptr['type_attr_visible']);
        $this->addInsertValue($params['required'], $ptr['type_attr_required']);
        $this->addMultiLangInsertValue($params['default'], $ptr['type_attr_def_val'], $ptr['id'], 'Catalog');
    }
}

class INSERT_CATALOG_TEMP_RECORD extends DB_Insert
{
    function INSERT_CATALOG_TEMP_RECORD()
    {
        parent :: DB_Insert('catalog_temp');
    }

    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $ct = $tables['catalog_temp']['columns'];

        $this -> addInsertValue($params['value'], $ct['value']);
        $this -> addInsertValue($params['form_id'], $ct['form_id']);
    }
}

class INSERT_PRODUCT_TO_CATEGORIES_RECORD extends DB_Insert
{
    function INSERT_PRODUCT_TO_CATEGORIES_RECORD()
    {
        parent :: DB_Insert('products_to_categories');
    }

    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $ptc = $tables['products_to_categories']['columns'];

        $this -> addInsertValue($params['pid'], $ptc['product_id']);
        $this -> addInsertValue($params['cid'], $ptc['category_id']);
    }
}

// -----------------------------------
// Additional Delete queries
// -----------------------------------

class DELETE_OLD_PRODUCT_SEARCH_RECORDS extends DB_Delete
{
    function DELETE_OLD_PRODUCT_SEARCH_RECORDS()
    {
        parent :: DB_Delete('products_search');
    }

    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $ps = $tables['products_search']['columns'];

        $this -> WhereField($ps['id'], DB_IN, '(\'' . implode('\',\'', $params['search_ids']) . '\')');
    }
}

class DELETE_OLD_PRODUCT_SEARCH_RESULT_RECORDS extends DB_Delete
{
    function DELETE_OLD_PRODUCT_SEARCH_RESULT_RECORDS()
    {
        parent :: DB_Delete('products_search_result');
    }

    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $psr = $tables['products_search_result']['columns'];

        $this -> WhereField($psr['id'], DB_IN, '(\'' . implode('\',\'', $params['search_ids']) . '\')');
    }
}

class DELETE_PRODUCT_IMAGES_BY_IMAGE_IDS extends DB_Delete
{
    function DELETE_PRODUCT_IMAGES_BY_IMAGE_IDS()
    {
        parent :: DB_Delete('product_images');
    }

    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $pi = $tables['product_images']['columns'];

        $this -> WhereField($pi['id'], DB_IN, "('" . implode("','", $params['i_ids']) . "')");
    }
}

class DELETE_PRODUCT_ATTRIBUTES_BY_PRODUCT_IDS extends DB_Delete
{
    function DELETE_PRODUCT_ATTRIBUTES_BY_PRODUCT_IDS()
    {
        parent :: DB_Delete('product_attributes');
    }

    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $pa = $tables['product_attributes']['columns'];

        $this -> WhereField($pa['p_id'], DB_IN, "('" . implode("','", $params['pids']) . "')");
    }
}

class DELETE_PRODUCTS_BY_PRODUCT_IDS extends DB_Delete
{
    function DELETE_PRODUCTS_BY_PRODUCT_IDS()
    {
        parent :: DB_Delete('products');
    }

    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $p = $tables['products']['columns'];

        $this->deleteMultiLangField($p['name'], $p['id'], 'Catalog');
        $this->WhereField($p['id'], DB_IN, "('" . implode("','", $params['pids']) . "')");
    }
}

class DELETE_PRODUCTS_TO_CATEGORIES_BY_PRODUCT_IDS extends DB_Delete
{
    function DELETE_PRODUCTS_TO_CATEGORIES_BY_PRODUCT_IDS()
    {
        parent :: DB_Delete('products_to_categories');
    }

    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $ptc = $tables['products_to_categories']['columns'];

        if (isset($params['cids']))
            $this -> Where($ptc['category_id'], DB_IN, "('" . implode("','", $params['cids']). "')");

        if (isset($params['cids']) && isset($params['pids']))
            $this -> WhereAND();

        if (isset($params['pids']))
            $this -> Where($ptc['product_id'], DB_IN, "('" . implode("','", $params['pids']) . "')");
    }
}

class DELETE_CATEGORY_DESCR_BY_CATEGORY_IDS extends DB_Delete
{
    function DELETE_CATEGORY_DESCR_BY_CATEGORY_IDS()
    {
        parent :: DB_Delete('categories_descr');
    }

    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $cd = $tables['categories_descr']['columns'];

        $this->deleteMultiLangField($cd['name'],          $cd['id'], 'Catalog');
        $this->deleteMultiLangField($cd['descr'],         $cd['id'], 'Catalog');
        $this->deleteMultiLangField($cd['image_descr'],   $cd['id'], 'Catalog');
        $this->deleteMultiLangField($cd['page_title'],    $cd['id'], 'Catalog');
        $this->deleteMultiLangField($cd['meta_keywords'], $cd['id'], 'Catalog');
        $this->deleteMultiLangField($cd['meta_descr'],    $cd['id'], 'Catalog');

        $this -> WhereField($cd['id'], DB_IN, "('" . implode("','", $params['cids']) . "')");
    }
}

class DELETE_CATEGORIES_FROM_TREE extends DB_Delete
{
    function DELETE_CATEGORIES_FROM_TREE()
    {
        parent :: DB_Delete('categories');
    }

    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $c = $tables['categories']['columns'];

        $this->WhereField($c['id'], DB_IN, "('" . implode("','", $params['ids']) . "')");
    }
}

class DELETE_CATEGORIES_BY_PARENT_CATEGORY_RANGE extends DB_Delete
{
    function DELETE_CATEGORIES_BY_PARENT_CATEGORY_RANGE()
    {
        parent :: DB_Delete('categories');
    }

    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $c = $tables['categories']['columns'];

        $this -> WhereValue($c['left'], DB_GTE, $params['left']);
        $this -> WhereAnd();
        $this -> WhereValue($c['left'], DB_LT, $params['right']);
    }
}

class DELETE_PRODUCT_IMAGE_BY_ATTR_ID extends DB_Delete
{
    function DELETE_PRODUCT_IMAGE_BY_ATTR_ID()
    {
        parent :: DB_Delete('product_images');
    }

    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $pi = $tables['product_images']['columns'];

        $this -> WhereField($pi['pa_id'], DB_EQ, $params['pa_id']);
    }
}

class DELETE_PRODUCT_ATTRIBUTE_BY_ATTR_ID extends DB_Delete
{
    function DELETE_PRODUCT_ATTRIBUTE_BY_ATTR_ID()
    {
        parent :: DB_Delete('product_attributes');
    }

    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $pa = $tables['product_attributes']['columns'];

        $this->deleteMultiLangField($pa['attr_value'], $pa['id'], 'Catalog');
        $this -> WhereValue($pa['id'], DB_EQ, $params['id']);
    }
}

class DELETE_ATTRIBUTES_BY_IDS extends DB_Delete
{
    function DELETE_ATTRIBUTES_BY_IDS()
    {
        parent :: DB_Delete('attributes');
    }

    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $a = $tables['attributes']['columns'];

        $this -> WhereField($a['id'], DB_IN, "('" . implode("','", $params['ids']) . "')");
    }
}

class DELETE_PRODUCT_ATTRIBUTES_BY_ATTR_IDS extends DB_Delete
{
    function DELETE_PRODUCT_ATTRIBUTES_BY_ATTR_IDS()
    {
        parent :: DB_Delete('product_attributes');
    }

    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $pa = $tables['product_attributes']['columns'];

        $this->deleteMultiLangField($pa['attr_value'], $pa['id'], 'Catalog');
        $this->WhereField($pa['a_id'], DB_IN, "('" . implode("','", $params['a_ids']) . "')");
    }
}

class DELETE_PRODUCT_TYPE_ATTRIBUTES_BY_ID_AND_ATTR_IDS extends DB_Delete
{
    function DELETE_PRODUCT_TYPE_ATTRIBUTES_BY_ID_AND_ATTR_IDS()
    {
        parent :: DB_Delete('product_type_attributes');
    }

    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $pta = $tables['product_type_attributes']['columns'];

        $this->deleteMultiLangField($pta['type_attr_def_val'], $pta['id'], 'Catalog');

        $this->WhereField($pta['a_id'], DB_IN, "('" . implode("','", $params['a_ids']) . "')");
        $this->WhereAND();
        $this->WhereField($pta['pt_id'], DB_EQ, $params['pt_id']);
    }
}

class DELETE_PRODUCT_TYPE_ATTRIBUTES_BY_PRODUCT_TYPE extends DB_Delete
{
    function DELETE_PRODUCT_TYPE_ATTRIBUTES_BY_PRODUCT_TYPE()
    {
        parent :: DB_Delete('product_type_attributes');
    }

    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $pta = $tables['product_type_attributes']['columns'];

        $this->deleteMultiLangField($pta['type_attr_def_val'], $pta['id'], 'Catalog');
        $this->WhereValue($pta['pt_id'], DB_EQ, $params['pt_id']);
    }
}

class DELETE_CATALOG_TEMP_BY_FORM_ID extends DB_Delete
{
    function DELETE_CATALOG_TEMP_BY_FORM_ID()
    {
        parent :: DB_Delete('catalog_temp');
    }

    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $ct = $tables['catalog_temp']['columns'];

        $this -> WhereValue($ct['form_id'], DB_EQ, $params['form_id']);
    }
}

class DELETE_PRODUCT_TYPE_BY_ID extends DB_Delete
{
    function DELETE_PRODUCT_TYPE_BY_ID()
    {
        parent :: DB_Delete('product_types');
    }

    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $pt = $tables['product_types']['columns'];

        $this->deleteMultiLangField($pt['name'], $pt['id'], 'Catalog');
        $this->deleteMultiLangField($pt['descr'], $pt['id'], 'Catalog');

        $this -> WhereField($pt['id'], DB_EQ, $params['pt_id']);
    }
}

class SELECT_PRODUCT_ATTRIBUTE_ID_BY_NAME_AND_TAG extends DB_Select
{
    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $a = $tables['attributes']['columns'];

        $this->addSelectField($a['id']);
        $this->WhereValue($a['name'], DB_EQ, $params['name']);
        $this->WhereAND();
        $this->WhereValue($a['view_tag'], DB_EQ, $params['view_tag']);
    }
}

class SELECT_INPUT_TYPE extends DB_Select
{
    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $table = $tables['input_types']['columns'];

        $this->addSelectField($table['id']);
        $this->WhereValue($table['id'], DB_EQ, $params['input_type_id']);
    }
}

class SELECT_INPUT_TYPE_VALUES extends DB_Select
{
    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $table = $tables['input_type_values']['columns'];

        $this->addSelectField($table['id']);
        $this->addSelectField($table['value']);
        $this->WhereValue($table['it_id'], DB_EQ, $params['input_type_id']);
    }
}

class INSERT_INPUT_TYPE extends DB_Insert
{
    function INSERT_INPUT_TYPE()
    {
        parent :: DB_Insert('input_types');
    }

    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $table = $tables['input_types']['columns'];

        $this -> addInsertValue($params['input_type_id'], $table['id']);
        $this -> addInsertValue($params['input_type_name'], $table['name']);
    }
}

class INSERT_INPUT_TYPE_VALUE extends DB_Insert
{
    function INSERT_INPUT_TYPE_VALUE()
    {
        parent :: DB_Insert('input_type_values');
    }

    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $table = $tables['input_type_values']['columns'];

        $this -> addInsertValue($params['input_type_id'], $table['it_id']);
        $this -> addInsertValue($params['input_type_value'], $table['value']);
    }
}

class DELETE_INPUT_TYPE extends DB_Delete
{
    function DELETE_INPUT_TYPE()
    {
        parent :: DB_Delete('input_types');
    }

    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $table = $tables['input_types']['columns'];

        $this->WhereField($table['id'], DB_EQ, $params['input_type_id']);
    }
}

class DELETE_INPUT_TYPE_VALUES_BY_IDS extends DB_Delete
{
    function DELETE_INPUT_TYPE_VALUES_BY_IDS()
    {
        parent :: DB_Delete('input_type_values');
    }

    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $table = $tables['input_type_values']['columns'];

        $this -> WhereField($table['id'], DB_IN, "('" . implode("','", $params['removed_input_type_values_ids']) . "')");

    }
}

class DELETE_INPUT_TYPE_VALUES_BY_INPUT_TYPE_ID extends DB_Delete
{
    function DELETE_INPUT_TYPE_VALUES_BY_INPUT_TYPE_ID()
    {
        parent :: DB_Delete('input_type_values');
    }

    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $table = $tables['input_type_values']['columns'];

        $this -> WhereField($table['it_id'], DB_EQ, $params['input_type_id']);

    }
}


class UPDATE_INPUT_TYPE_VALUE extends DB_Update
{
    function UPDATE_INPUT_TYPE_VALUE()
    {
        parent :: DB_Update('input_type_values');
    }

    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $table = $tables['input_type_values']['columns'];

        $this -> addUpdateValue($table['value'], $params['value']);
        $this -> WhereValue($table['id'], DB_EQ, $params['value_id']);
    }
}


class UPDATE_CUSTOM_INPUT_TYPE_VALUES_FOR_PRODUCTS extends DB_Update
{
    function UPDATE_CUSTOM_INPUT_TYPE_VALUES_FOR_PRODUCTS()
    {
        parent :: DB_Update('product_attributes');
    }

    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $table = $tables['product_attributes']['columns'];
//        CTrace::err($params);
        $this -> addUpdateValue($table['attr_value'], $params['not_selected_product_attr_value']);
        $this -> WhereValue($table['a_id'], DB_EQ, $params['attribute_id']);
        $this -> WhereAnd();
        $this -> WhereValue($table['attr_value'], DB_EQ, $params['current_product_attr_value']);

    }
}

class SELECT_MAX_INPUT_TYPE_ID extends DB_Select
{
    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $table = $tables['input_types']['columns'];

        $this->addSelectField($this->fMax($table['id']), 'max_id');
    }
}


class SELECT_INPUT_TYPE_BY_ATTRIBUTE_ID extends DB_Select
{
    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $table_attributes = $tables['attributes']['columns'];
        $table_input_types = $tables['input_types']['columns'];

        $this->addLeftJoin('input_types', $table_attributes['it_id'], DB_EQ, $table_input_types['a_id']);

        $this->addSelectField($table_attributes['type']);
        $this->addSelectField($table_attributes['it_id']);
        $this->addSelectField($table_attributes['name']);
        $this -> WhereValue($table_attributes['id'], DB_EQ, $params['attribute_id']);
    }
}

class SELECT_MULTI_CATS_BY_PID extends DB_Select
{
    function initQuery($params)
    {
        $pid = $params['pid'];

        $tables = Catalog::getTables();
        $c = $tables['products_to_categories']['columns'];

        $this->addSelectField($c['category_id'], 'mcats');
        $this->WhereValue($c['product_id'], DB_EQ, $pid);
    }
}

/*
class SELECT_PRODUCT_TYPE_ATTRIBUTE_VISIBILITY extends DB_Select
{
    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $pta = $tables['product_type_attributes']['columns'];

        $this->addSelectField($pta['type_attr_visible']);
        $this->addSelectField($pta['a_id']);
        $this->WhereValue($pta['pt_id'], DB_EQ, $params['pt_id']);
    }
}

class UPDATE_PRODUCT_ATTIBUTES_VISIBILITY extends DB_Update
{
    function UPDATE_PRODUCT_ATTIBUTES_VISIBILITY()
    {
        parent :: DB_Update('product_attributes');
    }

    function initQuery($params)
    {
        $tables = Catalog :: getTables();
        $pa = $tables['product_attributes']['columns'];
        $p = $tables['products']['columns'];

        $this->addUpdateTable('products');
        $this->addUpdateExpression($pa['type_attr_visible'], $params['visible'] ? '"1"' : '""');
        $this->WhereField($pa['p_id'], DB_EQ, $p['id']);
        $this->WhereAND();
        $this->WhereValue($pa['a_id'], DB_EQ, $params['a_id']);
        $this->WhereAND();
        $this->WhereValue($p['pt_id'], DB_EQ, $params['pt_id']);
    }
}
*/

?>