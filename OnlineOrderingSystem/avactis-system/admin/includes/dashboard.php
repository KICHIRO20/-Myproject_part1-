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
 * Avactis Dashboard Widget Administration Screen API
 *
 * @package Avactis
 * @subpackage Admin
 */

/**
 * Registers dashboard widgets.
 *
 * Handles POST requests
 *
 * @since 4.7.0
 */

function asc_dashboard_setup() {
	global $asc_registered_widgets, $asc_registered_widget_controls, $asc_dashboard_control_callbacks;
	$asc_dashboard_control_callbacks = array();
	$screen = get_current_screen();

	asc_add_dashboard_widget( 'dashboard_recent_visitors', ' ', 'asc_dashboard_recent_visitors' );
	asc_add_dashboard_widget( 'dashboard_top_10_sellers', ' ', 'asc_dashboard_top10sellers' );
//	asc_add_dashboard_widget( 'dashboard_revenue_stats', 'Revenue', 'asc_dashboard_revenue_stats' );
	asc_add_dashboard_widget( 'dashboard_revenue_last_10_days', ' ', 'asc_dashboard_revenue_last_10_days' );
	asc_add_dashboard_widget( 'dashboard_revenue_last_10_months', ' ', 'asc_dashboard_revenue_last_10_months' );
	asc_add_dashboard_widget( 'dashboard_core_upgrade_notify', ' ', 'asc_dashboard_notify_core_upgrade' );
}


function asc_add_dashboard_widget( $widget_id, $widget_name, $callback, $control_callback = null, $callback_args = null ) {
	$screen = get_current_screen();
	global $asc_dashboard_control_callbacks;

	/*
	if ( $control_callback && current_user_can( 'edit_dashboard' ) && is_callable( $control_callback ) ) {
		$asc_dashboard_control_callbacks[$widget_id] = $control_callback;
		if ( isset( $_GET['edit'] ) && $widget_id == $_GET['edit'] ) {
			list($url) = explode( '#', add_query_arg( 'edit', false ), 2 );
			$widget_name .= ' <span class="postbox-title-action"><a href="' . esc_url( $url ) . '">' . __( 'Cancel' ) . '</a></span>';
			$callback = '_asc_dashboard_control_callback';
		} else {
			list($url) = explode( '#', add_query_arg( 'edit', $widget_id ), 2 );
			$widget_name .= ' <span class="postbox-title-action"><a href="' . esc_url( "$url#$widget_id" ) . '" class="edit-box open-box">' . __( 'Configure' ) . '</a></span>';
		}
	}
	*/

	$side_widgets = array( 'dashboard_revenue_last_10_days', 'dashboard_revenue_last_10_months' );

	$location = 'normal';
	if ( in_array($widget_id, $side_widgets) )
		$location = 'side';

	$priority = 'core';
	if ( 'dashboard_browser_nag' === $widget_id )
		$priority = 'high';

	add_meta_box( $widget_id, $widget_name, $callback, $screen, $location, $priority, $callback_args );
}

/**
 * Displays the dashboard.
 *
 * @since 2.5.0
 */
function asc_dashboard() {

	$screen = get_current_screen();
	$columns = absint( $screen->get_columns() );
	$columns_css = '';
	if ( $columns ) {
		$columns_css = " columns-$columns";
	}

	?>
<div id="dashboard-widgets" class="metabox-holder<?php echo $columns_css; ?>">
	<div id="postbox-container-1" class="col-md-6 col-sm-6 postbox-container">
	<?php do_meta_boxes( $screen->id, 'normal', '' ); ?>
	</div>
	<div id="postbox-container-2" class="col-md-6 col-sm-6 postbox-container">
	<?php do_meta_boxes( $screen->id, 'side', '' ); ?>
	</div>
	<div id="postbox-container-3" class="col-md-6 col-sm-6 postbox-container">
	<?php do_meta_boxes( $screen->id, 'column3', '' ); ?>
	</div>
	<div id="postbox-container-4" class="col-md-6 col-sm-6 postbox-container">
	<?php do_meta_boxes( $screen->id, 'column4', '' ); ?>
	</div>
</div>

<?php
	//asc_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
	//asc_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );

}



/**
 * Dashboard widget that displays revenue stats about the store.
 *
 * @since 4.7.0
 */

function asc_dashboard_notify_core_upgrade() {
	//NotifyCoreUpgrade();    /* commented for Bug fix 2671 - Performance improvement : use ajax call to check whether update is available or not */
}

function asc_dashboard_recent_visitors() {
	ReportRecentVisitorStatisticsShort();
}

function asc_dashboard_top10sellers() {
	ReportTop10SellersByItemsLast30Days();
}

function asc_dashboard_revenue_last_10_days() {
?>
<div class="portlet light ">
	<div class="portlet-title">
		<div class="caption">
			<span class="caption-subject font-green-sharp bold uppercase"><i class="fa fa-bar-chart-o"></i>&nbsp;Statistics</span>
			<span class="caption-helper">Last 10 Days</span>
		</div>
	</div>
	<div class="portlet-body">
		<?php ChartOrdersByDayLast10Days(REPORT_OUTPUT_SIMPLE_AJAX_LOADER); ?>
	</div>
</div>
<?php
}

function asc_dashboard_revenue_last_10_months() {
?>
<div class="portlet light ">
	<div class="portlet-title">
		<div class="caption">
			<span class="caption-subject font-green-sharp bold uppercase"><i class="fa fa-bar-chart-o"></i>&nbsp;Statistics</span>
			<span class="caption-helper">Last 10 Months</span>
		</div>
	</div>
	<div class="portlet-body">
<?php ChartOrdersByDayLast10Months(REPORT_OUTPUT_SIMPLE_AJAX_LOADER); ?>
	</div>
</div>
<?php
}

function asc_dashboard_revenue_stats() {
	?>
<div class="portlet light ">
	<div class="portlet-title">
		<div class="caption">
			<i class="icon-share font-red-sunglo hide"></i> <span
				class="caption-subject font-red-sunglo bold uppercase">Revenue</span>
			<span class="caption-helper">monthly stats...</span>
		</div>
		<div class="actions">
			<div class="btn-group">
				<a href="" class="btn grey-salsa btn-circle btn-sm dropdown-toggle"
					data-toggle="dropdown" data-hover="dropdown"
					data-close-others="true"> Filter Range&nbsp;<span
					class="fa fa-angle-down"> </span>
				</a>
				<ul class="dropdown-menu pull-right">
					<li><a href="javascript:;"> Q1 2014 <span
							class="label label-sm label-default"> past </span>
					</a></li>
					<li><a href="javascript:;"> Q2 2014 <span
							class="label label-sm label-default"> past </span>
					</a></li>
					<li class="active"><a href="javascript:;"> Q3 2014 <span
							class="label label-sm label-success"> current </span>
					</a></li>
					<li><a href="javascript:;"> Q4 2014 <span
							class="label label-sm label-warning"> upcoming </span>
					</a></li>
				</ul>
			</div>
		</div>
	</div>
	<?php //    : Portlet Body Pending ?>
	<div class="portlet-body">
		<div id="site_activities_loading">
			<img src="images/loading.gif" alt="loading"/>
		</div>
		<div id="site_activities_content" class="display-none">
			<div id="site_activities" style="height: 228px;"></div>
		</div>
		<div style="margin: 20px 0 10px 30px">
			<div class="row">
				<div class="col-md-3 col-sm-3 col-xs-6 text-stat">
					<span class="label label-sm label-success">
						Revenue: </span>
					<h3>$13,234</h3>
				</div>
				<div class="col-md-3 col-sm-3 col-xs-6 text-stat">
					<span class="label label-sm label-danger">
						Shipment: </span>
					<h3>$1,134</h3>
				</div>
				<div class="col-md-3 col-sm-3 col-xs-6 text-stat">
					<span class="label label-sm label-primary">
						Orders: </span>
					<h3>235090</h3>
				</div>
			</div>
		</div>
	</div>
</div>
	<?php

	// Comments

	/**
	 * Filter the array of extra elements to list in the 'Revenue Stats'
	 * dashboard widget.
	 *
	 * @since 4.7.0
	 *
	 * @param array $items Array of extra 'Revenue Stats' widget items.
	 */
	$elements = apply_filters( 'asc_dashboard_revenue_stats', array() );

	if ( $elements ) {
		echo '<li>' . implode( "</li>\n<li>", $elements ) . "</li>\n";
	}

	?>
	<?php
	/*
	 * activity_box_end has a core action, but only prints content when multisite.
	 * Using an output buffer is the only way to really check if anything's displayed here.
	 */
	ob_start();

	/**
	 * Fires at the end of the 'Revenue Stats' dashboard widget.
	 *
	 * @since 4.7.0
	 */
	do_action( 'revenue_stats_end' );

	$actions = ob_get_clean();

}