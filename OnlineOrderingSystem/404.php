<?php include('init.php'); ?>
<!DOCTYPE HTML>
<HTML xmlns="http://www.w3.org/1999/xhtml" xml:lang="EN" lang="EN">
<HEAD>
<TITLE>Page Not Found</TITLE>
<META name="author" content="Avactis Team" />
<META http-equiv="Content-type" content="text/html; charset=UTF-8" />
<META name="viewport" content="width=device-width, minimumscale=1.0, maximum-scale=1.0" />
<!--[if lt IE 9]>
	<script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<link href='http://fonts.googleapis.com/css?family=Merriweather' rel='stylesheet' type='text/css'>
<style>
.fa-search { display:none !important;}
.search_404 {width:40%;}
</style>
</HEAD>
 <?php include(getTemplateFileAbsolutePath('pages/templates/part.header.tpl.html')); ?>
<BODY>
	<div id="wrapper">
		<br><br><br><br><br><br>
		<center>
			<h1><?php xmsg('ERRD','TITLE_404'); ?></h1>

			<h2><?php xmsg('ERRD','SUBTITLE_404'); ?></h2>

			<h5><?php xmsg('ERRD','MSG_404'); ?></h5>

			<div class="search_404"><?php SearchForm(); ?></div>
		</center>
	</div>
</BODY>
</HTML>