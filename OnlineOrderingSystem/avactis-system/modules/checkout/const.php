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
 * Checkout module.
 *
 * @package Checkout
 * @access  public
 */

define('ORDERS_INFO_SIMPLE_FORM', 1);
define('ORDERS_INFO_ADVANCED_FORM', 2);
define('SYMBOLIC_TAX_VALUE', -1);
define('TAX_NOT_LINEAR', -2);

define('ORDER_STATUS_ALL',100);
define('ORDER_STATUS_NOT_CREATED_YET',-1);
define('ORDER_STATUS_NEW',1);
define('ORDER_STATUS_IN_PROGRESS',2);
define('ORDER_STATUS_READY_TO_SHIP',3);
define('ORDER_STATUS_SHIPPED',4);
define('ORDER_STATUS_CANCELLED',5);
define('ORDER_STATUS_DECLINED',6);
define('ORDER_STATUS_COMPLETED',7);
define('ORDER_STATUS_DELETED',8);

define('ORDER_PAYMENT_STATUS_ALL',100);
define('ORDER_PAYMENT_STATUS_WAITING',1);
define('ORDER_PAYMENT_STATUS_FULLY_PAID',2);
define('ORDER_PAYMENT_STATUS_DECLINED',3);

define('PERSON_INFO_TYPE_PAYMENT_MODULE_ID', 6);
define('PERSON_INFO_TYPE_SHIPPING_MODULE_AND_METHOD_ID', 7);
define('PERSON_INFO_TYPE_STORE_OWNER_INFO_ID', 8);

define('CHECKOUT_POST_DATA_NOT_EMULATED', 1);
define('CHECKOUT_POST_DATA_EMULATED', 2);

define('ORDER_NOT_CREATED_YET', 'ORDER_NOT_CREATED_YET');

//                                                          .
define('ACCEPTED', 'ACCEPTED');
//                                                             .
define('NOT_ACCEPTED', 'NOT_ACCEPTED');
//                                                                 .
define('THE_ONE_ONLY_ACCEPTED', 'THE_ONE_ONLY_ACCEPTED');

//                                    ,                     ,                                 AZ => Accepted Currencies.
define('ACTIVE_AND_SELECTED_BY_CUSTOMER', 'ACTIVE_AND_SELECTED_BY_CUSTOMER');
//                                    ,                     ,                                 AZ => Accepted Currencies
//                                           .
define('ACTIVE_AND_ACCEPTED_BY_PM_SM_AND_SELECTED_BY_CUSTOMER', 'ACTIVE_AND_ACCEPTED_BY_PM_SM_AND_SELECTED_BY_CUSTOMER');
//                                     ,                "the only accepted"                               .
define('THE_ONLY_ACCEPTED', 'THE_ONLY_ACCEPTED');
//                               main store currency.
define('MAIN_STORE_CURRENCY', 'MAIN_STORE_CURRENCY');

define('DEFAULT_CURRENCY_ACCEPTANCE_RULE_NAME', MAIN_STORE_CURRENCY);

define('GET_PAYMENT_MODULE_FROM_ORDER', 'GET_PAYMENT_MODULE_FROM_ORDER');
define('GET_SHIPPING_MODULE_FROM_ORDER', 'GET_SHIPPING_MODULE_FROM_ORDER');

define('CURRENCY_TYPE_MAIN_STORE_CURRENCY', 'CURRENCY_TYPE_MAIN_STORE_CURRENCY');
define('CURRENCY_TYPE_CUSTOMER_SELECTED', 'CURRENCY_TYPE_CUSTOMER_SELECTED');
define('CURRENCY_TYPE_PAYMENT_GATEWAY', 'CURRENCY_TYPE_PAYMENT_GATEWAY');

// These constants are used in getPersonInfoAttributeIdList (CheckoutAZ class)
define('CUSTOM_ATTRIBUTES_ONLY', 'CUSTOM_ATTRIBUTES_ONLY');
define('STANDARD_ATTRIBUTES_ONLY', 'STANDARD_ATTRIBUTES_ONLY');
define('ALL_ATTRIBUTES', 'ALL_ATTRIBUTES');

define('CUSTOM_FIELD_TYPE_TEXT', 'CUSTOM_FIELD_TYPE_TEXT');
define('CUSTOM_FIELD_TYPE_CHECKBOX', 'CUSTOM_FIELD_TYPE_CHECKBOX');
define('CUSTOM_FIELD_TYPE_SELECT', 'CUSTOM_FIELD_TYPE_SELECT');
define('CUSTOM_FIELD_TYPE_TEXTAREA', 'CUSTOM_FIELD_TYPE_TEXTAREA');
define('CUSTOM_FIELD_TYPE_STANDARD', 'CUSTOM_FIELD_TYPE_STANDARD'); # for standard checkout fields

define('CUSTOM_FIELD_TAG_NAME_PREFIX','CUSTOM_FIELD_');

define('DBQUERY_FIELD_TYPE_CURRENCY_TYPE', "ENUM "
   ."('". CURRENCY_TYPE_MAIN_STORE_CURRENCY. "',"
    ."'". CURRENCY_TYPE_CUSTOMER_SELECTED. "',"
    ."'". CURRENCY_TYPE_PAYMENT_GATEWAY. "') " ." NOT NULL");

define('DBQUERY_FIELD_CUSTOM_TYPE', "ENUM "
   ."('". CUSTOM_FIELD_TYPE_TEXT. "',"
    ."'". CUSTOM_FIELD_TYPE_CHECKBOX. "',"
    ."'". CUSTOM_FIELD_TYPE_SELECT. "',"
    ."'". CUSTOM_FIELD_TYPE_STANDARD. "',"
    ."'". CUSTOM_FIELD_TYPE_TEXTAREA. "') " ." NOT NULL");
?>