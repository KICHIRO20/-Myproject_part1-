-- For Extension Manager -- BEGIN -- 
REPLACE INTO `resource_labels` ( `res_prefix` , `res_label` , `res_text` )
VALUES 
('SYS','EXTENSION_MNGR', 'Extension Manager'), 
('SYS','INSTALLED_EXTENSIONS', 'Manage Extensions'),
('SYS','AVAILABLE_EXTENSIONS','Install Extensions'),
('SYS','UNINSTALL','Uninstall Extension'),
('SYS','UPLOAD_EXT','Upload an Extension'),
('SYS','EXTENSION_INSTALLED','The extension has been installed successfully.'),
('SYS','EXTENSION_UNINSTALLED','The extension has been uninstalled successfully.'),
('SYS','EXTENSIONS', 'Extensions'),
('SYS','EXTENSION_CNFGR', 'Configure Extensions');
-- For Extension Manager -- END --

-- For Admin Template -- BEGIN --  
CREATE TABLE IF NOT EXISTS `admin_pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `identifier` varchar(255) NOT NULL,
  `classname` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `heading` varchar(255) NOT NULL,
  `help_identifier` varchar(255) NOT NULL,
  `item_value` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 ;


INSERT INTO `admin_pages` (
`identifier` ,
`classname` ,
`title` ,
`heading` ,
`help_identifier` ,
`item_value`
)
VALUES (
'MM_ListView', 'MM_ListView', 'SYS,EXTENSION_MNGR', 'SYS,EXTENSION_MNGR', 'extension_manager', '0'
);

-- For Admin Template -- END --  

-- For News Setting to display only 1 news -- 
UPDATE `news_settings` SET `setting_value` = '1' WHERE `setting_key` ='news_display_count';

-- For CMS URL SEO -- BEGIN --  

REPLACE INTO `resource_labels` SET res_prefix='MR', res_label='SETS_CMSPAGE_PREFIX_DESCR', res_text='System identifier for cms pages. Default value is cms. This ID may be changed but cannot be left blank.';
REPLACE INTO `resource_labels` SET res_prefix='MR', res_label='SETS_CMSPAGE_PREFIX', res_text='CMS page links ID';
REPLACE INTO `resource_labels` SET res_prefix='MR', res_label='LBL_CMS_LINK', res_text='CMS page Link:';
REPLACE INTO `resource_labels` SET res_prefix='MR', res_label='ERR_INVALID_CMS_PREFIX', res_text='Invalid cms page links ID.';
REPLACE INTO `resource_labels` SET res_prefix='CMS', res_label='CMS_SEO_PREFIX', res_text='Article SEO URL prefix';

ALTER TABLE `cms_pages` ADD seo_prefix varchar(255) default '' AFTER status;

INSERT INTO `mr_settings` SET setting_id='5', setting_key='CMS_PREFIX', setting_value='cms';
ALTER TABLE `mr_schemes` ADD cms_rule_tpl varchar(255) NOT NULL default '' AFTER prod_rule_tpl;

UPDATE `mr_schemes` SET cms_rule_tpl='^%seo_cms_prefix%/%query_cms_prefix%/(%page_id%)\\.html' WHERE scheme_id='6';
UPDATE `mr_schemes` SET cms_rule_tpl='^%query_cms_prefix%/(%page_id%)/%seo_cms_prefix%\\.html' WHERE scheme_id='5';
UPDATE `mr_schemes` SET cms_rule_tpl='^%seo_cms_prefix%/%query_cms_prefix%-(%page_id%)\\.html' WHERE scheme_id='4';
UPDATE `mr_schemes` SET cms_rule_tpl='^%query_cms_prefix%-(%page_id%)/%seo_cms_prefix%\\.html' WHERE scheme_id='3';
UPDATE `mr_schemes` SET cms_rule_tpl='^%seo_cms_prefix%-%query_cms_prefix%-(%page_id%)\\.html' WHERE scheme_id='2';
UPDATE `mr_schemes` SET cms_rule_tpl='^%query_cms_prefix%-(%page_id%)-%seo_cms_prefix%\\.html' WHERE scheme_id='1';
-- For CMS URL SEO -- END --  

-- For HTML EMAIL Notification -- BEGIN --  

REPLACE INTO `resource_labels` SET res_prefix='NTFCTN', res_label='ADV_CFG_EMAIL_HTML', res_text='HTML';
REPLACE INTO `resource_labels` SET res_prefix='NTFCTN', res_label='ADV_CFG_EMAIL_TEXT', res_text='TEXT';
REPLACE INTO `resource_labels` SET res_prefix='NTFCTN', res_label='ADV_CFG_NTFCTN_SETTINGS_GROUP_NAME', res_text='E-mail notification settings';
REPLACE INTO `resource_labels` SET res_prefix='NTFCTN', res_label='ADV_EMAIL_NOTIFICATION_NAME', res_text='E-Mail notification format';
REPLACE INTO `resource_labels` SET res_prefix='NTFCTN', res_label='ADV_EMAIL_NOTIFICATION_DESCR', res_text='Use settings to change e-mail notification format to either plain TEXT or HTML';

INSERT INTO `settings_descriptions` VALUES (NULL,'NTFCTN','ADV_CFG_NTFCTN_SETTINGS_GROUP_NAME','NTFCTN','ADV_CFG_NTFCTN_SETTINGS_GROUP_DESCR');
SET @set_descr_id = LAST_INSERT_ID();
INSERT INTO `settings_groups` VALUES ('EMAIL_NOTIFICATION_SETTINGS',@set_descr_id,1);

INSERT INTO `settings_descriptions` VALUES (NULL,'NTFCTN','ADV_EMAIL_NOTIFICATION_NAME','NTFCTN','ADV_EMAIL_NOTIFICATION_DESCR');
SET @set_descr_id = LAST_INSERT_ID();
INSERT INTO `settings` VALUES ('EMAIL_NOTIFICATION_SETTINGS','EMAIL_NOTIFICATION_FORMAT',@set_descr_id,'LIST','Validator','alwaysValid','TEXT','TEXT');

INSERT INTO `settings_descriptions` VALUES (NULL,'NTFCTN','ADV_CFG_EMAIL_TEXT','NTFCTN','ADV_CFG_EMAIL_TEXT');
SET @set_descr_id = LAST_INSERT_ID();
INSERT INTO `settings_list_values` VALUES ('EMAIL_NOTIFICATION_SETTINGS','EMAIL_NOTIFICATION_FORMAT','TEXT',@set_descr_id);

INSERT INTO `settings_descriptions` VALUES (NULL,'NTFCTN','ADV_CFG_EMAIL_HTML','NTFCTN','ADV_CFG_EMAIL_HTML');
SET @set_descr_id = LAST_INSERT_ID();
INSERT INTO `settings_list_values` VALUES ('EMAIL_NOTIFICATION_SETTINGS','EMAIL_NOTIFICATION_FORMAT','HTML',@set_descr_id);
-- For HTML EMAIL Notification -- END -- 

-- Reset Reports -- Begin -- 

REPLACE INTO `resource_labels` SET `res_prefix`='RPTS', `res_label`='BOTH_REPORTS', `res_text`='Both';

UPDATE `resource_labels` SET `res_text`="Are you sure you want to delete report data?" WHERE `res_prefix`='RPTS' AND `res_label`='CONFIRM_WARNING';

UPDATE `resource_labels` SET `res_text`="Report data has been reset." WHERE `res_prefix`='RPTS' AND `res_label`='REPORT_DATA_RESET';

UPDATE `resource_labels` SET `res_text`="By deleting report data you can reset existing reports and start accumulating new statistics from scratch.<br><br><br><span style='font-weight: bold; color: red;'>WARNING! Report data deletion is an irreversible operation. You will not be able to restore this data after it is deleted.</span> <br><br><br>To delete report data, select report type and click the Reset button.<br><br>To cancel report data deletion, click the Cancel button." WHERE `res_prefix`='RPTS' AND `res_label`='RESET_DATA_WARNING';

-- Reset Reports -- End --
