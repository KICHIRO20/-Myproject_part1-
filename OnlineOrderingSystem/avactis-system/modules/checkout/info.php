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
 * Checkout module meta info.
 *
 * @package Checkout
 * @author Vadim Lyalikov
 * @version $Id$
 */

$moduleInfo = array (
    'name'          => 'Checkout',
    'shortName'     => 'CHCKT',
    'resFile'       => 'checkout-messages',
	'groups'         => 'Main',
    'description'   => 'Checkout module description',
    'version'       => '0.1.47700',
    'author'        => 'Vadim Lyalikov',
    'contact'       => '',
    'systemModule'  => false,
    'mainFile'      => 'checkout_api.php',
    'constantsFile'=> 'const.php',
    'extraAPIFiles' => array(
        'DataReaderOrdersDB'    => 'abstract/data_reader_orders_db.php',
        'Checkout_AZ' => 'includes/checkout_api_az.php',
        'REST_Orders' => 'rest/REST_Orders.php',
    ),
    'actions'       => array(
        'AdminZone'    => array(
             'OrdersSearchByStatus'         => 'search-by-status-action.php',
             'OrdersSearchByDate'           => 'search-by-date-action.php',
             'OrdersSearchById'             => 'search-by-id-action.php',
             'CustomersSearchByLetter'      => 'search-by-letter-action.php',
             'CustomersSearchByField'       => 'search-by-field-action.php',
             'UpdateOrderAction'            => 'update-order-action.php',
             'SetOrdersForDeleteAction'     => 'set-orders-for-delete-action.php',
             'DeleteOrdersAction'           => 'delete-orders-action.php',
             'SaveSelectedPaymentModulesList' => 'save-selected-payment-modules-list-action.php',
             'SetCurrentPaymentModuleSettingsViewName' =>  'set-curr-payment-module-settings-view-name.php',
             'SaveSelectedShippingModulesList' => 'save-selected-shipping-modules-list-action.php',
             'SetCurrentShippingModuleSettingsViewName' =>  'set-curr-shipping-module-settings-view-name.php',
             'UpdateCheckoutInfo'           => 'update-checkout-info.php',
             'SaveSortedAttributes'         => 'save-sorted-attributes-checkout-info.php',
             'RemovePersonInfoOrderData'    => 'remove-person-info-order-data-action.php',
             'FlipPersonInfoTypeStatus'     => 'flip_person_info_type_status-action.php',
             'AddCustomField_action'        => 'add-custom-field-action.php',
             'EditCustomField_action'        => 'edit-custom-field-action.php',
             'RemoveCustomField_action'        => 'remove-custom-field-action.php'
        ),
             'SetCurrStep'                  => 'set-curr-step-action.php',
             'ConfirmOrder'                 => 'confirm-order-action.php',
             'SetCurrentOrder'              => 'set-current-order-action.php',
             'SetCurrentCustomer'           => 'set-current-customer-action.php',
             'UpdatePaymentStatus'          => 'update-payment-status-action.php',
             'JSSetCurrStep'                => 'js-set-curr-step-action.php'
    ),
    'hooks' => array
    (
        # 'hook_class_name' => array ( 'onAction'  => 'action_class_name',
        #                              'Hook_File' => 'hook_file_name' )

        'AutoSelectShippingMethod' => array ( 'onAction'  => 'SetCurrStep,JSSetCurrStep',
                                        'Hook_File' => 'auto_select_shipping_method.php' ),
        'AdditionalPersonInfoSetCurrStepHook' => array ( 'onAction'  => 'SetCurrStep',
                                           'Hook_File' => 'additional_person_info_set_curr_step_hook.php')



    ),
    'views'         => array(
         'AdminZone'    => array(
             'ManageOrders'                     => 'checkout-manage-orders-az.php',
             'OrderInfo'                        => 'checkout-order-info-az.php',
             'DeleteOrders'                     => 'checkout-delete-orders-az.php',
             'ManageCustomers'                  => 'checkout-manage-customers-az.php',
             'CustomerInfo'                     => 'checkout-customer-info-az.php',
             'CheckoutPaymentModulesList'       => 'checkout-payment-modules-list-az.php',
             'CheckoutPaymentModuleFreeVersion' => 'checkout-payment-module-free-version-az.php',
             'CheckoutPaymentModuleSettings'    => 'checkout-payment-module-settings-az.php',
             'CheckoutShippingModulesList'      => 'checkout-shipping-modules-list-az.php',
             'CheckoutShippingModuleFreeVersion'=> 'checkout-shipping-module-free-version-az.php',
             'CheckoutShippingModuleSettings'   => 'checkout-shipping-module-settings-az.php',
             'CheckoutInfoList'                 => 'checkout-info-list-az.php',
             'CheckoutInfoAttributeEdit'        => 'checkout-info-attribute-edit-az.php',
             'CheckoutInfoSortGroup'            => 'checkout-info-sort-group-az.php',
             'OrderInvoice'                     => 'checkout-order-invoice-az.php',
             'OrderPackingSlip'                 => 'checkout-order-packing-slip-az.php',
	         'ManageCustomFields'               => 'checkout-manage-custom-fields.php'
         ),
         'CustomerZone' => array(
             'CheckoutView'                     => 'checkout-view-cz.php',
             'CheckoutCustomerInfoInput'        => 'checkout-customer-info-input-cz.php',
             'CheckoutCustomerInfoOutput'       => 'checkout-customer-info-output-cz.php',
             'CheckoutShippingInfoInput'        => 'checkout-shipping-info-input-cz.php',
             'CheckoutShippingInfoOutput'       => 'checkout-shipping-info-output-cz.php',
             'CheckoutBillingInfoInput'         => 'checkout-billing-info-input-cz.php',
             'CheckoutBillingInfoOutput'        => 'checkout-billing-info-output-cz.php',
             'CheckoutCreditCardInfoInput'      => 'checkout-credit-card-info-input-cz.php',
             'CheckoutCreditCardInfoOutput'     => 'checkout-credit-card-info-output-cz.php',
             'CheckoutBankAccountInfoInput'     => 'checkout-bank-account-info-input-cz.php',
             'CheckoutBankAccountInfoOutput'    => 'checkout-bank-account-info-output-cz.php',
             'CheckoutOrder'                    => 'checkout-order-cz.php',
             'CheckoutNavigationBar'            => 'checkout-navigation-bar-cz.php',
             'CheckoutStepLink'                 => 'checkout-step-link-cz.php',
             'CheckoutShippingMethodsSelect'    => 'checkout-shipping-methods-select.php',
             'CheckoutShippingMethodsOutput'    => 'checkout-shipping-methods-output.php',
             'OneStepCheckout'                  => 'one-step-checkout-cz.php',
             'LastPlacedOrder'                  => 'last-placed-order-cz.php'
         ),
        'Aliases' => array(
            'Checkout' => 'CheckoutView',
            'CheckoutSummary' => 'CheckoutOrder'
        )
    )
);
?>