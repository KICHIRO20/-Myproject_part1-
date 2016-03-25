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
 * @package Localization
 * @access  public
 */

//USD
define('USD_CURRENCY_ID', 289);

define('DEFAULT_CURRENCY_ID', USD_CURRENCY_ID);


define('DEFAULT_CURRENCY_SIGN', "$");
define('DEFAULT_CURRENCY_CODE', "USD");
define('DEFAULT_CURRENCY_FORMAT', "2|.|,|");
define('DEFAULT_CURRENCY_POSITIVE_FORMAT', "{s}{v}");
define('DEFAULT_CURRENCY_NEGATIVE_FORMAT', "-{s}{v}");
define('DEFAULT_CURRENCY_PATTERN', '/^\d*\.\d{2}$/');

define('RESOURCE_NOT_DEFINED', '???');

define("ALL_OTHER_STATES_STATE_ID", -1);
define("STATE_UNDEFINED_STATE_ID", -2);
define("ALL_OTHER_COUNTRIES_COUNTRY_ID", -3);
define("SELECT_COUNTRY_DEFAULT_COUNTRY_ID", 223); /* USA */

define("STORE_CURRENCIES_CANNOT_ADD_MAIN_AS_ADDITIONAL",    1);
define("STORE_CURRENCIES_CANNOT_ADD_DUPLICATE",             2);
define("STORE_CURRENCIES_INVALID_MANUAL_RATE_ERROR",        3);
define("STORE_CURRENCIES_CANNOT_OBTAIN_NEW_RATE_FROM_WEB",  4);

?>