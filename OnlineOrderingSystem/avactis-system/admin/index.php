<?php

?><?php include('../admin.php'); ?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->
<HEAD>
<meta charset="utf-8"/>
<TITLE>Online Ordering System</TITLE>
<?php
		global $parent_file;
		$parent_file = 'index.php';
		//
		// Insert HTML Header
		//
	include('part_htmlheader.php');
	require_once('includes/dashboard.php');

	asc_dashboard_setup();
	$screen = get_current_screen();
?>



<script src="./amcharts/charts/amcharts.js" type="text/javascript"></script>
<script src="./amcharts/charts/serial.js" type="text/javascript"></script>

<script language="JavaScript">
function refreshLicenseMention()
{
	jQuery.post(
		'license_mention.php', // backend
		{},
		// Function is called when an answer arrives.
		function(result, output) {
			el = document.getElementById('p_license_mention');
			el.innerHTML = result;
		});
}

function refreshNotifyStoreUpgrade()
{
	jQuery.post(
		'update_notifier.php', // backend
		{},
		// Function is called when an answer arrives.
		function(result, output) {
			el = document.getElementById('dashboard_core_upgrade_notify');
			el.innerHTML = result;
		});
}

function submitLicenseInfoForm()
{
	var key = document.getElementById('NewLicenseKey');
	formatLicenseKey(key);

	if (key.value.length < 20 || key.value.length > 24)
	{
		alert('<?php msg("LICENSE_WARNING_010"); ?>');
		return;
	}
	document.forms['LicenseInfo'].submit();
}

function formatLicenseKey(element)
{
	key = element.value.toUpperCase();
	key = key.replace(/ /g, '').replace(/[^a-zA-Z0-9\-]/g, '');
	element.value = key;
}
</script>


<?php
	global $application;
	$mr = &$application->getInstance('MessageResources');

//    loadClass('CCategoryInfo');
//    $home_cat_info = new CCategoryInfo(1);
//    $prods_quan = $home_cat_info->getCategoryTagValue('productsnumberrecursively_all_product_links');

	$cs = modApiFunc('Configuration', 'getCacheSize', true);
	$timeline = modApiFunc('Timeline','getTimelineRecsCount');
	$timeline = ($timeline==0 ? getMsg("SYS","ADMIN_PHP_FILES_NO_LOG_RECORDS") : $timeline . getMsg("SYS","ADMIN_PHP_FILES_LOG_RECORDS"));
	$store_status = (bool)!modApiFunc("Configuration", "getValue", "store_online");
	$real_product_count = modApiFunc('Catalog','getRealProductsCount');
	$ctgrs_quan = modApiFunc('Catalog','getCategoriesCount');
	$new_orders_quan = modApiFunc("Checkout", "getOrderCount", 1);
	$in_progress_quan = modApiFunc("Checkout", "getOrderCount", 2);
	$ready_to_ship_quan = modApiFunc("Checkout", "getOrderCount", 3);
?>
</HEAD>


<BODY class="boxed page-sidebar-closed-hide-logo page-container-bg-solid page-sidebar-closed-hide-logo page-header-fixed" onload="refreshLicenseMention();refreshNotifyStoreUpgrade();">


<?php	//
		// Insert Page Header
		//
	include('part_header.php');
?>
<div class="clearfix"></div>
<div class="page-container">
	<div class="page-sidebar-wrapper">
		<?php require_once( ABSPATH . 'avactis-system/admin/admin-header.php' ); ?>
	</div>
	<div class="page-content-wrapper">
		<div class="page-content">
<!-- BEGIN PAGE HEADER-->
			<h3 class="page-title"><?php msg('MENU_DASHBOARD'); ?></h3>
			<?php SetupGuide(); ?>
			

			<div class="page-bar">
				<ul class="page-breadcrumb">
					<li><i class="fa fa-home"></i> <a href=""><?php msg('MENU_HOME'); ?></a> <i class="fa fa-angle-right"></i></li>
				</ul>
			</div>
<!-- END PAGE HEADER-->
<!-- BEGIN DASHBOARD STATS -->
			<div class="row">
				<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
					<a class="dashboard-stat dashboard-stat-light blue-soft" href="orders.php?asc_action=OrdersSearchByStatus&status_id=1">
						<div class="visual"><i class="fa fa-money"></i></div>
						<div class="details">
							<div class="number"><?php StatisticsSalesTotalToday(); ?></div>
							<div class="desc"><?php xmsg('RPTS','SALES_TOTAL_TODAY'); ?></div>
						</div>
					</a>
				</div>
				<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
					<a class="dashboard-stat dashboard-stat-light red-soft" href="orders.php?asc_action=OrdersSearchByStatus&status_id=1">
						<div class="visual"><i class="fa fa-shopping-cart"></i></div>
						<div class="details">
							<div class="number"><?php StatisticsOrdersNumberToday(); ?></div>
							<div class="desc"><?php xmsg('RPTS','ORDERS_NUMBER_TODAY'); ?></div>
						</div>
					</a>
				</div>
				<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
					<a class="dashboard-stat dashboard-stat-light green-soft" href="catalog_manage_products.php?asc_action=SetCurrCat&category_id=1">
						<div class="visual"><i class="fa fa-tags"></i></div>
						<div class="details">
							<div class="number"><?php echo $real_product_count; ?></div>
							<div class="desc"><?php echo $mr->getMessage("All_products"); ?></div>
						</div>
					</a>
				</div>
				<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
					<a class="dashboard-stat dashboard-stat-light purple-soft" href="catalog_manage_categories.php">
						<div class="visual"><i class="fa fa-folder-open"></i></div>
						<div class="details">
							<div class="number"><?php echo $ctgrs_quan; ?></div>
							<div class="desc"><?php echo $mr->getMessage("Categories"); ?></div>
						</div>
					</a>
				</div>
			</div>
<!-- END DASHBOARD STATS -->
<?php SetupWarnings(); ?>
<!-- BEGIN Dashboard-widgets-wrap -->
			<div class="row" id="dashboard-widgets-wrap">
				<?php asc_dashboard(); ?>
			</div>
<!-- END Dashboard-widgets-wrap -->
		</div>
	</div>
</div>
<script type="text/javascript" src="js/overlib.js"></script>
<?php include('part_footer.php') ?>
<script>
jQuery(document).ready(function() {
		ASC_ADMIN.init(); // init asc-admin core componets
		Layout.init(); // init layout
});
</script>
</BODY>
</HTML>