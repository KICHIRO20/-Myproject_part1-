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
    * General template tags that can go anywhere in a template.
    *
    * @package avactis
    * @subpackage Includes
    */

   add_action("asc_footer","footer_content");
   add_action("asc_head","header_content");

   /**
    * Fire the asc_head action
    *
    * @since 4.7.5
    * @uses do_action() Calls 'asc_head' hook.
    */
   function asc_head()
   {
   	do_action('asc_head');
   }

   /**
    * Fire the asc_footer action
    *
    * @since 4.7.5
    * @uses do_action() Calls 'asc_footer' hook.
    */
   function asc_footer() {
   	do_action('asc_footer');
   }

   /**
    * Load header template.
    *
    * Includes the header template header.php for a theme
    *
    * @uses header_content()
    * @since 4.7.5
    * @uses do_action() Calls 'get_header' in header.php
    *
    */
   function header_content()
   {
   	if (isset($GLOBALS['__TPL_DIR__']) && file_exists($GLOBALS['__TPL_DIR__'].'header.php'))
   	{
   		include_once($GLOBALS['__TPL_DIR__'].'header.php');
   	}
   }

   /**
    * Load footer template.
    *
    * Includes the footer template footer.php for a theme
    *
    * @uses footer_content()
    * @since 4.7.5
    * @uses do_action() Calls 'get_footer' in footer.php
    *
    */
   function footer_content()
   {
   	if (isset($GLOBALS['__TPL_DIR__']) && file_exists($GLOBALS['__TPL_DIR__'].'footer.php'))
   	{
   		include_once($GLOBALS['__TPL_DIR__'].'footer.php');
   	}
   }

   function asc_load_template( $_template_file, $require_once = true ) {
   	if ( $require_once )
   	include_once( $_template_file );
   	else
   	include( $_template_file );
   }

?>