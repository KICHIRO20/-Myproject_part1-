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

/***** Profiler Settings *****/
//uncomment the following code to start profiling
/*
$conf['tracelog'][10]['filter'] = array(
    'level' => array('err', 'wrn', 'dbg', 'inf')
);

$conf['profiler'] = array(
    'enabled' => 'yes',
    'display_include' => 'yes',
    'display_file_io' => 'yes',
    'display_queries' => 'no',
    'display_cache' => 'no',
	'display_block_tags' => 'no',
    'write_csv' => 'no',
);
*/

/***** Cache Engine Types ****/

//uncomment the following code if memcached server is running
/*
if(class_exists('Memcache')){
	$memcache = array('MEMCACHED' => array(
                'driver' => 'CCacheDriverMemcached',
                'args' => array(
                            0 => '127.0.0.1', // host
                            1 => '11211', // port
	)));
	$conf['cache_storages'] = array_merge($conf['cache_storages'],$memcache);
}
*/


/***** Cache Configuration ****/

// uncomment the following code to use custom cache settings
/*
$conf['cache']['persistent']	= 'SINGLE_FILE';
$conf['cache']['temporary']		= 'RAM';

$conf['cache']['checkout']		= 'RAM';

$conf['cache']['html']			= 'FILE';
$conf['cache']['hash']			= 'SINGLE_FILE';
$conf['cache']['database']		= 'SINGLE_FILE';
$conf['cache']['inifiles']		= 'SINGLE_FILE';
$conf['cache']['attr_ids']		= 'SINGLE_FILE';
$conf['cache']['templatesAZ']	= 'SINGLE_FILE_AZ';
$conf['cache']['templatesCZ']	= 'SINGLE_FILE_AZ';
$conf['cache']['modulesAZ']		= 'SINGLE_FILE_CZ';
$conf['cache']['modulesCZ']		= 'SINGLE_FILE_CZ';
*/

/**** To overwrite any other setting from conf.main.php file, copy it below this line ****/