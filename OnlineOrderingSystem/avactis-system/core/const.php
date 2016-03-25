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
define('PARAM_NOT_FOUND', 'PARAM_NOT_FOUND');

define('TAG_PARAM_PROD_ID', 'prod_id');
define('TAG_PARAM_MNF_ID', 'mnf_id');
define('TAG_PARAM_PROD_TYPE_ID', 'id');

define('CCACHE_USE_MEMORY_CACHE', false);
define('CCACHE_DO_NOT_USE_MEMORY_CACHE', true);

define('STATUS_SUCCESS','success');
define('STATUS_ERROR','error');

if (!defined('APC_EXTENSION_LOADED') )
{
	define('APC_EXTENSION_LOADED', extension_loaded('apc') && ini_get('apc.enabled'));
}

if (!defined('E_RECOVERABLE_ERROR'))
{
    define('E_RECOVERABLE_ERROR', 4096);
}

?>