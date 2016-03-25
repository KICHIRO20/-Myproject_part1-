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

require_once('asc-config.php');
require_once('functions.php');

$output = array();

$output["success"] = 1;
$output["msg"] = "";

if(isset($_GET["path"]) AND $_GET["path"] != ""){
	$current_folder = urldecode(clean($_GET["path"]));
}else{
	$current_folder = LIBRARY_FOLDER_PATH;
}


if(!CanRenameFolder()){
	$output["success"] = 0;
	$output["msg"] = _lang('you_do_not_have_permission_to_rename_folder');
	header("Content-type: text/plain;");
	echo json_encode($output);
	exit();
}

if(isset($_GET["new_name"]) AND $_GET["new_name"] != ""){
	$new_name = clean($_GET["new_name"]);
}else{
	$output["success"] = 0;
	$output["msg"] = _lang('the_new_name_is_required');
	header("Content-type: text/plain;");
	echo json_encode($output);
	exit();
}

if(isset($_GET["current_name"]) AND $_GET["current_name"] != ""){
	$current_name = clean($_GET["current_name"]);
	$folder = $current_folder .  $current_name;
}else{
	$output["success"] = 0;
	$output["msg"] = _lang('the_current_name_is_required');
	header("Content-type: text/plain;");
	echo json_encode($output);
	exit();
}

if(!startsWith($folder, LIBRARY_FOLDER_PATH)){
	$output["success"] = 0;
	$output["msg"] = _lang('you_can_not_rename_this_folder');
	header("Content-type: text/plain;");
	echo json_encode($output);
	exit();
}

if(!file_exists($folder)){
	$output["success"] = 0;
	$output["msg"] = _lang('the_folder_does_not_exist');
	header("Content-type: text/plain;");
	echo json_encode($output);
	exit();
}

if(!is_dir($folder)){
	$output["success"] = 0;
	$output["msg"] = _lang('that_is_not_a_folder');
	header("Content-type: text/plain;");
	echo json_encode($output);
	exit();
}

if(file_exists(($current_folder .  $new_name))){
	$output["success"] = 0;
	$output["msg"] = _lang('the_new_name_is_already_in_use');
	header("Content-type: text/plain;");
	echo json_encode($output);
	exit();
}

rename(($current_folder .  $current_name), ($current_folder .  $new_name));


include 'contents.php';

header("Content-type: text/plain;");
echo json_encode($output);
exit();