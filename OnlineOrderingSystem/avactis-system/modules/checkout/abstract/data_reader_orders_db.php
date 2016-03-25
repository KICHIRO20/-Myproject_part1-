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
 * @package Checkout
 * @author Egor V. Derevyankin
 *
 */

loadClass('DataReaderDefault');

class DataReaderOrdersDB extends DataReaderDefault
{
    function DataReaderOrdersDB()
    {
    }

    function initWork($settings)
    {
        $this->clearWork();
        $this->_settings=$settings;
        $this->_orders_ids=explode('|',$settings['orders_ids']);
        sort($this->_orders_ids);

        $this->_process_info['status']='INITED';
        $this->_process_info['items_count'] = count($this->_orders_ids);
        $this->_process_info['items_processing']=0;
    }

    function doWork()
    {
        $this->_process_info['items_count'] = count($this->_orders_ids);
        $this->_process_info['status']='HAVE_MORE_DATA';
        if($this->_sent_count < count($this->_orders_ids))
        {
            $order_id = $this->_sent_count++;
            $data = modApiFunc("Checkout", "getOrderInfo", $this->_orders_ids[$order_id], modApiFunc("Localization", "getMainStoreCurrency", $this->_orders_ids[$order_id]));
            $data['OrderCurrencyCode'] = modApiFunc("Localization", 'getCurrencyCodeById', modApiFunc("Localization", "getMainStoreCurrency", $this->_orders_ids[$order_id]));
            $this->_process_info['items_processing']=$this->_sent_count;
            if($this->_sent_count==count($this->_orders_ids))
                $this->_process_info['status']='NO_MORE_DATA';
            return $data;
        }
        else
        {
            $this->_process_info['items_processing']=$this->_sent_count;
            $this->_process_info['status']='NO_MORE_DATA';
            return null;
        };
    }

    function finishWork()
    {
        $this->clearWork();
    }

    function loadWork()
    {
        if(modApiFunc('Session','is_set','DataReaderOrdersSettings'))
            $this->_settings = modApiFunc('Session','get','DataReaderOrdersSettings');
        if(modApiFunc('Session','is_set','DataReaderOrdersOrdersIDs'))
            $this->_orders_ids = modApiFunc('Session','get','DataReaderOrdersOrdersIDs');
        if(modApiFunc('Session','is_set','DataReaderOrdersSentCount'))
            $this->_sent_count = modApiFunc('Session','get','DataReaderOrdersSentCount');
    }

    function clearWork()
    {
        modApiFunc('Session','un_set','DataReaderOrdersSettings');
        modApiFunc('Session','un_set','DataReaderOrdersOrdersIDs');
        modApiFunc('Session','un_set','DataReaderOrdersSentCount');
        $this->_orders_ids = null;
        $this->_sent_count = 0;
        $this->_settings = null;
    }

    function saveWork()
    {
        modApiFunc('Session','set','DataReaderOrdersSettings',$this->_settings);
        modApiFunc('Session','set','DataReaderOrdersOrdersIDs',$this->_orders_ids);
        modApiFunc('Session','set','DataReaderOrdersSentCount',$this->_sent_count);
    }

    var $_orders_ids;
    var $_sent_count;
    var $_settings;
}

?>