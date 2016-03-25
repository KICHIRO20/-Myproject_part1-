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
$store_configuration_tab =
	array(
		array(getxmsg('SYS','STRSET_GENERAL'), getxmsg('SYS','STRSET_GENERAL_DESCR'), "store_settings_general.php"),
		array(getxmsg('SYS','STRSET_STORE_OWNER'), getxmsg('SYS','STRSET_STORE_OWNER_DESCR'), "store_settings_store_owner.php"),
		array(getxmsg('SYS','STRSET_PAYM_METH'), getxmsg('SYS','STRSET_PAYM_METH_DESCR'), "payment_modules.php"),
		array(getxmsg('SYS','STRSET_SHIP_METH'), getxmsg('SYS','STRSET_SHIP_METH_DESCR'), "shipping_modules.php"),
		array(getxmsg('SYS','STRSET_EMAIL_NOTIFICATIONS'), getxmsg('SYS','STRSET_EMAIL_NOTIFICATIONS_DESCR'), "store_settings_notifications.php"),
		array(getxmsg('PF','PF_SETTINGS_LINK'), getxmsg('PF','PF_SETTINGS_LINK_DESCR'),'',"popup_window.php?page_view=PF_Settings"),
        array(getxmsg('PI','PI_SETTINGS_LINK'), getxmsg('PI','PI_SETTINGS_LINK_DESCR'),"pi_settings.php"),
		array(getxmsg('MR','MR_SETTINGS_LINK'), getxmsg('MR','MR_SETTINGS_LINK_DESCR'),"seo_url.php"),
		array(getxmsg('ERRD','ERRDOC'), getxmsg('ERRD','ERRDOC_DESCR'),'',"popup_window.php?page_view=Error_Document_Setting"),
		array(getxmsg('QB','QB_SETTINGS'), getxmsg('QB','QB_SETTINGS_DESCR'),'',"popup_window.php?page_view=QB_Settings"),
		array(getxmsg('CA','CA_SETTINGS'), getxmsg('CA','CA_SETTINGS_DESCR'), "register_form_editor.php"),
		array(getxmsg('SYS','CONFIG_CREDIT_CARDS_EDITOR'), getxmsg('SYS','CONFIG_CREDIT_CARDS_EDITOR_DESCR'), "credit_cards_editor.php"),
		array(getxmsg('SYS','CHECKOUT_INFO'), getxmsg('SYS','CHECKOUT_INFO_DESCR'), "checkout_info_list.php"),
	);

$localization_tab =
	array(
		array(getxmsg('SYS','STRSET_COUNTRIES'), getxmsg('SYS','STRSET_COUNTRIES_DESCR'), "store_settings_countries.php"),
		array(getxmsg('SYS','STRSET_LANGUAGES'), getxmsg('SYS','STRSET_LANGUAGES_DESCR'), "store_settings_languages.php"),
		array(getxmsg('SYS','MENU_LABEL_EDITOR'), getxmsg('SYS','STRSET_LABEL_EDITOR_DESCR'), "label_editor.php"),
		array(getxmsg('SYS','STRSET_STATES'), getxmsg('SYS','STRSET_STATES_DESCR'), "store_settings_states.php"),
		array(getxmsg('SYS','STRSET_TAXES'), getxmsg('SYS','STRSET_TAXES_DESCR'), "store_settings_taxes.php"),
		array(getxmsg('SYS','STRSET_TAX_ZIP_SETS'), getxmsg('SYS','STRSET_TAX_ZIP_SETS_DESCR'), "tax_zip_sets.php"),
		array(getxmsg('SYS','STRSET_DATE_FORMAT'), getxmsg('SYS','STRSET_DATE_FORMAT_DESCR'),'', "store_settings_local_date.php"),
		array(getxmsg('SYS','STRSET_NUM_FORMAT'), getxmsg('SYS','STRSET_NUM_FORMAT_DESCR'), "store_settings_local_number.php"),
		array(getxmsg('SYS','STRSET_WEIGHT_UNIT'), getxmsg('SYS','STRSET_WEIGHT_UNIT_DESCR'),'', "store_settings_local_weight.php"),
		array(getxmsg('SYS','STRSET_CURRENCY_FORMAT'), getxmsg('SYS','STRSET_CURRENCY_FORMAT_DESCR'), "store_settings_local_currency.php"),
		array(getxmsg('CC','CC_RATE_EDITOR'), getxmsg('CC','CC_RATE_EDITOR_DESCR'),'',"currency_rate_editor.php"),
	);
?>
<div class="portlet box blue tabbable">
	<div class="portlet-title">
		<div class="caption">
			<i class="fa fa-gears"></i><?php xmsg('SYS','MENU_STORE_SETTINGS'); ?>
		</div>
	</div>
	<div class="portlet-body">
		<div class=" portlet-tabs">
			<ul class="nav nav-tabs">
				<li class="active"><a data-toggle="tab" href="#store_configuration_tab"><?php xmsg('SYS','STRSET_HEADER_003'); ?></a></li>
				<li class=""><a data-toggle="tab" href="#localization_tab"><?php xmsg('SYS','STRSET_HEADER_002'); ?></a></li>
				<li class=""><a data-toggle="tab" href="#admin_settings_tab"><?php xmsg('SYS','ADMIN_MENU_HEADER_003'); ?></a></li>
			</ul>
			<div class="tab-content">

<!-- Start - Advanced Settings & Configuration -->
				<div id="admin_settings_tab" class="tab-pane">
				<?php SettingGroupList(); ?>
				</div>
<!-- End - Advanced Settings & Configuration -->

<!-- Start - Location/Taxes/Localization -->
				<div id="localization_tab" class="tab-pane">
					<div class="row margin-bottom-10 separator"></div>
				<?php foreach($localization_tab as $localization) { ?>
				 	<div class="row margin-bottom-10">
				 		<div class="col-md-1"></div>
						<div class="col-md-3">
						<?php if(!empty($localization[2])){ ?>
				 			<a href="<?php echo $localization[2]; ?>" style="display:inline;"><?php echo $localization[0]; ?></a>
						<?php } else { ?>
				 			<a href="javascript:void(0);" onclick="openURLinNewWindow('<?php echo $localization[3]; ?>')" style="display:inline;"><?php echo $localization[0]; ?></a>
						<?php } ?>
						</div>
						<div class="col-md-8">
				 			<span class="help-block" style="display:inline;"><?php echo $localization[1]; ?></span>
				 		</div>
				 	</div>
						<?php } ?>
				</div>
<!-- End - Location/Taxes/Localization -->

<!-- Start - Store Configuration -->
				<div id="store_configuration_tab" class="tab-pane active">
					<div class="row margin-bottom-10 separator"></div>
				<?php foreach($store_configuration_tab as $store_configuration) { ?>
				 	<div class="row margin-bottom-10">
				 		<div class="col-md-1"></div>
						<div class="col-md-3">
						<?php if(!empty($store_configuration[2])){ ?>
				 			<a href="<?php echo $store_configuration[2]; ?>" style="display:inline;"><?php echo $store_configuration[0]; ?></a>
						<?php } else { ?>
				 			<a href="javascript:void(0);" onclick="openURLinNewWindow('<?php echo $store_configuration[3]; ?>')" style="display:inline;"><?php echo $store_configuration[0]; ?></a>
						<?php } ?>
						</div>
						<div class="col-md-8">
				 			<span class="help-block" style="display:inline;"><?php echo $store_configuration[1]; ?></span>
				 		</div>
				 	</div>
				<?php } ?>
				</div>
<!-- End - Store Configuration -->

			</div>
		</div>
	</div>
</div>