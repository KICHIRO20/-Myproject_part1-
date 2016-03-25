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
 * PromoCodes module.
 *
 * @package PromoCodes
 * @access  public
 */


define('PROMO_CODE_NOT_APPLICABLE_DATE', 1);
define('PROMO_CODE_NOT_APPLICABLE_TIMES_USED', 2);
define('PROMO_CODE_NOT_APPLICABLE_STATUS', 3);
define('PROMO_CODE_NOT_APPLICABLE_AREA', 4);

define('PROMO_CODE_NO_ATTENTION_TO_FREE_SHIPPING', '0');
define('PROMO_CODE_GRANTS_FREE_SHIPPING', '1');
define('PROMO_CODE_FORBIDS_FREE_SHIPPING', '2');

define('PROMO_CODE_NO_ATTENTION_TO_FREE_HANDLING', '0');
define('PROMO_CODE_GRANTS_FREE_HANDLING', '1');
define('PROMO_CODE_FORBIDS_FREE_HANDLING', '2');

define('PROMO_CODE_DIRTY_CART', '0');
define('PROMO_CODE_STRICT_CART', '1');

?>