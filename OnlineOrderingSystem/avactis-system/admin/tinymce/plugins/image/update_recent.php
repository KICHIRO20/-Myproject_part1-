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

if(isset($_POST["src"]) AND is_url_exist(clean($_POST["src"]))){
	if(!isset($_SESSION['SimpleImageManager'])){
		$_SESSION['SimpleImageManager'] = array();
		$_SESSION['SimpleImageManager'][] = clean($_POST["src"]);
	}else{
		if(!in_array(clean($_POST["src"]), $_SESSION['SimpleImageManager'])){
			$_SESSION['SimpleImageManager'][] = clean($_POST["src"]);
		}
	}

}