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

/**
 * @package CustomerAccount
 * @author Egor V. Derevyankin
 *
 */

$moduleInfo = array
    (
        'name'         => 'Customer_Account',
        'shortName'    => 'CA',
        'groups'       => 'Main',
        'description'  => 'Customer Account module',
        'version'      => '0.1.47700',
        'author'       => 'Egor V. Derevyankin',
        'contact'      => '',
        'systemModule'  => false,
        'constantsFile'=> 'const.php',
        'mainFile'     => 'customer_account_api.php',
        'resFile'      => 'customer-account-messages',
        'extraAPIFiles' => array(
            'Customer_Account_Installer' => 'includes/ca_installer.php'
           ,'CCustomerInfo'              => 'abstract/customer_info.php'
           ,'CAValidator'                => 'abstract/validator.php'
           ,'DataReaderCustomersDB'      => 'abstract/data_reader_customers_db.php'
           ,'DataFilterCustomersDBCSV'   => 'abstract/data_filter_customers_db_csv.php'
        ),

        'actions' => array
        (
            'AdminZone' => array(
                'do_customers_export' => 'do_customers_export.php'
                ,'delete_customers_accounts' => 'delete_customers_accounts.php'
                ,'activate_customers_accounts' => 'activate_customers_accounts.php'
                ,'update_reg_form'   => 'update_reg_form.php'
                ,'drop_accounts_passwords' => 'drop_accounts_passwords.php'
                ,'update_group_sort_order' => 'update_group_sort_order.php'
                ,'update_customers_accounts' => 'update_customers_accounts.php'
                ,'add_customer_group' => 'add_customer_group.php'
                ,'delete_customer_groups' => 'delete_customer_groups.php'
            ),
            'register_customer' => 'register_customer.php'
           ,'activate_account'  => 'activate_account.php'
           ,'customer_sign_in'  => 'customer_sign_in.php'
           ,'customer_sign_out' => 'customer_sign_out.php'
           ,'save_personal_info' => 'save_personal_info.php'
           ,'save_account_password' => 'save_account_password.php'
           ,'change_account_password' => 'change_account_password.php'
           ,'drop_account_password' => 'drop_account_password.php'
        ),

        'hooks' => array
        (
            'CheckAccountBeforePageOut' => array ( 'onAction'  => 'ActionIsNotSetAction',
                                        'Hook_File' => 'check_account_before_page_out.php' ),
        ),

        'views' => array
        (
            'AdminZone' => array(
                'RegisterFormEditor' => 'register_form_editor_az.php'
               ,'CustomersList' => 'customers_list_az.php'
               ,'CustomerAccountInfo' => 'customer_account_info_az.php'
               ,'CustomerGroups' => 'customer_groups_az.php'
               ,'ExportCustomers' => 'export_customers_az.php'
            ),
            'CustomerZone' => array(
                'CustomerSignInBox' => 'auth_box_cz.php'
               ,'CustomerRegistrationForm' => 'reg_form_cz.php'
               ,'MessageBox'  => 'reg_msg_cz.php'
               ,'CustomerPersonalInfo' => 'customer_personal_info_cz.php'
               ,'OrderHistory' => 'customer_orders_history_cz.php'
               ,'OrderInfo' => 'customer_order_info_cz.php'
               ,'OrderInvoice' => 'customer_order_invoice_cz.php'
               ,'OrderDownloadLinks' => 'customer_order_download_links_cz.php'
               ,'CustomerNewPasswordForm' => 'customer_new_password_form_cz.php'
               ,'CustomerChangePasswordForm' => 'customer_change_password_form_cz.php'
               ,'CustomerForgotPasswordForm' => 'customer_forgot_password_form_cz.php'
               ,'OrderSearchForm' => 'customer_order_search_form_cz.php'
               ,'OrderSearchByIdForm' => 'customer_order_search_by_id_form_cz.php'
               ,'OrderList' => 'customer_order_list_cz.php'
               ,'CustomerHomePage' => 'customer_home_page.php'
            ),
            'Aliases' => array(
                'CustomerSignInForm' => 'CustomerSignInBox',
                'CustomerSignInOutHint' => 'CustomerSignInBox'
            )
        )
    );
?>