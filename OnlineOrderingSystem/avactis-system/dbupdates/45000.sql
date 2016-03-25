-- UPDATE MENU GROUP NAME FOR EXTENSION MENUS TO DISPLAY IN configure_extensions.php -- BEGIN --
UPDATE `menu_admin` SET `group_name` = 'SYS,EXTENSION_CNFGR' WHERE parent = 'SYS,EXTENSION_CNFGR';
UPDATE `menu_admin` SET `menu_url`='configure_extensions.php' WHERE `menu_name`='SYS,EXTENSION_CNFGR';
-- UPDATE MENU GROUP NAME FOR EXTENSION MENUS TO DISPLAY IN configure_extensions.php -- END --

-- CHANGES FOR MARKETPLACE INTEGRATION -- BEGIN --

/*-- Menu admin: */
INSERT INTO `menu_admin` (`id`, `menu_name`, `menu_description`, `icon_image`, `parent`, `has_subcategory`, `visibility`, `menu_url`, `group_name`, `new_window`) VALUES (NULL, 'CMS,BTN_ADD_NEW', '', '', 'SYS,EXTENSIONS', '0', '1', 'admin_template.php?identifier=AddMarketPlaceExtensions', 'home', '0');

/*Pages_admin:*/
INSERT INTO `admin_pages` (`id`, `identifier`, `classname`, `title`, `heading`, `help_identifier`, `item_value`) VALUES (NULL, 'AddMarketPlaceExtensions', 'AddMarketPlaceExtensions', 'CMS,BTN_ADD_NEW', 'CMS,BTN_ADD_NEW', '', '');

/*MESSAGES*/

REPLACE INTO `resource_labels` (`id`, `res_prefix`, `res_label`, `res_text`) VALUES (NULL, 'SYS', 'VERIFY_CREDENTIALS', 'Verifying Credentials......');
REPLACE INTO `resource_labels` (`id`, `res_prefix`, `res_label`, `res_text`) VALUES (NULL, 'SYS', 'EXTENSION_DOWNLOADED_INSTALLED', 'Extension Installed Successfully!');
REPLACE INTO `resource_labels` (`id`, `res_prefix`, `res_label`, `res_text`) VALUES (NULL, 'SYS', 'DOWNLOADING_EXT', 'Downloading extension....');
REPLACE INTO `resource_labels` (`id`, `res_prefix`, `res_label`, `res_text`) VALUES (NULL, 'SYS', 'EXTRACTING_EXT', 'Extracting extension zip file...');
REPLACE INTO `resource_labels` (`id`, `res_prefix`, `res_label`, `res_text`) VALUES (NULL, 'SYS', 'INSTALLING_EXT', 'Installing extension...');
REPLACE INTO `resource_labels` (`id`, `res_prefix`, `res_label`, `res_text`) VALUES (NULL, 'SYS', 'ZIP_EXTRACT_ERR', 'Zip could not be extracted!');
REPLACE INTO `resource_labels` (`id` ,`res_prefix` ,`res_label` ,`res_text`)VALUES (NULL , 'SYS', 'INSTALLED', 'Installed');
REPLACE INTO `resource_labels` (`id`, `res_prefix`, `res_label`, `res_text`) VALUES (NULL, 'SYS', 'INSTALL', 'Install');
REPLACE INTO `resource_labels` (`id`, `res_prefix`, `res_label`, `res_text`) VALUES (NULL, 'SYS', 'CONFIGURE_EXT_LINK_MSG', 'You can configure the extension through this link:');
REPLACE INTO `resource_labels` (`id`, `res_prefix`, `res_label`, `res_text`) VALUES (NULL, 'SYS', 'MARKETPLACE_CREDENTIALS', 'Marketplace Credentials:');
REPLACE INTO `resource_labels` (`id`, `res_prefix`, `res_label`, `res_text`) VALUES (NULL, 'SYS', 'EXT_FORM_HEADER', 'Please Enter Your Marketplace Credentials Here.');
REPLACE INTO `resource_labels` (`id`, `res_prefix`, `res_label`, `res_text`) VALUES (NULL, 'SYS', 'UPDATE_CREDS', 'Click here to update credentials');
REPLACE INTO `resource_labels` (`id`, `res_prefix`, `res_label`, `res_text`) VALUES (NULL, 'SYS', 'TO_REGISTER_ON_MARKETPLACE', 'to Register on Marketplace');
REPLACE INTO `resource_labels` (`id`, `res_prefix`, `res_label`, `res_text`) VALUES (NULL, 'SYS', 'NOT_VALID_MARKETPLC_USR', 'You are not a valid Marketplace User!');
REPLACE INTO `resource_labels` (`id`, `res_prefix`, `res_label`, `res_text`) VALUES (NULL, 'SYS', 'NO_AVACNEXT_MEMBERSHIP', 'You don''t have AvactisNext Membership!');
REPLACE INTO `resource_labels` (`id`, `res_prefix`, `res_label`, `res_text`) VALUES (NULL, 'SYS', 'EXT_UPDATED_SUCCESSFULLY', 'Extension Updated Successfully!');
REPLACE INTO `resource_labels` (`id`, `res_prefix`, `res_label`, `res_text`) VALUES (NULL, 'SYS', 'UPDATING_EXT', 'Updating Extension...');
REPLACE INTO `resource_labels` (`id`, `res_prefix`, `res_label`, `res_text`) VALUES (NULL, 'SYS', 'ZIPARCHIVE_CLASS_NOT_FOUND','Zip module not installed in php. Please contact your hosting provider to compile PHP with zip support by using the --enable-zip configure option.');
REPLACE INTO `resource_labels` (`id`, `res_prefix`, `res_label`, `res_text`) VALUES (NULL , 'SYS', 'EMPTY_EXT_LIST_MSG','No Extensions Found.');
UPDATE `resource_labels` SET `res_text` = 'Uninstall' WHERE `res_label` = 'UNINSTALL';
/*Create table for xml data storage*/
CREATE TABLE IF NOT EXISTS `extension_xml_data` (
  `extension_name` varchar(255) NOT NULL,
  `extension_zip_name` varchar(255) NOT NULL,
  `extension_folder_name` varchar(255) NOT NULL,
  `extension_type` varchar(255) NOT NULL,
  `extension_desc` longtext NOT NULL,
  `extension_price` float NOT NULL,
  `extension_image` text  NOT NULL
);

/** Marketplace settings**/
INSERT INTO `settings` (`group_name`, `param_name`, `param_description_id`, `param_type`, `param_validator_class`, `param_validator_method`, `param_current_value`, `param_default_value`) VALUES
('MARKETPLACE_USERNAME', 'MARKETPLACE_USERNAME', 0, 'STRING', 'Validator', 'alwaysValid', '', ''),
('MARKETPLACE_PASSWORD', 'MARKETPLACE_PASSWORD', 0, 'STRING', 'Validator', 'alwaysValid', '', ''),
('MARKETPLACE_LAST_BUILD_DATE', 'MARKETPLACE_LAST_BUILD_DATE', 0, 'STRING', 'Validator', 'alwaysValid', '', ''),
('MARKETPLACE_TTL', 'MARKETPLACE_TTL', 0, 'STRING', 'Validator', 'alwaysValid', '1440', '1440');
-- CHANGES FOR MARKETPLACE INTEGRATION -- END --

-- Secure store module --
REPLACE INTO `resource_labels` SET res_prefix='SS', res_label='NEWLY_ADDED_FILE', res_text='Newly added files list:';
REPLACE INTO `resource_labels` SET res_prefix='SS', res_label='MODIFIED_FILE', res_text='"Modified files list :';
REPLACE INTO `resource_labels` SET res_prefix='SS', res_label='VIEW_PAGE_SUBTITLE_SS', res_text='Secure Store (BETA)';
REPLACE INTO `resource_labels` SET res_prefix='SS', res_label='SS_SEND_FILES_DETAILS_SUBJECT', res_text='Modified and newly added file details';
REPLACE INTO `resource_labels` SET res_prefix='SS', res_label='SS_INPUT_LABEL', res_text='Enter number of days for scanning (if left blank, 3 days will be considered by default) :';
REPLACE INTO `resource_labels` SET res_prefix='SS', res_label='SS_SEND_MAIL_LABEL', res_text='Email the list of modified/added files to the store admin for analysis.';
REPLACE INTO `resource_labels` SET res_prefix='SS', res_label='FIND_UPDATE_SYS_MANAGE', res_text='Scan your store for changes in avactis-system';
REPLACE INTO `resource_labels` SET res_prefix='SS', res_label='FIND_UPDATE_TITLE', res_text='Scan Avactis system (BETA)';
REPLACE INTO `resource_labels` SET res_prefix='SS', res_label='FIND_UPDATE_TITLE_DESCR', res_text='Scan your avactis-system for added and modified files';
REPLACE INTO `resource_labels` SET res_prefix='SS', res_label='FIND_UPDATE_NO_RESULT_MSG', res_text='No files added/modified';
REPLACE INTO `resource_labels` SET res_prefix='SS', res_label='MAIL_SENT', res_text='Mail Sent';
REPLACE INTO `resource_labels` SET res_prefix='SS', res_label='ERROR_TO_SEND_MAIL', res_text='Mail not sent as no files modified/added.';
REPLACE INTO `resource_labels` SET res_prefix='SS', res_label='SHELL_ENABLED', res_text='shell_exec is enabled';
REPLACE INTO `resource_labels` SET res_prefix='SS', res_label='SHELL_NOT_ENABLED', res_text='shell_exec is not enabled. Please contact your hosting provider for enabling shell_exec for using this scan.';

