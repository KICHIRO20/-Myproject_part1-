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
 * @package Taxes
 * @access  public
 */


define('TAXES_STORE_OWNER_ADDRESS_ID', 3);
define('TAXES_TAX_NAME_DOES_NOT_NEED_ADDRESS_ID', 1025);
define('TAXES_COUNTRY_NOT_NEEDED_ID', -1025);
define('TAXES_STATE_NOT_NEEDED_ID', -1025);
define("TAXES_SPECIFIC_RATE_BASED_ON_ZIP", -2000000007);

define('TAXES_STATE_NOT_SET', 2007112118190);
define('TAXES_STATE_EMPTY', 2007112118191);
define('STATE_ID_ALL', 0);

define('TAX_COST_DISCOUNT', 3);
define('TAX_COST_SHIPPING', 2);

//                            .                              .       :
//  $OrderLevelDiscount                           (      )        .
//                                            .
//                       ,                                      .
define('COMPUTATION_POSTPONED', 'COMPUTATION_POSTPONED');
?>