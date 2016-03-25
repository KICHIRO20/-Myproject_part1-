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
 *
 * @package Wishlist
 * @author Sergey Kulitsky
 */
class AddToWishlist extends AjaxAction
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * AddToWishlist constructor.
     */
    function AddToWishlist()
    {
    }

    /**
     * Adds the product to the cart.
     */
    function onAction()
    {
        global $application;
        $request = $application -> getInstance('Request');

        if ($request -> getValueByKey('returning') == 'yes'
            && modApiFunc('Session', 'is_Set', 'WishlistData'))
        {
            // for returning customers reading the data from the session
            $data = modApiFunc('Session', 'get' , 'WishlistData');
            modApiFunc('Session', 'un_Set', 'WishlistData');
        }
        else
        {
            $prod_id = $request -> getValueByKey('prod_id');

            $data=array(
                'parent_entity' => 'product'
               ,'entity_id' => $prod_id
               ,'options' => $request -> getValueByKey('po')
               ,'qty' => $request -> getValueByKey('quantity_in_cart')
            );

            // setting qty = 1 if not specified
            if (!$data['qty'] && $data['qty'] !== 0)
            $data['qty'] = 1;

            $options_sent = $request -> getValueByKey('options_sent');
            $data['options_sent'] = $options_sent;

            if($data['options'] == null)
            {
                $data['options'] = array();
            }
        }

        // if customer is not logged in keeping the data in session and
        // force customer to login...
        if (!modApiFunc('Customer_Account', 'getCurrentSignedCustomer'))
        {
            modApiFunc('Session', 'set', 'WishlistData', $data);
            $request = new Request();
            $request -> setView(CURRENT_REQUEST_URL);
            $request -> setAction('AddToWishlist');
            $request -> setKey('returning', 'yes');
            modApiFunc('Session', 'set', 'toURLAfterSignIn',
                       $request -> getURL());

            $request = new Request();
            $request -> setView('CustomerAccountHome');
            $application -> redirect($request);
            return;
        }

        $wl_content = modApiFunc('Wishlist','getWishlistContent');
        if(is_array($wl_content))
        {
            foreach($wl_content as $wl)
            {
                if($wl['ID'] == $data['entity_id'] && $this->compareOptions($data['options'], $wl['Options']))
                {
                    modApiFunc('Wishlist', 'updateWishlistRecord', $wl['wl_id'], $wl['Quantity_In_Cart'] + $data['qty']);
                    $request = new Request();
                    $request -> setView(CURRENT_REQUEST_URL);
                    $application -> redirect($request);
                    return;
                }
            }
        }

        $result = modApiFunc('Cart', 'processPostedProductData', $data, $data['options_sent'], false);

        if (!$result['is_error'])
        {
            if (!empty($result['stock_discarded_by_warning']))
                modApiFunc('Session', 'set', 'StockDiscardedBy', $result['stock_discarded_by_warning']);

            modApiFunc('Wishlist', 'addToWishlist', $result['data']);
            $request = new Request();
            $request -> setView(CURRENT_REQUEST_URL);
            $application -> redirect($request);
        }
        else
        {
            if ($result['discard_by'] != 'none')
                modApiFunc('Session', 'set', 'OptionsDiscardedBy', $result['discard_by']);

            if ($result['stock_discarded_by'] != 'none')
                modApiFunc('Session', 'set', 'StockDiscardedBy', $result['stock_discarded_by']);

            modApiFunc('Session', 'set', 'sentCombination', $result['data']['options']);
            $request = new Request();
            $request -> setView('ProductInfo');
            $request -> setAction('SetCurrentProduct');
            $request -> setKey('prod_id',$prod_id);
            $request -> setProductID($prod_id);
            $p = new CProductInfo($prod_id);
            $request -> setCategoryID($p -> chooseCategoryID());
            $application -> redirect($request);
        };
    }

    function compareOptions($opt1,$opt2)
    {
        foreach($opt1 as $key => $item)
        {
        if(!isset($opt2[$key]) || $opt2[$key] != $opt1[$key])
            return false;
        }
     return true;
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