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

	asc_enqueue_script('jquery');
	asc_enqueue_script('jquery-ui-custom');
	asc_enqueue_script('jquery-slimscroll');
	asc_enqueue_script('jquery-blockui');
	asc_enqueue_script('jquery-cokie');
	asc_enqueue_script('jquery-colorbox');
	asc_enqueue_script('jquery-uniform');
	asc_enqueue_script('bootstrap-hover-dropdown');
	asc_enqueue_script('bootstrap-switch');
	asc_enqueue_script('bootbox');
	asc_enqueue_script('admin-toastr-notification');
	asc_enqueue_script('bootstrap-toastr');
	asc_enqueue_script('flot');
	asc_enqueue_script('flot-resize');
	asc_enqueue_script('flot-categories');
	asc_enqueue_script('moment');
	asc_enqueue_script('daterangepicker');
	asc_enqueue_script('asc-admin');
	asc_enqueue_script('admin-index');
	asc_enqueue_script('admin-layout');
	asc_enqueue_script('admin-avactis-main');
	asc_enqueue_script('admin-avactis-new-window');
	asc_enqueue_script('admin-avactis-md5');
	asc_enqueue_script('admin-avactis-dtree');
	asc_enqueue_script('admin-avactis-tree-css');
	asc_enqueue_script('admin-avactis-tree-component');
	asc_enqueue_script('admin-avactis-categories');

	if(isset($tpl_scripts) && !empty($tpl_scripts))
	{
		foreach ($tpl_scripts as $script)
		{
			asc_enqueue_script( $script );
		}
	}
	do_action( 'admin_print_scripts' );

global $application;
echo $application->combineAdminJS(array(
    //'js/main.js',

    //'js/validate.js',
    //'js/JsHttpRequest.js',
    //'js/new_window.js',
    //'js/overlib.js',
    //'js/utility.js',
   // 'dtree/dtree.js',
   // 'jstree/css.js',
   // 'jstree/tree_component.js',
   // 'js/categories.js',
    //'js/utility.js',
    //'templates/modules/users/md5.js',
    //'js/vMenu.js',
    //'js/jquery.colorbox-min.js',
));