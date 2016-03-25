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
 * WishList class
 *
 * Common API class for Wishlist functionality.
 *
 * @author Sergey Kulitsky
 * @version $Id: wishlist_api.php xxxx 2010-04-28 11:33:47Z azrael $
 * @package WishList
 */
class WishList
{
    function WishList()
    {
    }

    function install()
    {
    	include_once(dirname(__FILE__) . '/includes/install.inc');
    }

    function getTables()
    {
        static $tables;

        if (is_array($tables))
            return $tables;

        $tables = array ();

        $tbl_info = 'wishlist';
        $tables[$tbl_info] = array();
        $tables[$tbl_info]['columns'] = array
            (
                'wl_id'       => $tbl_info . '.wl_id',
                'customer_id' => $tbl_info . '.customer_id',
                'product_id'  => $tbl_info . '.product_id',
                'qty'         => $tbl_info . '.qty',
                'data'        => $tbl_info . '.data'
            );
        $tables[$tbl_info]['types'] = array
            (
                'wl_id'       => DBQUERY_FIELD_TYPE_INT . ' NOT NULL auto_increment',
                'customer_id' => DBQUERY_FIELD_TYPE_INT,
                'product_id'  => DBQUERY_FIELD_TYPE_INT,
                'qty'         => DBQUERY_FIELD_TYPE_INT,
                'data'        => DBQUERY_FIELD_TYPE_LONGTEXT
            );
        $tables[$tbl_info]['primary'] = array
            (
                'wl_id'
            );
        $tables[$tbl_info]['indexes'] = array
            (
                'customer_id' => 'customer_id',
                'product_id'  => 'product_id' // for possible future purposes
            );

        global $application;
        return $application -> addTablePrefix($tables);
    }

    function uninstall()
    {
        global $application;
        $query = new DB_Table_Delete(WishList :: getTables());
        $application -> db -> getDB_Result($query);
    }

    function addToWishlist($data, $customer_id = null)
    {
        global $application;

        // Note: basic checking takes place in AddToWishlist action
        //       where this function is called
        // Note: additional testing takes place every time
        //       the wishlist page is opened

        if (!isset($data['entity_id']) || !isset($data['qty']))
            return false;

        if (!$customer_id)
        {
            $account_name = modApiFunc('Customer_Account',
                                       'getCurrentSignedCustomer');
            if (!$account_name)
                return false;

            $obj = &$application -> getInstance('CCustomerInfo',
                                                $account_name);

            $customer_id = $obj -> getPersonInfo('ID');
        }

        execQuery('INSERT_WISHLIST_RECORD', array(
            'customer_id' => $customer_id,
            'product_id' => $data['entity_id'],
            'qty' => $data['qty'],
            'data' => $data
        ));

        return true;
    }

    function removeFromWishlist($wl_id, $customer_id = null)
    {
        global $application;

        if (!$customer_id)
        {
            $account_name = modApiFunc('Customer_Account',
                                       'getCurrentSignedCustomer');
            if (!$account_name)
                return array();

            $obj = &$application -> getInstance('CCustomerInfo',
                                                $account_name);

            $customer_id = $obj -> getPersonInfo('ID');
        }

        execQuery('DELETE_WISHLIST_RECORD', array(
            'wl_id' => $wl_id,
            'customer_id' => $customer_id
        ));
    }

    function updateWishlistRecord($wl_id, $new_qty, $customer_id = null)
    {
        global $application;

        if (!$customer_id)
        {
            $account_name = modApiFunc('Customer_Account',
                                       'getCurrentSignedCustomer');
            if (!$account_name)
                return array();

            $obj = &$application -> getInstance('CCustomerInfo',
                                                $account_name);

            $customer_id = $obj -> getPersonInfo('ID');
        }

        execQuery('UPDATE_WISHLIST_QUANTITY', array(
            'wl_id' => $wl_id,
            'qty' => $new_qty,
            'customer_id' => $customer_id
        ));
    }

    function getWishlistRecordCartData($wl_id, $customer_id = null)
    {
        global $application;

        if (!$customer_id)
        {
            $account_name = modApiFunc('Customer_Account',
                                       'getCurrentSignedCustomer');
            if (!$account_name)
                return false;

            $obj = &$application -> getInstance('CCustomerInfo',
                                                $account_name);

            $customer_id = $obj -> getPersonInfo('ID');
        }

        $record = execQuery('SELECT_WISHLIST_CONTENT', array(
            'wl_id' => $wl_id,
            'customer_id' => $customer_id
        ));

        if (!$record)
            return false;

        $result = unserialize($record[0]['data']);
        $result['qty'] = $record[0]['quantity'];

        return $result;
    }

    function getWishlistContent($customer_id = null)
    {
        global $application;

        if (!$customer_id)
        {
            $account_name = modApiFunc('Customer_Account',
                                       'getCurrentSignedCustomer');
            if (!$account_name)
                return array();

            $obj = &$application -> getInstance('CCustomerInfo',
                                                $account_name);

            $customer_id = $obj -> getPersonInfo('ID');
        }

        $result = execQuery('SELECT_WISHLIST_CONTENT', array(
            'customer_id' => $customer_id
        ));

        $mods_map = modApiFunc('Product_Options', 'getModsMap');
        $flip_mods_map = array_flip($mods_map);

        if (!$result)
            $result = array();
        else
            foreach($result as $k => $v)
            {
                $result[$k]['options'] = unserialize($v['data']);
                $result[$k]['options'] = $result[$k]['options']['options'];
                if (!$this -> checkWishlistItem($result[$k]))
                {
                    execQuery('DELETE_WISHLIST_RECORD',
                              array('wl_id' => $v['wl_id'],
                                    'customer_id' => $customer_id));
                    unset($result[$k]);
                    continue;
                }
                $result[$k]['modifiers'] = modApiFunc(
                    'Product_Options',
                    'getCombinationModifiers',
                    $result[$k]['options']
                );
                $result[$k]['inventory_id'] = modApiFunc(
                    'Product_Options',
                    'getInventoryIDByCombination',
                    'product',
                    $v['product_id'],
                    $result[$k]['options']
                );
                $result[$k] = modApiFunc('Cart', 'buildProduct',
                                         $result[$k], $flip_mods_map,
                                         $result[$k]['quantity']);
                $result[$k]['wl_id'] = $v['wl_id'];
                $result[$k]['customer_id'] = $customer_id;
            }

        return $result;
    }

    function checkWishlistItem($item)
    {
        $checkers = array(
            '__checkProductExists',
            '__checkProductOnline',
            '__checkProductHasOptions',
            '__checkProductOptionsExist',
            '__checkProductOptionsCombination',
            '__checkProductOptionsByRules',
            '__checkProductVisibility',
        );

        foreach($checkers as $check_method)
            if (modApiFunc('Cart', $check_method, null, $item) === false)
                return false;

        return true;
    }
}

?>