<?php

?><?php
/**
 * Build Administration Menu.
 *
 * @package Avactis
 * @subpackage Administration
 */


/**
 * Constructs the admin menu.
 *
 * The elements in the array are :
 *     0: Menu item name
 *     1: Minimum level or capability required.
 *     2: The URL of the item's file
 *     3: Class
 *     4: ID
 *     5: Icon for top level menu
 *
 * @global array $menu
 * @name $menu
 * @var array
 */
//$submenu[ 'index.php' ][0] = array( 'Home', 'read', 'index.php' );

// Dashboard
$menu[2] = array( getxmsg('SYS','MENU_DASHBOARD'), 'read', 'index.php', '', '', 'menu-dashboard', 'icon-home' );

// Catalog
$menu[5] = array( getxmsg('SYS','MENU_CATALOG'), 'read', 'catalog.php', '', '', 'menu-catalog', 'icon-book-open' );

$submenu[ 'catalog.php' ][0] = array( getxmsg('SYS','MENU_PRODUCTS'), 'read', 'catalog_manage_products.php' );
$submenu[ 'catalog.php' ][1] = array( getxmsg('SYS','MENU_CATEGORIES'), 'read', 'catalog_manage_categories.php' );
$submenu[ 'catalog.php' ][2] = array( getxmsg('SYS','MENU_PRODUCT_TYPES'), 'read', 'catalog_manage_product_types.php' );
$submenu[ 'catalog.php' ][3] = array( getxmsg('SYS','MENU_MANUFACTURERS'), 'read', 'mnf_manufacturers.php' );


// Orders
$menu[15] = array( getxmsg('SYS','MENU_ORDERS'), 'read', 'orders.php', '', '', 'menu-orders', 'icon-basket-loaded' );

//$submenu[ 'orders.php' ][0] = array( 'Orders', 'read', 'orders.php' );


// Users
$menu[30] = array( getxmsg('SYS','MENU_USERS'), 'read', 'users.php', '', '', 'menu-users', 'icon-users' );

$submenu[ 'users.php' ][0] = array( getxmsg('SYS','MENU_ADMIN_MEMBERS'), 'read', 'admin_members.php' );
$submenu[ 'users.php' ][1] = array( getxmsg('SYS','MENU_CUSTOMERS'), 'read', 'customers.php' );
$submenu[ 'users.php' ][2] = array( getxmsg('SYS','MENU_CUSTOMER_REVIEWS'), 'read', 'customer_reviews.php' );

// Storefront Design
$menu[40] = array( getxmsg('SYS','MENU_STOREFRONT_DESIGN'), 'read', 'storefront_design.php', '', '', 'menu-design', 'icon-pencil' );

$submenu[ 'storefront_design.php' ][0] = array( getxmsg('SYS','MENU_THEME_MANAGER'), 'read', 'look_feel.php' );
$submenu[ 'storefront_design.php' ][1] = array( getxmsg('SYS','MENU_PAGE_MANAGER'), 'read', 'layout_cms.php' );
$submenu[ 'storefront_design.php' ][2] = array( getxmsg('SYS','MENU_CMS_PAGES'), 'read', 'cms_pages.php' );
$submenu[ 'storefront_design.php' ][3] = array( getxmsg('SYS','MENU_NAV_CMS_MENUS'), 'read', 'cms_menus.php' );

// Extensions
$menu[55] = array( getxmsg('SYS','MENU_EXTENSIONS'), 'read', 'extensions.php', '', '', 'menu-extensions', 'icon-puzzle' );


$submenu[ 'extensions.php' ][1] = array( getxmsg('SYS','MENU_CONFIGURE_EXTENSIONS'), 'read', 'configure-extensions.php' );
//$submenu[ 'manage-extensions.php' ][2] = array( getxmsg('SYS','MENU_CONFIGURE_EXTENSIONS'), 'read', 'configure_extensions.php' );

// Reports
$menu[65] = array( getxmsg('SYS','MENU_REPORTS'), 'read', 'reports.php', '', '', 'menu-reports', 'icon-pie-chart' );

$submenu[ 'reports.php' ][0] = array( getxmsg('SYS','MENU_REPORTS'), 'read', 'reports.php' );
$submenu[ 'reports.php' ][1] = array( getxmsg('SYS','MENU_RESET_REPORTS'), 'read', 'reports_reset.php' );

// Marketing
$menu[70] = array( getxmsg('SYS','MENU_MARKETING'), 'read', 'marketing.php', '', '', 'menu-marketing', 'icon-present' );

$submenu[ 'marketing.php' ][0] = array( getxmsg('SYS','MENU_GLOBAL_DISCOUNTS'), 'read', 'marketing_manage_discounts.php' );
$submenu[ 'marketing.php' ][1] = array( getxmsg('SYS','MENU_PROMO_CODES'), 'read', 'marketing_manage_promo_codes.php' );
$submenu[ 'marketing.php' ][2] = array( getxmsg('SYS','MENU_GIFT_CERTIFICATES'), 'read', 'marketing_manage_gc.php' );
$submenu[ 'marketing.php' ][3] = array( getxmsg('SYS','MENU_NEWSLETTERS'), 'read', 'newsletter_archive.php' );
$submenu[ 'marketing.php' ][4] = array( getxmsg('SYS','MENU_SUBSCRIPTIONS'), 'read', 'subscriptions_manage.php' );
$submenu[ 'marketing.php' ][5] = array( getxmsg('SYS','MENU_TRANSACTION_TRACKING'), 'read', 'transaction_tracking_settings.php' );

// Settings
$menu[80] = array( getxmsg('SYS','MENU_SETTINGS'), 'read', 'settings.php', '', '', 'menu-settings', 'icon-equalizer' );

$submenu[ 'settings.php' ][1] = array( getxmsg('SYS','MENU_STORE_SETTINGS'), 'read', 'store_settings.php' );
$submenu[ 'settings.php' ][2] = array( getxmsg('SYS','MENU_MAIL_SETTINGS'), 'read', 'mail_settings.php' );
$submenu[ 'settings.php' ][3] = array( getxmsg('SYS','MENU_HTTPS_SETTINGS'), 'read', 'https_settings.php' );

// Tools
$menu[99] = array( getxmsg('SYS','MENU_TOOLS'), 'read', 'tools.php', '', '', 'menu-tools', 'icon-settings' );

$submenu[ 'tools.php' ][0] = array( getxmsg('SYS','MENU_SYSTEM_LOGS'), 'read', 'timeline.php' );
$submenu[ 'tools.php' ][1] = array( getxmsg('SYS','MENU_BACKUP_RESTORE'), 'read', 'admin_backup.php' );
$submenu[ 'tools.php' ][2] = array( getxmsg('SYS','MENU_CLEAR_CACHE_LOGS'), 'read', 'store_settings_cache.php' );

$submenu[ 'tools.php' ][4] = array( getxmsg('SYS','MENU_APPLICATION_SERVER_INFO'), 'read', 'admin_server_info.php' );
$submenu[ 'tools.php' ][5] = array( getxmsg('SYS','MENU_SECURE_STORE'), 'read', 'modified_file_scanner.php' );

require_once(ABSPATH . 'avactis-system/admin/includes/menu.php');
?>