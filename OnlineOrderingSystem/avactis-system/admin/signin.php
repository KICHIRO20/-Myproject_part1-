<?php

?><?php include('../admin.php'); ?>
<!DOCTYPE html>
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
<meta charset="utf-8"/>
<title>Online Ordering System For Appliances Store with Stock Management</title>

<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<meta http-equiv="Content-type" content="text/html; charset=utf-8">
<meta content="" name="description"/>
<meta content="" name="author"/>
<!-- BEGIN GLOBAL MANDATORY STYLES -->
<?php include('part_htmlheader.php'); ?>
	<!-- END GLOBAL MANDATORY STYLES -->
<?php
	//    : enqueue the scripts and styles needed for global admin dashboard
	asc_enqueue_style('select2');
	asc_enqueue_style( 'admin-login-soft' );
	asc_enqueue_style( 'components' );
	asc_enqueue_style( 'plugins' );
	asc_enqueue_style( 'admin-layout' );
	asc_enqueue_style( 'admin-default' );
	asc_enqueue_style( 'admin-custom' );
?>
<?php
	do_action( 'admin_print_styles' );
?>
<link rel="shortcut icon" href="favicon.ico"/>
</head>
<body class="login">
<!-- BEGIN LOGO -->
<div class="logo">
	<a href="index.php">
		<img src="images/fdfss.jpg" alt= ""/>
	</a>
</div>

<div class="content">
<style> .required { color: #ff0000; font-family: Tahoma, sans-serif; font-size: 8pt; font-weight: bold; text-decoration: none; } </style>
<SCRIPT LANGUAGE="JavaScript">
<!--
        document.write('<?php AdminSignIn(); ?>');
		jQuery(function() { setFocusOnFirstElement() });
//-->
</SCRIPT>
<NOSCRIPT>
<div class="form">
	<h3 class="form-section"><?php Msg('SIGN_IN_PAGE_NAME'); ?></h3>
	<div class="alert alert-danger"><?php Msg('SIGN_IN_JS_DISABLED_ERR_001'); ?></div>

	<div class="alert alert-warning">
		<B><?php Msg('SIGN_IN_JS_DISABLED_ERR_002'); ?></B><br /><br />
		<?php Msg('SIGN_IN_JS_DISABLED_ERR_003'); ?>
	</div>
	<div class="alert alert-warning">
		<B><?php Msg('SIGN_IN_JS_DISABLED_ERR_004'); ?></B><br /><br />
		<?php Msg('SIGN_IN_JS_DISABLED_ERR_005'); ?>
	</div>
	<div class="alert alert-info">
		<B><?php Msg('SIGN_IN_JS_DISABLED_ERR_006'); ?></B><br /><br />
		<?php Msg('SIGN_IN_JS_DISABLED_ERR_007'); ?>
	</div>
	<div class="alert alert-info">
		<B><?php Msg('SIGN_IN_JS_DISABLED_ERR_008'); ?></B><br /><br />
		<?php Msg('SIGN_IN_JS_DISABLED_ERR_009'); ?>
	</div>
	<div class="copyright">Online Ordering System for Appliances Store with Stock Management</div>
</div>
</NOSCRIPT>
</div>
<!-- END LOGIN -->

<?php
asc_enqueue_script('jquery.validate');
asc_enqueue_script('jquery-backstretch');
asc_enqueue_script('select2');
asc_enqueue_script('asc-admin');
asc_enqueue_script('admin-layout');
asc_enqueue_script('admin-login-soft');
asc_enqueue_script('admin-avactis-md5');
do_action( 'admin_print_scripts' );
?>
<script>
jQuery(document).ready(function() {
  ASC_ADMIN.init(); // init asc-admin core components
Layout.init(); // init current layout
//Demo.init(); // init demo features
  Login.init();
       // init background slide images
       jQuery.backstretch([
        "images/1.jpg",
        "images/2.jpg",
        "images/3.jpg",
        "images/4.jpg"
        ], {
          fade: 1000,
          duration: 8000
    }
    );
});
</script>
<?php include 'part_footer_scripts.php'; include('stat/stat.php'); ?>
<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>