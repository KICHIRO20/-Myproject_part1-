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
 * Action handler on update general settings.
 *
 * @package Configuration
 * @access  public
 * @author Alexey Kolesnikov
 */
class UpdateGeneralSettings extends AjaxAction
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Action constructor.
     *
     * @ finish the functions on this page
     */
    function UpdateGeneralSettings()
    {
    }

    /**
     * @
     */
    function onAction()
    {
        global $application;
        $request = $application->getInstance('Request');
        $form_errors = array();


        // !!!!!!!!!!!!!!!!!!!!!
        //
        //      !                                  ,
        //            null.                                              .
        //  . .                          NULL,             ,
        //                                    store_settings.
        //
        // !!!!!!!!!!!!!!!!!!!!!



    	$store_online = $request->getValueByKey( SYSCONFIG_STORE_ONLINE );
    	$store_offline_key = $request->getValueByKey( SYSCONFIG_STORE_OFFLINE_KEY );
    	$store_show_absent = $request->getValueByKey( SYSCONFIG_STORE_SHOW_ABSENT );
        $store_allow_buy_more_than_stock = $request->getValueByKey( SYSCONFIG_STORE_ALLOW_BUY_MORE_THAN_STOCK );
        $store_return_product_to_stock_order_deleted = $request->getValueByKey( SYSCONFIG_STORE_RETURN_PRODUCT_TO_STOCK_ORDER_DELETED );
        $store_return_product_to_stock_order_cancelled = $request->getValueByKey( SYSCONFIG_STORE_RETURN_PRODUCT_TO_STOCK_ORDER_CANCELLED );
        $store_enable_wishlist = $request->getValueByKey( SYSCONFIG_STORE_ENABLE_WISHLIST );
        //$store_order_absent = $request->getValueByKey( SYSCONFIG_STORE_ORDER_ABSENT );
    	$store_time_shift = $request->getValueByKey( SYSCONFIG_STORE_TIME_SHIFT );
    	$store_signin_count = $request->getValueByKey( SYSCONFIG_STORE_SIGNIN_COUNT );
    	$store_signin_timeout = $request->getValueByKey( SYSCONFIG_STORE_SIGNIN_TIMEOUT );
    	$store_show_cart = $request->getValueByKey( SYSCONFIG_STORE_SHOW_CART );
    	$min_subtotal_to_begin_checkout = floatval($request->getValueByKey( SYSCONFIG_MIN_SUBTOTAL_TO_BEGIN_CHECKOUT ));
    	if($min_subtotal_to_begin_checkout < 0.00)
    	{
    		$min_subtotal_to_begin_checkout = ZERO_PRICE;
    	}

        $paginator_default_rows_per_page_az = $request->getValueByKey(SYSCONFIG_STORE_PAGINATOR_DEFAULT_ROWS_PER_PAGE_AZ);

        $paginator_default_pages_per_line_az = $request->getValueByKey(SYSCONFIG_STORE_PAGINATOR_PAGES_PER_LINE_AZ);
        if ($paginator_default_pages_per_line_az <= 0)
        {
            $paginator_default_pages_per_line_az = 3;
        }

        if ( $request->getValueByKey(SYSCONFIG_STORE_PAGINATOR_ROWS_PER_PAGE_VALUES_AZ) != null )
            $paginator_rows_per_page_values_az = serialize(explode("|", $request->getValueByKey(SYSCONFIG_STORE_PAGINATOR_ROWS_PER_PAGE_VALUES_AZ)));
        else
            $paginator_rows_per_page_values_az = null;

        $paginator_default_rows_per_page_cz = $request->getValueByKey(SYSCONFIG_STORE_PAGINATOR_DEFAULT_ROWS_PER_PAGE_CZ);

        $paginator_default_pages_per_line_cz = $request->getValueByKey(SYSCONFIG_STORE_PAGINATOR_PAGES_PER_LINE_CZ);
        if ($paginator_default_pages_per_line_cz <= 0)
        {
            $paginator_default_pages_per_line_cz = 3;
        }

        if ( $request->getValueByKey(SYSCONFIG_STORE_PAGINATOR_ROWS_PER_PAGE_VALUES_CZ) != null )
            $paginator_rows_per_page_values_cz = serialize(explode("|", $request->getValueByKey(SYSCONFIG_STORE_PAGINATOR_ROWS_PER_PAGE_VALUES_CZ)));
        else
            $paginator_rows_per_page_values_cz = null;

        if (
            $paginator_default_rows_per_page_az != modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_PAGINATOR_DEFAULT_ROWS_PER_PAGE_AZ) ||
            $paginator_rows_per_page_values_az != modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_PAGINATOR_ROWS_PER_PAGE_VALUES_AZ) ||
            $paginator_default_rows_per_page_cz != modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_PAGINATOR_DEFAULT_ROWS_PER_PAGE_CZ) ||
            $paginator_rows_per_page_values_cz != modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_PAGINATOR_ROWS_PER_PAGE_VALUES_CZ) ||
            $paginator_default_pages_per_line_az != modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_PAGINATOR_PAGES_PER_LINE_AZ) ||
            $paginator_default_pages_per_line_cz != modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_PAGINATOR_PAGES_PER_LINE_CZ)
           )
        {
            modApiFunc("Paginator", "clearPaginatorsSession");
        }

        $add_to_cart_default_quantity = $request->getValueByKey(SYSCONFIG_STORE_ADD_TO_CART_DEFAULT_QUANTITY);
        if($add_to_cart_default_quantity != null && $add_to_cart_default_quantity < 1)
        {
            $add_to_cart_default_quantity = 1;
        }
        $add_to_cart_max_quantity = $request->getValueByKey(SYSCONFIG_STORE_ADD_TO_CART_MAX_QUANTITY);
        if($add_to_cart_max_quantity != null && $add_to_cart_max_quantity < 1)
        {
            $add_to_cart_max_quantity = 1;
        }
        $add_to_cart_limit_max_quantity_by_stock = $request->getValueByKey(SYSCONFIG_STORE_ADD_TO_CART_LIMIT_MAX_QUANTITY_BY_STOCK);
        $add_to_cart_add_not_replace = $request->getValueByKey(SYSCONFIG_STORE_ADD_TO_CART_ADD_NOT_REPLACE);

//==================== related products ===============================
        $rp_per_line = $request->getValueByKey(SYSCONFIG_RP_PER_LINE);
        if($rp_per_line !== null)
        {
            $rp_per_line = intval($rp_per_line);
            if($rp_per_line < 1)
                $rp_per_line = 1;
        };

        $rp_random_checkbox = $request->getValueByKey(SYSCONFIG_RP_RANDOM_CHECKBOX);
        if($rp_random_checkbox === 'on')
        {
            $rp_random_checkbox = 1;
        }
        else
        {
            $rp_random_checkbox = 0;
        }

        $rp_random_threshold = $request->getValueByKey(SYSCONFIG_RP_RANDOM_THRESHOLD);
        if($rp_random_threshold !== null)
        {
            $rp_random_threshold = intval($rp_random_threshold);
            if($rp_random_threshold < 1)
                $rp_random_threshold = 1;
        };
//==================== related products ===============================

//==================== featured products ===============================
        $fp_per_line = $request->getValueByKey(SYSCONFIG_FP_PER_LINE);
        if($fp_per_line !== null)
        {
            $fp_per_line = intval($fp_per_line);
            if($fp_per_line < 1)
                $fp_per_line = 1;
        };

        $fp_random_checkbox = $request->getValueByKey(SYSCONFIG_FP_RANDOM_CHECKBOX);
        if($fp_random_checkbox === 'on')
        {
            $fp_random_checkbox = 1;
        }
        else
        {
            $fp_random_checkbox = 0;
        }

        $fp_random_threshold = $request->getValueByKey(SYSCONFIG_FP_RANDOM_THRESHOLD);
        if($fp_random_threshold !== null)
        {
            $fp_random_threshold = intval($fp_random_threshold);
            if($fp_random_threshold < 1)
                $fp_random_threshold = 1;
        };
//==================== featured products ===============================

//==================== bestsellers ===============================
        $bs_per_line = $request->getValueByKey(SYSCONFIG_BS_PER_LINE);
        if($bs_per_line !== null)
        {
            $bs_per_line = intval($bs_per_line);
            if($bs_per_line < 1)
                $bs_per_line = 1;
        };

        $bs_random_checkbox = $request->getValueByKey(SYSCONFIG_BS_RANDOM_CHECKBOX);
        if($bs_random_checkbox === 'on')
        {
            $bs_random_checkbox = 1;
        }
        else
        {
            $bs_random_checkbox = 0;
        }

        $bs_random_threshold = $request->getValueByKey(SYSCONFIG_BS_RANDOM_THRESHOLD);
        if($bs_random_threshold !== null)
        {
            $bs_random_threshold = intval($bs_random_threshold);
            if($bs_random_threshold < 1)
                $bs_random_threshold = 1;
        };
//==================== bestsellers ===============================

        //      !                                  ,
        //            null.                                              .
    	$values = array(

    	    SYSCONFIG_STORE_ONLINE => $store_online


    	   ,SYSCONFIG_STORE_OFFLINE_KEY => $store_offline_key
    	   ,SYSCONFIG_STORE_SHOW_ABSENT => $store_show_absent
    	   ,SYSCONFIG_STORE_ALLOW_BUY_MORE_THAN_STOCK => $store_allow_buy_more_than_stock
           ,SYSCONFIG_STORE_RETURN_PRODUCT_TO_STOCK_ORDER_DELETED => $store_return_product_to_stock_order_deleted
           ,SYSCONFIG_STORE_RETURN_PRODUCT_TO_STOCK_ORDER_CANCELLED => $store_return_product_to_stock_order_cancelled
           ,SYSCONFIG_STORE_ENABLE_WISHLIST => $store_enable_wishlist
           ,SYSCONFIG_STORE_TIME_SHIFT => $store_time_shift
    	   ,SYSCONFIG_STORE_SIGNIN_COUNT => $store_signin_count
    	   ,SYSCONFIG_STORE_SIGNIN_TIMEOUT => $store_signin_timeout
    	   ,SYSCONFIG_STORE_SHOW_CART => $store_show_cart
    	   ,SYSCONFIG_MIN_SUBTOTAL_TO_BEGIN_CHECKOUT => $min_subtotal_to_begin_checkout
           ,SYSCONFIG_STORE_PAGINATOR_DEFAULT_ROWS_PER_PAGE_AZ => $paginator_default_rows_per_page_az
           ,SYSCONFIG_STORE_PAGINATOR_PAGES_PER_LINE_AZ => $paginator_default_pages_per_line_az
           ,SYSCONFIG_STORE_PAGINATOR_ROWS_PER_PAGE_VALUES_AZ => $paginator_rows_per_page_values_az
           ,SYSCONFIG_STORE_PAGINATOR_DEFAULT_ROWS_PER_PAGE_CZ => $paginator_default_rows_per_page_cz
           ,SYSCONFIG_STORE_PAGINATOR_PAGES_PER_LINE_CZ => $paginator_default_pages_per_line_cz
           ,SYSCONFIG_STORE_PAGINATOR_ROWS_PER_PAGE_VALUES_CZ => $paginator_rows_per_page_values_cz
           ,SYSCONFIG_STORE_ADD_TO_CART_DEFAULT_QUANTITY => $add_to_cart_default_quantity
           ,SYSCONFIG_STORE_ADD_TO_CART_MAX_QUANTITY => $add_to_cart_max_quantity
           ,SYSCONFIG_STORE_ADD_TO_CART_LIMIT_MAX_QUANTITY_BY_STOCK => $add_to_cart_limit_max_quantity_by_stock
           ,SYSCONFIG_STORE_ADD_TO_CART_ADD_NOT_REPLACE => $add_to_cart_add_not_replace
           ,SYSCONFIG_RP_PER_LINE           => $rp_per_line
           ,SYSCONFIG_RP_RANDOM_CHECKBOX    => $rp_random_checkbox
           ,SYSCONFIG_RP_RANDOM_THRESHOLD   => $rp_random_threshold
           ,SYSCONFIG_FP_PER_LINE           => $fp_per_line
           ,SYSCONFIG_FP_RANDOM_CHECKBOX    => $fp_random_checkbox
           ,SYSCONFIG_FP_RANDOM_THRESHOLD   => $fp_random_threshold
           ,SYSCONFIG_BS_PER_LINE           => $bs_per_line
           ,SYSCONFIG_BS_RANDOM_CHECKBOX    => $bs_random_checkbox
           ,SYSCONFIG_BS_RANDOM_THRESHOLD   => $bs_random_threshold
           );

        //                   ,                            ,                       .
        foreach($values as $key=>$value)
        {
            if ($value === null)
            {
                unset($values[$key]);
            }
        }

        $val = $request->getValueByKey( SYSCONFIG_STORE_NEXT_ORDER_ID );
        if(is_numeric($val))
        {
            $val = intval($val);
            if($val > 0)
            {
                $cur_val = modApiFunc("Checkout", "getNextOrderId");
                $max_certificate_id = (int)modApiFunc("GiftCertificateApi", "getMaxGiftCertificateID");
                $max_order_id_in_report = (int)modApiFunc("Reports", "getMaxOrderID");

                if($val <= $max_certificate_id && $max_certificate_id!=0)
                {
                    $form_errors[] = new ActionMessage(array('CORE_154', $max_certificate_id));
                }
                if($val <= $max_order_id_in_report && $max_order_id_in_report!=0)
                {
                    $form_errors[] = new ActionMessage(array('CORE_155', $max_order_id_in_report));
                }
                elseif($cur_val != $val)
                {
                    modApiFunc("Checkout", "setNextOrderId", $val);
                }
            }
        }

        if ($request -> getValueByKey('local') != 'Y' && empty($form_errors))
            modApiFunc('Session','set','ResultMessage','MSG_GNRL_SET_UPDATED');
    	modApiFunc('Configuration', 'setValue', $values);
    	modApiFunc('EventsManager','throwEvent','GeneralSettingsUpdated');

        modApiFunc('Session', 'set', 'form_errors', $form_errors);

    	$request = new Request();
    	$request->setView(CURRENT_REQUEST_URL);
    	$application->redirect($request);
    }

    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    /**#@-*/
}

?>