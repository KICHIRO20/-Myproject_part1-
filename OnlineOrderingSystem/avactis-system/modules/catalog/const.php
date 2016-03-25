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
 * Catalog module.
 *
 * @package Catalog
 * @access  public
 */

define('PRODUCT_FREESHIPPING_YES', 1);
define('PRODUCT_FREESHIPPING_NO', 2);
define('CATEGORY_STATUS_ONLINE', 1);
define('CATEGORY_STATUS_OFFLINE', 2);
define('PRODUCT_STATUS_ONLINE', 3);
define('PRODUCT_STATUS_OFFLINE', 4);
define('HOME_CATEGORY_ID', 1);

define('PRODUCT_CUSTOMER_REVIEWS_MESSAGE_RATE', 5);
define('PRODUCT_CUSTOMER_REVIEWS_MESSAGE', 6);
define('PRODUCT_CUSTOMER_REVIEWS_RATE', 7);
define('PRODUCT_CUSTOMER_REVIEWS_NOREVIEW', 8);

define('CATEGORY_SHOW_PRODUCTS_RECURSIVELY',0);
define('CATEGORY_DONOTSHOW_PRODUCTS_RECURSIVELY',1);

define('CATALOG_INSTALL_DATA_XML',dirname(__FILE__).'/includes/install_data.xml');

define('SALE_PRICE_PRODUCT_ATTRIBUTE_ID',               1);
define('LIST_PRICE_PRODUCT_ATTRIBUTE_ID',               2);
define('QUANTITY_IN_STOCK_PRICE_PRODUCT_ATTRIBUTE_ID',  3);
define('SKU_PRODUCT_ATTRIBUTE_ID',                      4);
define('MIN_QUANITY_PRODUCT_ATTRIBUTE_ID',              5);
define('LOW_STOCK_LEVEL_PRODUCT_ATTRIBUTE_ID',          6);
define('AVAILABLE_PRODUCT_ATTRIBUTE_ID',                7);
define('TAX_CLASS_PRODUCT_ATTRIBUTE_ID',                8);
define('LARGE_IMAGE_PRODUCT_ATTRIBUTE_ID',              9);
define('SMALL_IMAGE_PRODUCT_ATTRIBUTE_ID',              10);
define('IMAGE_ALT_TEXT_PRODUCT_ATTRIBUTE_ID',           11);
define('SHORT_DESCRIPTION_PRODUCT_ATTRIBUTE_ID',        12);
define('DETAILED_DESCRIPTION_PRODUCT_ATTRIBUTE_ID',     13);
define('PER_ITEM_SHIPPING_COST_PRODUCT_ATTRIBUTE_ID',   14);
define('WEIGHT_PRODUCT_ATTRIBUTE_ID',                   15);
define('PAGE_TITLE_PRODUCT_ATTRIBUTE_ID',               17);
define('META_KEYWORDS_PRODUCT_ATTRIBUTE_ID',            18);
define('META_DESCRIPTION_PRODUCT_ATTRIBUTE_ID',         19);
define('PER_ITEM_HANDLING_COST_PRODUCT_ATTRIBUTE_ID',   20);
define('FREE_SHIPPING_PRODUCT_ATTRIBUTE_ID',            21);
define('NEED_SHIPPING_PRODUCT_ATTRIBUTE_ID',            22);
define('SEO_PREFIX_PRODUCT_ATTRIBUTE_ID',               23);
define('MANUFACTURER_PRODUCT_ATTRIBUTE_ID',             24);
define('CUSTOMER_REVIEWS_PRODUCT_ATTRIBUTE_ID',         25);
define('MEMBERSHIP_VISIBILITY_PRODUCT_ATTRIBUTE_ID',    26);
define('UPC_PRODUCT_ATTRIBUTE_ID', getProductAttributeId('UPC', 27));
define('EAN_PRODUCT_ATTRIBUTE_ID', getProductAttributeId('EAN', 28));
define('JAN_PRODUCT_ATTRIBUTE_ID', getProductAttributeId('JAN', 29));
define('ISBN_PRODUCT_ATTRIBUTE_ID', getProductAttributeId('ISBN', 30));
define('MPN_PRODUCT_ATTRIBUTE_ID', getProductAttributeId('MPN', 31));


define('CTLG_INPUT_TYPE_MANUFACTURER', 8);

define('SEARCH_ALL_VALUES','SEARCH_ALL_VALUES');
define('SEARCH_ANY_VALUES','SEARCH_ANY_VALUES');

define('SORT_BY_PRODUCT_ID',                    'SORT_BY_PRODUCT_ID');
define('SORT_BY_PRODUCT_NAME',                  'SORT_BY_PRODUCT_NAME');
define('SORT_BY_PRODUCT_DATE_ADDED',            'SORT_BY_PRODUCT_DATE_ADDED');
define('SORT_BY_PRODUCT_DATE_UPDATED',          'SORT_BY_PRODUCT_DATE_UPDATED');
define('SORT_BY_PRODUCT_SALE_PRICE',            'SORT_BY_PRODUCT_SALE_PRICE');
define('SORT_BY_PRODUCT_LIST_PRICE',            'SORT_BY_PRODUCT_LIST_PRICE');
define('SORT_BY_PRODUCT_QUANTITY_IN_STOCK',     'SORT_BY_PRODUCT_QUANTITY_IN_STOCK');
define('SORT_BY_PRODUCT_SKU',                   'SORT_BY_PRODUCT_SKU');
define('SORT_BY_PRODUCT_PER_ITEM_SHIPPING_COST','SORT_BY_PRODUCT_PER_ITEM_SHIPPING_COST');
define('SORT_BY_PRODUCT_PER_ITEM_HANDLING_COST','SORT_BY_PRODUCT_PER_ITEM_HANDLING_COST');
define('SORT_BY_PRODUCT_WEIGHT',                'SORT_BY_PRODUCT_WEIGHT');
define('SORT_BY_PRODUCT_SORT_ORDER',            'SORT_BY_PRODUCT_SORT_ORDER');
define('SORT_BY_RAND' ,'SORT_BY_RAND');

define('SORT_DIRECTION_DESC', 'DESC');
define('SORT_DIRECTION_ASC', 'ASC');

define('RETURN_AS_ID_LIST','RETURN_AS_ID_LIST');
define('RETURN_AS_ID_OBJECT_LIST','RETURN_AS_ID_OBJECT_LIST');
define('RETURN_AS_CODE_LIST','RETURN_AS_CODE_LIST');
define('RETURN_AS_CODE_OBJECT_LIST','RETURN_AS_CODE_OBJECT_LIST');
define('RETURN_AS_OBJECT_LIST','RETURN_AS_OBJECT_LIST');

define('PAGINATOR_ENABLE', 'PAGINATOR_ENABLE');
define('PAGINATOR_DISABLE', 'PAGINATOR_DISABLE');

define ('CATEGORYINFO_DEFAULT_LOCALIZED_MODE', 0);
define ('CATEGORYINFO_LOCALIZED_DATA', 1);
define ('CATEGORYINFO_NOT_LOCALIZED_DATA', 2);

define ('IN_CATEGORY_ONLY', 3);
define ('IN_CATEGORY_RECURSIVELY', 4);
define ('IN_CATEGORY_RECURSIVELY_WITHOUT_CURRENT', 5);

define ('IMAGE_SMALL', 6);
define ('IMAGE_LARGE', 7);


define('UNIQUE_PRODUCTS', 'UNIQUE_PRODUCTS');
define('ALL_PRODUCT_LINKS', 'ALL_PRODUCT_LINKS');

define('PRODUCT_QUANTITY_IN_STOCK_ATTRIBUTE', 'PRODUCT_QUANTITY_IN_STOCK_ATTRIBUTE');
define('PRODUCT_OPTIONS_INVENTORY_TRACKING', 'PRODUCT_OPTIONS_INVENTORY_TRACKING');

define('GC_PRODUCT_ID', '170');
define('GC_PRODUCT_TYPE_ID', '-1');

define('GPC_NOT_SELECTED', 9);
define('GPC_ACCESSORIES_CLOTHING', 10);
define('GPC_ACCESSORIES_SHOES', 11);
define('GPC_ACCESSORIES', 12);
define('GPC_MEDIA_BOOKS', 13);
define('GPC_MEDIA_DVD', 14);
define('GPC_MEDIA_MUSIC', 15);
define('GPC_SOFTWARE_VIDEO_GAME', 16);

define('G_AGE_GROUP_NOT_SELECTED', 17);
define('G_AGE_GROUP_ADULT', 18);
define('G_AGE_GROUP_KIDS', 19);

define('G_GENDER_NOT_SELECTED', 20);
define('G_GENDER_MALE', 21);
define('G_GENDER_FEMALE', 22);
define('G_GENDER_UNISEX', 23);

?>