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
 * BackPress Styles Procedural API
 *
 * @since 2.6.0
 *
 * @package WordPress
 * @subpackage BackPress
 */

/**
 * Display styles that are in the $handles queue.
 *
 * Passing an empty array to $handles prints the queue,
 * passing an array with one string prints that style,
 * and passing an array of strings prints those styles.
 *
 * @global ASC_Styles $asc_styles The ASC_Styles object for printing styles.
 *
 * @since 2.6.0
 *
 * @param array|bool $handles Styles to be printed. Default 'false'.
 * @return array On success, a processed array of ASC_Dependencies items; otherwise, an empty array.
 */
function asc_print_styles( $handles = false ) {
	if ( '' === $handles ) // for asc_head
		$handles = false;
	/**
	 * Fires before styles in the $handles queue are printed.
	 *
	 * @since 2.6.0
	 */
	if ( ! $handles )
		do_action( 'asc_print_styles' );

	global $asc_styles;
	if ( ! is_a( $asc_styles, 'ASC_Styles' ) ) {
		if ( ! did_action( 'init' ) )
			_doing_it_wrong( __FUNCTION__, sprintf( __( 'Scripts and styles should not be registered or enqueued until the %1$s, %2$s, or %3$s hooks.' ),
				'<code>asc_enqueue_scripts</code>', '<code>admin_enqueue_scripts</code>', '<code>login_enqueue_scripts</code>' ), '3.3' );

		if ( !$handles )
			return array(); // No need to instantiate if nothing is there.
		else
			$asc_styles = new ASC_Styles();
	}

	return $asc_styles->do_items( $handles );
}

/**
 * Add extra CSS styles to a registered stylesheet.
 *
 * Styles will only be added if the stylesheet in already in the queue.
 * Accepts a string $data containing the CSS. If two or more CSS code blocks
 * are added to the same stylesheet $handle, they will be printed in the order
 * they were added, i.e. the latter added styles can redeclare the previous.
 *
 * @see ASC_Styles::add_inline_style()
 * @global ASC_Styles $asc_styles The ASC_Styles object for printing styles.
 *
 * @since 3.3.0
 *
 * @param string $handle Name of the stylesheet to add the extra styles to. Must be lowercase.
 * @param string $data   String containing the CSS styles to be added.
 * @return bool True on success, false on failure.
 */
function asc_add_inline_style( $handle, $data ) {
	global $asc_styles;
	if ( ! is_a( $asc_styles, 'ASC_Styles' ) ) {
		if ( ! did_action( 'init' ) )
			_doing_it_wrong( __FUNCTION__, sprintf( __( 'Scripts and styles should not be registered or enqueued until the %1$s, %2$s, or %3$s hooks.' ),
				'<code>asc_enqueue_scripts</code>', '<code>admin_enqueue_scripts</code>', '<code>login_enqueue_scripts</code>' ), '3.3' );
		$asc_styles = new ASC_Styles();
	}

	if ( false !== stripos( $data, '</style>' ) ) {
		_doing_it_wrong( __FUNCTION__, 'Do not pass style tags to asc_add_inline_style().', '3.7' );
		$data = trim( preg_replace( '#<style[^>]*>(.*)</style>#is', '$1', $data ) );
	}

	return $asc_styles->add_inline_style( $handle, $data );
}

/**
 * Register a CSS stylesheet.
 *
 * @see ASC_Dependencies::add()
 * @link http://www.w3.org/TR/CSS2/media.html#media-types List of CSS media types.
 * @global ASC_Styles $asc_styles The ASC_Styles object for printing styles.
 *
 * @since 2.6.0
 *
 * @param string      $handle Name of the stylesheet.
 * @param string|bool $src    Path to the stylesheet from the WordPress root directory. Example: '/css/mystyle.css'.
 * @param array       $deps   An array of registered style handles this stylesheet depends on. Default empty array.
 * @param string|bool $ver    String specifying the stylesheet version number. Used to ensure that the correct version
 *                            is sent to the client regardless of caching. Default 'false'. Accepts 'false', 'null', or 'string'.
 * @param string      $media  Optional. The media for which this stylesheet has been defined.
 *                            Default 'all'. Accepts 'all', 'aural', 'braille', 'handheld', 'projection', 'print',
 *                            'screen', 'tty', or 'tv'.
 */
function asc_register_style( $handle, $src, $deps = array(), $ver = false, $media = 'all' ) {
	global $asc_styles;
	if ( ! is_a( $asc_styles, 'ASC_Styles' ) ) {
		if ( ! did_action( 'init' ) )
			_doing_it_wrong( __FUNCTION__, sprintf( __( 'Scripts and styles should not be registered or enqueued until the %1$s, %2$s, or %3$s hooks.' ),
				'<code>asc_enqueue_scripts</code>', '<code>admin_enqueue_scripts</code>', '<code>login_enqueue_scripts</code>' ), '3.3' );
		$asc_styles = new ASC_Styles();
	}

	$asc_styles->add( $handle, $src, $deps, $ver, $media );
}

/**
 * Remove a registered stylesheet.
 *
 * @see ASC_Dependencies::remove()
 * @global ASC_Styles $asc_styles The ASC_Styles object for printing styles.
 *
 * @since 2.1.0
 *
 * @param string $handle Name of the stylesheet to be removed.
 */
function asc_deregister_style( $handle ) {
	global $asc_styles;
	if ( ! is_a( $asc_styles, 'ASC_Styles' ) ) {
		if ( ! did_action( 'init' ) )
			_doing_it_wrong( __FUNCTION__, sprintf( __( 'Scripts and styles should not be registered or enqueued until the %1$s, %2$s, or %3$s hooks.' ),
				'<code>asc_enqueue_scripts</code>', '<code>admin_enqueue_scripts</code>', '<code>login_enqueue_scripts</code>' ), '3.3' );
		$asc_styles = new ASC_Styles();
	}

	$asc_styles->remove( $handle );
}

/**
 * Enqueue a CSS stylesheet.
 *
 * Registers the style if source provided (does NOT overwrite) and enqueues.
 *
 * @see ASC_Dependencies::add(), ASC_Dependencies::enqueue()
 * @link http://www.w3.org/TR/CSS2/media.html#media-types List of CSS media types.
 * @global ASC_Styles $asc_styles The ASC_Styles object for printing styles.
 *
 * @since 2.6.0
 *
 * @param string      $handle Name of the stylesheet.
 * @param string|bool $src    Path to the stylesheet from the root directory of WordPress. Example: '/css/mystyle.css'.
 * @param array       $deps   An array of registered style handles this stylesheet depends on. Default empty array.
 * @param string|bool $ver    String specifying the stylesheet version number, if it has one. This parameter is used
 *                            to ensure that the correct version is sent to the client regardless of caching, and so
 *                            should be included if a version number is available and makes sense for the stylesheet.
 * @param string      $media  Optional. The media for which this stylesheet has been defined.
 *                            Default 'all'. Accepts 'all', 'aural', 'braille', 'handheld', 'projection', 'print',
 *                            'screen', 'tty', or 'tv'.
 */
function asc_enqueue_style( $handle, $src = false, $deps = array(), $ver = false, $media = 'all' ) {
	global $asc_styles;
	if ( ! is_a( $asc_styles, 'ASC_Styles' ) ) {
		if ( ! did_action( 'init' ) )
			_doing_it_wrong( __FUNCTION__, sprintf( __( 'Scripts and styles should not be registered or enqueued until the %1$s, %2$s, or %3$s hooks.' ),
				'<code>asc_enqueue_scripts</code>', '<code>admin_enqueue_scripts</code>', '<code>login_enqueue_scripts</code>' ), '3.3' );
		$asc_styles = new ASC_Styles();
	}

	if ( $src ) {
		$_handle = explode('?', $handle);
		$asc_styles->add( $_handle[0], $src, $deps, $ver, $media );
	}
	$asc_styles->enqueue( $handle );
}

/**
 * Remove a previously enqueued CSS stylesheet.
 *
 * @see ASC_Dependencies::dequeue()
 * @global ASC_Styles $asc_styles The ASC_Styles object for printing styles.
 *
 * @since 3.1.0
 *
 * @param string $handle Name of the stylesheet to be removed.
 */
function asc_dequeue_style( $handle ) {
	global $asc_styles;
	if ( ! is_a( $asc_styles, 'ASC_Styles' ) ) {
		if ( ! did_action( 'init' ) )
			_doing_it_wrong( __FUNCTION__, sprintf( __( 'Scripts and styles should not be registered or enqueued until the %1$s, %2$s, or %3$s hooks.' ),
				'<code>asc_enqueue_scripts</code>', '<code>admin_enqueue_scripts</code>', '<code>login_enqueue_scripts</code>' ), '3.3' );
		$asc_styles = new ASC_Styles();
	}

	$asc_styles->dequeue( $handle );
}

/**
 * Check whether a CSS stylesheet has been added to the queue.
 *
 * @global ASC_Styles $asc_styles The ASC_Styles object for printing styles.
 *
 * @since 2.8.0
 *
 * @param string $handle Name of the stylesheet.
 * @param string $list   Optional. Status of the stylesheet to check. Default 'enqueued'.
 *                       Accepts 'enqueued', 'registered', 'queue', 'to_do', and 'done'.
 * @return bool Whether style is queued.
 */
function asc_style_is( $handle, $list = 'enqueued' ) {
	global $asc_styles;
	if ( ! is_a( $asc_styles, 'ASC_Styles' ) ) {
		if ( ! did_action( 'init' ) )
			_doing_it_wrong( __FUNCTION__, sprintf( __( 'Scripts and styles should not be registered or enqueued until the %1$s, %2$s, or %3$s hooks.' ),
				'<code>asc_enqueue_scripts</code>', '<code>admin_enqueue_scripts</code>', '<code>login_enqueue_scripts</code>' ), '3.3' );
		$asc_styles = new ASC_Styles();
	}

	return (bool) $asc_styles->query( $handle, $list );
}

/**
 * Add metadata to a CSS stylesheet.
 *
 * Works only if the stylesheet has already been added.
 *
 * Possible values for $key and $value:
 * 'conditional' string      Comments for IE 6, lte IE 7 etc.
 * 'rtl'         bool|string To declare an RTL stylesheet.
 * 'suffix'      string      Optional suffix, used in combination with RTL.
 * 'alt'         bool        For rel="alternate stylesheet".
 * 'title'       string      For preferred/alternate stylesheets.
 *
 * @see ASC_Dependency::add_data()
 *
 * @since 3.6.0
 *
 * @param string $handle Name of the stylesheet.
 * @param string $key    Name of data point for which we're storing a value.
 *                       Accepts 'conditional', 'rtl' and 'suffix', 'alt' and 'title'.
 * @param mixed  $data   String containing the CSS data to be added.
 * @return bool True on success, false on failure.
 */
function asc_style_add_data( $handle, $key, $value ) {
	global $asc_styles;
	return $asc_styles->add_data( $handle, $key, $value );
}