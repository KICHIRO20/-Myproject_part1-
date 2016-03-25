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
	$req = &$application->getInstance('Request');
	$pg_view = $req->getValueByKey('page_view');

	if(empty($pg_view) && !empty($tpl_class))
		$pg_view = $tpl_class;
?>
<!DOCTYPE html>
<HTML>
<HEAD><TITLE><?php if(!empty($tpl_title)) echo $tpl_title; ?></TITLE>
<META NAME="Generator" CONTENT="">
<META NAME="Author" CONTENT="">
<META NAME="Keywords" CONTENT="">
<META NAME="Description" CONTENT="">
<META http-equiv="X-UA-Compatible" content="IE=edge">
<META content="width=device-width, initial-scale=1" name="viewport"/>
<META http-equiv="Content-Type" content="text/html; charset=<?php Charset('AZ'); ?>">
<style>
html, body { width:100%; height:100%; }
</style>
<SCRIPT language="JavaScript">
var __ASC_FORM_ID__ = '<?php echo modApiFunc('Session', 'get', '__ASC_FORM_ID__'); ?>';
</SCRIPT>

<?php
/* For siteurl */
global $application;
$httpsetting = ((isset($_SERVER["HTTPS"]) && (strtolower($_SERVER["HTTPS"]) == "on" || $_SERVER["HTTPS"] == 1 || $_SERVER["HTTPS"] === true)) || (isset($_SERVER["SERVER_PORT"]) && ($_SERVER["SERVER_PORT"] == "443")))? "https":"http";
if($httpsetting == 'http')
	$siteurl = $application->getAppIni('HTTP_URL');
else
	$siteurl = $application->getAppIni('HTTPS_URL');
define("AVACTIS_URL", $siteurl);

	//    : enqueue the scripts and styles needed for global admin dashboard
	asc_enqueue_style('bootstrap');
	asc_enqueue_style( 'font-awesome' );
	asc_enqueue_style( 'gfont-open-sans' );
	asc_enqueue_style( 'simple-line-icons' );
	asc_enqueue_style( 'jquery-uniform' );
	asc_enqueue_style( 'bootstrap-switch' );
//	asc_enqueue_style( 'jquery.gritter' );
//	asc_enqueue_style( 'admin-tasks' );
//	asc_enqueue_style( 'plugins' );
	asc_enqueue_style( 'components' );
	asc_enqueue_style( 'admin-layout' );
	asc_enqueue_style( 'admin-default' );
	asc_enqueue_style( 'admin-custom' );
	asc_enqueue_style( 'jquery-colorbox' );

	if(isset($tpl_styles) && !empty($tpl_styles))
	{
		foreach ($tpl_styles as $style)
		{
			asc_enqueue_style( $style );
		}
	}
	do_action( 'admin_print_styles' );

	//    : Figure out the correct place to load all admin scripts and styles
	asc_enqueue_script('bootstrap',"",null,"",true);
	do_action( 'admin_print_scripts' );
?>

	<script type="text/javascript" src="js/avactis-jquery_post_extend.js"></script>
	<!-- script type="text/javascript" src="js/jquery-migrate-1.1.1.min.js"></script-->

<!--[if lt IE 7]>
<style type="text/css">
.transparent_block {
  background-image: none;
  filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='images/halftranspixel.png', sizingMethod='scale');
}
</style>
<![endif]-->

<SCRIPT language="JavaScript">
var window_onload_timeout = 0;
</SCRIPT>
</HEAD>

<BODY style="background-color:#FFF;" onload="<?php echo $tpl_onload_js; ?>">
	<div class="clearfix"></div>
	<div class="portlet box">
		<div class="page-content-wrapper">
				<?php $pg_view(); ?>
		</div>
	</div>
<?php
// Insert Page Footer
	include('part_footer_popup.php');
    if (in_array($pg_view, array('CMS_Page_Data', 'LabelData', 'Newsletter_Compose')) || (isset($tpl_tinymce) && $tpl_tinymce==='yes')) {
		include('part_tinymce.php');
	}
?>
<script>
jQuery(document).ready(function() {
        ASC_ADMIN.init();
});

(function() {
  window.alert = function(msg) {
    bootbox.alert(msg);
  };
})();
</script>
</BODY>
</HTML>