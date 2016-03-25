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
 * Avactis Administration Screen API.
 *
 * @package Avactis
 * @subpackage Administration
 */

/**
 * Set the current screen object
 *
 * @since 3.0.0
 * @uses $current_screen
 *
 * @param mixed $hook_name Optional. The hook name (also known as the hook suffix) used to determine the screen,
 *	or an existing screen object.
 */
function set_current_screen( $hook_name = '' ) {
	ASC_Screen::get( $hook_name )->set_current_screen();
}

/**
 * Get the current screen object
 *
 * @since 4.7.0
 *
 * @return ASC_Screen Current screen object
 */
function get_current_screen() {
	global $current_screen;
	if ( ! isset( $current_screen ) )
		return null;

	return $current_screen;
}


final class ASC_Screen {

	/**
	 * The number of columns to display. Access with get_columns().
	 *
	 * @since 3.4.0
	 * @var int
	 * @access private
	 */
	private $columns = 0;

	public $action;

	public $base;

	public $id;

	protected $in_admin;

	/**
	 * The screen object registry.
	 *
	 * @since 4.7.0
	 * @var array
	 * @access private
	 */
	private static $_registry = array();


	public static function get( $hook_name = '' ) {
		if ( is_a( $hook_name, 'ASC_Screen' ) )
			return $hook_name;

		$in_admin = false;
		$action = '';

		if ( $hook_name )
			$id = $hook_name;
		else
			$id = $GLOBALS['hook_suffix'];

		if ( '.php' == substr( $id, -4 ) )
				$id = substr( $id, 0, -4 );

		if ( $hook_name ) {
			$id = sanitize_key( $id );

			$in_admin = 'site';
		}

		if ( 'index' == $id )
			$id = 'dashboard';
		elseif ( 'front' == $id )
			$in_admin = false;

		$id = 'dashboard';
		$base = $id;

		// If this is the current screen, see if we can be more accurate
		if ( isset( self::$_registry[ $id ] ) ) {
			$screen = self::$_registry[ $id ];
			if ( $screen === get_current_screen() )
				return $screen;
		} else {
			$screen = new ASC_Screen();
			$screen->id     = $id;
		}
		$screen->base       = $base;
		$screen->action     = $action;
		$screen->in_admin   = $in_admin;

		self::$_registry[ $id ] = $screen;
		return $screen;
	}


	/**
	 * Makes the screen object the current screen.
	 *
	 * @see set_current_screen()
	 * @since 4.7.0
	 */
	public function set_current_screen() {
		global $current_screen;
		$current_screen = $this;
		/**
		 * Fires after the current screen has been set.
		 *
		 * @since 3.0.0
		 *
		 * @param ASC_Screen $current_screen Current ASC_Screen object.
		 */
		//do_action( 'current_screen', $current_screen );
	}


	/**
	 * Constructor
	 *
	 * @since 4.7.0
	 * @access private
	 */
	private function __construct() {}

	/**
	 * Gets the number of layout columns the user has selected.
	 *
	 * The layout_columns option controls the max number and default number of
	 * columns. This method returns the number of columns within that range selected
	 * by the user via Screen Options. If no selection has been made, the default
	 * provisioned in layout_columns is returned. If the screen does not support
	 * selecting the number of layout columns, 0 is returned.
	 *
	 * @since 3.4.0
	 *
	 * @return int Number of columns to display.
	 */
	public function get_columns() {
		return $this->columns;
	}
}
?>