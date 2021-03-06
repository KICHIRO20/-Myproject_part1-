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

if(isset($_GET["toggle"]) AND $_GET["toggle"] != ""){
	if(isset($_SESSION['tinymce_toggle_view'])){
		if($_SESSION['tinymce_toggle_view'] == 'grid'){
			$_SESSION['tinymce_toggle_view'] = 'list';
		}else{
			$_SESSION['tinymce_toggle_view'] = 'grid';
		}
	}else{
		$_SESSION['tinymce_toggle_view'] = 'list';
	}
}

$output = array();

$output["success"] = 1;

if(isset($_GET["path"]) AND $_GET["path"] != ""){
	if(!startsWith(urldecode($_GET["path"]), LIBRARY_FOLDER_PATH)){
		$current_folder = LIBRARY_FOLDER_PATH;
	}else{
		$current_folder = urldecode(clean($_GET["path"]));
	}
}else{
	$current_folder = LIBRARY_FOLDER_PATH;
}

include 'contents.php';


header("Content-type: text/plain;");
echo json_encode($output);
exit();