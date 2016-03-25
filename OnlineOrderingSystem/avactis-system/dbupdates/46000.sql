UPDATE `store_settings` SET variable_value='4.6' WHERE variable_name='db_version';

------MARKETPLACE CHANGES BEGIN ---------------------
DROP TABLE IF EXISTS `extension_xml_data`;

DELETE FROM `settings` WHERE group_name='MARKETPLACE_USERNAME' AND param_name='MARKETPLACE_USERNAME';
DELETE FROM `settings` WHERE group_name='MARKETPLACE_PASSWORD' AND param_name='MARKETPLACE_PASSWORD';
INSERT INTO `settings` SET group_name='AVACTIS_LATEST_VERSION', param_name='AVACTIS_LATEST_VERSION', param_description_id='0', param_type='STRING', param_validator_class='Validator', param_validator_method='alwaysValid', param_current_value='', param_default_value='';


DELETE FROM `resource_labels` WHERE res_prefix='SYS' AND res_label='UPDATE_CREDS';
DELETE FROM `resource_labels` WHERE res_prefix='SYS' AND res_label='TO_REGISTER_ON_MARKETPLACE';
DELETE FROM `resource_labels` WHERE res_prefix='SYS' AND res_label='NO_AVACNEXT_MEMBERSHIP';
DELETE FROM `resource_labels` WHERE res_prefix='SYS' AND res_label='NOT_VALID_MARKETPLC_USR';
DELETE FROM `resource_labels` WHERE res_prefix='SYS' AND res_label='MARKETPLACE_CREDENTIALS';
DELETE FROM `resource_labels` WHERE res_prefix='SYS' AND res_label='EXT_FORM_HEADER';

UPDATE `resource_labels` SET res_text='The extension is uninstalled successfully. If you have added any blocks in page manager for this extension, please remove the same to avoid any issues.' WHERE res_prefix='SYS' AND res_label='EXTENSION_UNINSTALLED';


REPLACE INTO `resource_labels` SET res_prefix='SYS', res_label='MSG_HERE', res_text='here';
REPLACE INTO `resource_labels` SET res_prefix='SYS', res_label='EXTENSION_MANAGER_UPGRADE', res_text='Upgrade';
REPLACE INTO `resource_labels` SET res_prefix='SYS', res_label='EXTENSION_MANAGER_REVERT', res_text='Rollback';
REPLACE INTO `resource_labels` SET res_prefix='SYS', res_label='EXTENSION_MANAGER_README', res_text='Read More';
REPLACE INTO `resource_labels` SET res_prefix='SYS', res_label='EXTENSION_MANAGER_PRICE', res_text='Price';
REPLACE INTO `resource_labels` SET res_prefix='SYS', res_label='EXTENSION_MANAGER_NOT_COMPATIBLE', res_text='Not Compatible';
REPLACE INTO `resource_labels` SET res_prefix='SYS', res_label='EXTENSION_MANAGER_NAME', res_text='Name';
REPLACE INTO `resource_labels` SET res_prefix='SYS', res_label='EXTENSION_MANAGER_LATESTVERSION', res_text='Latest Version';
REPLACE INTO `resource_labels` SET res_prefix='SYS', res_label='EXTENSION_MANAGER_LATESTCOMPVERSION', res_text='Latest Compatible Version';
REPLACE INTO `resource_labels` SET res_prefix='SYS', res_label='EXTENSION_MANAGER_IMAGE', res_text='Image';
REPLACE INTO `resource_labels` SET res_prefix='SYS', res_label='EXTENSION_MANAGER_DESC', res_text='Description';
REPLACE INTO `resource_labels` SET res_prefix='SYS', res_label='EXTENSION_MANAGER_CATEGORY', res_text='Extension Category';
REPLACE INTO `resource_labels` SET res_prefix='SYS', res_label='CLICK_HERE', res_text='Click here';

DELETE FROM `module_class` WHERE module_class_name='AddMarketPlaceExtensions' AND module_class_type='view_az';
DELETE FROM `module_class` WHERE module_class_name='MM_ListView' AND module_class_type='view_az';
DELETE FROM `module_class` WHERE module_class_name='MarketPlaceActionURL' AND module_class_type='action';
DELETE FROM `module_class` WHERE module_class_name='InstallMarketPlaceExtension' AND module_class_type='action';
DELETE FROM `module_class` WHERE module_class_name='SaveMarketPlaceDetails' AND module_class_type='action';
DELETE FROM `module_class` WHERE module_class_name='InstallExtensionAction' AND module_class_type='action';
DELETE FROM `module_class` WHERE module_class_name='UninstallExtensionAction' AND module_class_type='action';

UPDATE `menu_admin` SET menu_description='SYS,EXTENSION_MNGR', menu_url='admin_template.php?identifier=ExtensionManager_ListView' WHERE menu_name='SYS,EXTENSION_MNGR';
UPDATE `menu_admin` SET has_subcategory='0' WHERE menu_name='SYS,EXTENSION_CNFGR';
DELETE FROM `menu_admin` WHERE menu_name='CMS,BTN_ADD_NEW';

DELETE FROM `admin_pages` WHERE identifier='MM_ListView';
DELETE FROM `admin_pages` WHERE identifier='AddMarketPlaceExtensions';
INSERT INTO `admin_pages` (`id`, `identifier`, `classname`, `title`, `heading`, `help_identifier`, `item_value`, `onload_js`, `parent`)
VALUES (NULL , 'ExtensionManager_ListView', 'ExtensionManager_ListView', 'SYS,EXTENSION_MNGR', 'SYS,EXTENSION_MNGR', 'extension_manager', '0', '0', 'extensions');

------MARKETPLACE CHANGES END ---------------------

------ERROR DOC CHANGES BEGIN ---------------------
REPLACE INTO `resource_labels` SET res_prefix='ERRD', res_label='ERRDOC', res_text='Error Document Manager';
REPLACE INTO `resource_labels` SET res_prefix='ERRD', res_label='ERRDOC_DESCR', res_text='This will enable apache error document manager';
REPLACE INTO `resource_labels` SET res_prefix='ERRD', res_label='ENABLE_ERRDOC', res_text='Enable';
REPLACE INTO `resource_labels` SET res_prefix='ERRD', res_label='DISABLE_ERRDOC', res_text='Disable';
REPLACE INTO `resource_labels` SET res_prefix='ERRD', res_label='ENABLED_ERRDOC', res_text='Error document has been enabled.';
REPLACE INTO `resource_labels` SET res_prefix='ERRD', res_label='DISABLED_ERRDOC', res_text='Error document has been disabled.';
REPLACE INTO `resource_labels` SET res_prefix='ERRD', res_label='TITLE_404', res_text='404';
REPLACE INTO `resource_labels` SET res_prefix='ERRD', res_label='SUBTITLE_404', res_text='Sorry - Page Not Found';
REPLACE INTO `resource_labels` SET res_prefix='ERRD', res_label='MSG_404', res_text='The page you are looking for does not exist; it may have been moved, or removed altogether.<br>You might want to try the search function. Alternatively, return to the <a href="index.php" title="click here to return to the home page."> home page </a>.';

UPDATE `resource_labels` SET res_text='Failed to find system configuration file config.php or insufficient rights to read the file.' WHERE res_prefix='SYS' AND res_label='CORE_030';

INSERT INTO `menu_admin` SET menu_name='ERRD,ERRDOC', menu_description='ERRD,ERRDOC_DESCR', icon_image='', parent='SYS,MENU_STORE_SETTINGS', has_subcategory='0', visibility='1', menu_url='popup_window.php?page_view=Error_Document_Setting', group_name='SYS,STRSET_HEADER_003', new_window='1';
------ERROR DOC CHANGES END ---------------------

--------- Wizard Begin ----------------------
REPLACE INTO `resource_labels` SET res_prefix='WZ', res_label='STORE_SETUP_GUIDE_NAME', res_text='Store-Setup Guide';
REPLACE INTO `resource_labels` SET res_prefix='WZ', res_label='STORE_SETUP_GUIDE_GROUP_DESCR', res_text='Store-setup guide wizard settings';
REPLACE INTO `resource_labels` SET res_prefix='WZ', res_label='STORE_SETUP_GUIDE_SHOW_HIDE_NAME', res_text='Show/Hide';
REPLACE INTO `resource_labels` SET res_prefix='WZ', res_label='STORE_SETUP_GUIDE_SHOW_HIDE_DESCR', res_text='Hide or show store-setup guide wizard';
REPLACE INTO `resource_labels` SET res_prefix='WZ', res_label='STORE_SETUP_GUIDE_HIDE', res_text='Hide';
REPLACE INTO `resource_labels` SET res_prefix='WZ', res_label='STORE_SETUP_GUIDE_HIDE_DESCR', res_text='Hide store-setup guide wizard';
REPLACE INTO `resource_labels` SET res_prefix='WZ', res_label='STORE_SETUP_GUIDE_SHOW', res_text='Show';
REPLACE INTO `resource_labels` SET res_prefix='WZ', res_label='STORE_SETUP_GUIDE_SHOW_DESCR', res_text='Show store-setup guide wizard';
--------- Wizard End ----------------------

---- MISCELLANOUS CHANGES BEGIN -------
DELETE FROM `menu_admin` WHERE menu_name='SYS,STRSET_CACHE';
INSERT INTO `menu_admin` SET menu_name='SYS,ADMIN_PHP_FILES_CACHE_N_LOGS', menu_description='SYS,STRSET_CACHE_DESCR', icon_image='', parent='SYS,ADMIN_MENU_HEADER_002', has_subcategory='0', visibility='1', menu_url='store_settings_cache.php', group_name='home', new_window='1';

DELETE FROM `menu_admin` WHERE menu_name='SL,SPOTLIGHT_TITLE';
---- MISCELLANOUS CHANGES END -------
