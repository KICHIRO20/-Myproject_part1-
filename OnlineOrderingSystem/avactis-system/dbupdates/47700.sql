UPDATE `store_settings` SET variable_value='4.7.7' WHERE variable_name='db_version';

REPLACE INTO `resource_labels` SET res_prefix='SYS', res_label='PRD_LENGTH_NAME', res_text='Length';
REPLACE INTO `resource_labels` SET res_prefix='SYS', res_label='PRD_LENGTH_DESCR', res_text='The length of the product in inches';
REPLACE INTO `resource_labels` SET res_prefix='SYS', res_label='PRD_WIDTH_NAME', res_text='Width';
REPLACE INTO `resource_labels` SET res_prefix='SYS', res_label='PRD_WIDTH_DESCR', res_text='The width of the product in inches';
REPLACE INTO `resource_labels` SET res_prefix='SYS', res_label='PRD_HEIGHT_NAME', res_text='Height';
REPLACE INTO `resource_labels` SET res_prefix='SYS', res_label='PRD_HEIGHT_DESCR', res_text='The height of the product in inches';

REPLACE INTO `resource_labels` SET res_prefix='ExtManager', res_label='ERR_CHK_ZIP_OR_PERMISSIONS', res_text='Install failed. Please check permissions on avactis-conf/cache and avactis-conf/backup. Or you might not have Zip.so installed on your server. Please contact your hosting provider for assistance.';

REPLACE INTO `resource_labels` SET res_prefix='SYS', res_label='HTTPS_WRN_005', res_text='An error occurred when creating a configuration file due to insufficient rights to write to file "avactis-conf/https_config.php".';
REPLACE INTO `resource_labels` SET res_prefix='SYS', res_label='HTTPS_WRN_007', res_text='Unable to delete the configuration file "avactis-conf/https_config.php", possibly due to insufficient deletion rights. Please change the access rights for this file or delete it manually.';
REPLACE INTO `resource_labels` SET res_prefix='SYS', res_label='HTTPS_WRN_012', res_text='File "avactis-conf/https_config.php" is not accessible for writing. Please, check permissions for this file.';


INSERT INTO `attributes` VALUES
(75, 4, 1, 'length', 'Length', 'PRD_LENGTH_NAME', 'PRD_LENGTH_DESCR', 'standard', 0, '0', '15', '15', 'N', 46),
(76, 4, 1, 'length', 'Width', 'PRD_WIDTH_NAME', 'PRD_WIDTH_DESCR', 'standard', 0, '0', '15', '15', 'N', 47),
(77, 4, 1, 'length', 'Height', 'PRD_HEIGHT_NAME', 'PRD_HEIGHT_DESCR', 'standard', 0, '0', '15', '15', 'N', 48);

INSERT INTO `product_type_attributes` VALUES(NULL, '1', '75', '1', '', ''),(NULL, '1', '76', '1', '', ''),(NULL, '1', '77', '1', '', ''),(NULL, '2', '75', '1', '', ''),(NULL, '2', '76', '1', '', ''),(NULL, '2', '77', '1', '', ''),(NULL, '3', '75', '1', '', ''),(NULL, '3', '76', '1', '', ''),(NULL, '3', '77', '1', '', ''),(NULL, '4', '75', '1', '', ''),(NULL, '4', '76', '1', '', ''),(NULL, '4', '77', '1', '', ''),(NULL, '5', '75', '1', '', ''),(NULL, '5', '76', '1', '', ''),(NULL, '5', '77', '1', '', ''),(NULL, '6', '75', '1', '', ''),(NULL, '6', '76', '1', '', ''),(NULL, '6', '77', '1', '', ''),(NULL, '7', '75', '1', '', ''),(NULL, '7', '76', '1', '', ''),(NULL, '7', '77', '1', '', ''),(NULL, '8', '75', '1', '', ''),(NULL, '8', '76', '1', '', ''),(NULL, '8', '77', '1', '', ''),(NULL, '9', '75', '1', '', ''),(NULL, '9', '76', '1', '', ''),(NULL, '9', '77', '1', '', ''),(NULL, '10', '75', '1', '', ''),(NULL, '10', '76', '1', '', ''),(NULL, '10', '77', '1', '', '');

INSERT INTO `asc_resource_labels` (`res_prefix`, `res_label`, `res_text`) VALUES
( 'TL', 'SYSTEM_LOG_CLEAR_TYPE', 'System log type'),
( 'TL', 'SYSTEM_LOG_CLER_ALL', 'All'),
( 'TL', 'SYSTEM_LOG_CLEAR_PAYMENT_MODULE_LOGS', 'Payment Module Logs');



