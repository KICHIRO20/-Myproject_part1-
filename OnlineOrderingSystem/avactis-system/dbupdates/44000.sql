-- ADDED FOR MAKING ADMIN MENUS VERTICAL -- BEGIN --
REPLACE INTO `resource_labels` (`res_prefix`, `res_label`, `res_text`) VALUES ('SYS','COLLAPSEMENU','Collapse menu');
REPLACE INTO `resource_labels` (`res_prefix`, `res_label`, `res_text`) VALUES ('SYS','EXPANDMENU','Expand menu');
-- ADDED FOR MAKING ADMIN MENUS VERTICAL -- END --

-- ADDED FOR COLORBOX ADMIN -- BEGIN --
REPLACE INTO `resource_labels` (`res_prefix`, `res_label`, `res_text`) VALUES ('SYS','PRDTYPE_CUST_ATTR_PAGE_NAME_SEL','Attribute Format: Select options');
REPLACE INTO `resource_labels` (`res_prefix`, `res_label`, `res_text`) VALUES ('NTFCTN','ADV_CFG_NTFCTN_SETTINGS_GROUP_DESCR','Change e-mail notification format to plain TEXT or HTML');
-- ADDED FOR COLORBOX ADMIN -- END --

-- ADDED TO RECREATE ADMIN MENUS -- BEGIN --
DELETE FROM `menu_admin` WHERE `parent`<> 'SYS,EXTENSION_CNFGR';
INSERT INTO `menu_admin` (`id`, `menu_name`, `menu_description`, `icon_image`, `parent`, `has_subcategory`, `visibility`, `menu_url`, `group_name`, `new_window`) VALUES
(1, 'SYS,DASHBOARD', '', 'images/home.png', '0', '0', '1', 'index.php', 'home', '0'),
(2, 'SYS,MENU_CATALOG', '', 'images/catalog.png', '0', '1', '1', 'javascript:void(0);', 'home', '0'),
(3, 'SYS,CTLG_TAB_002', 'SYS,CTLG_TAB_003', '', 'SYS,MENU_CATALOG', '0', '1', 'catalog_manage_products.php', 'home', '0'),
(4, 'SYS,CTLG_TAB_004', 'SYS,CTLG_TAB_005', '', 'SYS,MENU_CATALOG', '0', '1', 'catalog_manage_categories.php', 'home', '0'),
(5, 'SYS,CTLG_TAB_006', 'SYS,CTLG_TAB_007', '', 'SYS,MENU_CATALOG', '0', '1', 'catalog_manage_product_types.php', 'home', '0'),
(6, 'SYS,CTLG_TAB_010', 'SYS,CTLG_TAB_011', '', 'SYS,MENU_CATALOG', '0', '1', 'mnf_manufacturers.php', 'home', '0'),
(7, 'CTL,PRODUCTS_IMPORT', 'SYS,PRODUCTS_IMPORT_DESC', '', 'SYS,MENU_CATALOG', '0', '1', 'popup_window.php?page_view=ImportProductsView', 'home', '1'),
(8, 'CTL,PRODUCTS_EXPORT', 'SYS,PRODUCTS_EXPORT_DESC', '', 'SYS,MENU_CATALOG', '0', '1', 'popup_window.php?page_view=ExportProductsView', 'home', '1'),
(9, 'FRG,FG_TITLE', 'FRG,FG_DESCR', '', 'SYS,MENU_CATALOG', '0', '1', 'popup_window.php?page_view=Froogle_Export', 'home', '1'),
(10, 'SYS,MENU_CUSTOMERS', '', 'images/customers.png', '0', '1', '1', 'javascript:void(0);', 'home', '0'),
(11, 'SYS,CUSTOMERS_HEADER_001', '', '', 'SYS,MENU_CUSTOMERS', '0', '1', 'customers.php', 'home', '0'),
(12, 'SYS,MENU_CUSTOMER_REVIEWS', '', '', 'SYS,MENU_CUSTOMERS', '0', '1', 'customer_reviews.php', 'home', '0'),
(13, 'SYS,MENU_ORDERS', '', 'images/orders.png', '0', '0', '1', 'orders.php', 'home', '0'),
(14, 'SYS,MENU_MARKETING', '', 'images/marketing.png', '0', '1', '1', 'javascript:void(0);', 'home', '0'),
(15, 'SYS,MRKTNG_TAB_DISCOUNTS_MENU_TITLE', 'SYS,MRKTNG_TAB_003', '', 'SYS,MENU_MARKETING', '0', '1', 'marketing_manage_discounts.php', 'home', '0'),
(16, 'SYS,PROMOCODES_MENU_LBL', 'SYS,MRKTNG_TAB_005', '', 'SYS,MENU_MARKETING', '0', '1', 'marketing_manage_promo_codes.php', 'home', '0'),
(17, 'GCT,PAGE_TITLE', 'GCT,MARKETING_DESCRIPTION', '', 'SYS,MENU_MARKETING', '0', '1', 'marketing_manage_gc.php', 'home', '0'),
(18, 'NLT,NEWSLETTER_TITLE1', 'NLT,NEWSLETTER_DESCR', '', 'SYS,MENU_MARKETING', '0', '1', 'newsletter_archive.php', 'home', '0'),
(19, 'SYS,CUSTOMERS_INFO_SUBSCRIPTIONS', 'SUBSCR,SUBSCRIPTIONS_DESCR', '', 'SYS,MENU_MARKETING', '0', '1', 'subscriptions_manage.php', 'home', '0'),
(20, 'TT,MARKETING_LINK', 'TT,MARKETING_DESCRIPTION', '', 'SYS,MENU_MARKETING', '0', '1', 'transaction_tracking_settings.php', 'home', '0'),
(21, 'SYS,MENU_REPORTS', '', 'images/reports.png', '0', '1', '1', 'javascript:void(0);', 'home', '0'),
(22, 'SYS,REPORTS_PAGE_TITLE', '', '', 'SYS,MENU_REPORTS', '0', '1', 'reports.php', 'home', '0'),
(23, 'SYS,ADMIN_MENU_TIMELINE', 'SYS,ADMIN_MENU_TIMELINE_DESCR', '', 'SYS,MENU_REPORTS', '0', '1', 'timeline.php', 'home', '0'),
(24, 'SYS,ADMIN_MENU_RESET_REPORTS', 'SYS,ADMIN_MENU_RESET_REPORTS_DESCR', '', 'SYS,MENU_REPORTS', '0', '1', 'reports_reset.php', 'home', '1'),
(25, 'LF,LF_CUSTOMIZE', '', 'images/customize.png', '0', '1', '1', 'javascript:void(0);', 'home', '0'),
(26, 'SYS,CMS_HEADER_001', '', '', 'LF,LF_CUSTOMIZE', '0', '1', 'cms_pages.php', 'home', '0'),
(27, 'SYS,CMS_HEADER_002', '', '', 'LF,LF_CUSTOMIZE', '0', '1', 'cms_menus.php', 'home', '0'),
(28, 'SYS,LAYOUT_CMS_PAGE_TITLE', '', '', 'LF,LF_CUSTOMIZE', '0', '1', 'layout_cms.php', 'home', '0'),
(29, 'LF,LF_LAYOUT_DESIGN', '', '', 'LF,LF_CUSTOMIZE', '0', '1', 'look_feel.php', 'home', '0'),
(30, 'SYS,MENU_CONFIGURATION', '', 'images/configurations.png', '0', '1', '1', 'javascript:void(0);', 'home', '0'),
(31, 'SYS,ADMIN_MEMBERS_HEADER', 'SYS,ADMIN_MENU_MEMBERS_DESCR', '', 'SYS,MENU_CONFIGURATION', '0', '1', 'admin_members.php', 'home', '0'),
(32, 'SYS,ADMIN_MENU_HEADER_001', '', '', 'SYS,MENU_CONFIGURATION', '0', '1', 'admin.php', 'home', '0'),
(33, 'SYS,MENU_STORE_SETTINGS', '', '', 'SYS,MENU_CONFIGURATION', '1', '1', 'store_settings.php', 'home', '0'),
(34, 'SYS,STRSET_HEADER_003', '', '', 'SYS,MENU_STORE_SETTINGS', '1', '1', 'store_settings.php', 'home', '0'),
(35, 'SYS,STRSET_GENERAL', 'SYS,STRSET_GENERAL_DESCR', '', 'SYS,MENU_STORE_SETTINGS', '0', '1', 'store_settings_general.php', 'SYS,STRSET_HEADER_003', '1'),
(36, 'SYS,STRSET_STORE_OWNER', 'SYS,STRSET_STORE_OWNER_DESCR', '', 'SYS,MENU_STORE_SETTINGS', '0', '1', 'store_settings_store_owner.php', 'SYS,STRSET_HEADER_003', '1'),
(37, 'SYS,STRSET_PAYM_METH', 'SYS,STRSET_PAYM_METH_DESCR', '', 'SYS,MENU_STORE_SETTINGS', '0', '1', 'payment_modules.php', 'SYS,STRSET_HEADER_003', '0'),
(38, 'SYS,STRSET_SHIP_METH', 'SYS,STRSET_SHIP_METH_DESCR', '', 'SYS,MENU_STORE_SETTINGS', '0', '1', 'shipping_modules.php', 'SYS,STRSET_HEADER_003', '0'),
(39, 'SYS,STRSET_EMAIL_NOTIFICATIONS', 'SYS,STRSET_EMAIL_NOTIFICATIONS_DESCR', '', 'SYS,MENU_STORE_SETTINGS', '0', '1', 'store_settings_notifications.php', 'SYS,STRSET_HEADER_003', '0'),
(40, 'PF,PF_SETTINGS_LINK', 'PF,PF_SETTINGS_LINK_DESCR', '', 'SYS,MENU_STORE_SETTINGS', '0', '1', 'popup_window.php?page_view=PF_Settings', 'SYS,STRSET_HEADER_003', '1'),
(41, 'PI,PI_SETTINGS_LINK', 'PI,PI_SETTINGS_LINK_DESCR', '', 'SYS,MENU_STORE_SETTINGS', '0', '1', 'popup_window.php?page_view=PI_Settings', 'SYS,STRSET_HEADER_003', '1'),
(42, 'MR,MR_SETTINGS_LINK', 'MR,MR_SETTINGS_LINK_DESCR', '', 'SYS,MENU_STORE_SETTINGS', '0', '1', 'popup_window.php?page_view=MR_Settings', 'SYS,STRSET_HEADER_003', '1'),
(43, 'QB,QB_SETTINGS', 'QB,QB_SETTINGS_DESCR', '', 'SYS,MENU_STORE_SETTINGS', '0', '1', 'popup_window.php?page_view=QB_Settings', 'SYS,STRSET_HEADER_003', '1'),
(44, 'SYS,STRSET_HEADER_002', '', '', 'SYS,MENU_STORE_SETTINGS', '1', '1', 'store_settings.php', 'home', '0'),
(45, 'SYS,STRSET_COUNTRIES', 'SYS,STRSET_COUNTRIES_DESCR', '', 'SYS,MENU_STORE_SETTINGS', '0', '1', 'store_settings_countries.php', 'SYS,STRSET_HEADER_002', '1'),
(46, 'SYS,STRSET_LANGUAGES', 'SYS,STRSET_LANGUAGES_DESCR', '', 'SYS,MENU_STORE_SETTINGS', '0', '1', 'store_settings_languages.php', 'SYS,STRSET_HEADER_002', '1'),
(47, 'SYS,MENU_LABEL_EDITOR', 'SYS,STRSET_LABEL_EDITOR_DESCR', '', 'SYS,MENU_STORE_SETTINGS', '0', '1', 'label_editor.php', 'SYS,STRSET_HEADER_002', '0'),
(48, 'SYS,STRSET_STATES', 'SYS,STRSET_STATES_DESCR', '', 'SYS,MENU_STORE_SETTINGS', '0', '1', 'store_settings_states.php', 'SYS,STRSET_HEADER_002', '1'),
(49, 'SYS,STRSET_TAXES', 'SYS,STRSET_TAXES_DESCR', '', 'SYS,MENU_STORE_SETTINGS', '0', '1', 'store_settings_taxes.php', 'SYS,STRSET_HEADER_002', '0'),
(50, 'SYS,STRSET_TAX_ZIP_SETS', 'SYS,STRSET_TAX_ZIP_SETS_DESCR', '', 'SYS,MENU_STORE_SETTINGS', '0', '1', 'tax_zip_sets.php', 'SYS,STRSET_HEADER_002', '0'),
(51, 'SYS,STRSET_DATE_FORMAT', 'SYS,STRSET_DATE_FORMAT_DESCR', '', 'SYS,MENU_STORE_SETTINGS', '0', '1', 'store_settings_local_date.php', 'SYS,STRSET_HEADER_002', '1'),
(52, 'SYS,STRSET_NUM_FORMAT', 'SYS,STRSET_NUM_FORMAT_DESCR', '', 'SYS,MENU_STORE_SETTINGS', '0', '1', 'store_settings_local_number.php', 'SYS,STRSET_HEADER_002', '1'),
(53, 'SYS,STRSET_WEIGHT_UNIT', 'SYS,STRSET_WEIGHT_UNIT_DESCR', '', 'SYS,MENU_STORE_SETTINGS', '0', '1', 'store_settings_local_weight.php', 'SYS,STRSET_HEADER_002', '1'),
(54, 'SYS,STRSET_HEADER_007', '', '', 'SYS,MENU_STORE_SETTINGS', '1', '1', 'store_settings.php', 'home', '0'),
(55, 'SYS,CHECKOUT_INFO', 'SYS,CHECKOUT_INFO_DESCR', '', 'SYS,MENU_STORE_SETTINGS', '0', '1', 'checkout_info_list.php', 'SYS,STRSET_HEADER_006', '0'),
(56, 'CA,CA_SETTINGS', 'CA,CA_SETTINGS_DESCR', '', 'SYS,MENU_STORE_SETTINGS', '0', '1', 'register_form_editor.php', 'SYS,STRSET_HEADER_006', '0'),
(57, 'SYS,CONFIG_CREDIT_CARDS_EDITOR', 'SYS,CONFIG_CREDIT_CARDS_EDITOR_DESCR', '', 'SYS,MENU_STORE_SETTINGS', '0', '1', 'credit_cards_editor.php', 'SYS,STRSET_HEADER_006', '0'),
(58, 'SYS,STRSET_HEADER_006', '', '', 'SYS,MENU_STORE_SETTINGS', '1', '1', 'store_settings.php', 'home', '0'),
(59, 'SYS,STRSET_CURRENCY_FORMAT', 'SYS,STRSET_CURRENCY_FORMAT_DESCR', '', 'SYS,MENU_STORE_SETTINGS', '0', '1', 'store_settings_local_currency.php', 'SYS,STRSET_HEADER_007', '1'),
(60, 'CC,CC_RATE_EDITOR', 'CC,CC_RATE_EDITOR_DESCR', '', 'SYS,MENU_STORE_SETTINGS', '0', '1', 'popup_window.php?page_view=CurrencyRateEditor', 'SYS,STRSET_HEADER_007', '1'),
(61, 'SL,SPOTLIGHT_TITLE', '', '', 'SYS,MENU_CONFIGURATION', '0', '1', 'spotlight.php', 'home', '1'),
(62, 'SYS,EXTENSIONS', '', 'images/extension.png', '0', '1', '1', 'javascript:void(0);', 'home', '0'),
(63, 'SYS,EXTENSION_MNGR', '', '', 'SYS,EXTENSIONS', '0', '1', 'admin_template.php?identifier=MM_ListView', 'home', '0'),
(64, 'SYS,EXTENSION_CNFGR', '', '', 'SYS,EXTENSIONS', '1', '1', 'javascript:void(0);', 'home', '0'),
(65, 'SYS,ADMIN_MENU_HEADER_002', '', 'images/settings.png', '0', '1', '1', 'javascript:void(0);', 'home', '0'),
(66, 'SYS,ADMIN_MENU_BACKUP', 'SYS,ADMIN_MENU_BACKUP_DESCR', '', 'SYS,ADMIN_MENU_HEADER_002', '0', '1', 'admin_backup.php', 'home', '0'),
(67, 'SYS,STRSET_CACHE', 'SYS,STRSET_CACHE_DESCR', '', 'SYS,ADMIN_MENU_HEADER_002', '0', '1', 'store_settings_cache.php', 'home', '1'),
(68, 'SYS,ADMIN_MENU_HTTPSSETTINGS', 'SYS,ADMIN_MENU_HTTPSSETTINGS_DESCR', '', 'SYS,ADMIN_MENU_HEADER_002', '0', '1', 'https_settings.php', 'home', '1'),
(69, 'SYS,ADMIN_MENU_MAILSETTINGS', 'SYS,ADMIN_MENU_MAILSETTINGS_DESCR', '', 'SYS,ADMIN_MENU_HEADER_002', '0', '1', 'mail_settings.php', 'home', '1'),
(70, 'SYS,ADMIN_MENU_LICENSEINFO', 'SYS,ADMIN_MENU_LICENSEINFO_DESCR', '', 'SYS,ADMIN_MENU_HEADER_002', '0', '1', 'license_info.php', 'home', '1'),
(71, 'SYS,ADMIN_MENU_SERVERINFO', 'SYS,ADMIN_MENU_SERVERINFO_DESCR', '', 'SYS,ADMIN_MENU_HEADER_002', '0', '1', 'admin_server_info.php', 'home', '0');
-- ADDED TO RECREATE ADMIN MENUS -- END --

-- ADDED TO RECREATE ADMIN PAGES TABLE -- BEGIN --
DROP TABLE IF EXISTS `admin_pages`;
CREATE TABLE IF NOT EXISTS `admin_pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `identifier` varchar(255) NOT NULL,
  `classname` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `heading` varchar(255) NOT NULL,
  `help_identifier` varchar(255) NOT NULL,
  `item_value` varchar(255) NOT NULL,
  `onload_js` varchar(255) DEFAULT NULL,
  `parent` varchar(255) NOT NULL,
  PRIMARY KEY (`identifier`),
  KEY `IDX_id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
INSERT INTO `admin_pages` (`id`, `identifier`, `classname`, `title`, `heading`, `help_identifier`, `item_value`, `onload_js`, `parent`) VALUES
(1, 'MM_ListView', 'MM_ListView', 'SYS,EXTENSION_MNGR', 'SYS,EXTENSION_MNGR', 'extension_manager', '0', '', 'extensions');
-- ADDED TO RECREATE ADMIN PAGES TABLE -- END --

