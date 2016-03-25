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
	require_once('../admin.php');
//		$tpl_styles = array('admin-default','admin-custom');
		$tpl_title = getxMsg('SYS','REPORTS_PAGE_TITLE');
		$tpl_header = getxMsg('SYS','ADMIN_PHP_FILES_REPORTS');
		$tpl_onload_js = '';
		$tpl_parent = array(
					array(
						'name' => getxmsg('SYS','REPORTS_PAGE_TITLE'),
						'url' => 'reports.php'
				));
		$tpl_help = 'reports_tab';
		$tpl_class = 'reports_page';
//		$tpl_scripts = array('admin-layout','admin-avactis-main','admin-avactis-validate');
	include('admin.tpl.php');
?>

<?php
function reports_page()
{
?>

	<script src="./amcharts/charts/amcharts.js" type="text/javascript"></script>
	<script src="./amcharts/charts/serial.js" type="text/javascript"></script>
	<script src="./amcharts/stockchart/amstock.js" type="text/javascript"></script>

	<script src="./amcharts/charts/exporting/amexport.js" type="text/javascript"></script>
	<script src="./amcharts/charts/exporting/rgbcolor.js" type="text/javascript"></script>
	<script src="./amcharts/charts/exporting/canvg.js" type="text/javascript"></script>
	<script src="./amcharts/charts/exporting/jspdf.js" type="text/javascript"></script>
	<script src="./amcharts/charts/exporting/filesaver.js" type="text/javascript"></script>
	<script src="./amcharts/charts/exporting/jspdf.plugin.addimage.js" type="text/javascript"></script>

	<div class="portlet light">
		<div class="row">
			<div class="col-md-6">
				<?php ReportGroups(0,99, 'SALE_REPORTS'); ?>
			</div>
			<div class="col-md-6">
				<?php ReportGroups(100,9999, 'VISITORS_REPORTS'); ?>
			</div>
		</div>
	</div>
<?php
	ReportGroupPage();
}
?>
<script type="text/javascript" src="js/overlib.js"></script>