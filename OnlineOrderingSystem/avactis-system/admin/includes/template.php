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
 * Add a meta box to an edit form.
 *
 * @since 2.5.0
 *
 * @param string           $id            String for use in the 'id' attribute of tags.
 * @param string           $title         Title of the meta box.
 * @param callback         $callback      Function that fills the box with the desired content.
 *                                        The function should echo its output.
 * @param string|ASC_Screen $screen        Optional. The screen on which to show the box (like a post
 *                                        type, 'link', or 'comment'). Default is the current screen.
 * @param string           $context       Optional. The context within the screen where the boxes
 *                                        should display. Available contexts vary from screen to
 *                                        screen. Post edit screen contexts include 'normal', 'side',
 *                                        and 'advanced'. Comments screen contexts include 'normal'
 *                                        and 'side'. Menus meta boxes (accordion sections) all use
 *                                        the 'side' context. Global default is 'advanced'.
 * @param string           $priority      Optional. The priority within the context where the boxes
 *                                        should show ('high', 'low'). Default 'default'.
 * @param array            $callback_args Optional. Data that should be set as the $args property
 *                                        of the box array (which is the second parameter passed
 *                                        to your callback). Default null.
 */
function add_meta_box( $id, $title, $callback, $screen = null, $context = 'advanced', $priority = 'default', $callback_args = null ) {
	global $asc_meta_boxes;


	if ( empty( $screen ) )
		$screen = get_current_screen();
	elseif ( is_string( $screen ) )
		$screen = convert_to_screen( $screen );

	$page = $screen->id;

	//$page = 1; //    : Need to put logic for pageids?
	if ( !isset($asc_meta_boxes) )
		$asc_meta_boxes = array();
	if ( !isset($asc_meta_boxes[$page]) )
		$asc_meta_boxes[$page] = array();
	if ( !isset($asc_meta_boxes[$page][$context]) )
		$asc_meta_boxes[$page][$context] = array();

	foreach ( array_keys($asc_meta_boxes[$page]) as $a_context ) {
		foreach ( array('high', 'core', 'default', 'low') as $a_priority ) {
			if ( !isset($asc_meta_boxes[$page][$a_context][$a_priority][$id]) )
				continue;

			// If a core box was previously added or removed by a plugin, don't add.
			if ( 'core' == $priority ) {
				// If core box previously deleted, don't add
				if ( false === $asc_meta_boxes[$page][$a_context][$a_priority][$id] )
					return;

				/*
				 * If box was added with default priority, give it core priority to
				 * maintain sort order.
				 */
				if ( 'default' == $a_priority ) {
					$asc_meta_boxes[$page][$a_context]['core'][$id] = $asc_meta_boxes[$page][$a_context]['default'][$id];
					unset($asc_meta_boxes[$page][$a_context]['default'][$id]);
				}
				return;
			}
			// If no priority given and id already present, use existing priority.
			if ( empty($priority) ) {
				$priority = $a_priority;
			/*
			 * Else, if we're adding to the sorted priority, we don't know the title
			 * or callback. Grab them from the previously added context/priority.
			 */
			} elseif ( 'sorted' == $priority ) {
				$title = $asc_meta_boxes[$page][$a_context][$a_priority][$id]['title'];
				$callback = $asc_meta_boxes[$page][$a_context][$a_priority][$id]['callback'];
				$callback_args = $asc_meta_boxes[$page][$a_context][$a_priority][$id]['args'];
			}
			// An id can be in only one priority and one context.
			if ( $priority != $a_priority || $context != $a_context )
				unset($asc_meta_boxes[$page][$a_context][$a_priority][$id]);
		}
	}

	if ( empty($priority) )
		$priority = 'low';

	if ( !isset($asc_meta_boxes[$page][$context][$priority]) )
		$asc_meta_boxes[$page][$context][$priority] = array();

	$asc_meta_boxes[$page][$context][$priority][$id] = array('id' => $id, 'title' => $title, 'callback' => $callback, 'args' => $callback_args);
}


/**
 * Meta-Box template function
 *
 * @since 2.5.0
 *
 * @param string|object $screen Screen identifier
 * @param string $context box context
 * @param mixed $object gets passed to the box callback function as first parameter
 * @return int number of meta_boxes
 */
function do_meta_boxes( $screen, $context, $object ) {
	global $asc_meta_boxes;
	static $already_sorted = false;

	if ( empty( $screen ) )
		$screen = get_current_screen();
	elseif ( is_string( $screen ) )
	$screen = convert_to_screen( $screen );

	$page = $screen->id;

	//$hidden = get_hidden_meta_boxes( $screen );

	printf('<div id="%s-sortables" class="meta-box-sortables">', htmlspecialchars($context));

	$i = 0;
	do {
		// Grab the ones the user has manually sorted. Pull them out of their previous context/priority and into the one the user chose
		if ( !$already_sorted /*&& $sorted = get_user_option( "meta-box-order_$page" )*/ ) {
			foreach ( $sorted as $box_context => $ids ) {
				foreach ( explode(',', $ids ) as $id ) {
					if ( $id && 'dashboard_browser_nag' !== $id )
						add_meta_box( $id, null, null, $screen, $box_context, 'sorted' );
				}
			}
		}
		$already_sorted = true;

		if ( !isset($asc_meta_boxes) || !isset($asc_meta_boxes[$page]) || !isset($asc_meta_boxes[$page][$context]) )
			break;

		foreach ( array('high', 'sorted', 'core', 'default', 'low') as $priority ) {
			if ( isset($asc_meta_boxes[$page][$context][$priority]) ) {
				foreach ( (array) $asc_meta_boxes[$page][$context][$priority] as $box ) {
					if ( false == $box || ! $box['title'] )
						continue;
					$i++;
					$hidden_class = in_array($box['id'], $hidden) ? ' hide-if-js' : '';
					echo '<div id="' . $box['id'] . '" class="postbox ' . /*postbox_classes($box['id'], $page) .*/ $hidden_class . '" ' . '>' . "\n";
					if ( 'dashboard_browser_nag' != $box['id'] )
						echo '<div class="handlediv" title="' . 'Click to toggle' . '"><br /></div>';
					echo "<h3 class='hndle'><span>{$box['title']}</span></h3>\n";
					echo '<div class="inside">' . "\n";
					call_user_func($box['callback'], $object, $box);
					echo "</div>\n";
					echo "</div>\n";
				}
			}
		}
	} while(0);

	echo "</div>";

	return $i;

}

/**
 * Convert a screen string to a screen object
 *
 * @since 4.7.0
 *
 * @param string $hook_name The hook name (also known as the hook suffix) used to determine the screen.
 * @return ASC_Screen Screen object.
 */
function convert_to_screen( $hook_name ) {
	if ( ! class_exists( 'ASC_Screen' ) ) {
		_doing_it_wrong( 'convert_to_screen(), add_meta_box()', __( "Likely direct inclusion of wp-admin/includes/template.php in order to use add_meta_box(). This is very wrong. Hook the add_meta_box() call into the add_meta_boxes action instead." ), '3.3' );
		return (object) array( 'id' => '_invalid', 'base' => '_are_belong_to_us' );
	}

	return ASC_Screen::get( $hook_name );
}

?>