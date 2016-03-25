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
 * Configuration module, constants.
 *
 * @package Configuration
 * @author Alexey Florinsky
 */

define('SYSCONFIG_STORE_ONLINE', 'store_online');
define('SYSCONFIG_STORE_OFFLINE_KEY', 'store_offline_key');
define('SYSCONFIG_STORE_SHOW_ABSENT', 'store_show_absent');
define('SYSCONFIG_STORE_ALLOW_BUY_MORE_THAN_STOCK', 'store_allow_buy_more_than_stock');
define('SYSCONFIG_STORE_RETURN_PRODUCT_TO_STOCK_ORDER_DELETED', 'store_return_product_to_stock_order_deleted');
define('SYSCONFIG_STORE_RETURN_PRODUCT_TO_STOCK_ORDER_CANCELLED', 'store_return_product_to_stock_order_cancelled');
define('SYSCONFIG_STORE_ENABLE_WISHLIST', 'store_enable_wishlist');
define('SYSCONFIG_STORE_PM_OFFLINE_CC_JAVASCRIPT', 'store_pm_offline_cc_javascript');
//define('SYSCONFIG_STORE_ORDER_ABSENT', 'store_order_absent');
define('SYSCONFIG_STORE_TIME_SHIFT', 'store_time_shift');
define('SYSCONFIG_STORE_SIGNIN_COUNT', 'store_signin_count');
define('SYSCONFIG_STORE_SIGNIN_TIMEOUT', 'store_signin_timeout');
define('SYSCONFIG_STORE_SHOW_CART', 'store_show_cart');
define('SYSCONFIG_MIN_SUBTOTAL_TO_BEGIN_CHECKOUT', 'min_subtotal_to_begin_checkout');
define('ZERO_PRICE', 0.00);

define('SYSCONFIG_STORE_PAGINATOR_DEFAULT_ROWS_PER_PAGE_AZ', 'paginator_default_rows_per_page_az');
define('SYSCONFIG_STORE_PAGINATOR_PAGES_PER_LINE_AZ', 'paginator_pages_per_line_az');
define('SYSCONFIG_STORE_PAGINATOR_ROWS_PER_PAGE_VALUES_AZ', 'paginator_rows_per_page_values_az');

define('SYSCONFIG_STORE_PAGINATOR_DEFAULT_ROWS_PER_PAGE_CZ', 'paginator_default_rows_per_page_cz');
define('SYSCONFIG_STORE_PAGINATOR_PAGES_PER_LINE_CZ', 'paginator_pages_per_line_cz');
define('SYSCONFIG_STORE_PAGINATOR_ROWS_PER_PAGE_VALUES_CZ', 'paginator_rows_per_page_values_cz');

define('SYSCONFIG_STORE_ADD_TO_CART_DEFAULT_QUANTITY', 'add_to_cart_default_quantity');
define('SYSCONFIG_STORE_ADD_TO_CART_MAX_QUANTITY', 'add_to_cart_max_quantity');
define('SYSCONFIG_STORE_ADD_TO_CART_LIMIT_MAX_QUANTITY_BY_STOCK', 'add_to_cart_limit_max_quantity_by_stock');
define('SYSCONFIG_STORE_ADD_TO_CART_ADD_NOT_REPLACE', 'add_to_cart_add_not_replace');

define('SYSCONFIG_STORE_NEXT_ORDER_ID', 'store_next_order_id');
//define('SYSCONFIG_STORE_DISPLAY_PRODUCT_PRICE_INCLUDING_TAXES', 'store_display_product_price_including_taxes');
//define('SYSCONFIG_STORE_DISPLAY_TOTALS_INCLUDING_TAXES', 'store_display_totals_including_taxes');

define('SYSCONFIG_STORE_OWNER_NAME', 'store_owner_name');
define('SYSCONFIG_STORE_OWNER_WEBSITE', 'store_owner_website');
define('SYSCONFIG_STORE_OWNER_PHONES', 'store_owner_phones');
define('SYSCONFIG_STORE_OWNER_FAX', 'store_owner_fax');
define('SYSCONFIG_STORE_OWNER_STREET_LINE_1', 'store_owner_street_line_1');
define('SYSCONFIG_STORE_OWNER_STREET_LINE_2', 'store_owner_street_line_2');
define('SYSCONFIG_STORE_OWNER_CITY', 'store_owner_city');
define('SYSCONFIG_STORE_OWNER_STATE', 'store_owner_state');
define('SYSCONFIG_STORE_OWNER_POSTCODE', 'store_owner_postcode');
define('SYSCONFIG_STORE_OWNER_COUNTRY', 'store_owner_country');
define('SYSCONFIG_STORE_OWNER_EMAIL', 'store_owner_email');
define('SYSCONFIG_STORE_OWNER_EMAIL_FROM', 'store_owner_email_from');
define('SYSCONFIG_STORE_OWNER_SITE_ADMINISTRATOR_EMAIL', 'store_owner_site_administrator_email');
define('SYSCONFIG_STORE_OWNER_SITE_ADMINISTRATOR_EMAIL_FROM', 'store_owner_site_administrator_email_from');
define('SYSCONFIG_STORE_OWNER_ORDERS_DEPARTMENT_EMAIL', 'store_owner_orders_department_email');
define('SYSCONFIG_STORE_OWNER_ORDERS_DEPARTMENT_EMAIL_FROM', 'store_owner_orders_department_email_from');
define('SYSCONFIG_STORE_OWNER_PAGE_TITLE', 'store_owner_page_title');

define('SYSCONFIG_CACHE_LEVEL', 'store_cache_level');

define('SYSCONFIG_DB_VERSION', 'db_version');

define('STORE_SHOW_ABSENT_SHOW_BUY', 1);
define('STORE_SHOW_ABSENT_SHOW_NOT_BUY', 2);
define('STORE_SHOW_ABSENT_NOT_SHOW_NOT_BUY', 3);

define('SYSCONFIG_RP_PER_LINE',         'rp_per_line');
define('SYSCONFIG_RP_RANDOM_CHECKBOX',  'rp_random_checkbox');
define('SYSCONFIG_RP_RANDOM_THRESHOLD', 'rp_random_threshold');
define('SYSCONFIG_FP_PER_LINE',         'fp_per_line');
define('SYSCONFIG_FP_RANDOM_CHECKBOX',  'fp_random_checkbox');
define('SYSCONFIG_FP_RANDOM_THRESHOLD', 'fp_random_threshold');
define('SYSCONFIG_BS_PER_LINE',         'bs_per_line');
define('SYSCONFIG_BS_RANDOM_CHECKBOX',  'bs_random_checkbox');
define('SYSCONFIG_BS_RANDOM_THRESHOLD', 'bs_random_threshold');

define('SYSCONFIG_NEWSLETTERS_SIGNATURE', 'newsletters_signature');

define('SYSCONFIG_CHECKOUT_FORM_HASH', 'checkout_form_hash');

define('SETTINGS_WITH_DESCRIPTION',    'SETTINGS_WITH_DESCRIPTION');
define('SETTINGS_WITHOUT_DESCRIPTION', 'SETTINGS_WITHOUT_DESCRIPTION');

define('SETTIGS_POST_MAP_NAME','ApplicationSettingsMap');

define('PARAM_TYPE_INT',    'INT');
define('PARAM_TYPE_STRING', 'STRING');
define('PARAM_TYPE_FLOAT',  'FLOAT');
define('PARAM_TYPE_LIST',   'LIST');

define('DBQUERY_FIELD_TYPE_ENUM_PARAM_TYPES', "ENUM (   '".PARAM_TYPE_INT."',
                                                        '".PARAM_TYPE_FLOAT."',
                                                        '".PARAM_TYPE_STRING."',
                                                        '".PARAM_TYPE_LIST."')" );

/**
 * Support mode flags
 */
define('ASC_S_ALL', 15);           #Enable all support options
define('ASC_S_STATISTICS', 1);     #Disable Statistics
define('ASC_S_NOTIFICATIONS', 2);  #Disable Notifications
define('ASC_S_DISPLAY_ERRORS', 4); #Display Errors - On
define('ASC_S_LOG_ERRORS', 8);     #Log errors - On
define('ASC_S_DISABLE', 0);        #Disable support mode
?>