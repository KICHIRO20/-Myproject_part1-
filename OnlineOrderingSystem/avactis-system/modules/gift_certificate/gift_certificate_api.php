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

loadModuleFile('gift_certificate/abstract/gift_certificate_classes.php');

/**
 * GiftCertificate module
 *
 * @author Alexey Florinsky
 * @version $Id$
 * @package GiftCertificate
 */
class GiftCertificateApi
{
    function GiftCertificateApi()
    {

    }

    function getImageInfo()
    {
        global $application;
        $imageInfo = array(
            'largeimage' => array(
                    'url'       => '',
                    'width'     => '',
                    'height'    => '',
                    'is_exist'  => false
                ),
            'smallimage' => array(
                    'url'       => '',
                    'width'     => 0,
                    'height'    => 0,
                    'is_exist'  => false
                )
        );

        $images_dir = $application->getAppIni('PATH_IMAGES_DIR');
        $images_url = $application->getAppIni('URL_IMAGES_DIR');
        if($application->getCurrentProtocol()=="https"
            && $application->getAppIni('HTTPS_URL_IMAGES_DIR'))
        {
            $images_url = $application->getAppIni('HTTPS_URL_IMAGES_DIR');
        }

        $gc_images = CConf::get('gc_image_name');
        foreach($gc_images as $id => $fname)
        {
            if(file_exists($images_dir . $fname))
            {
                $imageInfo[$id] = array(
                        'url'       => $images_url . $fname,
                        'is_exist'  => true
                );
                $size = @getimagesize($images_dir . $fname);
                if ($size !== false && isset($size[0]) && isset($size[1]))
                {
                    $imageInfo[$id]['width'] = $size[0];
                    $imageInfo[$id]['height'] = $size[1];
                }
            }
        }

        return $imageInfo;
    }

    function isCodeValid($gc_code)
    {
        if (strlen($gc_code)==19 && preg_match('/^[A-Z0-9\-]+$/', $gc_code))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    function doesCodeExist($gc_code)
    {
        $res = execQuery('SELECT_GC_LIST_BY_FILTER', array('gc_code' => $gc_code));
        if (!empty($res)) return true;
    }

    function generateCode()
    {
        for(;;)
        {
            $code = $this->__getRandCode();
            $res = execQuery('SELECT_GC_LIST_BY_FILTER', array('gc_code' => $code));
            if (empty($res)) return $code;
        }
    }

    function getCurrentBalance($amount)
    {
        foreach($this->__gc_current_list as $gc_obj)
        {
            $amount -= $gc_obj->remainder;
        }
        return ($amount>0 ? $amount : 0.0);
    }

    function getCurrentDetailedBalance($amount)
    {
        $details = array();
        foreach($this->__gc_current_list as $gc_obj)
        {
            $g = $gc_obj->getMap();
            $diff = $amount - $gc_obj->remainder;
            if ($diff == 0)
            {
                $g['gc_remainder'] = 0.0;
                $amount = 0.0;
            }
            elseif($diff > 0)
            {
                $g['gc_remainder'] = 0.0;
                $amount -= $gc_obj->remainder;
            }
            else // $diff < 0
            {
                $g['gc_remainder'] -= $amount;
                $amount = 0.0;
            }
            $details[] = $g;
        }
        return $details;
    }

    function applyCurrentBalance($amount, $order_id)
    {
        $gc_list = $this->getCurrentDetailedBalance($amount);
        $used_gc = array();
        foreach($gc_list as $gc_info)
        {
            $gc_obj = new GiftCertificateUpdater($gc_info['gc_code']);
            //                                                           ,    
            //         ,                                                        .
            //                                               ,              
            //                         -                                 
            if ($gc_obj->remainder != $gc_info['gc_remainder'])
            {
                $gc_obj->remainder = $gc_info['gc_remainder'];
                $gc_obj->save();
                $used_gc[] = $gc_obj->code;
            }
        }
        GiftCertificateLogger::used($used_gc, $order_id);
    }

    function addCurrentGiftCertificate($gc_code)
    {
        $errors = array();
        if ($this->isCodeValid($gc_code) && $this->doesCodeExist($gc_code))
        {
            $gc = new GiftCertificate($gc_code);
            if ($gc->isError() == true)
            {
                $errors = $gc->errors;
            }
            else
            {
                if ($gc->isApplicable() == true)
                {
                    $this->__gc_current_list[$gc_code] = $gc;
                }
                else
                {
                    $errors[] = GC_E_NOT_APPLICABLE;
                }
            }
        }
        else
        {
            $errors[] = GC_E_INVALID_CODE;
        }
        return $errors;
    }

    function getMaxGiftCertificateID()
    {
        $res = execQuery('SELECT_MAX_GC_ID');
        return $res[0]['gc_purchased_order_id'];
    }

    function getCurrentGiftCertificateList()
    {
        return array_keys($this->__gc_current_list);
    }

    function getCurrentGiftCertificateListFull()
    {
        return $this->__gc_current_list;
    }

    function removeCurrentGiftCertificate($gc_code)
    {
        if (isset($this->__gc_current_list[$gc_code]))
        {
            unset($this->__gc_current_list[$gc_code]);
        }
    }

    function clearCurrentGiftCertificateList()
    {
        $this->__gc_current_list = array();
    }

    function loadState()
    {
        if (modApiFunc('Session','is_Set','GC_CURRENT_LIST'))
        {
            $current_gc_list = modApiFunc('Session','get','GC_CURRENT_LIST');
            foreach($current_gc_list as $gc_code)
            {
                $this->addCurrentGiftCertificate($gc_code);
            }
        }
    }

    function saveState()
    {
        $current_gc_list = array_keys($this->__gc_current_list);
        modApiFunc('Session','set','GC_CURRENT_LIST', $current_gc_list);
    }

    function __getRandCode()
    {
        $alpha = 'QWERTYUIOPASDFGHJKLZXCVBNM1234567890';
        $groups = 4;
        $group_len = 4;
        $group_sep = '-';

        $code = '';
        for($i=0; $i<$groups; $i++)
        {
            for($j=0; $j<$group_len; $j++)
            {
                $code .= $alpha{rand(0, strlen($alpha)-1)};
            }
            $code .= $group_sep;
        }
        return substr($code, 0, -1); // remove last separator
    }

    function getGiftCertificate($gc_code)
    {
       $filter = new GiftCertificateFilter();
       $filter->use_paginator = false;
       $filter->code = $gc_code;

       $gc_list = new GiftCertificateList($filter);

       return @$gc_list->gc_list[0];
    }

    function getGiftCertificatesForOrderId($order_id)
    {
       $filter = new GiftCertificateFilter();
       $filter->use_paginator = false;
       $filter->purchased_order_id = $order_id;

       $gc_list = new GiftCertificateList($filter);

       return $gc_list->gc_list;
    }

    function addPurchasedCertificate($gc_data)
    {
        $cr = new GiftCertificateCreator();

        $cr->initByMap($gc_data);

        $cr->save();

        return $cr;
    }

    function addGCFromOrderProducts($order_products,$order_id)
    {
        $certs = array();

        if (!is_array($order_products) || empty($order_products))
        {
            return null;
        }
        else
        {
            foreach ($order_products as $id=>$product)
            {
                if ($product['type'] === GC_PRODUCT_TYPE_ID)
                {
                    $gc_data = array();

                    foreach ($product['custom_attributes'] as $i => $attr)
                    {
                        $gc_data[$attr['tag']] = $attr['value'];
                    }

                    $default_currency_code = modApiFunc("Localization", "getCurrencyCodeById", modApiFunc("Localization", "getMainStoreCurrency"));
                    $local_currency_code = modApiFunc("Localization", "getCurrencyCodeById", modApiFunc("Localization", "getLocalDisplayCurrency"));

                    $gc_data['gc_amount'] = modApiFunc("Currency_Converter", "convert", $product['SalePrice'], $local_currency_code, $default_currency_code);

                    $gc_data['gc_purchased_order_id'] = $order_id;

                    $certs[] = $this->addPurchasedCertificate($gc_data);

                    # remove gc product from database
                    modApiFunc("Catalog","removeGCProduct",$product['storeProductID']);
                }
            }

            return $certs;
        }
    }

    function createGC($order_id)
    {
        loadClass('GiftCertificateLogger');

        $order = modApiFunc("Checkout","getOrderInfo",$order_id,modApiFunc("Localization", "whichCurrencyToDisplayOrderIn", $order_id));
        $gcs = $this->addGCFromOrderProducts($order['Products'],$order_id);

        foreach ($gcs as $i=>$gc) // analyzing GC results
        {
            if (!empty($gc->errors)) // error
            {
                GiftCertificateLogger::failed($gc, $order_id);
            }
            else //successful operation
            {
                GiftCertificateLogger::purchased($gc, $order_id);
            }
        }
    }

    function updateGCstatus($order_id,$status)
    {
        loadClass('GiftCertificateUpdater');

        $order = modApiFunc("Checkout","getOrderInfo",$order_id,modApiFunc("Localization", "whichCurrencyToDisplayOrderIn", $order_id));
        $gcs = $this->getGiftCertificatesForOrderId($order_id);

        if (is_array($gcs) && !empty($gcs))
        {
            foreach ($gcs as $i=>$_gc)
            {
                $gc_updated = new GiftCertificateUpdater($_gc['gc_code']);
                $gc_updated->status = $status;

                if (!empty($gc_updated->errors)) // error
                {
                    #GiftCertificateLogger::failed($gc_updated, $order_id);
                }
                else
                {
                    $gc_updated->save();
                    if ($gc_updated->purchased_order_id !== null) // GC was purchased by customer
                        modApiFunc('EventsManager','throwEvent','GiftCertificatePurchased',$gc_updated);
                    else // GC was created by Admin
                        modApiFunc('EventsManager','throwEvent','GiftCertificateCreated',$gc_updated);
                }
            }
        }
    }

    function onOrdersUpdated($oids)
    {
        if (is_array($oids))
        {
            # Payment statuses
            if (is_array($oids['payment_status']))
            {
                foreach ($oids['payment_status'] as $id => $o)
    	        {
        	    $main_store_currency_id = modApiFunc("Localization", "getMainStoreCurrency");
                    $order_data = modApiFunc("Checkout","getOrderInfo", $id, $main_store_currency_id);

                    $payment_status = $o['new_status'];
                    if ($payment_status == 2) // order is fully paid
                    {
                        $this->updateGCstatus($id,GC_STATUS_ACTIVE);
                        //modApiFunc('EventsManager','throwEvent','GiftCertificatePurchased',$id);
                    }
                    else // order is NOT fully paid
                    {
                        $this->updateGCstatus($id,GC_STATUS_PENDING);
                    }
        	}
            }

        }

    }

    function insertOrderGC($order_id, $gc_list)
    {
        global $application;
        $tables = $this->getTables();

        $tr = $tables['order_gc']['columns'];

        if (is_array($gc_list))
        foreach ($gc_list as $code=>$gc_obj)
        {
            $query = new DB_Insert('order_gc');
            $query->addInsertValue($order_id, $tr['order_id']);
            $query->addInsertValue($gc_obj->id, $tr['gc_id']);
            $query->addInsertValue($gc_obj->code, $tr['gc_code']);
            $result = $application->db->getDB_Result($query);
        }
    }


    function getOrderGCs($order_id = NULL, $gc_code = NULL)
    {
        global $application;
        $tables = $this->getTables();
        $tr = $tables['order_gc']['columns'];

        $result_array = array();
        $query = new DB_Select();
        $query->addSelectField($tr["order_id"], "order_id");
        $query->addSelectField($tr["gc_id"], "gc_id");
        $query->addSelectField($tr["gc_code"], "gc_code");

        if($order_id !== NULL)
        {
            #$query->WhereAnd();
            $query->WhereValue($tr["order_id"], DB_EQ, $order_id);
        }
        if($gc_code !== NULL)
        {
            if($order_id !== NULL)
                $query->WhereAND();

            $query->WhereValue($tr["gc_code"], DB_EQ, $gc_code);
        }
        $result_rows = $application->db->getDB_Result($query);

        return $result_rows;
    }

    function install()
    {
        global $application;

        $query = new DB_Table_Create(GiftCertificateApi::getTables());

        modApiFunc('EventsManager','addEventHandler','OrdersWereUpdated','GiftCertificateApi','onOrdersUpdated');

        #Advanced Settings (Shipping Options for GC products)

        $group_info = array('GROUP_NAME'        => 'GIFT_CERTIFICATES',
                            'GROUP_DESCRIPTION' => array(   'NAME'          => array('GCT', 'ADV_CFG_GC_GROUP_NAME'),
                                                            'DESCRIPTION'   => array('GCT', 'ADV_CFG_GC_GROUP_DESCR')),
                            'GROUP_VISIBILITY'    => 'SHOW'); /*@     add to constants */

        modApiFunc('Settings','createGroup', $group_info);

        $param_info = array(
                         'GROUP_NAME'        => $group_info['GROUP_NAME'],
                         'PARAM_NAME'        => 'GC_NEED_SHIPPING',
                         'PARAM_DESCRIPTION' => array( 'NAME'        => array('GCT', 'ADV_CFG_NEED_SHIPPING_NAME'),
                                                       'DESCRIPTION' => array('GCT', 'ADV_CFG_NEED_SHIPPING_DESCR') ),
                         'PARAM_TYPE'          => PARAM_TYPE_LIST,
                         'PARAM_VALUE_LIST'    => array(
                                 array(  'VALUE' => '2',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('GCT', 'ADV_CFG_NO'),
                                                                       'DESCRIPTION' => array('GCT', 'ADV_CFG_NO') ),
                                       ),
                                 array(  'VALUE' => '1',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('GCT', 'ADV_CFG_YES'),
                                                                       'DESCRIPTION' => array('GCT', 'ADV_CFG_YES') ),
                                       )),
                         'PARAM_CURRENT_VALUE' => '2',
                         'PARAM_DEFAULT_VALUE' => '2',
        );
        modApiFunc('Settings','createParam', $param_info);

        $param_info = array(
                         'GROUP_NAME'        => $group_info['GROUP_NAME'],
                         'PARAM_NAME'        => 'GC_FREE_SHIPPING',
                         'PARAM_DESCRIPTION' => array( 'NAME'        => array('GCT', 'ADV_CFG_FREE_SHIPPING_NAME'),
                                                       'DESCRIPTION' => array('GCT', 'ADV_CFG_FREE_SHIPPING_DESCR') ),
                         'PARAM_TYPE'          => PARAM_TYPE_LIST,
                         'PARAM_VALUE_LIST'    => array(
                                 array(  'VALUE' => '2',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('GCT', 'ADV_CFG_NO'),
                                                                       'DESCRIPTION' => array('GCT', 'ADV_CFG_NO') ),
                                       ),
                                 array(  'VALUE' => '1',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('GCT', 'ADV_CFG_YES'),
                                                                       'DESCRIPTION' => array('GCT', 'ADV_CFG_YES') ),
                                       )),
                         'PARAM_CURRENT_VALUE' => '2',
                         'PARAM_DEFAULT_VALUE' => '2',
        );

        modApiFunc('Settings','createParam', $param_info);

        $param_info = array(
                         'GROUP_NAME'        => $group_info['GROUP_NAME'],
                         'PARAM_NAME'        => 'GC_PER_ITEM_SHIPPING_COST',
                         'PARAM_DESCRIPTION' => array( 'NAME'        => array('GCT', 'ADV_CFG_PER_ITEM_SHIPPING_COST_NAME'),
                                                       'DESCRIPTION' => array('GCT', 'ADV_CFG_PER_ITEM_SHIPPING_COST_DESCR') ),
                         'PARAM_TYPE'          => PARAM_TYPE_FLOAT,
                         'PARAM_VALUE_LIST'    => array(
                                       ),
                         'PARAM_CURRENT_VALUE' => '0.0',
                         'PARAM_DEFAULT_VALUE' => '0.0',
        );

        modApiFunc('Settings','createParam', $param_info);

        $param_info = array(
                         'GROUP_NAME'        => $group_info['GROUP_NAME'],
                         'PARAM_NAME'        => 'GC_PER_ITEM_HANDLING_COST',
                         'PARAM_DESCRIPTION' => array( 'NAME'        => array('GCT', 'ADV_CFG_PER_ITEM_HANDLING_COST_NAME'),
                                                       'DESCRIPTION' => array('GCT', 'ADV_CFG_PER_ITEM_HANDLING_COST_DESCR') ),
                         'PARAM_TYPE'          => PARAM_TYPE_FLOAT,
                         'PARAM_VALUE_LIST'    => array(
                                       ),
                         'PARAM_CURRENT_VALUE' => '0.0',
                         'PARAM_DEFAULT_VALUE' => '0.0',
        );

        modApiFunc('Settings','createParam', $param_info);

        $param_info = array(
                         'GROUP_NAME'        => $group_info['GROUP_NAME'],
                         'PARAM_NAME'        => 'GC_WEIGHT',
                         'PARAM_DESCRIPTION' => array( 'NAME'        => array('GCT', 'ADV_CFG_WEIGHT_NAME'),
                                                       'DESCRIPTION' => array('GCT', 'ADV_CFG_WEIGHT_DESCR') ),
                         'PARAM_TYPE'          => PARAM_TYPE_FLOAT,
                         'PARAM_VALUE_LIST'    => array(
                                       ),
                         'PARAM_CURRENT_VALUE' => '0.0',
                         'PARAM_DEFAULT_VALUE' => '0.0',
        );

        modApiFunc('Settings','createParam', $param_info);
    }

    function uninstall()
    {
        $query = new DB_Table_Delete(GiftCertificateApi::getTables());
    }

    function getTables()
    {
        static $tables;

        if (is_array($tables))
        {
            return $tables;
        };

        $table = 'gc_list';
        $tables[$table] = array(
            'columns'   => array(
                'gc_id'             => $table.'.gc_id'
               ,'gc_code'           => $table.'.gc_code'
               ,'gc_to'             => $table.'.gc_to'
               ,'gc_from'           => $table.'.gc_from'
               ,'gc_message'        => $table.'.gc_message'
               ,'gc_amount'         => $table.'.gc_amount'
               ,'gc_remainder'      => $table.'.gc_remainder'
               ,'gc_sendtype'       => $table.'.gc_sendtype'
               ,'gc_date_created'   => $table.'.gc_date_created'
               ,'gc_status'         => $table.'.gc_status'
               ,'gc_fname'      => $table.'.gc_fname'
               ,'gc_lname'      => $table.'.gc_lname'
               ,'gc_email'      => $table.'.gc_email'
               ,'gc_address'    => $table.'.gc_address'
               ,'gc_city'       => $table.'.gc_city'
               ,'gc_state_id'   => $table.'.gc_state_id'
               ,'gc_country_id' => $table.'.gc_country_id'
               ,'gc_zip'        => $table.'.gc_zip'
               ,'gc_phone'      => $table.'.gc_phone'
               ,'gc_purchased_order_id'   => $table.'.gc_purchased_order_id'
             )
           ,'types'     => array(
                'gc_id'       => DBQUERY_FIELD_TYPE_INT.' not null auto_increment'
               ,'gc_code'     => DBQUERY_FIELD_TYPE_CHAR255
               ,'gc_to'       => DBQUERY_FIELD_TYPE_CHAR255
               ,'gc_from'     => DBQUERY_FIELD_TYPE_CHAR255
               ,'gc_message'  => DBQUERY_FIELD_TYPE_CHAR255
               ,'gc_amount'   => DBQUERY_FIELD_TYPE_FLOAT
               ,'gc_remainder'=> DBQUERY_FIELD_TYPE_FLOAT
               ,'gc_sendtype'     => DBQUERY_FIELD_TYPE_CHAR1
               ,'gc_date_created' => DBQUERY_FIELD_TYPE_INT
               ,'gc_status'     => DBQUERY_FIELD_TYPE_CHAR1
               ,'gc_fname'      => DBQUERY_FIELD_TYPE_CHAR255
               ,'gc_lname'      => DBQUERY_FIELD_TYPE_CHAR255
               ,'gc_email'      => DBQUERY_FIELD_TYPE_CHAR255
               ,'gc_address'    => DBQUERY_FIELD_TYPE_CHAR255
               ,'gc_city'       => DBQUERY_FIELD_TYPE_CHAR255
               ,'gc_state_id'   => DBQUERY_FIELD_TYPE_INT
               ,'gc_country_id' => DBQUERY_FIELD_TYPE_INT
               ,'gc_zip'        => DBQUERY_FIELD_TYPE_CHAR255
               ,'gc_phone'      => DBQUERY_FIELD_TYPE_CHAR255
               ,'gc_purchased_order_id'=> DBQUERY_FIELD_TYPE_INT
             )
           ,'primary'   => array(
                'gc_id'
             )
           ,'indexes'   => array(
               'IDX_code' =>  'gc_code'
             )
        );

        $table = 'order_gc';
        $tables[$table] = array(
            'columns'   => array(
                'gc_ord_id'         => $table.'.gc_ord_id',
                'gc_id'             => $table.'.gc_id',
                'order_id'          => $table.'.order_id',
                'gc_code'           => $table.'.gc_code',
             )
           ,'types'     => array(
                'gc_ord_id' => DBQUERY_FIELD_TYPE_INT.' not null auto_increment'
               ,'gc_id'     => DBQUERY_FIELD_TYPE_INT.' not null default 0'
               ,'order_id'  => DBQUERY_FIELD_TYPE_INT.' not null default 0'
               ,'gc_code'   => DBQUERY_FIELD_TYPE_CHAR255
             )
           ,'primary'   => array(
                'gc_ord_id'
             )
           ,'indexes'   => array(
               'IDX_gc_id' =>  'gc_id',
               'IDX_order_id' =>  'order_id',
               'IDX_gc_code' =>  'gc_code',
             )
        );


        global $application;
        return $application->addTablePrefix($tables);
    }

    function DeleteOrders($ordersId)
    {
        global $application;

        $tables = $this->getTables();
        $opc = $tables['order_gc']['columns'];
        $DB_IN_string = "('".implode("', '", $ordersId)."')";

        $query = new DB_Delete('order_gc');
        $query->WhereField($opc['gc_ord_id'], DB_IN, $DB_IN_string);
        $application->db->getDB_Result($query);
    }



    var $__gc_current_list = array();
}

?>