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
 * URLModifier class.
 * Class is used for conversion of the URL.
 * Rules of modification of the URL are given in the fields of the class
 * $this->_fRulesToEncode and $this->_fRulesToDecode.
 * This class is used to create and to parse Search Engine Friendy URLs.
 *
 * An example of encoding URL to get SEF URL:
 * <code>
 *  $URLMod = new URLModifier();
 *  $URLMod->setURL($url);
 *  $URLMod->encodeURL();
 *  $url = $URLMod->getURL();
 * <code>
 *
 * @package Core
 * @author Alexey Florinsky
 * @access  public
 */

class URLModifier
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Object constructor
     */
    function URLModifier()
    {
    }

    /**
     * Sets the URL which will be converted.
     *
     * @param string $url URL
     * @return void
     */
    function setURL($url)
    {
        $this->_prepareRules();
        $this->_fURL = parse_url($url);
        $this->_fProcessedURL = parse_url($url);

        if (!isset($this->_fURL['query']))
        {
            $this->_fURL['query'] = '';
        }
    }

    /**
     * Encodes the URL which was set in the object.
     *
     * @return boolean Returns TRUE if URL has been converted, FALSE otherwise
     */
    function encodeURL()
    {
        if ($this->processURL($this->_fRulesToEncode) == false)
        {
            //                            ,            default                URL.
            //         : QUERY_STRING      key1=value1&asc_action=ActionClass&key2=value2&key3=value3
            //                         : key1=value1/asc_action=ActionClass/key2=value2/key3=value3
            if (empty($this->_fProcessedURL['query']))
            {
                return false; //
            }
            else
            {
                $this->_fProcessedURL['query'] = strtr($this->_fProcessedURL['query'], array( '&' => '/' ));
            }
        }
        //                    , URL         -        return true
        return !($this->_fURL['query'] == $this->_fProcessedURL['query']);
    }

    /**
     * Decodes the URL which was set in the object.
     *
     * @return boolean Returns TRUE if URL has been converted, FALSE otherwise
     */
    function decodeURL()
    {
        if ($this->processURL($this->_fRulesToDecode) == false )
        {
            //                            ,            default                URL.
            //         : QUERY_STRING      key1=value1/asc_action=ActionClass/key2=value2/key3=value3
            //                         :  key1=value1&asc_action=ActionClass&key2=value2&key3=value3
            if (empty($this->_fProcessedURL['query']))
            {
                return false; //
            }
            else
            {
                $this->_fProcessedURL['query'] = strtr($this->_fProcessedURL['query'], array( '/' => '&' ));
            }
        }
        //                    , URL         -        return true
        return !($this->_fURL['query'] == $this->_fProcessedURL['query']);
    }

    /**
     * Gets the converted URL.
     * If the URL wasn't converted, the initial URL will be returned.
     *
     * @return string URL
     */
    function getURL()
    {
        return $this->glue_url($this->_fProcessedURL);
    }

    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    /**
     * Parses the URL with the rules $rules_list.
     *
     * @param array $rules_list Rules array
     * @return boolean Returns TRUE if URL has been converted, FALSE otherwise
     */
    function processURL($rules_list)
    {
        foreach($rules_list as $ruleFrom => $ruleTo )
        {
            $mathes = array();
            if ( preg_match($ruleFrom, $this->_fURL['query'], $mathes) === 1 )
            {
                $trans = array();
                for ($i=1; $i<sizeof($mathes); $i++)
                {
                    $trans['{'.$i.'}'] = $mathes[$i];
                }
                $this->_fProcessedURL['query'] = strtr($ruleTo, $trans);
                $this->_addSEO($mathes);
                return true;
            }
        }
        return false;
    }

    function _addSEO($m)
    {
        global $application;
        if(strstr($this->_fProcessedURL['query'],'%cSEO%'))
        {
            $catObj = new CCategoryInfo($m[1]);
            $seo_prefix = $catObj->getCategoryTagValue('seo_url_prefix');
            if (!empty($seo_prefix))
            {
                $seo_prefix .= '-';
            }
            else
            {
                $this->_fProcessedURL['query'] = preg_replace('/([a-zA-Z0-9_\-\%\+]+)lng[A-Z]{2}-([a-zA-Z0-9_\-\%\+\.]+)/', '${1}${2}', $this->_fProcessedURL['query']);
            }
            $this->_fProcessedURL['query'] = str_replace('%cSEO%',$seo_prefix,$this->_fProcessedURL['query']);
            return;
        };
        if(strstr($this->_fProcessedURL['query'],'%pSEO%'))
        {
            $catObj = new CProductInfo($m[1]);
            $seo_prefix = $catObj->getProductTagValue('SEOPrefix');
            if (!empty($seo_prefix))
            {
                $seo_prefix .= '-';
            }
            else
            {
                $this->_fProcessedURL['query'] = preg_replace('/([a-zA-Z0-9_\-\%\+]+)lng[A-Z]{2}-([a-zA-Z0-9_\-\%\+\.]+)/', '${1}${2}', $this->_fProcessedURL['query']);
            }
            $this->_fProcessedURL['query'] = str_replace('%pSEO%',$seo_prefix,$this->_fProcessedURL['query']);
            return;
        };
    }

    /**
     * Creates a URL from the array. The passed $parsed array must match
     * the form, which is returned by function parse_url()
     *
     * @param string $url URL
     * @return new URL
     */
    function glue_url($parsed)
    {
       if (! is_array($parsed)) return false;
           $uri  = isset($parsed['scheme']) ? $parsed['scheme'].':'.((_ml_strtolower($parsed['scheme']) == 'mailto') ? '':'//'): '';
           $uri .= isset($parsed['user']) ? $parsed['user'].($parsed['pass']? ':'.$parsed['pass']:'').'@':'';
           $uri .= isset($parsed['host']) ? $parsed['host'] : '';
           $uri .= isset($parsed['port']) ? ':'.$parsed['port'] : '';
           $uri .= isset($parsed['path']) ? $parsed['path'] : '';
           $uri .= isset($parsed['query']) ? '?'.$parsed['query'] : '';
           $uri .= isset($parsed['fragment']) ? '#'.$parsed['fragment'] : '';
       return $uri;
    }

    function _prepareRules()
    {
        global $application;

        //                            {%page_number%}   {%category_id%}
        $tpl_category = $application->getAppIni('SEFU_CATEGORY_QUERY_STRING_SUFFIX');

        //                            {%product_id%}
        $tpl_product  = $application->getAppIni('SEFU_PRODUCT_QUERY_STRING_SUFFIX');

        $_r1 = strtr($tpl_category, array('{%page_number%}' =>'1',   '{%category_id%}'=>'{1}')); //pg1-cid{1}.html
        $_r2 = strtr($tpl_category, array('{%page_number%}' =>'{2}', '{%category_id%}'=>'{1}')); // pg{2}-cid{1}.html
        $_r3 = strtr($tpl_product,  array('{%product_id%}'  =>'{1}')); // pid{1}.html
        $_rl2 = 'lng{2}-';
        $_rl3 = 'lng{3}-';
        $this->_fRulesToEncode = array (
            '/^asc_action=SetCurrCat&category_id=(\d+)&lng=([A-Z]{2})/'  => '%cSEO%'.$_rl2.$_r1,
            '/^asc_action=SetCurrCat&category_id=(\d+)/'  => '%cSEO%'.$_r1,

            '/^asc_action=Paginator_SetPage&pgname=Catalog_ProdsList_(\d+)&pgnum=(\d+)&lng=([A-Z]{2})/' => '%cSEO%'.$_rl3.$_r2,
            '/^asc_action=Paginator_SetPage&pgname=Catalog_ProdsList_(\d+)&pgnum=(\d+)/' => '%cSEO%'.$_r2,

            '/^asc_action=SetCurrentProduct&prod_id=(\d+)&lng=([A-Z]{2})/' => '%pSEO%'.$_rl2.$_r3,
            '/^asc_action=SetCurrentProduct&prod_id=(\d+)/' => '%pSEO%'.$_r3,

            '/^asc_action=SetProductListSortField&field=SORT_BY_PRODUCT_NAME,ASC/' => 'sort-by-name-asc.html',
            '/^asc_action=SetProductListSortField&field=SORT_BY_PRODUCT_NAME,DESC/' => 'sort-by-name-desc.html',
            '/^asc_action=SetProductListSortField&field=SORT_BY_PRODUCT_NAME/'      => 'sort-by-name.html',

            '/^asc_action=SetProductListSortField&field=SORT_BY_PRODUCT_SORT_ORDER,ASC/'  => 'sort-by-default-asc.html',
            '/^asc_action=SetProductListSortField&field=SORT_BY_PRODUCT_SORT_ORDER,DESC/' => 'sort-by-default-desc.html',
            '/^asc_action=SetProductListSortField&field=SORT_BY_PRODUCT_SORT_ORDER/'      => 'sort-by-default.html',

            '/^asc_action=SetProductListSortField&field=SORT_BY_PRODUCT_SALE_PRICE,ASC/'  => 'sort-by-price-asc.html',
            '/^asc_action=SetProductListSortField&field=SORT_BY_PRODUCT_SALE_PRICE,DESC/' => 'sort-by-price-desc.html',
            '/^asc_action=SetProductListSortField&field=SORT_BY_PRODUCT_SALE_PRICE/'      => 'sort-by-price.html',

            '/^asc_action=SetProductListSortField&field=SORT_BY_PRODUCT_LIST_PRICE,ASC/'  => 'sort-by-list-price-asc.html',
            '/^asc_action=SetProductListSortField&field=SORT_BY_PRODUCT_LIST_PRICE,DESC/' => 'sort-by-list-price-desc.html',
            '/^asc_action=SetProductListSortField&field=SORT_BY_PRODUCT_LIST_PRICE/'      => 'sort-by-list-price.html',

            '/^asc_action=SetProductListSortField&field=SORT_BY_PRODUCT_DATE_ADDED,ASC/'  => 'sort-by-date-added-asc.html',
            '/^asc_action=SetProductListSortField&field=SORT_BY_PRODUCT_DATE_ADDED,DESC/' => 'sort-by-date-added-desc.html',
            '/^asc_action=SetProductListSortField&field=SORT_BY_PRODUCT_DATE_ADDED/'      => 'sort-by-date-added.html',

            '/^asc_action=SetProductListSortField&field=SORT_BY_PRODUCT_DATE_UPDATED,ASC/'  => 'sort-by-date-updated-asc.html',
            '/^asc_action=SetProductListSortField&field=SORT_BY_PRODUCT_DATE_UPDATED,DESC/' => 'sort-by-date-updated-desc.html',
            '/^asc_action=SetProductListSortField&field=SORT_BY_PRODUCT_DATE_UPDATED/'      => 'sort-by-date-updated.html',

            '/^asc_action=SetProductListSortField&field=SORT_BY_PRODUCT_QUANTITY_IN_STOCK,ASC/'  => 'sort-by-quantity-asc.html',
            '/^asc_action=SetProductListSortField&field=SORT_BY_PRODUCT_QUANTITY_IN_STOCK,DESC/' => 'sort-by-quantity-desc.html',
            '/^asc_action=SetProductListSortField&field=SORT_BY_PRODUCT_QUANTITY_IN_STOCK/'      => 'sort-by-quantity.html',

            '/^asc_action=SetProductListSortField&field=SORT_BY_PRODUCT_SKU,ASC/'  => 'sort-by-sku-asc.html',
            '/^asc_action=SetProductListSortField&field=SORT_BY_PRODUCT_SKU,DESC/' => 'sort-by-sku-desc.html',
            '/^asc_action=SetProductListSortField&field=SORT_BY_PRODUCT_SKU/'      => 'sort-by-sku.html',

            '/^asc_action=SetProductListSortField&field=SORT_BY_PRODUCT_PER_ITEM_SHIPPING_COST,ASC/'  => 'sort-by-per-item-shipping-cost-asc.html',
            '/^asc_action=SetProductListSortField&field=SORT_BY_PRODUCT_PER_ITEM_SHIPPING_COST,DESC/' => 'sort-by-per-item-shipping-cost-desc.html',
            '/^asc_action=SetProductListSortField&field=SORT_BY_PRODUCT_PER_ITEM_SHIPPING_COST/'      => 'sort-by-per-item-shipping-cost.html',

            '/^asc_action=SetProductListSortField&field=SORT_BY_PRODUCT_PER_ITEM_HANDLING_COST,ASC/'  => 'sort-by-per-item-handling-cost-asc.html',
            '/^asc_action=SetProductListSortField&field=SORT_BY_PRODUCT_PER_ITEM_HANDLING_COST,DESC/' => 'sort-by-per-item-handling-cost-desc.html',
            '/^asc_action=SetProductListSortField&field=SORT_BY_PRODUCT_PER_ITEM_HANDLING_COST/'      => 'sort-by-per-item-handling-cost.html',

            '/^asc_action=SetProductListSortField&field=SORT_BY_PRODUCT_WEIGHT,ASC/'  => 'sort-by-weight-asc.html',
            '/^asc_action=SetProductListSortField&field=SORT_BY_PRODUCT_WEIGHT,DESC/' => 'sort-by-weight-desc.html',
            '/^asc_action=SetProductListSortField&field=SORT_BY_PRODUCT_WEIGHT/'      => 'sort-by-weight.html',

        );

        $_r1 = strtr($tpl_category, array('{%page_number%}' =>'1',     '{%category_id%}'=>'(\d+)'));
        $_r2 = strtr($tpl_category, array('{%page_number%}' =>'(\d+)', '{%category_id%}'=>'(\d+)'));
        $_r3 = strtr($tpl_product,  array('{%product_id%}'  =>'(\d+)'));
        $_rl = 'lng([A-Z]{2})-';
        $this->_fRulesToDecode = array (
            # '/^[a-z0-9_\-]*pg1-cid(\d+).html/i'
            '/^[a-zA-Z0-9_\-\%\+]*'.$_rl.$_r1.'/i'  =>  'asc_action=SetCurrCat&category_id={2}&current_language={1}',
            //'/^[a-zA-Z0-9_\-\%\+]*'.$_r1.'/i'  =>  'asc_action=SetCurrCat&category_id={1}',

            # '/^[a-z0-9_\-]*pg(\d+)-cid(\d+).html/i'
            '/^[a-zA-Z0-9_\-\%\+]*'.$_rl.$_r2.'/i'  => 'asc_action=Paginator_SetPage&pgname=Catalog_ProdsList_{3}&pgnum={2}&current_language={1}',
            '/^[a-zA-Z0-9_\-\%\+]*'.$_r2.'/i'  => 'asc_action=Paginator_SetPage&pgname=Catalog_ProdsList_{2}&pgnum={1}',

            # '/^[a-z0-9_\-]*pid(\d+).html/i'
            '/^[a-zA-Z0-9_\-\%\+]*'.$_rl.$_r3.'/i'  => 'asc_action=SetCurrentProduct&prod_id={2}&current_language={1}',
            '/^[a-zA-Z0-9_\-\%\+]*'.$_r3.'/i'  => 'asc_action=SetCurrentProduct&prod_id={1}',

            '/^sort-by-name.html/i'      => 'asc_action=SetProductListSortField&field=SORT_BY_PRODUCT_NAME',
            '/^sort-by-name-desc.html/i' => 'asc_action=SetProductListSortField&field=SORT_BY_PRODUCT_NAME,DESC',
            '/^sort-by-name-asc.html/i'  => 'asc_action=SetProductListSortField&field=SORT_BY_PRODUCT_NAME,ASC',

            '/^sort-by-default.html/i'      => 'asc_action=SetProductListSortField&field=SORT_BY_PRODUCT_SORT_ORDER',
            '/^sort-by-default-asc.html/i'  => 'asc_action=SetProductListSortField&field=SORT_BY_PRODUCT_SORT_ORDER,ASC',
            '/^sort-by-default-desc.html/i' => 'asc_action=SetProductListSortField&field=SORT_BY_PRODUCT_SORT_ORDER,DESC',

            '/^sort-by-price.html/i'      => 'asc_action=SetProductListSortField&field=SORT_BY_PRODUCT_SALE_PRICE',
            '/^sort-by-price-asc.html/i'  => 'asc_action=SetProductListSortField&field=SORT_BY_PRODUCT_SALE_PRICE,ASC',
            '/^sort-by-price-desc.html/i' => 'asc_action=SetProductListSortField&field=SORT_BY_PRODUCT_SALE_PRICE,DESC',

            '/^sort-by-list-price.html/i'      => 'asc_action=SetProductListSortField&field=SORT_BY_PRODUCT_LIST_PRICE',
            '/^sort-by-list-price-asc.html/i'  => 'asc_action=SetProductListSortField&field=SORT_BY_PRODUCT_LIST_PRICE,ASC',
            '/^sort-by-list-price-desc.html/i' => 'asc_action=SetProductListSortField&field=SORT_BY_PRODUCT_LIST_PRICE,DESC',

            '/^sort-by-date-added.html/i'      => 'asc_action=SetProductListSortField&field=SORT_BY_PRODUCT_DATE_ADDED',
            '/^sort-by-date-added-asc.html/i'  => 'asc_action=SetProductListSortField&field=SORT_BY_PRODUCT_DATE_ADDED,ASC',
            '/^sort-by-date-added-desc.html/i' => 'asc_action=SetProductListSortField&field=SORT_BY_PRODUCT_DATE_ADDED,DESC',

            '/^sort-by-date-updated.html/i'      => 'asc_action=SetProductListSortField&field=SORT_BY_PRODUCT_DATE_UPDATED',
            '/^sort-by-date-updated-asc.html/i'  => 'asc_action=SetProductListSortField&field=SORT_BY_PRODUCT_DATE_UPDATED,ASC',
            '/^sort-by-date-updated-desc.html/i' => 'asc_action=SetProductListSortField&field=SORT_BY_PRODUCT_DATE_UPDATED,DESC',

            '/^sort-by-quantity.html/i'      => 'asc_action=SetProductListSortField&field=SORT_BY_PRODUCT_QUANTITY_IN_STOCK',
            '/^sort-by-quantity-asc.html/i'  => 'asc_action=SetProductListSortField&field=SORT_BY_PRODUCT_QUANTITY_IN_STOCK,ASC',
            '/^sort-by-quantity-desc.html/i' => 'asc_action=SetProductListSortField&field=SORT_BY_PRODUCT_QUANTITY_IN_STOCK,DESC',

            '/^sort-by-sku.html/i'      => 'asc_action=SetProductListSortField&field=SORT_BY_PRODUCT_SKU',
            '/^sort-by-sku-asc.html/i'  => 'asc_action=SetProductListSortField&field=SORT_BY_PRODUCT_SKU,ASC',
            '/^sort-by-sku-desc.html/i' => 'asc_action=SetProductListSortField&field=SORT_BY_PRODUCT_SKU,DESC',

            '/^sort-by-per-item-shipping-cost.html/i'      => 'asc_action=SetProductListSortField&field=SORT_BY_PRODUCT_PER_ITEM_SHIPPING_COST',
            '/^sort-by-per-item-shipping-cost-asc.html/i'  => 'asc_action=SetProductListSortField&field=SORT_BY_PRODUCT_PER_ITEM_SHIPPING_COST,ASC',
            '/^sort-by-per-item-shipping-cost-desc.html/i' => 'asc_action=SetProductListSortField&field=SORT_BY_PRODUCT_PER_ITEM_SHIPPING_COST,DESC',

            '/^sort-by-per-item-handling-cost.html/i'      => 'asc_action=SetProductListSortField&field=SORT_BY_PRODUCT_PER_ITEM_HANDLING_COST',
            '/^sort-by-per-item-handling-cost-asc.html/i'  => 'asc_action=SetProductListSortField&field=SORT_BY_PRODUCT_PER_ITEM_HANDLING_COST,ASC',
            '/^sort-by-per-item-handling-cost-desc.html/i' => 'asc_action=SetProductListSortField&field=SORT_BY_PRODUCT_PER_ITEM_HANDLING_COST,DESC',

            '/^sort-by-weight.html/i'      => 'asc_action=SetProductListSortField&field=SORT_BY_PRODUCT_WEIGHT',
            '/^sort-by-weight-asc.html/i'  => 'asc_action=SetProductListSortField&field=SORT_BY_PRODUCT_WEIGHT,ASC',
            '/^sort-by-weight-desc.html/i' => 'asc_action=SetProductListSortField&field=SORT_BY_PRODUCT_WEIGHT,DESC',

            );
    }

    var $_fURL;
    var $_fProcessedURL;
    var $_fRulesToEncode = array();
    var $_fRulesToDecode = array();

    /**#@-*/
}

?>