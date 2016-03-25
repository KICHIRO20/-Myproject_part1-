<?php

?><?php
	require_once('../admin.php');
	$req = &$application->getInstance('Request');
	$pg_view = $req->getValueByKey('page_view');

	if(empty($pg_view))
		die;
	//@since v4.7.6
	$tpl_params = apply_filters('asc_add_admin_tpl_parameters','');

	$tpl_title = $tpl_params['tpl_title'];
	$tpl_header = $tpl_params['tpl_header'];
	$tpl_onload_js = $tpl_params['tpl_onload_js'];
	$tpl_parent = $tpl_params['tpl_parent'];
	$tpl_help = $tpl_params['tpl_help'];

	$tpl_class = $pg_view;

	include('admin.tpl.php');
?>