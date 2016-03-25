UPDATE `store_settings` SET variable_value='4.7.6' WHERE variable_name='db_version';

UPDATE `resource_labels` SET res_text='Customer Reviews' WHERE res_label='MENU_CUSTOMER_REVIEWS';
-------------- FOOTER MENU LABELS FOR METRO THEME ---------------------

REPLACE INTO `resource_labels` SET res_prefix='CZ', res_label='FOOTER-1', res_text='<h2>Footer-1</h2>To edit these sample labels simply login to the control panel of Avactis shopping cart, click the "Label Editor" and search label you needed e.g "FOOTER-1" and you can edit this label as needed.';
REPLACE INTO `resource_labels` SET res_prefix='CZ', res_label='FOOTER-2', res_text='<h2>FOOTER-2</h2><ul class="list-unstyled"><li><i class="fa fa-angle-right"></i><a href="cmspage.php?page_id=3">About us</a></li><li><i class="fa fa-angle-right"></i><a href="cmspage.php?page_id=6">Customer Service</a></li><li><i class="fa fa-angle-right"></i><a href="cmspage.php?page_id=7">Privacy Policy</a></li><li><i class="fa fa-angle-right"></i><a href="cmspage.php?page_id=4">Need Help?</a></li><li><i class="fa fa-angle-right"></i><a href="cmspage.php?page_id=8">Contact Us</a></li></ul>';
REPLACE INTO `resource_labels` SET res_prefix='CZ', res_label='FOOTER-3', res_text='<h2>FOOTER-3</h2><a class="twitter-timeline" href="https://twitter.com/Avactis" data-tweet-limit="1" data-theme="dark" data-widget-id="372680831296217088" data-chrome="noheader nofooter noscrollbar noborders transparent">Loading Tweets by @Avactis...</a>';
REPLACE INTO `resource_labels` SET res_prefix='CZ', res_label='FOOTER-4', res_text='<h2>Footer-4</h2><ul class="social-icons"><li><a class="rss" href="javascript:void(0)" data-original-title="rss"></a></li><li><a class="twitter" href="javascript:void(0)" data-original-title="twitter"></a></li><li><a class="facebook" href="javascript:void(0)" data-original-title="facebook"></a></li><li><a class="googleplus" href="javascript:void(0)" data-original-title="googleplus"></a></li><li><a class="linkedin" href="javascript:void(0)" data-original-title="linkedin"></a></li><li><a class="youtube" href="javascript:void(0)" data-original-title="youtube"></a></li></ul>';

---------------- CMS NAV MENU LABELS ----------------------

REPLACE INTO `resource_labels` SET res_prefix='CMS', res_label='CMS_MANAGE_MENU', res_text='Manage Menu';
REPLACE INTO `resource_labels` SET res_prefix='CMS', res_label='CMS_MENU_STRUCTURE', res_text='Menu Structure';
REPLACE INTO `resource_labels` SET res_prefix='CMS', res_label='CMS_NAV_MENU_DATA', res_text='Drag each item into the order you prefer. Click the arrow on the right of the item to reveal additional configuration options.';
REPLACE INTO `resource_labels` SET res_prefix='CMS', res_label='CMS_NAV_MENU_NAME', res_text='Menu Name';
REPLACE INTO `resource_labels` SET res_prefix='CMS', res_label='CMS_NAV_SEARCH', res_text='Search';
REPLACE INTO `resource_labels` SET res_prefix='CMS', res_label='CMS_NAV_MENU_ALL', res_text='View All';
REPLACE INTO `resource_labels` SET res_prefix='CMS', res_label='CMS_MENU_LINK_TEXT', res_text='Link Text';
REPLACE INTO `resource_labels` SET res_prefix='CMS', res_label='CMS_MENU_URL', res_text='URL';
REPLACE INTO `resource_labels` SET res_prefix='CMS', res_label='CMS_NAV_MENU_SELECT', res_text='Select a menu to edit:';
REPLACE INTO `resource_labels` SET res_prefix='CMS', res_label='CMS_NAV_MENU_ASSIGNED', res_text='Assigned Menu';
REPLACE INTO `resource_labels` SET res_prefix='CMS', res_label='CMS_NAV_MENU_LABEL', res_text='Navigation Label';
REPLACE INTO `resource_labels` SET res_prefix='CMS', res_label='CMS_NAV_MENU_REMOVE', res_text='Remove';
REPLACE INTO `resource_labels` SET res_prefix='CMS', res_label='CMS_NAV_NEW_MENU', res_text='Use New Menu';
REPLACE INTO `resource_labels` SET res_prefix='CMS', res_label='CMS_NAV_MENU_LOCATION', res_text='Theme Location';
REPLACE INTO `resource_labels` SET res_prefix='CMS', res_label='CMS_NAV_DELETE', res_text='Delete Menu';
REPLACE INTO `resource_labels` SET res_prefix='CMS', res_label='CMS_NAV_BTN_CREATE', res_text='Create Menu';
REPLACE INTO `resource_labels` SET res_prefix='CMS', res_label='CMS_NAV_CONTENT', res_text='Give your menu a name above, then click Create Menu.';
REPLACE INTO `resource_labels` SET res_prefix='CMS', res_label='CMS_MENU_TYPE_CATEGORY_LIST', res_text='Category';
REPLACE INTO `resource_labels` SET res_prefix='CMS', res_label='CMS_MENU_NO_CATEGORY', res_text='No Categories';

REPLACE INTO `resource_labels` SET res_prefix='SYS', res_label='MENU_NAV_CMS_MENUS', res_text='Menu Manager';

---------------- PAYMENT AND SHIPPING LABELS ----------------------

REPLACE INTO `resource_labels` SET res_prefix='SYS', res_label='ERROR_SELECT_SHIPPING_METHOD', res_text='No changes done';
REPLACE INTO `resource_labels` SET res_prefix='SYS', res_label='ERROR_SELECT_PAYMENT_METHOD', res_text='No changes done'; 
REPLACE INTO `resource_labels` SET res_prefix='SYS', res_label='OVERVIEW', res_text='Overview'; 
REPLACE INTO `resource_labels` SET res_prefix='SYS', res_label='MORE_PAYMENT_METHODS', res_text='More Payment Methods'; 
REPLACE INTO `resource_labels` SET res_prefix='SYS', res_label='MORE_SHIPPING_METHODS', res_text='More Shipping Methods';  

--------------- QUICKBOOKS SETTINGS LABELS -------------------------

REPLACE INTO `resource_labels` SET res_prefix='QB', res_label='MSG_QUICKBOOK_SETTINGS_UPDATED', res_text='QuickBooks settings have been successfully updated.';

--------------- Analytics -------------------------
REPLACE INTO `resource_labels` SET res_prefix='AN', res_label='MODULE_NAME', res_text='Analytics';
REPLACE INTO `resource_labels` SET res_prefix='AN', res_label='MODULE_DESCR', res_text='Analytics are called in every page of your store';
REPLACE INTO `resource_labels` SET res_prefix='AN', res_label='OPTIONS_UPDATED', res_text='Fields are updated successfully';
REPLACE INTO `resource_labels` SET res_prefix='AN', res_label='DUPLICATE_FIELD_OPTION', res_text='Please remove duplicate field option';
