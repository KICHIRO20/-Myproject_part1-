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
 *                                 .                                                   .
 *                                              .
 */
define('SUBSCRIPTION_TOPIC_ACTIVE', 'A');

/**
 *                                                     .
 *                                                      .
 */
define('SUBSCRIPTION_TOPIC_CANNOT_SUBSCRIBE', 'S');

/**
 *                                   .
 *                                                       .
 */
define('SUBSCRIPTION_TOPIC_INACTIVE', 'I');

/**
 *                                              .
 *                                            .
 */
define('SUBSCRIPTION_TOPIC_FULL_ACCESS', 'F');

/**
 *                                                                      .
 *                                            .
 */
define('SUBSCRIPTION_TOPIC_GUEST_ONLY', 'G');

/**
 *                                                                    .
 *                                            .
 */
define('SUBSCRIPTION_TOPIC_REGISTERED_ONLY', 'R');

define('SUBSCRIPTION_TOPIC_AUTOSUBSCRIBE_NO',  'N');
define('SUBSCRIPTION_TOPIC_AUTOSUBSCRIBE_YES', 'Y');

/**
 *         email                                                     .
 *                  ,                                                      .
 *                                                                                   .
 */
define('MAX_EMAILS_AT_ONCE', 1000);

define('SUBSCRIPTION_TEMP_UNKNOWN', 'U');
define('SUBSCRIPTION_TEMP_EXISTS', 'E');
define('SUBSCRIPTION_TEMP_DONT_EXISTS', 'N');
//define('SUBSCRIPTION_TEMP_', '');

define('SUBSCRIBE_COOKIE', 'subscription');

define('SUBSCRIBE_COOKIE_LIVE', 3600*24*3650); // ten years

define('SUBSCRIPTION_KEY_FIELD_TYPE', DBQUERY_FIELD_TYPE_CHAR10);
define('SUBSCRIPTION_KEY_SIZE', 10);
?>