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
	include('../admin.php');
	if(isset($_GET['identifier']) && !empty($_GET['identifier'])){
		$identifier = $_GET['identifier'];
	}
	if(!isset($result) || empty($result)){
		$result = modApiFunc('MenuManager','getPages',$identifier);
	}
	if(!isset($result[0]['classname']) || empty($result[0]['classname'])){
		header("location:index.php");
		die;
	}
		$title = explode(",",$result[0]['title']);
		$header = explode(",",$result[0]['heading']);

		$tpl_title = getxMsg($title[0],$title[1]);
		$tpl_header = getxMsg($header[0],$header[1]);
		$tpl_onload_js = $result[0]['onload_js'];
		$tpl_parent = array();
		$tpl_help = $result[0]['help_identifier'];
		$tpl_class = $result[0]['classname'];
		//$tpl_tinymce = 'yes';
		//$tpl_styles = array('admin-default','admin-custom');
		//$tpl_scripts = array('admin-layout','admin-avactis-main','admin-avactis-validate');
	include('admin.tpl.php');
?>