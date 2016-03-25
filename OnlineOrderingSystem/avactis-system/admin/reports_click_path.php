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
	$type = modApiFunc('Request','getValueByKey','type');
	$title="";
	if ($type == "robot") {
		$title=getxMsg('RPTS','REPORT_CRAWLER_SCANNED_PAGES');
	} else {
		$title=getxMsg('RPTS','REPORT_SEANCE_CLICK_PATH');
	}
//		$tpl_styles = array('admin-default','admin-custom');
		$tpl_title = $title;
		$tpl_header = $title;
		$tpl_onload_js = '';
		$tpl_parent = array(
					array(
						'name' => getxmsg('SYS','REPORTS_PAGE_TITLE'),
						'url' => 'reports.php'
				));
		$tpl_help = 'reports_tab';
		$tpl_class = 'report_path';
//		$tpl_scripts = array('admin-layout','admin-avactis-main','admin-avactis-validate');
	include('popup_window.php');
?>
<?php
	function report_path(){
		$type = modApiFunc('Request','getValueByKey','type');
		$title="";
		if ($type == "robot") {
			$title=getxMsg('RPTS','REPORT_CRAWLER_SCANNED_PAGES');
		} else {
			$title=getxMsg('RPTS','REPORT_SEANCE_CLICK_PATH');
		}
?>
<script language="javascript" type="text/javascript">
    function submitHandler(formname)
    {
      var form = document.getElementById(formname);
      if (form.onsubmit) form.onsubmit();
      form.submit();
    }
</script>
<!-- BEGIN Portlet PORTLET-->
<div class="portlet light">
	<div class="portlet-title">
		<div class="caption">
			<span class="caption-subject font-green-sharp bold uppercase">
				<i class="fa fa-gift"></i>&nbsp;<?php print($title); ?>
			</span>
		</div>
	</div>
	<div class="portlet-body">
		<form action="" name="CacheSettings" id="CacheSettings" method="post">
			<input type="hidden" name="asc_action" value="UpdateCacheSettings">
			<?php
					ReportSeanceClickPath(REPORT_OUTPUT_CONTENT);
			?>
		</form>
	</div>
</div>
<!-- END Portlet PORTLET-->
<?php
	}
?>