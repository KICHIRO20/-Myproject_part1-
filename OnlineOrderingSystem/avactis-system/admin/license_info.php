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
require_once('../admin.php');

$tpl_title = getxMsg('SYS','LICENSE_PAGE_TITLE');
$tpl_header = getxMsg('SYS','LICENSE_WARNING_008');
$tpl_onload_js = 'js/countries_states.js';
		$tpl_parent = array(
					array(
						'name' => getxmsg('SYS','ADMIN_MENU_HEADER_002'),
						'url' => 'tools.php'
					),
					array(
						'name' => getxMsg('SYS','LICENSE_WARNING_008'),
						'url' => ''
				));

$tpl_help = 'License';
$tpl_class = 'LicenseInfo';
include('admin.tpl.php');
?>