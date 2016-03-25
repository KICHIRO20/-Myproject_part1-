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
?><!-- header -->
<?php
	$admin_info = modApiFunc("Users", "getUserInfo", modApiFunc("Users", "getCurrentUserID"));
	$store_status = (bool)!modApiFunc("Configuration", "getValue", "store_online");
	$storefront_list = array();
	$config_array = LayoutConfigurationManager::static_get_cz_layouts_list();
    $timeline = modApiFunc('Timeline','getTimelineRecsCount');
    $timeline = ($timeline==0 ? getMsg("SYS","ADMIN_PHP_FILES_NO_LOG_RECORDS") : $timeline . getMsg("SYS","ADMIN_PHP_FILES_LOG_RECORDS"));
	foreach($config_array as $k => $v)
	{
	    if (preg_match('/^.*\.ini$/', $v['PATH_LAYOUTS_CONFIG_FILE']))
	    {
	        $storefront_list[] = $v['SITE_URL'];
	    }
	}
?>
<SCRIPT LANGUAGE="JavaScript">
<!--

	function clearInstance(instance)
	{
		jQuery('#i-'+instance).html('<img src="images/ajax/snake.gif">');
		jQuery.post('jquery_ajax_handler.php',
			{'asc_action': 'ClearInstanceAjax', 'instance':instance},
                         function(msg){ showToastMsg('success','','<?php xMsg("SYS","MSG_CACHE_CLEARED"); ?>',''); });
	}

    function updateStoreStatus()
    {
        update_store_url = '<?php
            $rqst = new Request();
            $rqst->setView(CURRENT_REQUEST_URL);
            $rqst->setAction('UpdateGeneralSettings');
            $rqst->setKey(SYSCONFIG_STORE_ONLINE, ($store_status ? 1: 0) );
            $rqst->setKey('local', 'Y');
            echo $rqst->getURL();
        ?>';


        document.location.href=update_store_url;
    }

jQuery(document).ready(function($){
$(".statusButton_closed").parent().dblclick(function(){
	$(".statusButton_closed").animate({left:'25px'});
	$("#statusButton").removeClass("statusButton_closed");
	$("#statusButton").addClass("statusButton_online");
	});

$(".statusButton_online").parent().dblclick(function(){
	$(".statusButton_online").animate({left:'0px'});
	$("#statusButton").removeClass("statusButton_online");
	$("#statusButton").addClass("statusButton_closed");
	});
});

//-->
</SCRIPT>

<div class="page-header navbar navbar-fixed-top">
	<!-- BEGIN HEADER INNER -->
	<div class="page-header-inner">
		<!-- BEGIN LOGO -->
		<div class="page-logo">
			<a href="index.php"><img class="logo-default" alt="Avactis logo" src="images/logo1.jpg"></a>
			<div class="menu-toggler sidebar-toggler"> </div>
		</div>
		<!-- END LOGO -->

		<a class="menu-toggler responsive-toggler" data-target=".navbar-collapse" data-toggle="collapse" href="javascript:;"> </a>

		<!-- BEGIN PAGE TOP -->
		<div class="page-top">
			<!-- BEGIN HEADER SEARCH BOX -->
			<!-- DOC: Apply "search-form-expanded" right after the "search-form" class to have half expanded search box -->
			<?php ProductSearchFormShort(); ?>
			<!-- END HEADER SEARCH BOX -->
<?php echo apply_filters("part_header_after_storefront","");
            ?>
			<!-- BEGIN TOP NAVIGATION MENU -->
			<div class="top-menu">
				<ul class="nav navbar-nav pull-right">

					<!-- BEGIN LANGUAGE BAR -->
					<?php SelectLanguage(); ?>
					<!-- END LANGUAGE BAR -->

					<!-- BEGIN USER LOGIN DROPDOWN -->
					<li class="dropdown dropdown-user">
						<a title="<?php Msg('ADMIN_INFO_PAGE_TITLE'); ?>" href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true" >
							<span class="username username-hide-on-mobile"> <?php echo $admin_info['firstname']. ' ' .$admin_info['lastname']; ?> </span>
							<i class="fa fa-angle-down"></i>
						</a>

						<ul class="dropdown-menu">
							<li>
								<a title="<?php Msg('ADMIN_INFO_PAGE_TITLE'); ?>" href="admin_member_info.php?asc_action=SetSelectedUser/uid=<?php echo modApiFunc('Users', 'getCurrentUserID'); ?>/edit=1"> <i class="fa fa-user"></i> My Profile </a>
							</li>
							<li>
								<a href="javascript:updateStoreStatus()" title="<?php msg('LFTBX_HEADER_STORE_STATUS_HINT'); ?>"> <i class="fa fa-check"></i> <?php msg($store_status? 'LFTBX_HEADER_CLSD' : 'LFTBX_HEADER_ONLN'); ?></a>
							</li>
							<li>
								<a href="javascript:;" onclick="clearInstance('cache');"> <i class="fa fa-refresh"></i> <?php msg("BTN_CLEAR_CACHE"); ?></a>
							</li>
							<li class="divider"></li>
							<li>
								<a target="_blank" href="http://www.avactis.com/contact-avactis-support/"> <i class="fa fa-link"></i> <?php msg('MENU_SUPPORT'); ?></a>
							</li>
							<li>
								<a target="_blank" href="http://www.avactis.com/forums/"> <i class="fa fa-link"></i> <?php msg('MENU_COMMUNITY_FORUMS'); ?></a>
							</li>
							<li class="divider"></li>
							<li><a href="signin.php?asc_action=SignOut" title="<?php msg('MENU_SIGN_OUT'); ?>"> <i class="fa fa-lock"></i> <?php msg('MENU_SIGN_OUT');?></a></li>
						</ul>
					</li>
					<!-- END USER LOGIN DROPDOWN -->
					<!-- BEGIN STOREFRONT LIST -->
					<li class="dropdown dropdown-user">
					<?php
						if (count($storefront_list) == 1)
						{
							echo '<a class="dropdown-toggle" href="'.$storefront_list[0].'" target="_blank"><span class="username"> View My Shop </span> <i class="fa fa-shopping-cart"></i></a>';
						}
						else
						{
							echo '<a title="View My Shop" class="dropdown-toggle" data-close-others="true" data-hover="dropdown" data-toggle="dropdown" href="javascript:void(0);"><span class="username"> View My Shop </span> <i class="fa fa-angle-down"></i></a>';

							echo '<ul class="dropdown-menu">';
							for($i=0; $i<count($storefront_list); $i++)
							{
								echo '<li><a href="'.$storefront_list[$i].'" target="_blank">'.getMsg("SYS", "ADMIN_PHP_FILES_STOREFRONT").' '.($i+1).'</a></li>';
							}
							echo '</ul>';
						}
					?>
					</li>
					<!-- END STOREFRONT LIST -->
				</ul>
			</div>
			<!-- END TOP NAVIGATION MENU -->
		</div>
		<!-- END PAGE TOP -->
	</div>
	<!-- END HEADER INNER -->
</div>


<!-- //header -->