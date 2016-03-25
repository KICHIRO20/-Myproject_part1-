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
global $title, $hook_suffix, $current_screen;

if ( empty( $current_screen ) )
	set_current_screen();

do_action( 'admin_enqueue_scripts', $hook_suffix );
do_action( "admin_print_styles-$hook_suffix" );
do_action( 'admin_print_styles' );
do_action( "admin_print_scripts-$hook_suffix" );
do_action( 'admin_print_scripts' );
do_action( "admin_head-$hook_suffix" );
do_action( 'admin_head' );

require(ABSPATH . 'avactis-system/admin/menu-header.php');
?>
