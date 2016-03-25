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
loadCoreFile('version.php' );
define("VIDEO_TUTORIALS_URL", "http://video.avactis.com/version.".PRODUCT_VERSION_NUMBER."/");

/**
 * Creates references to Page Help and Video Tutorials.
 *
 * @package Core
 * @access public
 * @author Alexey Florinsky
 */
class HelpLinkCreator
{

    /**
     * Creates the HelpLinkCreator object.
     * @access public
     */
	function HelpLinkCreator()
	{
        global $application;

        $this->initCodeList();

        // getting lables from message resources
        $obj = &$application->getInstance('MessageResources');
        $pagehelp_label = $obj->getMessage( new ActionMessage('PAGE_HELP_LABEL') );
        $tutorial_label = $obj->getMessage( new ActionMessage('VIDEO_TUTORIAL_LABEL') );

        // the template variable %HELP_URL% will be replaced by URL
        $this->pagehelp_link_template = '<A HREF="%HELP_URL%" target="_blank">'.$pagehelp_label.'</A>';
        $this->tutorial_link_template = '<A HREF="javascript: void(0);" onclick="javascript:openURLinNewWindow(\'%HELP_URL%\');">'.$tutorial_label.'</A>';

        // where files located (relative to Admin Zone)
        // the template variable %PAGE_NAME% will be replaced by page name form the code_list
        $this->pagehelp_path_template = 'http://wiki.avactis.com/index.php?title=%PAGE_HELP_NAME%';
        $this->tutorial_path_template = VIDEO_TUTORIALS_URL.'VideoEn%VIDEO_TUTORIAL_NAME%.php';
	}


    /**
     * Creates a list of pairs ('code' => 'page name').
     * By this list the page name will be defined and put to the refence template.
     *
     * @access public
     */
    function initCodeList()
    {
        $this->code_list =
        array(
               'credit_card_editr'            => array( 'VIDEO_TUTORIAL_NAME'  => 'AddCustomeAttributeBigText',
                                                        'PAGE_HELP_NAME' => 'Credit_Card_List_Editor',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'Clear_Cache'                   => array( 'VIDEO_TUTORIAL_NAME'  => '',
                                                        'PAGE_HELP_NAME' => 'Clear_Cache',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'License'                   => array( 'VIDEO_TUTORIAL_NAME'  => '',
                                                        'PAGE_HELP_NAME' => 'License',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'Reset_Reports'                   => array( 'VIDEO_TUTORIAL_NAME'  => '',
                                                        'PAGE_HELP_NAME' => 'Reset_Reports',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'ZIP_Based_Tax_Rates'          => array( 'VIDEO_TUTORIAL_NAME'  => 'AddCustomeAttributeBigText',
                                                        'PAGE_HELP_NAME' => 'zip_postal_code_tax_rates',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'customer_account_settings'    => array( 'VIDEO_TUTORIAL_NAME'  => 'AddCustomeAttributeBigText',
                                                        'PAGE_HELP_NAME' => 'Customer_Account_Settings',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'store_currencies'             => array( 'VIDEO_TUTORIAL_NAME'  => 'AddCustomeAttributeBigText',
                                                        'PAGE_HELP_NAME' => 'Exchange_Rates_Editor',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'featured_products'            => array( 'VIDEO_TUTORIAL_NAME'  => 'AddCustomeAttributeBigText',
                                                        'PAGE_HELP_NAME' => 'Featured_Products',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'Manage_Newsletters'            => array( 'VIDEO_TUTORIAL_NAME'  => 'AddCustomeAttributeBigText',
                                                        'PAGE_HELP_NAME' => 'Manage_Newsletters',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'add_custome_attr_big_text'    => array( 'VIDEO_TUTORIAL_NAME'  => 'AddCustomeAttributeBigText',
                                                        'PAGE_HELP_NAME' => 'Large_Text_Attribute',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'add_custome_attr_single_line' => array( 'VIDEO_TUTORIAL_NAME'  => 'AddCustomeAttributeSingleLine',
                                                        'PAGE_HELP_NAME' => 'Single_Line_Attribute',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'admin'                        => array( 'VIDEO_TUTORIAL_NAME'  => 'Admin',
                                                        'PAGE_HELP_NAME' => 'Admin',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'admin_add_member'             => array( 'VIDEO_TUTORIAL_NAME'  => 'AdminAddMember',
                                                        'PAGE_HELP_NAME' => 'adminmembers',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'admin_backup'                 => array( 'VIDEO_TUTORIAL_NAME'  => 'AdminBackup',
                                                        'PAGE_HELP_NAME' => 'cron_Backup__Restore_uc',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'admin_delete_member'          => array( 'VIDEO_TUTORIAL_NAME'  => 'AdminDeleteMember',
                                                        'PAGE_HELP_NAME' => '',
                                                        'DISPLAY_PAGEHELP' => 'No',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'admin_edit_member'            => array( 'VIDEO_TUTORIAL_NAME'  => 'AdminEditMember',
                                                        'PAGE_HELP_NAME' => '',
                                                        'DISPLAY_PAGEHELP' => 'No',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'admin_member_info'            => array( 'VIDEO_TUTORIAL_NAME'  => 'AdminMemberInfo',
                                                        'DISPLAY_PAGEHELP' => 'No',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'admin_member_list'            => array( 'VIDEO_TUTORIAL_NAME'  => 'AdminMemberList',
                                                        'PAGE_HELP_NAME' => 'adminmembers',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'admin_password_update'        => array( 'VIDEO_TUTORIAL_NAME'  => 'AdminPasswordUpdate',
                                                        'PAGE_HELP_NAME' => '',
                                                        'DISPLAY_PAGEHELP' => 'No',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'admin_server_info'            => array( 'VIDEO_TUTORIAL_NAME'  => 'AdminServerInfo',
                                                        'PAGE_HELP_NAME' => 'Application_Server_Info',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),


               'campaigns'                    => array( 'VIDEO_TUTORIAL_NAME'  => 'campaigns',
                                                        'PAGE_HELP_NAME' => '',
                                                        'DISPLAY_PAGEHELP' => 'No',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'catalog_add_category'         => array( 'VIDEO_TUTORIAL_NAME'  => 'CatalogAddCategory',
                                                        'PAGE_HELP_NAME' => 'Add_Categories',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'catalog_add_product'          => array( 'VIDEO_TUTORIAL_NAME'  => 'CatalogAddProduct',
                                                        'PAGE_HELP_NAME' => 'Add_New_Products',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'catalog_category_info'        => array( 'VIDEO_TUTORIAL_NAME'  => 'CatalogCategoryInfo',
                                                        'PAGE_HELP_NAME' => 'Category_Info',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'catalog_delete_category'      => array( 'VIDEO_TUTORIAL_NAME'  => 'CatalogDeleteCategory',
                                                        'PAGE_HELP_NAME' => 'Delete_Category',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'catalog_delete_product_type'  => array( 'VIDEO_TUTORIAL_NAME'  => 'CatalogDeleteProductType',
                                                        'PAGE_HELP_NAME' => '',
                                                        'DISPLAY_PAGEHELP' => 'No',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'catalog_delete_products'      => array( 'VIDEO_TUTORIAL_NAME'  => 'CatalogDeleteProducts',
                                                        'PAGE_HELP_NAME' => 'Delete_Products',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'catalog_edit_category'        => array( 'VIDEO_TUTORIAL_NAME'  => 'CatalogEditCategory',
                                                        'PAGE_HELP_NAME' => 'Edit_Categories',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'catalog_edit_product'         => array( 'VIDEO_TUTORIAL_NAME'  => 'CatalogEditProduct',
                                                        'PAGE_HELP_NAME' => 'Edit_Products',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'catalog_edit_product_type'    => array( 'VIDEO_TUTORIAL_NAME'  => 'CatalogEditProductType',
                                                        'PAGE_HELP_NAME' => 'Edit_Product_Type',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'catalog_info_product'         => array( 'VIDEO_TUTORIAL_NAME'  => 'CatalogInfoProduct',
                                                        'PAGE_HELP_NAME' => '',
                                                        'DISPLAY_PAGEHELP' => 'No',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'catalog_manage_categories'    => array( 'VIDEO_TUTORIAL_NAME'  => 'CatalogManageCategories',
                                                        'PAGE_HELP_NAME' => 'Categories',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'catalog_manage_product_types' => array( 'VIDEO_TUTORIAL_NAME'  => 'CatalogManageProductTypes',
                                                        'PAGE_HELP_NAME' => 'Manage_Product_Types',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'catalog_manage_products'      => array( 'VIDEO_TUTORIAL_NAME'  => 'CatalogManageProducts',
                                                        'PAGE_HELP_NAME' => 'Products',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'catalog_move_category'        => array( 'VIDEO_TUTORIAL_NAME'  => 'CatalogMoveCategory',
                                                        'PAGE_HELP_NAME' => 'Move_Category',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'catalog_move_copy_products'   => array( 'VIDEO_TUTORIAL_NAME'  => 'CatalogCopyProducts',
                                                        'PAGE_HELP_NAME' => 'Copy_Products',
                                                        'DISPLAY_PAGEHELP' => 'No',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'catalog_move_products'        => array( 'VIDEO_TUTORIAL_NAME'  => 'CatalogMoveProducts',
                                                        'PAGE_HELP_NAME' => 'Move_Products',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'catalog_new_product_type'     => array( 'VIDEO_TUTORIAL_NAME'  => 'CatalogNewProductType',
                                                        'PAGE_HELP_NAME' => '',
                                                        'DISPLAY_PAGEHELP' => 'No',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'catalog_sort_products'        => array( 'VIDEO_TUTORIAL_NAME'  => 'CatalogSortProducts',
                                                        'PAGE_HELP_NAME' => 'Sort_Products',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'catalog_sort_subcategories'   => array( 'VIDEO_TUTORIAL_NAME'  => 'CatalogSortSubcategories',
                                                        'PAGE_HELP_NAME' => 'Sort_Categories',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'catalog_tab'                  => array( 'VIDEO_TUTORIAL_NAME'  => 'CatalogTab',
                                                        'PAGE_HELP_NAME' => 'Catalog',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'catalog_product_group_edit'   => array( 'VIDEO_TUTORIAL_NAME'  => 'CatalogProductGroupEdit',
                                                        'PAGE_HELP_NAME' => 'edit_products',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'checkout_info'                => array( 'VIDEO_TUTORIAL_NAME'  => '',
                                                        'PAGE_HELP_NAME' => 'checkout_form_editor',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'checkout_info_sort_group'     => array( 'VIDEO_TUTORIAL_NAME'  => '',
                                                        'PAGE_HELP_NAME' => 'checkout_form_editor',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'config_store_owner_profile'  => array( 'VIDEO_TUTORIAL_NAME'  => 'ConfigStoreOwnerProfile',
                                                        'PAGE_HELP_NAME' => 'Store_Owners_Profile',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'cms_tab'                     => array( 'VIDEO_TUTORIAL_NAME'  => 'CMSTab',
                                                        'PAGE_HELP_NAME' => 'Adding_CMS/blog',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

		'cms_menu_tab'                     => array( 'VIDEO_TUTORIAL_NAME'  => 'CMSMenuTab',
                                                        'PAGE_HELP_NAME' => 'Adding_CMS_Menu',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'customer_care'                => array( 'VIDEO_TUTORIAL_NAME'  => 'CustomerCare',
                                                        'PAGE_HELP_NAME' => '',
                                                        'DISPLAY_PAGEHELP' => 'No',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'customer_info'                => array( 'VIDEO_TUTORIAL_NAME'  => 'CustomerInfo',
                                                        'PAGE_HELP_NAME' => '',
                                                        'DISPLAY_PAGEHELP' => 'No',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'customers_tab'                => array( 'VIDEO_TUTORIAL_NAME'  => 'CustomersTab',
                                                        'PAGE_HELP_NAME' => 'Customers',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'customer_reviews_tab'         => array( 'VIDEO_TUTORIAL_NAME'  => 'CustomerReviewsTab',
                                                        'PAGE_HELP_NAME' => 'Reviews',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'edit_custome_attr_single_line'=> array( 'VIDEO_TUTORIAL_NAME'  => 'EditCustomeAttributeSingleLine',
                                                        'PAGE_HELP_NAME' => '',
                                                        'DISPLAY_PAGEHELP' => 'No',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'edit_custome_attr_big_text'   => array( 'VIDEO_TUTORIAL_NAME'  => 'EditCustomeAttributeBigText',
                                                        'PAGE_HELP_NAME' => '',
                                                        'DISPLAY_PAGEHELP' => 'No',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'general_settings'             => array( 'VIDEO_TUTORIAL_NAME'  => 'StoreSettingsGeneralSettings',
                                                        'PAGE_HELP_NAME' => 'General_Settings',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'mail_settings'                => array( 'VIDEO_TUTORIAL_NAME'  => 'StoreSettingsMailSettings',
                                                        'PAGE_HELP_NAME' => 'Mail_Settings',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'help'                         => array( 'VIDEO_TUTORIAL_NAME'  => 'help',
                                                        'PAGE_HELP_NAME' => '',
                                                        'DISPLAY_PAGEHELP' => 'No',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'home_tab'                     => array( 'VIDEO_TUTORIAL_NAME'  => 'HomeTab',
                                                        'PAGE_HELP_NAME' => '',
                                                        'DISPLAY_PAGEHELP' => 'No',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'label_editor_tab'             => array( 'VIDEO_TUTORIAL_NAME'  => 'LabelEditorTab',
                                                        'PAGE_HELP_NAME' => 'Label_Editor',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'language_settings'            => array( 'VIDEO_TUTORIAL_NAME'  => 'LanguageSettings',
                                                        'PAGE_HELP_NAME' => 'Languages',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'location_countries'           => array( 'VIDEO_TUTORIAL_NAME'  => 'LocationCountries',
                                                        'PAGE_HELP_NAME' => 'Countries',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'location_states'              => array( 'VIDEO_TUTORIAL_NAME'  => 'LocationStates',
                                                        'PAGE_HELP_NAME' => 'States',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'look_feel'                    => array( 'VIDEO_TUTORIAL_NAME'  => 'Look_Feel',
                                                        'PAGE_HELP_NAME' => 'look__feel',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'marketing_gift_certificates'  => array( 'VIDEO_TUTORIAL_NAME'  => 'Marketing_Gift_Certificates',
                                                        'PAGE_HELP_NAME' => 'Gift_Certificates',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'my_setup'                     => array( 'VIDEO_TUTORIAL_NAME'  => 'MySetup',
                                                        'PAGE_HELP_NAME' => '',
                                                        'DISPLAY_PAGEHELP' => 'No',
                                                        'DISPLAY_TUTORIAL' => 'No' ),


               'notification_list'=> array( 'VIDEO_TUTORIAL_NAME'  => 'NotificationInitiallyPlacedOrder',
                                                        'PAGE_HELP_NAME' => 'e_mail_notifications',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'notification_info'=> array( 'VIDEO_TUTORIAL_NAME'  => 'NotificationInitiallyPlacedOrder',
                                                        'PAGE_HELP_NAME' => '',
                                                        'DISPLAY_PAGEHELP' => 'No',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'order_edit'                  => array( 'VIDEO_TUTORIAL_NAME'  => 'OrderEdit',
                                                        'PAGE_HELP_NAME' => '',
                                                        'DISPLAY_PAGEHELP' => 'No',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'order_info'                  => array( 'VIDEO_TUTORIAL_NAME'  => 'OrderInfo',
                                                        'PAGE_HELP_NAME' => 'Order_Details',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'order_info_before_delete'    => array( 'VIDEO_TUTORIAL_NAME'  => '',
                                                        'PAGE_HELP_NAME' => '',
                                                        'DISPLAY_PAGEHELP' => 'No',
                                                        'DISPLAY_TUTORIAL' => 'No' ),


               'orders_delete'               => array( 'VIDEO_TUTORIAL_NAME'  => 'OrderDelete',
                                                        'PAGE_HELP_NAME' => 'OrderDelete',
                                                        'DISPLAY_PAGEHELP' => 'No',
                                                        'DISPLAY_TUTORIAL' => 'No'),

               'orders_tab'                   => array( 'VIDEO_TUTORIAL_NAME'  => 'OrdersTab',
                                                        'PAGE_HELP_NAME' => 'Orders',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),


               'reports_tab'                  => array( 'VIDEO_TUTORIAL_NAME'  => 'ReportsTab',
                                                        'PAGE_HELP_NAME' => 'Reports',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),


               'store_settings'               => array( 'VIDEO_TUTORIAL_NAME'  => 'StoreSettings',
                                                        'PAGE_HELP_NAME' => 'Store_Settings',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'store_settings_currency'      => array( 'VIDEO_TUTORIAL_NAME'  => 'StoreSettingsCurrency',
                                                        'PAGE_HELP_NAME' => 'Currency_Format',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'store_settings_datetime'      => array( 'VIDEO_TUTORIAL_NAME'  => 'StoreSettingsDateTime',
                                                        'PAGE_HELP_NAME' => 'Date_and_Time_Format',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'store_settings_number'      => array( 'VIDEO_TUTORIAL_NAME'  => 'StoreSettingsNumber',
                                                        'PAGE_HELP_NAME' => 'Number_Format',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'store_settings_payment'       => array( 'VIDEO_TUTORIAL_NAME'  => 'StoreSettingsPayment',
                                                        'PAGE_HELP_NAME' => 'Payment_Settings',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'store_settings_product_tax_classes'
                                              => array( 'VIDEO_TUTORIAL_NAME'  => 'StoreSettingsProductTaxClasses',
                                                        'PAGE_HELP_NAME' => '',
                                                        'DISPLAY_PAGEHELP' => 'No',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'store_settings_shipping'      => array( 'VIDEO_TUTORIAL_NAME'  => 'StoreSettingsShipping',
                                                        'PAGE_HELP_NAME' => 'Shipping_Settings',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'store_settings_taxes'         => array( 'VIDEO_TUTORIAL_NAME'  => 'StoreSettingsTaxes',
                                                        'PAGE_HELP_NAME' => 'Tax_Settings',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'store_settings_taxes_add_edit_display'
                                              => array( 'VIDEO_TUTORIAL_NAME'  => 'StoreSettingsTaxesAddEditDisplay',
                                                        'PAGE_HELP_NAME' => '',
                                                        'DISPLAY_PAGEHELP' => 'No',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'store_settings_taxes_add_edit_name'
                                              => array( 'VIDEO_TUTORIAL_NAME'  => 'StoreSettingsTaxesAddEditName',
                                                        'PAGE_HELP_NAME' => '',
                                                        'DISPLAY_PAGEHELP' => 'No',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'store_settings_taxes_add_edit_class'
                                              => array( 'VIDEO_TUTORIAL_NAME'  => 'StoreSettingsTaxesAddEditClass',
                                                        'PAGE_HELP_NAME' => '',
                                                        'DISPLAY_PAGEHELP' => 'No',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'store_settings_taxes_add_edit_rate'
                                              => array( 'VIDEO_TUTORIAL_NAME'  => 'StoreSettingsTaxesAddEditRate',
                                                        'PAGE_HELP_NAME' => 'Edit_Tax_Rate',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'store_settings_taxes_shipping_taxes'
                                              => array( 'VIDEO_TUTORIAL_NAME'  => 'StoreSettingsTaxesShippingTaxes',
                                                        'PAGE_HELP_NAME' => '',
                                                        'DISPLAY_PAGEHELP' => 'No',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'store_settings_taxes_calculator'
                                              => array( 'VIDEO_TUTORIAL_NAME'  => 'StoreSettingsTaxesCalculator',
                                                        'PAGE_HELP_NAME' => 'Tax_Calculator',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'store_settings_weight'      => array( 'VIDEO_TUTORIAL_NAME'  => 'StoreSettingsWeight',
                                                        'PAGE_HELP_NAME' => 'Weight_Unit',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'update_system'                => array( 'VIDEO_TUTORIAL_NAME'  => 'UpdateSystem',
                                                        'PAGE_HELP_NAME' => '',
                                                        'DISPLAY_PAGEHELP' => 'No',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'admin_backup_create'          => array( 'VIDEO_TUTORIAL_NAME'  => '',
                                                        'PAGE_HELP_NAME' => '',
                                                        'DISPLAY_PAGEHELP' => 'No',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'admin_backup_restore'         => array( 'VIDEO_TUTORIAL_NAME'  => '',
                                                        'PAGE_HELP_NAME' => '',
                                                        'DISPLAY_PAGEHELP' => 'No',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'admin_backup_delete'          => array( 'VIDEO_TUTORIAL_NAME'  => '',
                                                        'PAGE_HELP_NAME' => '',
                                                        'DISPLAY_PAGEHELP' => 'No',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'admin_backup_info'            => array( 'VIDEO_TUTORIAL_NAME'  => '',
                                                        'PAGE_HELP_NAME' => '',
                                                        'DISPLAY_PAGEHELP' => 'No',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'catalog_search'               => array( 'VIDEO_TUTORIAL_NAME'  => '',
                                                        'PAGE_HELP_NAME' => 'Search',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'marketing_tab'                => array( 'VIDEO_TUTORIAL_NAME'  => 'VideoTutorialNameMarketing',
                                                        'PAGE_HELP_NAME' => 'PageHelpNameMarketing',
                                                        'DISPLAY_PAGEHELP' => 'No',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'marketing_discounts'          => array( 'VIDEO_TUTORIAL_NAME'  => 'VideoTutorialNameMarketingDiscounts',
                                                        'PAGE_HELP_NAME' => 'Discounts',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'marketing_promo_codes'        => array( 'VIDEO_TUTORIAL_NAME'  => 'VideoTutorialNameMarketingPromoCodes',
                                                        'PAGE_HELP_NAME' => 'Manage_Coupons_and_Promo_Codes',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'marketing_add_promo_code'     => array( 'VIDEO_TUTORIAL_NAME'  => 'VideoTutorialNameMarketingAddPromoCode',
                                                        'PAGE_HELP_NAME' => 'PageHelpNameMarketingAddPromoCode',
                                                        'DISPLAY_PAGEHELP' => 'No',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'marketing_edit_promo_code'    => array( 'VIDEO_TUTORIAL_NAME'  => 'VideoTutorialNameMarketingEditPromoCode',
                                                        'PAGE_HELP_NAME' => 'PageHelpNameMarketingEditPromoCode',
                                                        'DISPLAY_PAGEHELP' => 'No',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'product_options'              => array( 'VIDEO_TUTORIAL_NAME'  => '',
                                                        'PAGE_HELP_NAME' => 'Product_Options_General_Guidel',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'product_options_combinations' => array( 'VIDEO_TUTORIAL_NAME'  => '',
                                                        'PAGE_HELP_NAME' => 'Product_Options_Combinations',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'product_options_inventory'    => array( 'VIDEO_TUTORIAL_NAME'  => '',
                                                        'PAGE_HELP_NAME' => 'Product_Options_Inventory_Trac',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'product_options_add_edit'     => array( 'VIDEO_TUTORIAL_NAME'  => '',
                                                        'PAGE_HELP_NAME' => 'Add_or_Edit_Product_Option',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'shipping_tester'              => array( 'VIDEO_TUTORIAL_NAME'  => '',
                                                        'PAGE_HELP_NAME' => 'Real-time_Shipping_Calculators',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'export_products'              => array( 'VIDEO_TUTORIAL_NAME'  => '',
                                                        'PAGE_HELP_NAME' => 'Product_Export',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'export_labels'                => array( 'VIDEO_TUTORIAL_NAME'  => '',
                                                        'PAGE_HELP_NAME' => 'Export_Labels',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'import_products'              => array( 'VIDEO_TUTORIAL_NAME'  => '',
                                                        'PAGE_HELP_NAME' => 'Product_Import',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'import_labels'                => array( 'VIDEO_TUTORIAL_NAME'  => '',
                                                        'PAGE_HELP_NAME' => 'Import_Labels',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'change_default_language'      => array( 'VIDEO_TUTORIAL_NAME'  => '',
                                                        'PAGE_HELP_NAME' => 'languages',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'froogle_export'              => array( 'VIDEO_TUTORIAL_NAME'  => '',
                                                        'PAGE_HELP_NAME' => 'Google_Base_Froogle_Export',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'https_settings'              => array( 'VIDEO_TUTORIAL_NAME'  => '',
                                                        'PAGE_HELP_NAME' => 'HTTPS_Settings',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'digital_products_egoods'     => array( 'VIDEO_TUTORIAL_NAME'  => '',
                                                        'PAGE_HELP_NAME' => 'Digital_Products_E_Goods',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'detailed_product_images'     => array( 'VIDEO_TUTORIAL_NAME'  => '',
                                                        'PAGE_HELP_NAME' => 'Product_Detailed_Images',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'mod_rewrite_settings'        => array(  'VIDEO_TUTORIAL_NAME'  => '',
                                                        'PAGE_HELP_NAME' => 'Search_Engine_Optimized_URLs',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'related_products'           => array(   'VIDEO_TUTORIAL_NAME'  => '',
                                                        'PAGE_HELP_NAME' => 'Related_Products',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'bestsellers'                => array(   'VIDEO_TUTORIAL_NAME'  => '',
                                                        'PAGE_HELP_NAME' => 'Bestsellers',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'admin_cz_layouts_info'        => array( 'VIDEO_TUTORIAL_NAME'  => 'VideoTutorialNameAdminCZLayoutsInfo',
                                                        'PAGE_HELP_NAME' => 'PageHelpNameAdminCZLayoutsInfo',
                                                        'DISPLAY_PAGEHELP' => 'No',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'manufacturers'                => array( 'VIDEO_TUTORIAL_NAME'  => '',
                                                        'PAGE_HELP_NAME' => 'Manufacturers',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'Product_Image_Settings'       => array( 'VIDEO_TUTORIAL_NAME'  => '',
                                                        'PAGE_HELP_NAME' => 'Product_Image_Settings',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'Products_in_Multicategories'       => array( 'VIDEO_TUTORIAL_NAME'  => '',
                                                        'PAGE_HELP_NAME' => 'Products_in_Multicategories',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'Quantity_Discounts'       => array( 'VIDEO_TUTORIAL_NAME'  => '',
                                                        'PAGE_HELP_NAME' => 'Quantity_Discounts',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'System_Logs'              => array( 'VIDEO_TUTORIAL_NAME'  => '',
                                                        'PAGE_HELP_NAME' => 'System_Logs',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'St-Digital_Products_E-Goods'              => array( 'VIDEO_TUTORIAL_NAME'  => '',
                                                        'PAGE_HELP_NAME' => 'st_digital_products_e_goods',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'transaction_tracking_settings'=> array( 'VIDEO_TUTORIAL_NAME'  => '',
                                                        'PAGE_HELP_NAME' => 'Manage_transaction_tracking',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'localization_accepted_currencies'=> array( 'VIDEO_TUTORIAL_NAME'  => '',
                                                        'PAGE_HELP_NAME' => 'Localization_Accepted_Currencies',
                                                        'DISPLAY_PAGEHELP' => 'No',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'mnf_add_manufacturer'=> array( 'VIDEO_TUTORIAL_NAME'  => '',
                                                        'PAGE_HELP_NAME' => 'Manufacturers',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'mnf_edit_manufacturer'=> array( 'VIDEO_TUTORIAL_NAME'  => '',
                                                        'PAGE_HELP_NAME' => 'Manufacturers',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),

               'sort_manufacturers' => array( 'VIDEO_TUTORIAL_NAME'  => '',
                                                        'PAGE_HELP_NAME' => 'Sort_Manufacturers',
                                                        'DISPLAY_PAGEHELP' => 'No',
                                                        'DISPLAY_TUTORIAL' => 'No' ),
               'Subscriptions_MainPage' => array( 'VIDEO_TUTORIAL_NAME'  => '',
                                                        'PAGE_HELP_NAME' => 'Manage_Subscriptions',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),
               'Subscriptions_SortTopics' => array( 'VIDEO_TUTORIAL_NAME'  => '',
                                                        'PAGE_HELP_NAME' => 'Manage_Subscriptions',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),
               'layout_cms' => array( 'VIDEO_TUTORIAL_NAME'  => '',
                                                        'PAGE_HELP_NAME' => 'Page_Manager',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),
              'extension_manager' => array( 'VIDEO_TUTORIAL_NAME'  => '',
                                                        'PAGE_HELP_NAME' => 'Extensions_Manager',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),
              'modified_file_scan' => array( 'VIDEO_TUTORIAL_NAME'  => '',
                                                        'PAGE_HELP_NAME' => 'Secure_Store',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' ),
	      'error_document_manager' => array( 'VIDEO_TUTORIAL_NAME'  => '',
                                                        'PAGE_HELP_NAME' => 'Error_Document_Settings',
                                                        'DISPLAY_PAGEHELP' => 'Yes',
                                                        'DISPLAY_TUTORIAL' => 'No' )
        );

		$new_links = apply_filters('asc_add_page_help_link',array());
		$this->code_list = array_merge($this->code_list,$new_links);
    }


    /**
     * @param $pagecode - the page code
     * @return the link (full HTML tag) to the Page Help
     * @access public
     */
    function getPageHelpLink($pagecode)
    {
        $pagename = $this->_getPagenameByCode($pagecode);

        if ($pagename == '') return '';

        if ($pagename['DISPLAY_PAGEHELP'] == 'Yes')
        {
            $href = str_replace( '%PAGE_HELP_NAME%', $pagename['PAGE_HELP_NAME'] , $this->pagehelp_path_template );
            $link = str_replace( '%HELP_URL%',  $href,                   $this->pagehelp_link_template );
        }
        else
        {
            $link = '';
        }

        return $link;
    }

    /**
     * @param $pagecode - the page code
     * @return the link (full HTML tag) to the Video Tutorial
     * @access public
     */
    function getTutorialLink($pagecode)
    {
        $pagename = $this->_getPagenameByCode($pagecode);

        if ($pagename == '') return '';

        if ($pagename['DISPLAY_TUTORIAL'] == 'Yes')
        {
            $href = str_replace( '%VIDEO_TUTORIAL_NAME%', $pagename['VIDEO_TUTORIAL_NAME'] , $this->tutorial_path_template );
            $link = str_replace( '%HELP_URL%',  $href,                   $this->tutorial_link_template );
        }
        else
        {
            $link = '';
        }

        return $link;
    }

    function _getPagenameByCode($pagecode)
    {
        // searching Page name
        if ( array_key_exists($pagecode, $this->code_list) )
        {
            $pagename = $this->code_list[$pagecode];
        }
        else
        {
            //  error marker
            $pagename = '';
        }

        return $pagename;
    }



    /**
	 * The message key for this message.
	 * @access private
	 */
	var $code_list = array();

    var $pagehelp_link_template;
    var $tutorial_link_template;

    var $pagehelp_path;
    var $tutorial_path;

}
?>