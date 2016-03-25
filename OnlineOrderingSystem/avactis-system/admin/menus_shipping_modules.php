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
?><div class="portlet box blue-hoki">
	<div class="portlet-title">
		<div class="caption bold uppercase">
			<i class="fa fa-gears"></i><?php xMsg('SYS','SHIP_METH_PAGE_NAME'); ?>
		</div>
	</div>
	<div class="portlet-body">
		<!--starting portlet -tab --->
		<div class=" portlet-tabs">
			<ul class="nav nav-tabs">
				<li class="active"><a data-toggle="tab" href="#general_settings_tab"><?php xmsg('SYS','OVERVIEW'); ?></a></li>
				<li class=""><a data-toggle="tab" href="#edit_shipping_settings_tab"><?php xmsg('SCC','LABEL_EDIT_SETTINGS'); ?></a></li>
				<li class=""><a data-toggle="tab" href="#shipping_settings_tab"><?php xmsg('SYS','SHIP_METH_HEADER_002'); ?></a></li>
			</ul>
			<div class="tab-content">

				<!-- Start - Overview of Shipping Settings -->
				<div id="general_settings_tab" class="tab-pane active">
					<?php ShippingCostCalculatorSection(); ?>
				</div>
				<!--End --->
				<!-- Start Edit Shipping Method Settings -->
				<div id="edit_shipping_settings_tab" class="tab-pane">
					<?php ShippingCostCalculatorSettings(); ?>
				</div>
				<!--End-- >
				<!-- Start Manage Shipping Methods -->
				<div id="shipping_settings_tab" class="tab-pane">
					<?php CheckoutShippingModulesList(); ?>
				</div>
				<!--End-- >
			</div>
			<!--End of tab content -->
		</div>
		<!--End of portlet -tab --->
	</div>
	<!---End of portlet body -->
</div>