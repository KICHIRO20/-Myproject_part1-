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

loadCoreFile('cstoredatetime.php');

class EventInfoBase {
    protected $_name;
    protected $_time;
    protected $_fields=array();

    function __construct() {
        $object_class_name = get_class($this);
        $this->_name = strtolower(str_replace('EventInfo_', '', $object_class_name));
        $t = new CStoreDatetime();
        $this->_time = $t->getTimestamp();
    }

    function getName() {
        return $this->_name;
    }

    function setName($name) {
        $this->_name = (string) $name;
        return $this;
    }

    function getTime() {
        return $this->_time;
    }

    function setTime($time) {
        $this->_time = $time;
        return $this;
    }

    function getFields($key=null) {
        if ($key == null) {
            return $this->_fields;
        }
        elseif (isset($this->_fields[$key])) {
            return $this->_fields[$key];
        }
        return null;
    }

    function setFields($map) {
        $this->_fields = $map;
        return $this;
    }

    function addField($key, $value) {
        $this->_fields[$key] = $value;
        return $this;
    }

    function getInfo() {
        return array(
            'name' => $this->name,
            'time' => $this->time,
            'fields' => $this->fields
        );
    }
}

class EventInfo_OrderCreated extends EventInfoBase
{
    function onEvent($oid)
    {
        $order_currency = modApiFunc("Localization", "whichCurrencyToDisplayOrderIn", $oid);
        $order = modApiFunc('Checkout','getOrderInfo',$oid, $order_currency);
        $keys = array('ID', 'StatusId', 'Status', 'PaymentStatusId', 'PaymentStatus', 'PaymentMethod', 'ShippingMethod');
        foreach ($keys as $k)
        {
            $this->addField($k, $order[$k]);
        }

        $price  = array('OrderTotalToPay','OrderTotal','TotalShippingAndHandlingCost','OrderTaxTotal');
        foreach ($price as $k)
        {
            $this->addField($k, $order['Price'][$k]);
        }

        $currency_iso = modApiFunc('Localization', 'getCurrencyCodeById', $order_currency);
        $this->addField('CustomerId', $order['PersonId']); // ???????????????????????
        $this->addField('OrderCurrency', $currency_iso);
        $this->addField('ProductNumber', count($order['Products']));
    }
}

class EventInfo_OrderStatusUpdated extends EventInfoBase
{

    function onEvent($info)
    {
    	$ost = modApiFunc('Checkout', 'getOrderStatusList');
    	$pst = modApiFunc('Checkout', 'getOrderPaymentStatusList');

    	$diff = array();
    	if (isset($info['order_status'])) {
    		foreach ($info['order_status'] as $id => $statuses) {
                if(empty($statuses)) continue;
	    		$diff[$id]['StatusID'] = array($statuses['old_status'], $statuses['new_status']);
	    		$diff[$id]['Status'] = array($ost[$statuses['old_status']]['name'], $ost[$statuses['new_status']]['name']);
	    	}
    	}

    	if (isset($info['payment_status'])) {
    		foreach ($info['payment_status'] as $id => $statuses) {
                if(empty($statuses)) continue;
	    		$diff[$id]['PaymentStatusID'] = array($statuses['old_status'], $statuses['new_status']);
	    		$diff[$id]['PaymentStatus'] = array($pst[$statuses['old_status']]['name'], $pst[$statuses['new_status']]['name']);
	    	}
    	}

    	foreach ($diff as $oid=>$data) {
    		$this->addField($oid, $data);
    	}
    }
}

class EventInfo_OrdersWillBeDeleted extends EventInfoBase
{

    function onEvent($info)
    {
    	$this->setName('ordersdeleted');
    	$this->addField('id', $info);
    }
}

class EventInfo_CustomerRegistered extends EventInfoBase
{

    function onEvent($info)
    {
    	if (isset($info['info'])) {
    		foreach ($info['info'] as $field=>$value) {
    			$this->addField($field, $value);
    		}
    	}
    }
}