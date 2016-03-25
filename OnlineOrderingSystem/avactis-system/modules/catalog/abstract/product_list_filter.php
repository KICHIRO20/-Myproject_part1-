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

define('SYNC_CATEGORY_ID_CHANGED','SYNC_CATEGORY_ID_CHANGED');
define('SYNC_MANUFACTURER_ID_CHANGED', 'SYNC_MANUFACTURER_ID_CHANGED');
define('SYNC_SORT_FIELD_CHANGED', 'SYNC_SORT_FIELD_CHANGED');
define('SYNC_MIN_MAX_PRICE_CHANGED', 'SYNC_MIN_MAX_PRICE_CHANGED');

/**
 *                                  :
 * -                  PRODUCT_LIST_PARAMS
 *               ,                                   . .
 * -                                                             ,
 *            ,                                                    .
 *
 */
class CProductListFilter
{
    var $product_list_params_obj = null;
    var $forced_mnf_id = 0;

    function getMnfPaginatorName()
    {
        if ($this -> forced_mnf_id)
            return 'Manufacturer_ProdsList_' . $this -> forced_mnf_id;

        return '';
    }

    /**
     *                     $product_list_params_obj
     *        PRODUCT_LIST_PARAMS   default                              .
     *
     *                                               $product_list_params_obj
     *                              .                             $product_list_params_obj
     *                 default                                                          ,
     *                                  . .
     *
     *    22        2009, Default                                                  :
     * -                           HOME                       ,
     * -                      - SORT_BY_PRODUCT_SORT_ORDER,  . .   ,                  AZ
     * -               ,  . .
     * -                  Customer Zone,                   online          ,
     * -                  Customer Zone,
     *                        HOME,  . .                         ,
     *             ,                                   ,
     * -                  Customer Zone     General Settings        ,
     *                     ,                                           :           Quantity
     *                                           ,                                                    ,
     *                                .
     *
     * @return CProductListFilter
     */
    function CProductListFilter()
    {
        global $application;
        $this->product_list_params_obj = new PRODUCT_LIST_PARAMS();
        $this->product_list_params_obj->category_id = 1;
        $this->product_list_params_obj->select_mode_recursiveness = IN_CATEGORY_ONLY;

        if (modApiFunc('Users', 'getZone') == "CustomerZone")
        {
            $this->product_list_params_obj->membership_filter = true;
            //                                           ,
            // Bestsellers, Related Products, Featured Products, Random Set
            $product_type_filter = modApiFunc('Catalog', 'getCurrentProductTypeFilter');
            if (is_array($product_type_filter) && !empty($product_type_filter))
            {
                $this->product_list_params_obj->filter_product_type_id_list = $product_type_filter;
            }
            $this->product_list_params_obj->select_online_products_only = true;
            if (modApiFunc("Configuration", "getValue", SYSCONFIG_STORE_SHOW_ABSENT) == STORE_SHOW_ABSENT_NOT_SHOW_NOT_BUY)
            {
                $this->product_list_params_obj->filter_stock_level_min = 1;
            }

            $sort_by_from_config = $application->getAppIni('PRODUCT_LIST_SORTER_DEFAULT');
            $this->product_list_params_obj->sort_by = $sort_by_from_config;

            //                                  Storefront
            loadClass('CCategoryInfo');
            $cat = &$application->getInstance('CCategoryInfo', 1);
            if ($cat->getShowProductsRecursivelyStatus() == CATEGORY_SHOW_PRODUCTS_RECURSIVELY)
            {
                $this->product_list_params_obj->select_mode_recursiveness = IN_CATEGORY_RECURSIVELY;
            }
        }
        else
        {
            $this->product_list_params_obj->sort_direction = 'ASC';
        }
    }

    function saveState()
    {
        $data = serialize($this->product_list_params_obj);
        modApiFunc('Session', 'set', 'ProductListFilterSerializedData', $data);
    }

    function loadState()
    {
        if(modApiFunc('Session', 'is_Set', 'ProductListFilterSerializedData'))
        {
            $this->product_list_params_obj = unserialize(modApiFunc('Session', 'get', 'ProductListFilterSerializedData'));

            if (modApiFunc('Users', 'getZone') == "CustomerZone")
            {
                //                                  -
                if (modApiFunc("Configuration", "getValue", SYSCONFIG_STORE_SHOW_ABSENT) == STORE_SHOW_ABSENT_NOT_SHOW_NOT_BUY)
                {
                    $this->product_list_params_obj->filter_stock_level_min = 1;
                }
                else
                {
                    $this->product_list_params_obj->filter_stock_level_min = null;
                }
                $this->product_list_params_obj->select_online_products_only = true;
            }
        }
    }

    /**
     *                        .
     *
     *                                         id          ,             CustomerZone            ,
     *                 online.                                                          .
     *
     *                         id          ,                  true,       false,
     *
     * @param int $id Category Id
     * @return bool Return true if category id changed, else return false
     */
    function changeCurrentCategoryId($id)
    {
        global $application;
	if ($this->__isCategoryIdChangingAllowed($id) && $this->product_list_params_obj->category_id != $id)
        {
            //           ,       id
            $this->product_list_params_obj->category_id = $id;

            //                                  Storefront
            if (modApiFunc('Users', 'getZone') == "CustomerZone")
            {
                $cat = &$application->getInstance('CCategoryInfo', $id);
                if ($cat->getShowProductsRecursivelyStatus() == CATEGORY_SHOW_PRODUCTS_RECURSIVELY)
                {
                    $this->product_list_params_obj->select_mode_recursiveness = IN_CATEGORY_RECURSIVELY;
                }
                else
                {
                    $this->product_list_params_obj->select_mode_recursiveness = IN_CATEGORY_ONLY;
                }
            }
            $this->product_list_params_obj->select_mode_uniqueness = UNIQUE_PRODUCTS;

            //                                            -
            $this->__runSynchronization(SYNC_CATEGORY_ID_CHANGED);
            return true;
        }
        return false;
    }

    function getCurrentCategoryId()
    {
        return $this->product_list_params_obj->category_id;
    }

    function changeCurrentManufactureId($id, $force_filter_by_mnf = false)
    {
        if ($this->__isManufacturerIdChangingAllowed($id))
        {
            if ($force_filter_by_mnf)
                $this -> forced_mnf_id = $id;

            $setting = modApiFunc('Settings', 'getParamValue','CATALOG_NAVIGATION','CTL_NAV_MANUFACTURER_FILTER');

            if ($setting == "WHOLE_CATALOG")
            {
                $this->product_list_params_obj->select_mode_recursiveness = IN_CATEGORY_RECURSIVELY;
                $this->product_list_params_obj->category_id = 1;
            }
            $this->product_list_params_obj->filter_manufacturer_id_list = array($id);
            $this->__runSynchronization(SYNC_MANUFACTURER_ID_CHANGED);

            return true;
        }
        return false;
    }

    function changePriceFilter($from, $to)
    {
		    $this->product_list_params_obj->filter_sale_price_min = $from;
		    $this->product_list_params_obj->filter_sale_price_max = $to;
		    $this->__runSynchronization(SYNC_MIN_MAX_PRICE_CHANGED);
    }

    function changeCurrentProductTypeFilterIds($ids)
    {
        $this->product_list_params_obj->filter_product_type_id_list = null;
        if (is_array($ids) && !empty($ids))
        {
            $this->product_list_params_obj->filter_product_type_id_list = $ids;
        }
    }

    function getCurrentManufactureId()
    {
        if ( $this->product_list_params_obj->filter_manufacturer_id_list !== null &&
             is_array($this->product_list_params_obj->filter_manufacturer_id_list) )
        {
            return $this->product_list_params_obj->filter_manufacturer_id_list[0];
        }
        else
        {
            return null;
        }
    }

    function resetManufacturerId()
    {
        $this->product_list_params_obj->filter_manufacturer_id_list = null;
    }

    function changeCurrentMinSalePrice($min)
    {
        $this->product_list_params_obj->filter_sale_price_min = $min;
    }

    function getCurrentMinSalePrice()
    {
        return $this->product_list_params_obj->filter_sale_price_min;
    }

    function changeCurrentMaxSalePrice($max)
    {
        $this->product_list_params_obj->filter_sale_price_max = $max;
    }

    function getCurrentMaxSalePrice()
    {
        return $this->product_list_params_obj->filter_sale_price_max;
    }

    function changeCurrentSortField($field, $direction = SORT_DIRECTION_ASC)
    {
        $available_fields = array(  SORT_BY_PRODUCT_DATE_ADDED,
                                    SORT_BY_PRODUCT_DATE_UPDATED,
                                    SORT_BY_PRODUCT_LIST_PRICE,
                                    SORT_BY_PRODUCT_NAME,
                                    SORT_BY_PRODUCT_PER_ITEM_HANDLING_COST,
                                    SORT_BY_PRODUCT_PER_ITEM_SHIPPING_COST,
                                    SORT_BY_PRODUCT_QUANTITY_IN_STOCK,
                                    SORT_BY_PRODUCT_SALE_PRICE,
                                    SORT_BY_PRODUCT_SKU,
                                    SORT_BY_PRODUCT_SORT_ORDER,
                                    SORT_BY_PRODUCT_WEIGHT);

        if ( in_array($field, $available_fields) and
             in_array($direction, array(SORT_DIRECTION_ASC, SORT_DIRECTION_DESC)) )
        {
            $this->product_list_params_obj->sort_by = $field;
            $this->product_list_params_obj->sort_direction = $direction;
            $this->__runSynchronization(SYNC_SORT_FIELD_CHANGED);
            return true;
        }
        return false;
    }

    function getCurrentSortField()
    {
        return array($this->product_list_params_obj->sort_by, $this->product_list_params_obj->sort_direction);
    }

    function getProductListParamsObject()
    {
        return clone($this->product_list_params_obj);
    }

    function enableSynchronization()
    {
        $this->__synchronization = true;
    }

    function disableSynchronization()
    {
        $this->__synchronization = false;
    }


    /**************************************************
                        PRIVATE
     **************************************************/

    function __isCategoryIdChangingAllowed($cid)
    {
        global $application;
        // id                                 (            )
        if (modApiFunc('Catalog','isCorrectCategoryId',$cid))
        {
            //                 storefront -          ,                  Offline:
            if (modApiFunc('Users', 'getZone') == "CustomerZone")
            {
                $cat = &$application->getInstance('CCategoryInfo', $cid);
                //                       online
                if( $cat->getCategoryTagValue('RecursiveStatus') == CATEGORY_STATUS_OFFLINE)
                {
                    return false;
                }
            }
            return true;
        }
        return false;
    }

    function __isManufacturerIdChangingAllowed($mnf_id)
    {
        if (modApiFunc('Catalog', 'isCorrectManufacturerId', $mnf_id))
        {
            //                 storefront -          ,                     Inactive:
            if (modApiFunc('Users', 'getZone') == "CustomerZone")
            {
                $mnf = modApiFunc("Manufacturers", "getManufacturerInfo", $mnf_id);
                if($mnf['manufacturer_active'] == DB_FALSE)
                {
                    return false;
                }
            }
            return true;
        }
        return false;
    }

    function __runSynchronization($event)
    {
        if ($this->__synchronization === false)
        {
            return;
        }

        switch ($event)
        {
            case SYNC_CATEGORY_ID_CHANGED:
                //                :                                                                .
                modApiFunc("Paginator", "resetPaginators");
                $this->product_list_params_obj->filter_manufacturer_id_list = null;
		$this->product_list_params_obj->filter_sale_price_min = null;
		$this->product_list_params_obj->filter_sale_price_max = null;
                break;

            case SYNC_MANUFACTURER_ID_CHANGED:
                //                    :                         .
		$this->product_list_params_obj->filter_sale_price_min = null;
		$this->product_list_params_obj->filter_sale_price_max = null;
		modApiFunc("Paginator", "resetPaginators");
		break;

	    case SYNC_MIN_MAX_PRICE_CHANGED:
		//
		modApiFunc("Paginator", "resetPaginators");
		break;

            case SYNC_SORT_FIELD_CHANGED:
                //
                break;

        }
    }

    var $__synchronization = true;

}

?>