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

loadModuleFile('catalog/catalog_api.php');
loadModuleFile('catalog/abstract/product_class.php');

/**
 * The words in the searching request shorter than PRODUCT_SEARCH_MIN_WORD_LENGTH
 * are skipped.
 */
define('PRODUCT_SEARCH_MIN_WORD_LENGTH', 3);

/**
 * The words in the searching request longer than PRODUCT_SEARCH_MAX_WORD_LENGTH
 * are skipped.
 */
define('PRODUCT_SEARCH_MAX_WORD_LENGTH', 20);

/**
 * The number of the first words in the searching request, which will be used
 * in the search. The rest of the words are skipped.
 */
define('PRODUCT_SEARCH_MAX_WORD_NUMBER', 10);

/**
 * The max length of the searching request.
 * If the string of the searching request is longer than max length, then it will
 * be truncated up to the specified length.
 */
define('PRODUCT_SEARCH_MAX_PATTERN_LENGTH', 4096);

/**
 * The size of the selection from the DB on each relevance level.
 */
define('PRODUCT_SEARCH_RESULT_LIMIT', 100);

/**
 * Time in seconds, after that the search result will be deleted from
 * the database.
 */
define('PRODUCT_SEARCH_RESULT_LIFE_TIME',3600);

/**
 * The class is used to search the products in the catalog by any specified
 * text string.
 *
 * The current realization (Nov 19, 2007).
 * 1. To search in the fields:
 *       - ProductName,
 *       - ProductSKU,
 *       - ShortDescription and DetailedDescription
 *       - Meta Keywords and Meta Description
 * 2. Five relevance levels are realized:
 *    - searching in ProductName with the condition ALL_WORD - relevance 0 (the greatest)
 *    - searching in ProductSKU with the condition ALL_WORD - relevance 1
 *    - searching in Description (Short and Detailed) with the condition ALL_WORD - relevance 2
 *    - searching in Description (Short and Detailed) with the condition ANY_WORD - relevance 3
 *    - searching in Meta fields (Keywords and Description) with the condition ANY_WORD - relevance 4
 * 3. Output the result by the relevance level.
 *
 * The class usage:
 * 1. The search should be performed first, using the prepareProductsSearch($pattern)
 *    method.
 * 2. This method saves the searching result - the array of products id  - in
 *    the DB and returns the searching id.
 * 3. Using getProdsListInSearchResult($search_id), one can get the searching
 *    result as the array of references to the objects CProductInfo.
 *
 *
 * @package Catalog
 * @author Alexey Florinsky
 */
class CatalogSearch
{

    /**#@+
     * @access public
     */

    /**
     * A constructor of the class.
     */
    function CatalogSearch()
    {
        loadCoreFile('cstring.php');
        $this->cstring = new CString();
    }

    /**
     * Searches the products in the DB.
     * The searching result is saved in the DB.
     * The method returns the search id.
     * The searching result can be null.
     *
     * @param string $pattern Search request
     * @return int Search ID
     */
    function prepareProductsSearch($pattern)
    {
        /* Extract the words from the string, to be searched by. */
        $words = array_values($this->getIndexWordList($pattern));

       /*
        If the searching words are empty, then save the search in the DB without
        its results.
        It can be used, to output the request string to the customer area search
        and process the empty searching result.
        */
        if (empty($words))
        {
            return $this->saveSearchResult($pattern,array(),array());
        }

        /* Perform the relevant searching */
        $result = array(); // to unify all search statements

        /* Phrase */
        $result = array_unique(array_merge($result, $this->searchProductsByName(array($pattern), SEARCH_ALL_VALUES, $result)));
        $result = array_unique(array_merge($result, $this->searchProductsByDescriptions(array($pattern), SEARCH_ALL_VALUES, $result) ));
        if (count($result) < PRODUCT_SEARCH_RESULT_LIMIT )
        {
            /* All words */
            $result = array_unique(array_merge($result, $this->searchProductsByName($words, SEARCH_ALL_VALUES, $result) ));
            $result = array_unique(array_merge($result, $this->searchProductsById($words, SEARCH_ALL_VALUES, $result) ));
            if (count($result) < PRODUCT_SEARCH_RESULT_LIMIT )
            {
                $result = array_unique(array_merge($result, $this->searchProductsBySku($words, SEARCH_ALL_VALUES, $result) ));
                $result = array_unique(array_merge($result, $this->searchProductsByDescriptions($words, SEARCH_ALL_VALUES, $result) ));
                if (count($result) < PRODUCT_SEARCH_RESULT_LIMIT )
                {
                    /* Any word */
                    $result = array_unique(array_merge($result, $this->searchProductsByName($words, SEARCH_ANY_VALUES, $result) ));
                    if (count($result) < PRODUCT_SEARCH_RESULT_LIMIT )
                    {
                        $result = array_unique(array_merge($result, $this->searchProductsByDescriptions($words, SEARCH_ANY_VALUES, $result) ));
                        $result = array_unique(array_merge($result, $this->searchProductsByMeta($words, SEARCH_ANY_VALUES, $result) ));
                    }
                }
            }
        }

        #
        # remove GC products from search results (by product type id)
        #
        loadClass('CProductListFilter');
        $f = new CProductListFilter();
        $productListFilter = $f->getProductListParamsObject();
        $productListFilter->filter_product_type_id_list = array(GC_PRODUCT_TYPE_ID);

        $_ids = modApiFunc('Catalog', 'getProductListByFilter', $productListFilter, RETURN_AS_ID_LIST);

        $gc_ids = array();
        foreach ($_ids as $id)
            $gc_ids[] = $id['product_id'];

        $result = array_diff($result,$gc_ids);
        ##############################################################

        /* The searching result should contain only unique products */
        $search_id = $this->saveSearchResult($pattern,$result,$words);
        return $search_id;
    }

    /**
     * Returns the array of the references to CProductInfo products from the
     * searching result, using paginator.
     *
     * @param int $search_id Search ID
     * @return array A list of CProductInfo objects
     */
    function getProdsListInSearchResult($search_id, $return_as_pids_array = false)
    {
        #modApiFunc('paginator', 'setCurrentPaginatorName', 'Catalog_Search');

        $params = array('search_id' => $search_id);
        $params['paginator'] = null;
        $params['paginator'] = modApiFunc('Catalog', 'selectProductSearchResultPg',
                                          $params, PAGINATOR_ENABLE);

        $result = modApiFunc('Catalog', 'selectProductSearchResult', $params);

        if($return_as_pids_array)
        {
            return $result;
        };

        $products_listing = array();

        for ($i=0; $i<sizeof($result); $i++)
        {
            $offset = modApiFunc('paginator', 'getCurrentPaginatorOffset');
            if (!is_numeric($offset))
            {
                $offset=0;
            }

            $product_info = new CProductInfo($result[$i]['product_id']);
            $product_info->setAdditionalProductTag('N', ($i+1+$offset) );
            array_push($products_listing, $product_info);
        }

        $this->updateSearchResultAccessTime($search_id);
        return $products_listing;
    }

    /**
     * Returns the initial request string for the specified search id.
     *
     * @param int $search_id Search ID
     * @return string Search request string
     */
    function getSearchPatternInSearchResult($search_id)
    {
        $result = modApiFunc('Catalog', 'selectProductSearchPattern', $search_id);
        if (isset($result[0]['pattern']))
        {
            $this->updateSearchResultAccessTime($search_id);
            return $result[0]['pattern'];
        }
        else
        {
            return '';
        }
    }

    /**
     * Returns a list of index words, to be searched by, for the specified
     * search id.
     * It is used to highlight in the searching results.
     *
     * @param int $search_id Search ID
     * @return array A list of index words
     */
    function getSearchIndexWordsInSearchResult($search_id)
    {
        $result = execQuery('SELECT_SEARCH_INDEX_WORDS_IN_SEARCH_RESULT', array('search_id'=>$search_id));
        if (isset($result[0]['words']))
        {
            $this->updateSearchResultAccessTime($search_id);
            return unserialize($result[0]['words']);
        }
        else
        {
            return array();
        }
    }

    /**
     * Deletes searching results from the DB, the time of the last access
     * to which was more than PRODUCT_SEARCH_RESULT_LIFE_TIME seconds ago.
     *
     */
    function deleteOldSearches()
    {
        $result = modApiFunc('Catalog', 'selectOldProductSearchRecords', time() - 3600);

        $search_ids = array();
        foreach($result as $element)
        {
            array_push($search_ids, $element['search_id']);
        }

        if (!empty($search_ids))
            modApiFunc('Catalog', 'deleteOldProductSearchRecords', $search_ids);
    }


    /**#@-*/



    /**#@+
     * @access private
     */


    /**
     * Saves the searching result in the DB.
     * The parameters $result and $words can be empty. It means the searching
     * does not have any results.
     *
     * @param string $pattern Search pattern string (user input, for example)
     * @param array $result A list of product ids, plain list (examle: 31,25,33,42), sorted by relevance
     * @param array $words A list of index words
     * @return int Search ID
     */
    function saveSearchResult($pattern,$result,$words)
    {
        global $application;

        modApiFunc('Catalog', 'insertProductSearchRecord', $pattern, $words);
        $mysql = &$application -> getInstance('DB_MySQL');
        $search_id = $mysql -> DB_Insert_Id();

        $relevance = 0;
        foreach($result as $pid)
        {
            modApiFunc('Catalog', 'insertProductSearchResultRecord', $search_id, $pid, $relevance);
            $relevance++;
        }

        return $search_id;
    }

    /**
     * Searches the products in the DB by the name, using index words.
     * For the optimization used a direct searching in the table products
     * by the attributes available and stock level without filtering.
     * The searching result is limited by the const value PRODUCT_SEARCH_RESULT_LIMIT.
     *
     * @param array $words A plain list of index words (example: ('item','flower','day') )
     * @param int $condition Constant: SEARCH_ANY_VALUES or SEARCH_ALL_VALUES
     * @return array A plain list of product ids
     */
    function searchProductsByName($words, $condition, $id_list_to_skip)
    {
        $params = $this->getCommonSearchFilter();
        $params->product_id_list_to_ignore = $id_list_to_skip;
        $params->filter_product_name_like_values = $words;
        $params->filter_product_name_search_condition = $condition;

        return $this->getQueryResult($params);
    }

    function searchProductsById($words, $condition, $id_list_to_skip)
    {
        $params = $this->getCommonSearchFilter();
        $id_list = array();
        foreach ($words as $w)
        {
            if (is_numeric($w))
            {
                $id_list[] = intval($w);
            }
        }
        if (empty($id_list))
        {
            return array();
        }
        $params->product_id_list_to_select = $id_list;
        return $this->getQueryResult($params);
    }

    function searchProductsBySku($words, $condition, $id_list_to_skip)
    {
        $params = $this->getCommonSearchFilter();
        $params->product_id_list_to_ignore = $id_list_to_skip;
        $params->filter_sku_like_values = $words;
        $params->filter_sku_search_condition = $condition;
        return $this->getQueryResult($params);
    }

    function searchProductsByDescriptions($words, $condition, $id_list_to_skip)
    {
        $params = $this->getCommonSearchFilter();
        $params->product_id_list_to_ignore = $id_list_to_skip;
        $params->filter_short_description_like_values = $words;
        $params->filter_short_description_search_condition = $condition;
        $res_short_descr = $this->getQueryResult($params);

        $params->filter_short_description_like_values = null;
        $params->filter_short_description_search_condition = null;
        $params->product_id_list_to_ignore = array_merge($id_list_to_skip, $res_short_descr);
        $params->filter_detailed_description_like_values = $words;
        $params->filter_detailed_description_search_condition = $condition;
        $res_detailed_descr = $this->getQueryResult($params);

        return array_merge($res_short_descr, $res_detailed_descr);
    }

    function searchProductsByMeta($words, $condition, $id_list_to_skip)
    {
        $params = $this->getCommonSearchFilter();
        $params->product_id_list_to_ignore = $id_list_to_skip;
        $params->filter_page_title_like_values = $words;
        $params->filter_page_title_search_condition = $condition;
        $res_page_title = $this->getQueryResult($params);

        $params = $this->getCommonSearchFilter();
        $params->product_id_list_to_ignore = array_merge($id_list_to_skip, $res_page_title);
        $params->filter_meta_keywords_like_values = $words;
        $params->filter_meta_keywords_search_condition = $condition;
        $res_meta_keywords = $this->getQueryResult($params);

        $params = $this->getCommonSearchFilter();
        $params->product_id_list_to_ignore = array_merge($id_list_to_skip, $res_page_title, $res_meta_keywords);
        $params->filter_meta_description_like_values = $words;
        $params->filter_meta_description_search_condition = $condition;
        $res_meta_description = $this->getQueryResult($params);

        return array_merge($res_page_title, $res_meta_keywords, $res_meta_description);
    }

    function getQueryResult($params)
    {
        $result = execQuery('SELECT_PRODUCT_LIST', $params->getParams());
        $plain_result = array();
        for($i=0; $i<count($result); $i++)
        {
            array_push($plain_result, $result[$i]['product_id']);
        }
        return $plain_result;
    }

    function getCommonSearchFilter()
    {
        //         default        c default
        loadClass('CProductListFilter');
        $f = new CProductListFilter();
        $params = $f->getProductListParamsObject();

        //
        $params->category_id = 1;
        $params->select_mode_recursiveness = IN_CATEGORY_RECURSIVELY;
        $params->select_mode_uniqueness = UNIQUE_PRODUCTS;
        //                                                                      ,
        //
        $params->params['filter']['meta_description']['select_undefined'] = false;
        $params->params['filter']['meta_description']['select_invisible'] = false;
        $params->params['filter']['meta_keywords']['select_undefined'] = false;
        $params->params['filter']['meta_keywords']['select_invisible'] = false;
        $params->params['filter']['page_title']['select_undefined'] = false;
        $params->params['filter']['page_title']['select_invisible'] = false;
        $params->params['filter']['detailed_description']['select_undefined'] = false;
        $params->params['filter']['detailed_description']['select_invisible'] = false;
        $params->params['filter']['short_description']['select_undefined'] = false;
        $params->params['filter']['short_description']['select_invisible'] = false;
        $params->params['filter']['sku']['select_undefined'] = false;
        $params->params['filter']['sku']['select_invisible'] = false;
        //
        $params->setSelectLimits(0, PRODUCT_SEARCH_RESULT_LIMIT);
        return $params;
    }

    /**
     * Returns a list of the index words from the random string $string,
     * which can be used for searching.
     * From the random string $string the following words will be deleted:
     * - shorter than PRODUCT_SEARCH_MIN_WORD_LENGTH
     * - longer than PRODUCT_SEARCH_MAX_WORD_LENGTH
     * - all HTML tags and HTML-characters
     *
     * @param string $string Search pattern
     * @return array Index words list
     */
    function getIndexWordList($string)
    {
        return getIndexWordsFromText($string);
    }


    /**
     * Updates the time from the last access to the searching result to the
     * current one.
     *
     *               $search_id                                                       .
     *             ,                                                           $search_id
     *                                 .
     *
     * @param int $search_id Search ID
     */
	function updateSearchResultAccessTime($search_id)
    {
        static $already_updated = null;
        if ($already_updated == null)
        {
            $already_updated = array();
        }

        if (!in_array($search_id, $already_updated))
        {
            modApiFunc('Catalog', 'updateProductSearchTime', $search_id);
            $already_updated[] = $search_id;
        }
    }


    var $cstring;
    /**#@-*/

}

?>