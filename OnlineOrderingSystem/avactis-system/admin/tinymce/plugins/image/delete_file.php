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


if(!CanDeleteFiles()){
	$output["success"] = 0;
	$output["msg"] = _lang('you_do_not_have_permission_to_delete_files');
	header("Content-type: text/plain;");
	echo json_encode($output);
	exit();
}

if(isset($_GET["file"]) AND $_GET["file"] != ""){
	$file = urldecode(clean($_GET["file"]));
}else{
	$output["success"] = 0;
	$output["msg"] = _lang('the_file_name_is_required');
	header("Content-type: text/plain;");
	echo json_encode($output);
	exit();
}

if(!startsWith($file, LIBRARY_FOLDER_PATH)){
	$output["success"] = 0;
	$output["msg"] = _lang('you_can_not_delete_this_file');
	header("Content-type: text/plain;");
	echo json_encode($output);
	exit();
}

if(!file_exists($file)){
	$output["success"] = 0;
	$output["msg"] = _lang('the_file_does_not_exist');
	header("Content-type: text/plain;");
	echo json_encode($output);
	exit();
}

if(!is_file($file)){
	$output["success"] = 0;
	$output["msg"] = _lang('that_is_not_a_file');
	header("Content-type: text/plain;");
	echo json_encode($output);
	exit();
}

if(!unlink($file)){
	$output["success"] = 0;
	$output["msg"] = _lang('the_file_could_not_be_deleted');
	header("Content-type: text/plain;");
	echo json_encode($output);
	exit();
}

include 'contents.php';


header("Content-type: text/plain;");
echo json_encode($output);
exit();