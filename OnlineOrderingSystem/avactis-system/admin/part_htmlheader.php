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
?><META NAME="Generator" CONTENT="">
<META NAME="Author" CONTENT="">
<META NAME="Keywords" CONTENT="">
<META NAME="Description" CONTENT="">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1" name="viewport"/>
<META http-equiv="Content-Type" content="text/html; charset=<?php Charset('AZ'); ?>">

<SCRIPT language="JavaScript">
var __ASC_FORM_ID__ = '<?php echo modApiFunc('Session', 'get', '__ASC_FORM_ID__'); ?>';
</SCRIPT>

<?php
global $application;
echo $application->combineAdminCSS(array(
    /* 'styles/buttons_styles.css',
    'styles/stylesheet.css',
    'dtree/dtree.css',
    'jstree/tree_component.css',
    'styles/vMenu.css',
    'styles/colorbox.css' */
		//'styles/stylesheet.css',
		//'styles/colorbox.css',
		//'dtree/dtree.css',
		//'styles/vMenu.css',
		//'styles/buttons_styles.css',
		//'jstree/tree_component.css',
));

/* For siteurl */
global $application;
$httpsetting = ((isset($_SERVER["HTTPS"]) && (strtolower($_SERVER["HTTPS"]) == "on" || $_SERVER["HTTPS"] == 1 || $_SERVER["HTTPS"] === true)) || (isset($_SERVER["SERVER_PORT"]) && ($_SERVER["SERVER_PORT"] == "443")))? "https":"http";

if($httpsetting == 'http')
{
	$siteurl = $application->getAppIni('HTTP_URL');
}
else
{
	$siteurl = $application->getAppIni('HTTPS_URL');
}

define("AVACTIS_URL", $siteurl);



?>
<!--  script type="text/javascript" src="js/jquery-1.10.2.min.js"></script-->

<?php
	//    : enqueue the scripts and styles needed for global admin dashboard
	asc_enqueue_style('bootstrap');
	//asc_enqueue_style( 'colors' );
	asc_enqueue_style( 'ie' );
	asc_enqueue_style( 'gfont-open-sans' );
	asc_enqueue_style( 'font-awesome' );
	asc_enqueue_style( 'simple-line-icons' );
	asc_enqueue_style( 'jquery-uniform' );
	asc_enqueue_style( 'bootstrap-switch' );
	asc_enqueue_style( 'jquery.gritter' );
	asc_enqueue_style( 'jquery-colorbox' );
	asc_enqueue_style( 'admin-tasks' );
	asc_enqueue_style( 'components' );
	asc_enqueue_style( 'plugins' );
	asc_enqueue_style( 'admin-layout' );
	asc_enqueue_style( 'admin-default' );
	asc_enqueue_style( 'admin-custom' );
	asc_enqueue_style( 'bootstrap-toastr' );
	//asc_enqueue_style('admin-buttons');
	asc_enqueue_style('admin-tree-component');
	asc_enqueue_style('admin-tree-css');

	asc_enqueue_script('bootstrap',"",null,"",true);
	//asc_enqueue_script('utils');
?>
<?php
	//    : Figure out the correct place to load all admin scripts and styles
	do_action( 'admin_print_scripts' );
	do_action( 'admin_print_styles' );
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