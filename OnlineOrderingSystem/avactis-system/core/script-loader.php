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
 * WordPress scripts and styles default loader.
 *
 * Most of the functionality that existed here was moved to
 * {@link http://backpress.automattic.com/ BackPress}. WordPress themes and
 * plugins will only be concerned about the filters and actions set in this
 * file.
 *
 * Several constants are used to manage the loading, concatenating and compression of scripts and CSS:
 * define('SCRIPT_DEBUG', true); loads the development (non-minified) versions of all scripts and CSS, and disables compression and concatenation,
 * define('CONCATENATE_SCRIPTS', false); disables compression and concatenation of scripts and CSS,
 * define('COMPRESS_SCRIPTS', false); disables compression of scripts,
 * define('COMPRESS_CSS', false); disables compression of CSS,
 * define('ENFORCE_GZIP', true); forces gzip for compression (default is deflate).
 *
 * The globals $concatenate_scripts, $compress_scripts and $compress_css can be set by plugins
 * to temporarily override the above settings. Also a compression test is run once and the result is saved
 * as option 'can_compress_scripts' (0/1). The test will run again if that option is deleted.
 *
 * @package WordPress
 */
/** Set ABSPATH for execution */
if ( !defined('ABSPATH') )
       define('ABSPATH', dirname(dirname(dirname(__FILE__))) . '/');

if ( !defined('ASC_CORE') )
       define('ASC_CORE', 'avactis-system/core');

/** BackPress: WordPress Dependencies Class */
require( ABSPATH . ASC_CORE . '/class.asc-dependencies.php' );

/** BackPress: WordPress Scripts Class */
require( ABSPATH . ASC_CORE . '/class.asc-scripts.php' );

/** BackPress: WordPress Scripts Functions */
require( ABSPATH . ASC_CORE . '/functions.asc-scripts.php' );

/** BackPress: WordPress Styles Class */
require( ABSPATH . ASC_CORE . '/class.asc-styles.php' );

/** BackPress: WordPress Styles Functions */
require( ABSPATH . ASC_CORE . '/functions.asc-styles.php' );

/**
 * Register all WordPress scripts.
 *
 * Localizes some of them.
 * args order: $scripts->add( 'handle', 'url', 'dependencies', 'query-string', 1 );
 * when last arg === 1 queues the script for the footer
 *
 * @since 2.6.0
 *
 * @param object $scripts ASC_Scripts object.
 */
function asc_default_scripts( &$scripts ) {
	include ABSPATH . ASC_CORE . '/version.php'; // include an unmodified $asc_version

	$develop_src = false !== strpos( $asc_version, '-src' );

	if ( ! defined( 'SCRIPT_DEBUG' ) ) {
		define( 'SCRIPT_DEBUG', $develop_src );
	}

	if ( ! $guessurl = admin_storefront_url() ) {
		$guessed_url = true;
		$guessurl = site_url();
	}

	$scripts->base_url = $guessurl;
	$scripts->content_url = defined('ASC_CONTENT_URL')? ASC_CONTENT_URL : '';
	$scripts->default_version = PRODUCT_VERSION_NUMBER;
	$scripts->default_dirs = array('avactis-system/admin/js/', 'includes/js/');

	$suffix = SCRIPT_DEBUG ? '' : '.min';
	$dev_suffix = $develop_src ? '' : '.min';

	$scripts->add( 'prototype', '//ajax.googleapis.com/ajax/libs/prototype/1.7.1.0/prototype.js', array(), '1.7.1');
	$scripts->add( 'scriptaculous-root', '//ajax.googleapis.com/ajax/libs/scriptaculous/1.9.0/scriptaculous.js', array('prototype'), '1.9.0');
	$scripts->add( 'scriptaculous-builder', '//ajax.googleapis.com/ajax/libs/scriptaculous/1.9.0/builder.js', array('scriptaculous-root'), '1.9.0');
	$scripts->add( 'scriptaculous-dragdrop', '//ajax.googleapis.com/ajax/libs/scriptaculous/1.9.0/dragdrop.js', array('scriptaculous-builder', 'scriptaculous-effects'), '1.9.0');
	$scripts->add( 'scriptaculous-effects', '//ajax.googleapis.com/ajax/libs/scriptaculous/1.9.0/effects.js', array('scriptaculous-root'), '1.9.0');
	$scripts->add( 'scriptaculous-slider', '//ajax.googleapis.com/ajax/libs/scriptaculous/1.9.0/slider.js', array('scriptaculous-effects'), '1.9.0');
	$scripts->add( 'scriptaculous-sound', '//ajax.googleapis.com/ajax/libs/scriptaculous/1.9.0/sound.js', array( 'scriptaculous-root' ), '1.9.0' );
	$scripts->add( 'scriptaculous-controls', '//ajax.googleapis.com/ajax/libs/scriptaculous/1.9.0/controls.js', array('scriptaculous-root'), '1.9.0');
	$scripts->add( 'scriptaculous', false, array('scriptaculous-dragdrop', 'scriptaculous-slider', 'scriptaculous-controls') );

	// jQuery
	$scripts->add( 'jquery', false, array( 'jquery-core', 'jquery-migrate' ), '1.11.0' );
	$scripts->add( 'jquery-core', 'includes/jquery/jquery.js', array(), '1.11.0' );
	$scripts->add( 'jquery-migrate', "includes/jquery/jquery-migrate$suffix.js", array(), '1.2.1' );

	// full jQuery UI
	$scripts->add( 'jquery-ui-core', 'includes/jquery/ui/jquery.ui.core.min.js', array('jquery'), '1.10.4');
	$scripts->add( 'jquery-effects-core', 'includes/jquery/ui/jquery.ui.effect.min.js', array('jquery'), '1.10.4', 1 );

	$scripts->add( 'jquery-effects-blind', 'includes/jquery/ui/jquery.ui.effect-blind.min.js', array('jquery-effects-core'), '1.10.4', 1 );
	$scripts->add( 'jquery-effects-bounce', 'includes/jquery/ui/jquery.ui.effect-bounce.min.js', array('jquery-effects-core'), '1.10.4', 1 );
	$scripts->add( 'jquery-effects-clip', 'includes/jquery/ui/jquery.ui.effect-clip.min.js', array('jquery-effects-core'), '1.10.4', 1 );
	$scripts->add( 'jquery-effects-drop', 'includes/jquery/ui/jquery.ui.effect-drop.min.js', array('jquery-effects-core'), '1.10.4', 1 );
	$scripts->add( 'jquery-effects-explode', 'includes/jquery/ui/jquery.ui.effect-explode.min.js', array('jquery-effects-core'), '1.10.4', 1 );
	$scripts->add( 'jquery-effects-fade', 'includes/jquery/ui/jquery.ui.effect-fade.min.js', array('jquery-effects-core'), '1.10.4', 1 );
	$scripts->add( 'jquery-effects-fold', 'includes/jquery/ui/jquery.ui.effect-fold.min.js', array('jquery-effects-core'), '1.10.4', 1 );
	$scripts->add( 'jquery-effects-highlight', 'includes/jquery/ui/jquery.ui.effect-highlight.min.js', array('jquery-effects-core'), '1.10.4', 1 );
	$scripts->add( 'jquery-effects-pulsate', 'includes/jquery/ui/jquery.ui.effect-pulsate.min.js', array('jquery-effects-core'), '1.10.4', 1 );
	$scripts->add( 'jquery-effects-scale', 'includes/jquery/ui/jquery.ui.effect-scale.min.js', array('jquery-effects-core'), '1.10.4', 1 );
	$scripts->add( 'jquery-effects-shake', 'includes/jquery/ui/jquery.ui.effect-shake.min.js', array('jquery-effects-core'), '1.10.4', 1 );
	$scripts->add( 'jquery-effects-slide', 'includes/jquery/ui/jquery.ui.effect-slide.min.js', array('jquery-effects-core'), '1.10.4', 1 );
	$scripts->add( 'jquery-effects-transfer', 'includes/jquery/ui/jquery.ui.effect-transfer.min.js', array('jquery-effects-core'), '1.10.4', 1 );

	$scripts->add( 'jquery-ui-accordion', 'includes/jquery/ui/jquery.ui.accordion.min.js', array('jquery-ui-core', 'jquery-ui-widget'), '1.10.4', 1 );
	$scripts->add( 'jquery-ui-autocomplete', 'includes/jquery/ui/jquery.ui.autocomplete.min.js', array('jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-position', 'jquery-ui-menu'), '1.10.4', 1 );
	$scripts->add( 'jquery-ui-button', 'includes/jquery/ui/jquery.ui.button.min.js', array('jquery-ui-core', 'jquery-ui-widget'), '1.10.4', 1 );
	$scripts->add( 'jquery-ui-datepicker', 'includes/jquery/ui/jquery.ui.datepicker.min.js', array('jquery-ui-core'), '1.10.4', 1 );
	$scripts->add( 'jquery-ui-dialog', 'includes/jquery/ui/jquery.ui.dialog.min.js', array('jquery-ui-resizable', 'jquery-ui-draggable', 'jquery-ui-button', 'jquery-ui-position'), '1.10.4', 1 );
	$scripts->add( 'jquery-ui-draggable', 'includes/jquery/ui/jquery.ui.draggable.min.js', array('jquery-ui-core', 'jquery-ui-mouse'), '1.10.4');
	$scripts->add( 'jquery-ui-droppable', 'includes/jquery/ui/jquery.ui.droppable.min.js', array('jquery-ui-draggable'), '1.10.4');
	$scripts->add( 'jquery-ui-menu', 'includes/jquery/ui/jquery.ui.menu.min.js', array( 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-position' ), '1.10.4', 1 );
	$scripts->add( 'jquery-ui-mouse', 'includes/jquery/ui/jquery.ui.mouse.min.js', array('jquery-ui-widget'), '1.10.4');
	$scripts->add( 'jquery-ui-position', 'includes/jquery/ui/jquery.ui.position.min.js', array('jquery'), '1.10.4', 1 );
	$scripts->add( 'jquery-ui-progressbar', 'includes/jquery/ui/jquery.ui.progressbar.min.js', array('jquery-ui-widget'), '1.10.4', 1 );
	$scripts->add( 'jquery-ui-resizable', 'includes/jquery/ui/jquery.ui.resizable.min.js', array('jquery-ui-core', 'jquery-ui-mouse'), '1.10.4', 1 );
	$scripts->add( 'jquery-ui-selectable', 'includes/jquery/ui/jquery.ui.selectable.min.js', array('jquery-ui-core', 'jquery-ui-mouse'), '1.10.4', 1 );
	$scripts->add( 'jquery-ui-slider', 'includes/jquery/ui/jquery.ui.slider.min.js', array('jquery-ui-core', 'jquery-ui-mouse'), '1.10.4', 1 );
	$scripts->add( 'jquery-ui-sortable', 'includes/jquery/ui/jquery.ui.sortable.min.js', array('jquery-ui-core', 'jquery-ui-mouse'), '1.10.4');
	$scripts->add( 'jquery-ui-spinner', 'includes/jquery/ui/jquery.ui.spinner.min.js', array( 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-button' ), '1.10.4', 1 );
	$scripts->add( 'jquery-ui-tabs', 'includes/jquery/ui/jquery.ui.tabs.min.js', array('jquery-ui-core', 'jquery-ui-widget'), '1.10.4', 1 );
	$scripts->add( 'jquery-ui-tooltip', 'includes/jquery/ui/jquery.ui.tooltip.min.js', array( 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-position' ), '1.10.4', 1 );
	$scripts->add( 'jquery-ui-widget', 'includes/jquery/ui/jquery.ui.widget.min.js', array('jquery'), '1.10.4');

	// deprecated, not used in core, most functionality is included in jQuery 1.3
	$scripts->add( 'jquery-form', "includes/jquery/jquery.form$suffix.js", array('jquery'), '3.37.0', 1 );

	// jQuery plugins
	$scripts->add( 'jquery-color', "includes/jquery/jquery.color.min.js", array('jquery'), '2.1.1', 1 );
	$scripts->add( 'suggest', "includes/jquery/suggest$suffix.js", array('jquery'), '1.1-20110113', 1 );
	$scripts->add( 'schedule', 'includes/jquery/jquery.schedule.js', array('jquery'), '20m', 1 );
	$scripts->add( 'jquery-query', "includes/jquery/jquery.query.js", array('jquery'), '2.1.7', 1 );
	$scripts->add( 'jquery-serialize-object', "includes/jquery/jquery.serialize-object.js", array('jquery'), '0.2', 1 );
	$scripts->add( 'jquery-hotkeys', "includes/jquery/jquery.hotkeys$suffix.js", array('jquery'), '0.0.2m', 1 );
	$scripts->add( 'jquery-table-hotkeys', "includes/jquery/jquery.table-hotkeys$suffix.js", array('jquery', 'jquery-hotkeys'), false, 1 );
	$scripts->add( 'jquery-touch-punch', "includes/jquery/jquery.ui.touch-punch.js", array('jquery-ui-widget', 'jquery-ui-mouse'), '0.2.2', 1 );

/*	$scripts->add( 'thickbox', "/wp-includes/js/thickbox/thickbox.js", array('jquery'), '3.1-20121105', 1 );
	did_action( 'init' ) && $scripts->localize( 'thickbox', 'thickboxL10n', array(
			'next' => __('Next &gt;'),
			'prev' => __('&lt; Prev'),
			'image' => __('Image'),
			'of' => __('of'),
			'close' => __('Close'),
			'noiframes' => __('This feature requires inline frames. You have iframes disabled or your browser does not support them.'),
			'loadingAnimation' => includes_url('js/thickbox/loadingAnimation.gif'),
	) );*/

	// common bits for both uploaders
	$max_upload_size = ( (int) ( $max_up = @ini_get('upload_max_filesize') ) < (int) ( $max_post = @ini_get('post_max_size') ) ) ? $max_up : $max_post;

	if ( empty($max_upload_size) )
		$max_upload_size = __('not configured');
/*
	// error message for both plupload and swfupload
	$uploader_l10n = array(
		'queue_limit_exceeded' => __('You have attempted to queue too many files.'),
		'file_exceeds_size_limit' => __('%s exceeds the maximum upload size for this site.'),
		'zero_byte_file' => __('This file is empty. Please try another.'),
		'invalid_filetype' => __('This file type is not allowed. Please try another.'),
		'not_an_image' => __('This file is not an image. Please try another.'),
		'image_memory_exceeded' => __('Memory exceeded. Please try another smaller file.'),
		'image_dimensions_exceeded' => __('This is larger than the maximum size. Please try another.'),
		'default_error' => __('An error occurred in the upload. Please try again later.'),
		'missing_upload_url' => __('There was a configuration error. Please contact the server administrator.'),
		'upload_limit_exceeded' => __('You may only upload 1 file.'),
		'http_error' => __('HTTP error.'),
		'upload_failed' => __('Upload failed.'),
		'big_upload_failed' => __('Please try uploading this file with the %1$sbrowser uploader%2$s.'),
		'big_upload_queued' => __('%s exceeds the maximum upload size for the multi-file uploader when used in your browser.'),
		'io_error' => __('IO error.'),
		'security_error' => __('Security error.'),
		'file_cancelled' => __('File canceled.'),
		'upload_stopped' => __('Upload stopped.'),
		'dismiss' => __('Dismiss'),
		'crunching' => __('Crunching&hellip;'),
		'deleted' => __('moved to the trash.'),
		'error_uploading' => __('&#8220;%s&#8221; has failed to upload.')
	);
*/

/**
	Avactis additional js
*/
		$scripts->add( 'bootstrap', 'includes/bootstrap/js/bootstrap.min.js', array( 'jquery' ), '3.2.0');
		$scripts->add( 'bootstrap-switch', 'includes/bootstrap-switch/js/bootstrap-switch.min.js', array( 'bootstrap' ), false);
		$scripts->add( 'bootstrap-hover-dropdown', 'includes/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js', array( 'bootstrap' ), false);
		$scripts->add( 'jquery-ui-custom', 'includes/jquery-ui/jquery-ui-1.10.3.custom.min.js', array( 'jquery' ), '1.10.3');
		$scripts->add( 'jquery-slimscroll', 'includes/jquery-slimscroll/jquery.slimscroll.min.js', array( 'jquery' ), false);
		$scripts->add( 'jquery-blockui', 'includes/jquery/jquery.blockui.min.js', array( 'jquery' ), false);
		$scripts->add( 'jquery-cokie', 'includes/jquery/jquery.cokie.min.js', array( 'jquery' ), false);
		$scripts->add( 'jquery-uniform', 'includes/uniform/jquery.uniform.min.js', array( 'jquery' ), false);
		$scripts->add( 'jquery-pulsate', 'includes/jquery/jquery.pulsate.min.js', array( 'jquery' ), false);

		$scripts->add( 'flot', 'includes/flot/jquery.flot.min.js', array( 'jquery' ), false);
		$scripts->add( 'flot-categories', 'includes/flot/jquery.flot.categories.min.js', array( 'flot' ), false);
		$scripts->add( 'flot-resize', 'includes/flot/jquery.flot.resize.min.js', array( 'flot' ), false);

		$scripts->add( 'daterangepicker', 'includes/bootstrap-daterangepicker/daterangepicker.js', array( 'bootstrap' ), false);
		$scripts->add( 'moment', 'includes/bootstrap-daterangepicker/moment.min.js', array(), false);
		$scripts->add( 'bootstrap-datepicker', 'includes/bootstrap-datepicker/js/bootstrap-datepicker.js', array( 'bootstrap' ), false);
        $scripts->add( 'bootstrap-datetimepicker','includes/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js',array( 'bootstrap' ), false);
		$scripts->add( 'bootstrap-dataTables', 'includes/datatables/plugins/bootstrap/dataTables.bootstrap.js', array( 'bootstrap' ), false);
		$scripts->add( 'bootstrap-toastr', 'includes/bootstrap-toastr/toastr.min.js', array( 'bootstrap' ), false);
		$scripts->add( 'jquery-dataTables', 'includes/datatables/media/js/jquery.dataTables.min.js', array( 'jquery' ), false);
		$scripts->add( 'dataTables', 'includes/datatable.js', array(), false);
		$scripts->add( 'bootbox', 'includes/bootbox/bootbox.min.js', array(), false);

		$scripts->add( 'fullcalendar', 'includes/fullcalendar/fullcalendar.min.js', array( 'jquery-ui-resizable', 'jquery-ui-draggable' ), false);
		$scripts->add( 'jquery-easypiechart', 'includes/jquery-easypiechart/jquery.easypiechart.min.js', array( 'jquery' ), false);
		$scripts->add( 'jquery-sparkline', 'includes/jquery/jquery.sparkline.min.js', array( 'jquery' ), false);
		$scripts->add( 'jquery-gritter', 'includes/gritter/js/jquery.gritter.min.js', array( 'jquery' ), false);
		$scripts->add( 'jquery-colorbox', 'includes/colorbox/jquery.colorbox-min.js', array( 'jquery' ), false);
		$scripts->add( 'asc-admin', 'includes/asc-admin.js', array(), false);
        $scripts->add( 'component-pickers', 'includes/components-pickers.js', array(), false);

		$scripts->add( 'ie9-excanvas', 'includes/excanvas.min.js', array(), false);
		$scripts->add( 'ie9-respond', 'includes/respond.min.js', array(), false);

		$scripts->add( 'jquery.validate', 'includes/jquery-validation/jquery.validate.min.js', array('jquery'), false);
		$scripts->add( 'validate-additional-methods', 'includes/jquery-validation/additional-methods.min.js', array('jquery'), false);
		$scripts->add( 'jquery-bootstrap-wizard', 'includes/bootstrap-wizard/jquery.bootstrap.wizard.min.js', array('jquery'), false);
		$scripts->add( 'jquery-backstretch', 'includes/backstretch/jquery.backstretch.min.js', array('jquery'), false);
		$scripts->add( 'select2', 'includes/select2/select2.min.js', array(), false);

		$scripts->add( 'jquery-iframe-transport', 'avactis-system/admin/js/jquery.iframe-transport.js', array(), false);
		$scripts->add( 'sanitize_tags', 'avactis-system/admin/js/sanitize_tags.js', array(), false);
		$scripts->add( 'admin-index', 'avactis-system/admin/js/index.js', array(), false);
		$scripts->add( 'admin-tasks', 'avactis-system/admin/js/tasks.js', array(), false);
		$scripts->add( 'admin-layout', 'avactis-system/admin/js/layout.js', array(), false);
		$scripts->add( 'form-wizard', 'avactis-system/admin/js/form-wizard.js', array('jquery'), false);
		$scripts->add( 'admin-login-soft', 'avactis-system/admin/js/login-soft.js', array(), false);

		$scripts->add( 'admin-avactis-main', 'avactis-system/admin/js/main.js', array(), false);
		$scripts->add( 'admin-avactis-md5', 'avactis-system/admin/templates/modules/users/md5.js', array(), false);
		$scripts->add( 'admin-avactis-dtree', 'avactis-system/admin/dtree/dtree.js', array(), false);
		$scripts->add( 'admin-avactis-validate', 'avactis-system/admin/js/validate.js', array(), false);
		$scripts->add( 'admin-avactis-new-window', 'avactis-system/admin/js/new_window.js', array(), false);
		$scripts->add( 'admin-avactis-countries-states', 'avactis-system/admin/js/countries_states.js', array(), false);
		$scripts->add( 'admin-avactis-tree-css', 'avactis-system/admin/jstree/css.js', array(), false);
		$scripts->add( 'admin-avactis-tree-component', 'avactis-system/admin/jstree/tree_component.js', array(), false);
		$scripts->add( 'admin-avactis-categories', 'avactis-system/admin/js/categories.js', array(), false);
		$scripts->add( 'admin-avactis-utility', 'avactis-system/admin/js/utility.js', array(), false);
                $scripts->add( 'admin-toastr-notification', 'avactis-system/admin/js/toastr-notification.js', array(), false);
	}

/**
 * Assign default styles to $styles object.
 *
 * Nothing is returned, because the $styles parameter is passed by reference.
 * Meaning that whatever object is passed will be updated without having to
 * reassign the variable that was passed back to the same value. This saves
 * memory.
 *
 * Adding default styles is not the only task, it also assigns the base_url
 * property, the default version, and text direction for the object.
 *
 * @since 2.6.0
 *
 * @param object $styles
 */
function asc_default_styles( &$styles ) {
	include ABSPATH . ASC_CORE . '/version.php'; // include an unmodified $asc_version

	if ( ! defined( 'SCRIPT_DEBUG' ) )
		define( 'SCRIPT_DEBUG', false !== strpos( $asc_version, '-src' ) );

	if ( ! $guessurl = admin_storefront_url() ) {
		$guessed_url = true;
		$guessurl = site_url();
	}

	$styles->base_url = $guessurl;
	$styles->content_url = defined('ASC_CONTENT_URL')? ASC_CONTENT_URL : '';
	$styles->default_version = PRODUCT_VERSION_NUMBER; // get_bloginfo( 'version' );
	$styles->text_direction = function_exists( 'is_rtl' ) && is_rtl() ? 'rtl' : 'ltr';
	$styles->default_dirs = array('/wp-admin/', '/wp-includes/css/');

	$open_sans_font_url = '';

	/* translators: If there are characters in your language that are not supported
	 * by Open Sans, translate this to 'off'. Do not translate into your own language.
	 */
/*	if ( 'off' !== _x( 'on', 'Open Sans font: on or off' ) ) { */
		$subsets = 'latin,latin-ext';

		/* translators: To add an additional Open Sans character subset specific to your language,
		 * translate this to 'greek', 'cyrillic' or 'vietnamese'. Do not translate into your own language.
		 */
		//$subset = _x( 'no-subset', 'Open Sans font: add new subset (greek, cyrillic, vietnamese)' );

		if ( 'cyrillic' == $subset ) {
			$subsets .= ',cyrillic,cyrillic-ext';
		} elseif ( 'greek' == $subset ) {
			$subsets .= ',greek,greek-ext';
		} elseif ( 'vietnamese' == $subset ) {
			$subsets .= ',vietnamese';
		}

		// Hotlink Open Sans, for now
		$open_sans_font_url = "//fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,300,400,600&subset=$subsets";
	//}

	// Register a stylesheet for the selected admin color scheme.
	$styles->add( 'colors', true, array( 'wp-admin', 'buttons', 'open-sans', 'dashicons' ) );

	$suffix = SCRIPT_DEBUG ? '' : '.min';

	$styles->add_data( 'ie', 'conditional', 'lte IE 7' );

	//Avactis additional css
	$styles->add( 'bootstrap',"includes/bootstrap/css/bootstrap.min.css", array(), '3.2.0' );
	$styles->add( 'gfont-open-sans',"//fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" );
	$styles->add( 'font-awesome',"includes/font-awesome/css/font-awesome.min.css" );
	$styles->add( 'simple-line-icons',"includes/simple-line-icons/simple-line-icons.min.css" );
	$styles->add( 'jquery-uniform',"includes/uniform/css/uniform.default.min.css" );
	$styles->add( 'bootstrap-switch',"includes/bootstrap-switch/css/bootstrap-switch.min.css" );
	$styles->add( 'select2',"includes/select2/select2.css" );
	$styles->add( 'components',"includes/components.css" );
	$styles->add( 'plugins',"includes/plugins.css" );

	$styles->add( 'fullcalendar',"includes/fullcalendar/fullcalendar.css" );
	$styles->add( 'bootstrap-daterangepicker',"includes/bootstrap-daterangepicker/daterangepicker-bs3.css" );
	$styles->add( 'bootstrap-datepicker',"includes/bootstrap-datepicker/css/datepicker.css" );
	$styles->add( 'bootstrap-dataTables',"includes/datatables/plugins/bootstrap/dataTables.bootstrap.css" );
	$styles->add( 'bootstrap-toastr',"includes/bootstrap-toastr/toastr.min.css" );
	$styles->add( 'jquery.gritter',"includes/gritter/css/jquery.gritter.css" );
	$styles->add( 'jquery-colorbox',"includes/colorbox/colorbox.css" );

	$styles->add( 'admin-login-soft',"avactis-system/admin/styles/login-soft.css" );
	$styles->add( 'admin-tasks',"avactis-system/admin/styles/tasks.css" );
	$styles->add( 'admin-default',"avactis-system/admin/styles/default.css" );
	$styles->add( 'admin-custom',"avactis-system/admin/styles/custom.css" );
	$styles->add( 'admin-layout',"avactis-system/admin/styles/layout.css" );
	//$styles->add( 'admin-buttons',"avactis-system/admin/styles/button_styles.css" );
	$styles->add( 'admin-tree-component',"avactis-system/admin/jstree/tree_component.css" );
	$styles->add( 'admin-tree-css',"avactis-system/admin/jstree/themes/default/style.css" );

	foreach ( $rtl_styles as $rtl_style ) {
		$styles->add_data( $rtl_style, 'rtl', 'replace' );
		if ( $suffix ) {
			$styles->add_data( $rtl_style, 'suffix', $suffix );
		}
	}
}

/**
 * Reorder JavaScript scripts array to place prototype before jQuery.
 *
 * @since 2.3.1
 *
 * @param array $js_array JavaScript scripts array
 * @return array Reordered array, if needed.
 */
function asc_prototype_before_jquery( $js_array ) {
	if ( false === $prototype = array_search( 'prototype', $js_array, true ) )
		return $js_array;

	if ( false === $jquery = array_search( 'jquery', $js_array, true ) )
		return $js_array;

	if ( $prototype < $jquery )
		return $js_array;

	unset($js_array[$prototype]);

	array_splice( $js_array, $jquery, 0, 'prototype' );

	return $js_array;
}

/**
 * Load localized data on print rather than initialization.
 *
 * These localizations require information that may not be loaded even by init.
 *
 * @since 2.5.0
 */
function asc_just_in_time_script_localization() {

	asc_localize_script( 'autosave', 'autosaveL10n', array(
		'autosaveInterval' => AUTOSAVE_INTERVAL,
		'blog_id' => 1,
	) );

}

/**
 * Administration Screen CSS for changing the styles.
 *
 * If installing the 'wp-admin/' directory will be replaced with './'.
 *
 * The $_asc_admin_css_colors global manages the Administration Screens CSS
 * stylesheet that is loaded. The option that is set is 'admin_color' and is the
 * color and key for the array. The value for the color key is an object with
 * a 'url' parameter that has the URL path to the CSS file.
 *
 * The query from $src parameter will be appended to the URL that is given from
 * the $_asc_admin_css_colors array value URL.
 *
 * @since 2.6.0
 * @uses $_asc_admin_css_colors
 *
 * @param string $src Source URL.
 * @param string $handle Either 'colors' or 'colors-rtl'.
 * @return string URL path to CSS stylesheet for Administration Screens.
 */
function asc_style_loader_src( $src, $handle ) {
	global $_asc_admin_css_colors;

	if ( defined('ASC_INSTALLING') )
		return preg_replace( '#^wp-admin/#', './', $src );

	if ( 'colors' == $handle ) {
		$color = get_user_option('admin_color');

		if ( empty($color) || !isset($_asc_admin_css_colors[$color]) )
			$color = 'fresh';

		$color = $_asc_admin_css_colors[$color];
		$parsed = parse_url( $src );
		$url = $color->url;

		if ( ! $url ) {
			return false;
		}

		if ( isset($parsed['query']) && $parsed['query'] ) {
			asc_parse_str( $parsed['query'], $qv );
			$url = add_query_arg( $qv, $url );
		}

		return $url;
	}

	return $src;
}

/**
 * Prints the script queue in the HTML head on admin pages.
 *
 * Postpones the scripts that were queued for the footer.
 * print_footer_scripts() is called in the footer to print these scripts.
 *
 * @since 2.8.0
 *
 * @see asc_print_scripts()
 */
function print_head_scripts() {
	global $asc_scripts, $concatenate_scripts;

	if ( ! did_action('asc_print_scripts') ) {
		/** This action is documented in functions.asc-scripts.php */
		do_action( 'asc_print_scripts' );
	}

	if ( !is_a($asc_scripts, 'ASC_Scripts') )
		$asc_scripts = new ASC_Scripts();

	script_concat_settings();
	$asc_scripts->do_concat = $concatenate_scripts;
	$asc_scripts->do_head_items();

	/**
	 * Filter whether to print the head scripts.
	 *
	 * @since 2.8.0
	 *
	 * @param bool $print Whether to print the head scripts. Default true.
	 */
	if ( apply_filters( 'print_head_scripts', true ) ) {
		_print_scripts();
	}

	$asc_scripts->reset();
	return $asc_scripts->done;
}

/**
 * Prints the scripts that were queued for the footer or too late for the HTML head.
 *
 * @since 2.8.0
 */
function print_footer_scripts() {
	global $asc_scripts, $concatenate_scripts;

	if ( !is_a($asc_scripts, 'ASC_Scripts') )
		return array(); // No need to run if not instantiated.

	script_concat_settings();
	$asc_scripts->do_concat = $concatenate_scripts;
	$asc_scripts->do_footer_items();

	/**
	 * Filter whether to print the footer scripts.
	 *
	 * @since 2.8.0
	 *
	 * @param bool $print Whether to print the footer scripts. Default true.
	 */
	if ( apply_filters( 'print_footer_scripts', true ) ) {
		_print_scripts();
	}

	$asc_scripts->reset();
	return $asc_scripts->done;
}

/**
 * @internal use
 */
function _print_scripts() {
	global $asc_scripts, $compress_scripts;

	$zip = $compress_scripts ? 1 : 0;
	if ( $zip && defined('ENFORCE_GZIP') && ENFORCE_GZIP )
		$zip = 'gzip';

	if ( $concat = trim( $asc_scripts->concat, ', ' ) ) {

		if ( !empty($asc_scripts->print_code) ) {
			echo "\n<script type='text/javascript'>\n";
			echo "/* <![CDATA[ */\n"; // not needed in HTML 5
			echo $asc_scripts->print_code;
			echo "/* ]]> */\n";
			echo "</script>\n";
		}

		$concat = str_split( $concat, 128 );
		$concat = 'load%5B%5D=' . implode( '&load%5B%5D=', $concat );

		$src = $asc_scripts->base_url . "/wp-admin/load-scripts.php?c={$zip}&" . $concat . '&ver=' . $asc_scripts->default_version;
		echo "<script type='text/javascript' src='" . esc_attr($src) . "'></script>\n";
	}

	if ( !empty($asc_scripts->print_html) )
		echo $asc_scripts->print_html;
}

/**
 * Prints the script queue in the HTML head on the front end.
 *
 * Postpones the scripts that were queued for the footer.
 * asc_print_footer_scripts() is called in the footer to print these scripts.
 *
 * @since 2.8.0
 */
function asc_print_head_scripts() {

	if ( ! did_action('asc_print_scripts') ) {
		/** This action is documented in functions.asc-scripts.php */
		do_action( 'asc_print_scripts' );
	}

	global $asc_scripts;

	if ( !is_a($asc_scripts, 'ASC_Scripts') )
		return array(); // no need to run if nothing is queued

	return print_head_scripts();
}

/**
 * Private, for use in *_footer_scripts hooks
 *
 * @since 3.3.0
 */
function _asc_footer_scripts() {
	print_late_styles();
	print_footer_scripts();
}

/**
 * Hooks to print the scripts and styles in the footer.
 *
 * @since 2.8.0
 */
function asc_print_footer_scripts() {
	/**
	 * Fires when footer scripts are printed.
	 *
	 * @since 2.8.0
	 */
	do_action( 'asc_print_footer_scripts' );
}

/**
 * Wrapper for do_action('asc_enqueue_scripts')
 *
 * Allows plugins to queue scripts for the front end using asc_enqueue_script().
 * Runs first in asc_head() where all is_home(), is_page(), etc. functions are available.
 *
 * @since 2.8.0
 */
function asc_enqueue_scripts() {
	/**
	 * Fires when scripts and styles are enqueued.
	 *
	 * @since 2.8.0
	 */
	do_action( 'asc_enqueue_scripts' );
}

/**
 * Prints the styles queue in the HTML head on admin pages.
 *
 * @since 2.8.0
 */
function print_admin_styles() {
	global $asc_styles, $concatenate_scripts, $compress_css;

	if ( !is_a($asc_styles, 'ASC_Styles') )
		$asc_styles = new ASC_Styles();

	script_concat_settings();
	$asc_styles->do_concat = $concatenate_scripts;
	$zip = $compress_css ? 1 : 0;
	if ( $zip && defined('ENFORCE_GZIP') && ENFORCE_GZIP )
		$zip = 'gzip';

	$asc_styles->do_items(false);

	/**
	 * Filter whether to print the admin styles.
	 *
	 * @since 2.8.0
	 *
	 * @param bool $print Whether to print the admin styles. Default true.
	 */
	if ( apply_filters( 'print_admin_styles', true ) ) {
		_print_styles();
	}

	$asc_styles->reset();
	return $asc_styles->done;
}

/**
 * Prints the styles that were queued too late for the HTML head.
 *
 * @since 3.3.0
 */
function print_late_styles() {
	global $asc_styles, $concatenate_scripts;

	if ( !is_a($asc_styles, 'ASC_Styles') )
		return;

	$asc_styles->do_concat = $concatenate_scripts;
	$asc_styles->do_footer_items();

	/**
	 * Filter whether to print the styles queued too late for the HTML head.
	 *
	 * @since 3.3.0
	 *
	 * @param bool $print Whether to print the 'late' styles. Default true.
	 */
	if ( apply_filters( 'print_late_styles', true ) ) {
		_print_styles();
	}

	$asc_styles->reset();
	return $asc_styles->done;
}

/**
 * @internal use
 */
function _print_styles() {
	global $asc_styles, $compress_css;

	$zip = $compress_css ? 1 : 0;
	if ( $zip && defined('ENFORCE_GZIP') && ENFORCE_GZIP )
		$zip = 'gzip';

	if ( !empty($asc_styles->concat) ) {
		$dir = $asc_styles->text_direction;
		$ver = $asc_styles->default_version;
		$href = $asc_styles->base_url . "/wp-admin/load-styles.php?c={$zip}&dir={$dir}&load=" . trim($asc_styles->concat, ', ') . '&ver=' . $ver;
		echo "<link rel='stylesheet' href='" . esc_attr($href) . "' type='text/css' media='all' />\n";

		if ( !empty($asc_styles->print_code) ) {
			echo "<style type='text/css'>\n";
			echo $asc_styles->print_code;
			echo "\n</style>\n";
		}
	}

	if ( !empty($asc_styles->print_html) )
		echo $asc_styles->print_html;
}

/**
 * Determine the concatenation and compression settings for scripts and styles.
 *
 * @since 2.8.0
 */
function script_concat_settings() {
	global $concatenate_scripts, $compress_scripts, $compress_css;

	$compressed_output = ( ini_get('zlib.output_compression') || 'ob_gzhandler' == ini_get('output_handler') );

	if ( ! isset($concatenate_scripts) ) {
		$concatenate_scripts = defined('CONCATENATE_SCRIPTS') ? CONCATENATE_SCRIPTS : true;
		if ( ! is_admin() || ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ) )
			$concatenate_scripts = false;
	}

	$concatenate_scripts = false;
	$compress_scripts = false;
	$compress_css = false;
/*	if ( ! isset($compress_scripts) ) {
		$compress_scripts = defined('COMPRESS_SCRIPTS') ? COMPRESS_SCRIPTS : true;
		if ( $compress_scripts && ( ! get_site_option('can_compress_scripts') || $compressed_output ) )
			$compress_scripts = false;
	}

	if ( ! isset($compress_css) ) {
		$compress_css = defined('COMPRESS_CSS') ? COMPRESS_CSS : true;
		if ( $compress_css && ( ! get_site_option('can_compress_scripts') || $compressed_output ) )
			$compress_css = false;
	}
*/
}

add_action( 'asc_default_scripts', 'asc_default_scripts' );
add_filter( 'asc_print_scripts', 'asc_just_in_time_script_localization' );
add_filter( 'print_scripts_array', 'asc_prototype_before_jquery' );

add_action( 'asc_default_styles', 'asc_default_styles' );
add_filter( 'style_loader_src', 'asc_style_loader_src', 10, 2 );