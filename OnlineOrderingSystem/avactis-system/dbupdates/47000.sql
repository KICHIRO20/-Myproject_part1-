UPDATE `store_settings` SET variable_value='4.7.0' WHERE variable_name='db_version';

UPDATE `resource_labels` SET res_text='Customer Reviews' WHERE res_label='CUSTOMER_REVIEWS_HEADER_001';
UPDATE `resource_labels` SET res_text='&copy; 2004-%s Avactis. All Rights Reserved.' WHERE res_label='COPYRIGHT_TEXT';
UPDATE `resource_labels` SET res_text='Click to change store status' WHERE res_label='LFTBX_HEADER_STORE_STATUS_HINT';

REPLACE INTO `resource_labels` SET res_prefix='SYS', res_label='RELOAD', res_text='Reload';

REPLACE INTO `resource_labels` SET res_prefix='SYS', res_label='ADD_EXTENSION', res_text = 'Add Extensions';
REPLACE INTO `resource_labels` SET res_prefix='SYS', res_label='NO_EXT_INSTALLED_MSG' = ' Currently no extension installed. ';

REPLACE INTO `resource_labels` SET res_prefix='SYS', res_label='MNG_STATE_RESULT_MESSAGE', res_text = 'States display have been successfully saved.';
REPLACE INTO `resource_labels` SET res_prefix='SYS', res_label='MNG_STATE_RESULT_ERROR_MESSAGE', res_text = 'Error to save states changes.';
REPLACE INTO `resource_labels` SET res_prefix='SYS', res_label='MNG_CNTR_RESULT_MESSAGE', res_text = 'Country display have been successfully saved.';
REPLACE INTO `resource_labels` SET res_prefix='SYS', res_label='MNG_CNTR_RESULT_ERROR_MESSAGE', res_text = 'Error to save country changes.';

REPLACE INTO `resource_labels` SET res_prefix='SYS', res_label='MENU_DASHBOARD', res_text = 'Dashboard';
REPLACE INTO `resource_labels` SET res_prefix='SYS', res_label='MENU_PRODUCTS', res_text = 'Products';
REPLACE INTO `resource_labels` SET res_prefix='SYS', res_label='MENU_CATEGORIES', res_text = 'Categories';
REPLACE INTO `resource_labels` SET res_prefix='SYS', res_label='MENU_PRODUCT_TYPES', res_text = 'Product Types';
REPLACE INTO `resource_labels` SET res_prefix='SYS', res_label='MENU_MANUFACTURERS', res_text = 'Manufacturers';
REPLACE INTO `resource_labels` SET res_prefix='SYS', res_label='MENU_USERS', res_text = 'Users';
REPLACE INTO `resource_labels` SET res_prefix='SYS', res_label='MENU_ADMIN_MEMBERS', res_text = 'Admin Members';
REPLACE INTO `resource_labels` SET res_prefix='SYS', res_label='MENU_GLOBAL_DISCOUNTS', res_text = 'Global Discounts';
REPLACE INTO `resource_labels` SET res_prefix='SYS', res_label='MENU_PROMO_CODES', res_text = 'Promo Codes';
REPLACE INTO `resource_labels` SET res_prefix='SYS', res_label='MENU_GIFT_CERTIFICATES', res_text = 'Gift Certificates';
REPLACE INTO `resource_labels` SET res_prefix='SYS', res_label='MENU_NEWSLETTERS', res_text = 'Newsletters';
REPLACE INTO `resource_labels` SET res_prefix='SYS', res_label='MENU_SUBSCRIPTIONS', res_text = 'Subscriptions';
REPLACE INTO `resource_labels` SET res_prefix='SYS', res_label='MENU_TRANSACTION_TRACKING', res_text = 'Transaction Tracking';
REPLACE INTO `resource_labels` SET res_prefix='SYS', res_label='MENU_RESET_REPORTS', res_text = 'Reset Reports';
REPLACE INTO `resource_labels` SET res_prefix='SYS', res_label='MENU_STOREFRONT_DESIGN', res_text = 'Storefront Design';
REPLACE INTO `resource_labels` SET res_prefix='SYS', res_label='MENU_THEME_MANAGER', res_text = 'Theme Manager';
REPLACE INTO `resource_labels` SET res_prefix='SYS', res_label='MENU_PAGE_MANAGER', res_text = 'Page Manager';
REPLACE INTO `resource_labels` SET res_prefix='SYS', res_label='MENU_CMS_PAGES', res_text = 'CMS Pages';
REPLACE INTO `resource_labels` SET res_prefix='SYS', res_label='MENU_CMS_MENUS', res_text = 'Links &amp; Menus';
REPLACE INTO `resource_labels` SET res_prefix='SYS', res_label='MENU_SETTINGS', res_text = 'Settings';
REPLACE INTO `resource_labels` SET res_prefix='SYS', res_label='MENU_MAIL_SETTINGS', res_text = 'Mail Settings';
REPLACE INTO `resource_labels` SET res_prefix='SYS', res_label='MENU_HTTPS_SETTINGS', res_text = 'HTTPS Settings';
REPLACE INTO `resource_labels` SET res_prefix='SYS', res_label='MENU_TOOLS', res_text = 'Tools';
REPLACE INTO `resource_labels` SET res_prefix='SYS', res_label='MENU_SYSTEM_LOGS', res_text = 'System Logs';
REPLACE INTO `resource_labels` SET res_prefix='SYS', res_label='MENU_BACKUP_RESTORE', res_text = 'Backup/Restore';
REPLACE INTO `resource_labels` SET res_prefix='SYS', res_label='MENU_CLEAR_CACHE_LOGS', res_text = 'Clear Cache &amp; Logs';
REPLACE INTO `resource_labels` SET res_prefix='SYS', res_label='MENU_LICENSE_INFO', res_text = 'License Info';
REPLACE INTO `resource_labels` SET res_prefix='SYS', res_label='MENU_APPLICATION_SERVER_INFO', res_text = 'Application/Server Info';
REPLACE INTO `resource_labels` SET res_prefix='SYS', res_label='MENU_SECURE_STORE', res_text = 'Secure store';
REPLACE INTO `resource_labels` SET res_prefix='SYS', res_label='MENU_EXTENSIONS', res_text = 'Extensions';
REPLACE INTO `resource_labels` SET res_prefix='SYS', res_label='MENU_MANAGE_EXTENSIONS', res_text = 'Manage Extensions';
REPLACE INTO `resource_labels` SET res_prefix='SYS', res_label='MENU_CONFIGURE_EXTENSIONS', res_text = 'Configure Extensions';


REPLACE INTO `resource_labels` SET res_prefix='ExtManager', res_label='ERR_FAILED_TO_GET_FILE', res_text='Install failed as could not get the file from server. Please retry after sometime. If problem persists, please contact support@avactis.com.';
REPLACE INTO `resource_labels` SET res_prefix='ExtManager', res_label='ERR_FILE_DOESNOT_EXIST', res_text='Install failed as could not find the requested file from server. Please contact support@avactis.com.';
REPLACE INTO `resource_labels` SET res_prefix='ExtManager', res_label='ERR_INVALID_EXTENSION', res_text='Could not find the requested extension compatible for your store version. Please contact support@avactis.com.';
REPLACE INTO `resource_labels` SET res_prefix='ExtManager', res_label='ERR_LICENSE_NOT_HAVE_ACCESS', res_text='Your license doesn\'t have access to the requested extension.You might want to purchase the extension from marketplace.avactis.com or membership from www.avactis.com. If you think you should have access to it, please contact sales@avactis.com.';
REPLACE INTO `resource_labels` SET res_prefix='ExtManager', res_label='ERR_CHK_ZIP_OR_PERMISSIONS', res_text='Install failed. Please check permissions on avactis-system/cache and avactis-system/backup. Or you might not have Zip.so installed on your server. Please contact your hosting provider for assistance.';
REPLACE INTO `resource_labels` SET res_prefix='ExtManager', res_label='ERR_UNINSTALL_FAILED', res_text='Extension uninstall failed. Please contact support@avactis.com.';
REPLACE INTO `resource_labels` SET res_prefix='ExtManager', res_label='ERR_EXTN_BACKUP_FAILED', res_text='Uninstall Failed as extension could not be backed-up. Please check permissions on avactis-system/backup/. Please contact your hosting provider for assistance.';
REPLACE INTO `resource_labels` SET res_prefix='ExtManager', res_label='INF_CURRENT_VERSION', res_text='Your store\'s current version is:';
REPLACE INTO `resource_labels` SET res_prefix='ExtManager', res_label='INF_NEW_VERSION', res_text='Newer version ';
REPLACE INTO `resource_labels` SET res_prefix='ExtManager', res_label='INF_UPGRADE_TO_NEW_1', res_text=' is available for upgrade.';
REPLACE INTO `resource_labels` SET res_prefix='ExtManager', res_label='INF_UPGRADE_TO_NEW_2', res_text=' to upgrade.';
REPLACE INTO `resource_labels` SET res_prefix='ExtManager', res_label='INF_BACKUP_MESG', res_text='Please remember to backup your store files and database before you upgrade your store.';
REPLACE INTO `resource_labels` SET res_prefix='ExtManager', res_label='ERR_DB_BACKUP_FAILED', res_text='Failed to create DB Backup. Please check permissions on avactis-system/backup/ and retry. Please contact support@avactis.com for any issues.';
REPLACE INTO `resource_labels` SET res_prefix='ExtManager', res_label='ERR_CORE_UPGRADE_FAILED', res_text='AvactisNext Core Upgrade failed. - ';
REPLACE INTO `resource_labels` SET res_prefix='ExtManager', res_label='ERR_EXTRACT_FAILED', res_text='Failed to extract files. Please check permissions on store root folder and retry. Please contact support@avactis.com for any issues.';
REPLACE INTO `resource_labels` SET res_prefix='ExtManager', res_label='ERR_FILE_BACKUP_FAILED', res_text='Failed to backup store files. Please check permissions on avactis-system/backup/ and retry. Please contact support@avactis.com for any issues.';
REPLACE INTO `resource_labels` SET res_prefix='ExtManager', res_label='INF_SAME_VERSION', res_text='Your store is up-to-date!';
REPLACE INTO `resource_labels` SET res_prefix='ExtManager', res_label='WRN_UPGRADE_TIME', res_text='Please note that the upgrade might take time based on your database size as it will create database backup.';
REPLACE INTO `resource_labels` SET res_prefix='ExtManager', res_label='ERR_MARKETPLACE_NOT_AVAILABLE', res_text='Connection to marketplace.avactis.com failed. The server might be down for maintenance. Please retry after sometime. If problem persists, please contact support@avactis.com.';
REPLACE INTO `resource_labels` SET res_prefix='ExtManager', res_label='INF_UPGRADE_STEPS', res_text='Please follow the below simple steps to upgrade your store now: ';
REPLACE INTO `resource_labels` SET res_prefix='ExtManager', res_label='INF_UPGRADE_STEP_1', res_text='1. Turn your store offline by clicking ';
REPLACE INTO `resource_labels` SET res_prefix='ExtManager', res_label='INF_UPGRADE_STEP_2', res_text='2. Take backup of your store files and database manually or as instructed ';
REPLACE INTO `resource_labels` SET res_prefix='ExtManager', res_label='INF_UPGRADE_STEP_3', res_text='3. After verifying backups of both files and database, please click ';
REPLACE INTO `resource_labels` SET res_prefix='ExtManager', res_label='INF_UPGRADE_STEP_4', res_text='4. Please turn your store online by clicking ';
REPLACE INTO `resource_labels` SET res_prefix='ExtManager', res_label='INF_UPGRADE_STEP_5', res_text='Thats it! Your store should be upgraded!! If you face any issues, please contact support@avactis.com';
REPLACE INTO `resource_labels` SET res_prefix='ExtManager', res_label='ERR_LICENSE_NOT_SET= "You are using an <strong>UNREGISTERED</strong> installation of <em>Avactis Shopping Cart.</em> Please register your store.';
REPLACE INTO `resource_labels` SET res_prefix='ExtManager', res_label='EXTN_FREE', res_text='Free';
REPLACE INTO `resource_labels` SET res_prefix='ExtManager', res_label='EXTN_BUY', res_text='Buy';
REPLACE INTO `resource_labels` SET res_prefix='ExtManager', res_label='EXTN_PREMIUM', res_text='Premium';
