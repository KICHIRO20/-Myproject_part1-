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
 * @package CustomerAccount
 * @author Egor V. Derevyankin
 *
 */

define('CUSTOMER_ACCOUNT_INSTALL_DATA_XML', dirname(__FILE__).'/includes/install_data.xml');

define('PERSON_INFO_GROUP_ATTR_ALL', 1);
define('PERSON_INFO_GROUP_ATTR_VISIBLE', 2);
define('PERSON_INFO_GROUP_ATTR_HIDDEN', 3);

define('AUTH_SCHEME_BY_LOGIN', 1);
define('AUTH_SCHEME_BY_EMAIL', 2);

define('COPY_REG_DATA_TO_PERSON_DATA', 1);
define('NO_COPY_REG_DATA_TO_PERSON_DATA', 2);

define('ACCOUNT_ACTIVATION_SCHEME_NONE',1);
define('ACCOUNT_ACTIVATION_SCHEME_BY_ADMIN',2);
define('ACCOUNT_ACTIVATION_SCHEME_BY_CUSTOMER',3);

define('CHECKOUT_TYPE_QUICK',1);
define('CHECKOUT_TYPE_AUTOACCOUNT',2);
define('CHECKOUT_TYPE_ACCOUNT_REQUIRED',3);

define('PSEUDO_CUSTOMER_SUFFIX', '::pseudo');
define('PSEUDO_NA_CUSTOMER_PERFIX','N_A_');

define('AUTOACCOUNT_LENGTH',6);

define('ANONYMOUS_ACCOUNT_NAME', 'anonymous');

?>