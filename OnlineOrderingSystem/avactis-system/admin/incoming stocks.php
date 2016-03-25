<?php

?><?php
	require_once('../admin.php');
//		$tpl_styles = array('admin-default','admin-custom');
		$tpl_title = getxMsg('SYS','CTLG_TAB_002');
		$tpl_header = getxMsg('SYS','MNG_PRD_PAGE_NAME');
		$tpl_onload_js = '';
		$tpl_parent = array(
					array(
						'name' => getxmsg('SYS','CTLG_TAB_PAGE_NAME'),
						'url' => 'catalog.php'
				),
					array(
						'name' => getxmsg('SYS','MNG_PRD_PAGE_NAME'),
						'url' => ''
				));
		$tpl_help = 'incoming stocks';
		$tpl_class = 'ProductList';
		$tpl_tinymce = 'yes';
	//	$tpl_scripts = array('admin-layout','admin-avactis-main','admin-avactis-validate');
		$tpl_jquery_ready = array('ASC_ADMIN.init();');
	include('admin.tpl.php');
?>