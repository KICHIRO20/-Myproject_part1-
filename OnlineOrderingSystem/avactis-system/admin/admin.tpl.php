<?php

?><?php
	if (isset($tpl_parent) && !empty($tpl_parent))
	{
		global $parent_file;
		$parent_file = $tpl_parent[0]['url'];
	}
?>
<!DOCTYPE html>
<HTML>
<HEAD>
  <TITLE>Online Ordering System for Appliances Store with Stock Management</TITLE>
<?php
	// Insert HTML Header
	include('part_htmlheader.php');

	if(isset($tpl_styles) && !empty($tpl_styles))
	{
		foreach ($tpl_styles as $style)
		{
			asc_enqueue_style( $style );
		}
		do_action( 'admin_print_styles' );
	}
?>
</HEAD>
<BODY  class="page-sidebar-closed-hide-logo page-container-bg-solid page-sidebar-closed-hide-logo page-header-fixed" onload="<?php echo $tpl_onload_js; ?>">
<?php
	// Insert Page Header
	include('part_header.php');
?>
	<div class="clearfix"></div>
	<div class="page-container">
		<div class="page-sidebar-wrapper">
		<?php require_once( ABSPATH . 'avactis-system/admin/admin-header.php' ); ?>
		</div>
		<div class="page-content-wrapper">
			<div class="page-content" style="min-height:820px;">
				<h3 class="page-title"><?php echo $tpl_header; ?></h3>
			<?php if ( isset($tpl_parent) && !empty($tpl_parent) ) { ?>
				<div class="page-bar">
					<ul class="page-breadcrumb">
						<li>
							<i class="fa fa-home"></i>
							<a href="index.php"><?php xmsg('SYS','MENU_HOME'); ?></a>
						<?php
							foreach( $tpl_parent as $parent)
							{
						?>
								<i class="fa fa-angle-right"></i>
						</li>
						<li>
							<a href="<?php echo $parent['url']; ?>"><?php echo $parent['name']; ?></a>
						<?php } ?>
						</li>
					</ul>
					<div class="page-toolbar">
						<div class="btn-group pull-right">
							<button data-close-others="true" data-delay="1000" data-hover="dropdown" data-toggle="dropdown" class="btn btn-fit-height grey-salt dropdown-toggle" type="button"><?php xmsg('SYS','MENU_HELP'); ?> <i class="fa fa-angle-down"></i></button>
							<ul role="menu" class="dropdown-menu pull-right">
								<li>
									<?php PageHelpLink($tpl_help); ?>
								</li>
								<li>
									<?php VideoTutorialLink($tpl_help); ?>
								</li>
							</ul>
						</div>
					</div>
				</div>
			<?php } ?>
				<div class="row">
					<div class="col-md-12">
						<?php $tpl_class(); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php
	// Insert Page Footer
	include('part_footer.php');

	if (in_array($tpl_class, array('CMS_Page_Data', 'LabelData', 'Newsletter_Compose')) || (isset($tpl_tinymce) && $tpl_tinymce==='yes')) {
    	include('part_tinymce.php');
	}
?>

<script>
jQuery(document).ready(function() {
	Layout.init();
        ASC_ADMIN.init();
<?php
	if(isset($tpl_jquery_ready) && !empty($tpl_jquery_ready))
	{
		foreach ($tpl_jquery_ready as $jquery_ready)
		{
			echo "$jquery_ready\n";
		}
	}
?>
});

(function() {
  window.alert = function(msg) {
    bootbox.alert(msg);
  };
})();
</script>
</BODY>
</HTML>