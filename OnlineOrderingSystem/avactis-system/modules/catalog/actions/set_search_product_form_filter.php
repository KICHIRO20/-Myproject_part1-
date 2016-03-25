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

loadModuleFile('catalog/abstract/product_list_filter.php');

/**
 * The size of the selection from the DB on each relevance level.
 */
define('PSF_PRODUCT_SEARCH_RESULT_LIMIT', 300);

/**
 * @package Catalog
 * @author Sergey Kulitsky
 *
 */

class SetSearchProductFormFilter extends AjaxAction
{
    function SetSearchProductFormFilter()
    {
        $this -> overflow = false;
    }

    function onAction()
    {
        global $application;

        $request = &$application -> getInstance('Request');
        $filter = $request -> getValueByKey('filter');

        $filter['pattern'] = trim($filter['pattern']);
        #if ($filter['pattern'] == '') return;

        // if category is not set -> set it to Home
        if (!isset($filter['category']))
        {
            $filter['category'] = 1;
            $filter['recursive'] = 'Y';
        }

        // setting category
        modApiFunc('CProductListFilter', 'changeCurrentCategoryId',
                   $filter['category']);

        // building pattern type
        $pattern_type = SEARCH_ALL_VALUES;
        if (@$filter['pattern_type'] == 'any')
            $pattern_type = SEARCH_ANY_VALUES;

        // building the word list
        $WordList = array();
        if (@$filter['pattern'] != '' && @$filter['pattern_type'] == 'exactly')
        {
            $WordList[] = trim($filter['pattern']);
        }
        elseif (@$filter['pattern'] != '')
        {
            $tmp_wordlist = explode(' ', $filter['pattern']);
            foreach($tmp_wordlist as $v)
                if (trim($v))
                    $WordList[] = trim($v);
        }
        if (empty($WordList))
        {
            $WordList = null;
            $filter['pattern'] = '';
        }
        else
        {
            $filter['pattern'] = join(' ', $WordList);
        }

        // if word list is empty -> force all fields to be included
        if ($WordList == null)
        {
            $filter['in_name'] = 'Y';
            $filter['in_sku'] = 'Y';
            $filter['in_descr'] = 'Y';
            $filter['in_det_descr'] = 'Y';
            $filter['in_title'] = 'Y';
            $filter['in_keywords'] = 'Y';
            $filter['in_meta_descr'] = 'Y';
            $filter['in_id'] = 'Y';
        }

        // if WordList is not empty but all fields are not included
        // -> force all fields to be included
        if ($WordList && !isset($filter['in_name']) && !isset($filter['in_sku'])
            && !isset($filter['in_descr']) && !isset($filter['in_det_descr'])
            && !isset($filter['in_title']) && !isset($filter['in_keywords'])
            && !isset($filter['in_meta_descr'])
            && !isset($filter['in_id']))
        {
            $filter['in_name'] = 'Y';
            $filter['in_sku'] = 'Y';
            $filter['in_descr'] = 'Y';
            $filter['in_det_descr'] = 'Y';
            $filter['in_title'] = 'Y';
            $filter['in_keywords'] = 'Y';
            $filter['in_meta_descr'] = 'Y';
            $filter['in_id'] = 'Y';
        }

        // if price_min/price_max is not numeric -> empty the value
        if (!is_numeric(@$filter['price_min']))
            $filter['price_min'] = '';
        if (!is_numeric(@$filter['price_max']))
            $filter['price_max'] = '';

/*        // if the manufacturer is invalid -> set it to all
        if (modApiFunc('Catalog', 'isCorrectManufacturerId', @$filter['manufacturer']))
            $filter['manufacturer'] = 'all';*/

        // if the manufacturer is not set -> set it to all
        if (!isset($filter['manufacturer']))
            $filter['manufacturer'] = 'all';

        // getting the result
        $result = array();
        if (isset($filter['in_name']) || !$filter['pattern'])
            $result = array_merge($result,
                      $this -> getQueryResult($this -> getCommonSearchFilter(
                      $filter, 'in_name', $WordList, $pattern_type, $result)));

        if (isset($filter['in_sku']))
            $result = array_merge($result,
                      $this -> getQueryResult($this -> getCommonSearchFilter(
                      $filter, 'in_sku', $WordList, $pattern_type, $result)));

        if (isset($filter['in_descr']))
            $result = array_merge($result,
                      $this -> getQueryResult($this -> getCommonSearchFilter(
                      $filter, 'in_descr', $WordList, $pattern_type, $result)));

        if (isset($filter['in_det_descr']))
            $result = array_merge($result,
                      $this -> getQueryResult($this -> getCommonSearchFilter(
                      $filter, 'in_det_descr', $WordList, $pattern_type, $result)));

        if (isset($filter['in_title']))
            $result = array_merge($result,
                      $this -> getQueryResult($this -> getCommonSearchFilter(
                      $filter, 'in_title', $WordList, $pattern_type, $result)));

        if (isset($filter['in_keywords']))
            $result = array_merge($result,
                      $this -> getQueryResult($this -> getCommonSearchFilter(
                      $filter, 'in_keywords', $WordList, $pattern_type, $result)));

        if (isset($filter['in_meta_descr']))
            $result = array_merge($result,
                      $this -> getQueryResult($this -> getCommonSearchFilter(
                      $filter, 'in_meta_descr', $WordList, $pattern_type, $result)));

        if (isset($filter['in_id']))
            $result = array_merge($result,
                      $this -> getQueryResult($this -> getCommonSearchFilter(
                      $filter, 'in_id', $WordList, $pattern_type, $result)));

        // getting unique product_id
        $result = array_unique($result);

        // saving the result and the filter
        $filter['result'] = $result;
        // force the result array to be non-empty
        $filter['result'][] = 0;
        $filter['overflow'] = $this -> overflow;
        modApiFunc('Session', 'set', 'SearchProductFormFilter', $filter);

        // final redirect to product group edit form
        $redirect = new Request();
        $redirect -> setView('ProductList');

        $application -> redirect($redirect);
    }

    function getCommonSearchFilter($filter, $field, $wordlist, $pattern_type, $ignore_list)
    {
        $CFilter = new CProductListFilter();

        // setting category
        if (isset($filter['category']) && $filter['category'] != 'all')
            $CFilter -> changeCurrentCategoryId($filter['category']);
        else
            $CFilter -> product_list_params_obj -> category_id = null;

        // setting if category should be searched recursively
        if (isset($filter['recursive']))
            $CFilter -> product_list_params_obj -> select_mode_recursiveness = IN_CATEGORY_RECURSIVELY;
        else
            $CFilter -> product_list_params_obj -> select_mode_recursiveness = IN_CATEGORY_ONLY;

        // setting manufacturer
        if (isset($filter['manufacturer']) && $filter['manufacturer'] != 'all')
            $CFilter -> product_list_params_obj -> filter_manufacturer_id_list = array($filter['manufacturer']);
        else
            $CFilter -> resetManufacturerId();

        // setting min price
        if (is_numeric(@$filter['price_min']))
            $CFilter -> changeCurrentMinSalePrice($filter['price_min']);
        if (is_numeric(@$filter['price_max']))
            $CFilter -> changeCurrentMaxSalePrice($filter['price_max']);

        // setting the word criteria for provided fields
        if (isset($filter['in_name']) && $field == 'in_name')
        {
            $CFilter -> product_list_params_obj -> filter_product_name_like_values = $wordlist;
            $CFilter -> product_list_params_obj -> filter_product_name_search_condition = $pattern_type;
        }
        else
        {
            $CFilter -> product_list_params_obj -> filter_product_name_like_values = null;
        }

        if (isset($filter['in_sku']) && $field == 'in_sku')
        {
            $CFilter -> product_list_params_obj -> filter_sku_like_values = $wordlist;
            $CFilter -> product_list_params_obj -> filter_sku_search_condition = $pattern_type;
        }
        else
        {
            $CFilter -> product_list_params_obj -> filter_sku_like_values = null;
        }

        if (isset($filter['in_descr']) && $field == 'in_descr')
        {
            $CFilter -> product_list_params_obj -> filter_short_description_like_values = $wordlist;
            $CFilter -> product_list_params_obj -> filter_short_description_search_condition = $pattern_type;
        }
        else
        {
            $CFilter -> product_list_params_obj -> filter_short_description_like_values = null;
        }

        if (isset($filter['in_det_descr']) && $field == 'in_det_descr')
        {
            $CFilter -> product_list_params_obj -> filter_detailed_description_like_values = $wordlist;
            $CFilter -> product_list_params_obj -> filter_detailed_description_search_condition = $pattern_type;
        }
        else
        {
            $CFilter -> product_list_params_obj -> filter_detailed_description_like_values = null;
        }

        if (isset($filter['in_title']) && $field == 'in_title')
        {
            $CFilter -> product_list_params_obj -> filter_page_title_like_values = $wordlist;
            $CFilter -> product_list_params_obj -> filter_page_title_search_condition = $pattern_type;
        }
        else
        {
            $CFilter -> product_list_params_obj -> filter_page_title_like_values = null;
        }

        if (isset($filter['in_keywords']) && $field == 'in_keywords')
        {
            $CFilter -> product_list_params_obj -> filter_meta_keywords_like_values = $wordlist;
            $CFilter -> product_list_params_obj -> filter_meta_keywords_search_condition = $pattern_type;
        }
        else
        {
            $CFilter -> product_list_params_obj -> filter_meta_keywords_like_values = null;
        }

        if (isset($filter['in_meta_descr']) && $field == 'in_meta_descr')
        {
            $CFilter -> product_list_params_obj -> filter_meta_description_like_values = $wordlist;
            $CFilter -> product_list_params_obj -> filter_meta_description_search_condition = $pattern_type;
        }
        else
        {
            $CFilter -> product_list_params_obj -> filter_meta_description_like_values = null;
        }

        $params = $CFilter -> getProductListParamsObject();

        // search by product id (commented out)
        if ($field == 'in_id')
        {
            // force the list to be non-empty
            $id_list = array(0);
            if (is_array($wordlist))
                foreach ($wordlist as $w)
                    if (is_numeric($w))
                        $id_list[] = intval($w);

            $params -> product_id_list_to_select = $id_list;
        }

        // setting list to ignore
        $params -> product_id_list_to_ignore = $ignore_list;

        // hide products which have the attributes hidden or undefined
        $params -> params['filter']['meta_description']['select_undefined'] = false;
        $params -> params['filter']['meta_description']['select_invisible'] = false;
        $params -> params['filter']['meta_keywords']['select_undefined'] = false;
        $params -> params['filter']['meta_keywords']['select_invisible'] = false;
        $params -> params['filter']['page_title']['select_undefined'] = false;
        $params -> params['filter']['page_title']['select_invisible'] = false;
        $params -> params['filter']['detailed_description']['select_undefined'] = false;
        $params -> params['filter']['detailed_description']['select_invisible'] = false;
        $params -> params['filter']['short_description']['select_undefined'] = false;
        $params -> params['filter']['short_description']['select_invisible'] = false;
        $params -> params['filter']['sku']['select_undefined'] = false;
        $params -> params['filter']['sku']['select_invisible'] = false;

        // force to limit the result
        $params -> setSelectLimits(0, PSF_PRODUCT_SEARCH_RESULT_LIMIT);

        return $params;
    }

    function getQueryResult($params)
    {
        $result = execQuery('SELECT_PRODUCT_LIST', $params->getParams());
        $plain_result = array();
        foreach($result as $v)
            $plain_result[] = $v['product_id'];

        $this -> overflow = $this -> overflow
                    || count($plain_result) >= PSF_PRODUCT_SEARCH_RESULT_LIMIT;

        return $plain_result;
    }

    var $overflow;
}