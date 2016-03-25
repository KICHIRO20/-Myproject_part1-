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
?><!-- footer -->
<SCRIPT LANGUAGE="JavaScript">
<!--
jQuery(function() { setFocusOnFirstElement() });
//-->
</SCRIPT>
<?php
	asc_enqueue_script('jquery');
	asc_enqueue_script('jquery-colorbox');
//	asc_enqueue_script('admin-index');
//	asc_enqueue_script('admin-layout');
	asc_enqueue_script('bootbox');
	asc_enqueue_script('admin-avactis-main');
	asc_enqueue_script('admin-avactis-new-window');
	asc_enqueue_script( 'admin-avactis-validate' );
	asc_enqueue_script( 'asc-admin' );

	if(isset($tpl_scripts) && !empty($tpl_scripts))
	{
		foreach ($tpl_scripts as $script)
		{
			asc_enqueue_script( $script );
		}
	}
	do_action( 'admin_print_scripts' );

	include('stat/stat.php'); ?>
<!-- //footer -->

<script type="text/javascript">
    jQuery(document).ready(function(){
        Resize_Box();
    });

    function Resize_Box(){
        var x = jQuery('body').width();
        var y = jQuery('body').height() + 30;
        parent.jQuery.fn.colorbox.resize({
            innerWidth: x,
            innerHeight: y
        });
    }
</script>