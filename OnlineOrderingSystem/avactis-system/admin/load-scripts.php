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
 * Disable error reporting
 *
 * Set this to error_reporting( E_ALL ) or error_reporting( E_ALL | E_STRICT ) for debugging
 */
error_reporting(0);

/** Set ABSPATH for execution */
if ( !defined('ABSPATH') )
       define('ABSPATH', dirname(dirname(dirname(__FILE__))) . '/');

if ( !defined('ASC_CORE') )
       define('ASC_CORE', 'avactis-system/core');

/**
 * @ignore
 */
function __() {}

/**
 * @ignore
 */
function _x() {}

/**
 * @ignore
 */
function add_filter() {}

/**
 * @ignore
 */
function esc_attr() {}

/**
 * @ignore
 */
function apply_filters() {}

/**
 * @ignore
 */
function get_option() {}

/**
 * @ignore
 */
function is_lighttpd_before_150() {}

/**
 * @ignore
 */
function add_action() {}

/**
 * @ignore
 */
function did_action() {}

/**
 * @ignore
 */
function do_action_ref_array() {}

/**
 * @ignore
 */
function get_bloginfo() {}

/**
 * @ignore
 */
function is_admin() {return true;}

/**
 * @ignore
 */
function site_url() {}

/**
 * @ignore
 */
function admin_url() {}

/**
 * @ignore
 */
function home_url() {}

/**
 * @ignore
 */
function includes_url() {}

/**
 * @ignore
 */
function asc_guess_url() {}

if ( ! function_exists( 'json_encode' ) ) :
/**
 * @ignore
 */
function json_encode() {}
endif;

function get_file($path) {

	if ( function_exists('realpath') )
		$path = realpath($path);

	if ( ! $path || ! @is_file($path) )
		return '';

	return @file_get_contents($path);
}

$load = $_GET['load'];
if ( is_array( $load ) )
	$load = implode( '', $load );

$load = preg_replace( '/[^a-z0-9,_-]+/i', '', $load );
$load = array_unique( explode( ',', $load ) );

if ( empty($load) )
	exit;

require(ABSPATH . ASC_CORE . '/script-loader.php');
require(ABSPATH . ASC_CORE . '/version.php');

$compress = ( isset($_GET['c']) && $_GET['c'] );
$force_gzip = ( $compress && 'gzip' == $_GET['c'] );
$expires_offset = 31536000; // 1 year
$out = '';

$asc_scripts = new ASC_Scripts();
asc_default_scripts($asc_scripts);

foreach( $load as $handle ) {
	if ( !array_key_exists($handle, $asc_scripts->registered) )
		continue;

	$path = ABSPATH . $asc_scripts->registered[$handle]->src;
	$out .= get_file($path) . "\n";
}

header('Content-Type: application/x-javascript; charset=UTF-8');
header('Expires: ' . gmdate( "D, d M Y H:i:s", time() + $expires_offset ) . ' GMT');
header("Cache-Control: public, max-age=$expires_offset");

if ( $compress && ! ini_get('zlib.output_compression') && 'ob_gzhandler' != ini_get('output_handler') && isset($_SERVER['HTTP_ACCEPT_ENCODING']) ) {
	header('Vary: Accept-Encoding'); // Handle proxies
	if ( false !== stripos($_SERVER['HTTP_ACCEPT_ENCODING'], 'deflate') && function_exists('gzdeflate') && ! $force_gzip ) {
		header('Content-Encoding: deflate');
		$out = gzdeflate( $out, 3 );
	} elseif ( false !== stripos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') && function_exists('gzencode') ) {
		header('Content-Encoding: gzip');
		$out = gzencode( $out, 3 );
	}
}

echo $out;
exit;