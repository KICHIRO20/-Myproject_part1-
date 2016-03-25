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
 * Core Initialization
 *
 * @package Core
 */

$_current_path = dirname( dirname(__FILE__) ); #                /system/
$_current_path = strtr($_current_path,'\\','/');
$_core_directory = $_current_path.'/avactis-system/core/';

include_once($_core_directory . 'conf.php');
include_once($_core_directory . 'functions.php' );

error_reporting(E_ERROR);
set_error_handler('__error_handler__', E_ERROR);
register_shutdown_function('__shutdown__');

include_once($_core_directory . 'bootstrap.php');
include_once($_core_directory . 'const.php' );
include_once($_core_directory . 'ctimer.php');
include_once($_core_directory . 'profiler.php');
include_once($_core_directory . 'cfile.php');
include_once($_core_directory . 'trace.php');
include_once($_core_directory . 'tracefilter.php');
include_once($_core_directory . 'tracelogrotation.php');
include_once($_core_directory . 'tracewriter.php');
include_once($_core_directory . 'upgrade.php');

CConf::init(dirname(__FILE__).'/conf/');

CProfiler::init();

// init default trace loggers
$tracelog = CConf::get('tracelog');
if ($tracelog)
{
    CTrace::startScript();
	CTrace::setId(getmypid());
	foreach ($tracelog as $trace_cfg)
	{
		if ($trace_cfg['enabled'] == 'yes')
		{
			$r = new CTraceLogRotation($trace_cfg['file'], $trace_cfg['rotation']['size'], $trace_cfg['rotation']['rotate']);
			$r->rotate();
			$tw = new CTraceWriter($trace_cfg['file'], $trace_cfg['template'], new CTraceFilter($trace_cfg['filter']));
			CTrace::registerWriter($tw);
		}
	}
}

// must be defined after CTrace initialization.
// all php notices, warnings and errors will be logged, so we need to set E_ALL level
//error_reporting(E_ERROR);
//set_error_handler('__error_handler__', E_ERROR);

CTrace::inf('Request: ' . getCurrentURL());
CProfiler::start('init');
$bootstrap = new Bootstrap();
$bootstrap->preboot();
//CTrace::inf('Point 1 (after preboot)');
$bootstrap->preloadCorePHP();
do_action( 'init' );
//CTrace::inf('Point 2 (after preloading core php)');
$bootstrap->preloadModulesPHP();
//CTrace::inf('Point 3 (after preloading modules php)');
global $zone;
if ($zone == 'CustomerZone') {
    $bootstrap->preloadModulesViewsCzPHP();
    //CTrace::inf('Point 3 (after preloading modules views php)');
}
$bootstrap->boot();
//CTrace::inf('Point 4 (after boot)');
$bootstrap->postboot();
//CTrace::inf('Point 5 (after postboot)');
CProfiler::stop('init');



