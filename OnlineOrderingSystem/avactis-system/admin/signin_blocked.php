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
?><?php include('../admin.php'); ?>
<!DOCTYPE html>
<html>
<head>
<title><?php Msg('SIGN_IN_BLOCKED_PAGE_TITLE'); ?></title>
<?php include('part_htmlheader.php'); ?>
	<!-- END GLOBAL MANDATORY STYLES -->
<?php
	//    : enqueue the scripts and styles needed for global admin dashboard
	asc_enqueue_style( 'admin-login-soft' );
?>
<?php
	do_action( 'admin_print_styles' );
?>
</head>
<body class="login">
<style>
.required {
	color: #ff0000;
	font-family: Tahoma, sans-serif;
	font-size: 8pt;
	font-weight: bold;
	text-decoration: none;
}
</style>
<div class="logo">
	<a href="index.php">
	<img src="images/logo-login.png" alt= ""/>
	</a>
</div>
	<div class="content">
			<h3><?php Msg('SIGN_IN_PAGE_NAME'); ?></h3>
			<p><?php Msg('SIGN_IN_BLOCKED_001'); ?></p>
		<div class="form-group">
		<p><?php Msg('COPYRIGHT_TEXT'); ?></p>
		</div>
	</div>
<?php
asc_enqueue_script('jquery-backstretch');
do_action( 'admin_print_scripts' );
?>
<script>
jQuery(document).ready(function() {

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
</BODY>
</HTML>