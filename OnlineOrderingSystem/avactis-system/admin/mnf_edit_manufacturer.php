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
//		$tpl_styles = array('admin-default','admin-custom');
		$tpl_title = getxMsg('MNF', 'MNF_EDIT_PAGE_TITLE');
		$tpl_header = getxMsg('MNF', 'MNF_EDIT_PAGE_TITLE');
		$tpl_onload_js = '';
		$tpl_parent = array(
						array(
							'name' => getxmsg('SYS','CTLG_TAB_PAGE_NAME'),
							'url' => '#'
							),
							array(
									'name' => getxmsg('SYS','CTLG_MANUFACTURERS_PAGE_TITLE'),
									'url' => 'mnf_manufacturers.php'
							),
							array(
									'name' => getxmsg('MNF', 'MNF_EDIT_PAGE_TITLE'),
									'url' => 'mnf_edit_manufacturer.php'
							)
						);
		$tpl_help = 'mnf_edit_manufacturer';
		$tpl_class = 'Edit_Manufucture_Fun';
//		$tpl_scripts = array('admin-layout','admin-avactis-main','admin-avactis-validate');
	include('admin.tpl.php');
?>

<?php
	function Edit_Manufucture_Fun(){
		EditManufacturer();
		include('part_tinymce.php');
	}
?>