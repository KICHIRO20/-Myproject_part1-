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

loadClass('DataFilterDefault');

class DataFilterCustomersDBCSV extends DataFilterDefault
{
    function DataFilterCustomersDBCSV()
    {
        loadClass('CCustomerInfo');
    }

    function initWork($settings)
    {
        $this->clearWork();

        $this->_settings['attrs'] = $settings['headers'];

        $this->_process_info['status']='INITED';
    }

    function doWork($data)
    {
        global $application;

        if ($data === null)
        {
            return null;
        };

        $filtered = array();

        $obj = new CCustomerInfo($data['customer_account']);

        foreach($this->_settings['attrs'] as $tag)
        {
            $output = '';

            preg_match('/^customer(.+)/i',$tag,$m1);
            if(preg_match('/^(billing|shipping|orders)(.+)/i',$m1[1],$m2))
            {
                $group = $m2[1];
                $attr = $m2[2];
            }
            else
            {
                $attr = $m1[1];
                if(in_array(_ml_strtolower($attr),array('id','status')))
                {
                    $group = 'base';
                }
                else
                {
                    $group = 'Customer';
                };
            };

            if(_ml_strtolower($attr) == 'accountname')
            {
                $output = $obj->getDisplayAccountName();
                $filtered[$tag] = $output;
                continue;
            };

            if(_ml_strtolower($group) != 'orders')
            {
                $attr_value = $obj->getPersonInfo($attr,$group);

                switch(_ml_strtolower($attr))
                {
                    case 'status':
                        $MR = &$application->getInstance('MessageResources','customer-account-messages','AdminZone');
                        $output = $MR->getMessage('CUSTOMER_STATUS_'.$attr_value);
                        break;
                    case 'country':
                        $output = modApiFunc('Location','getCountry',$attr_value);
                        break;
                    case 'state':
                        if(modApiFunc('Location','getStateCode',$attr_value) != '')
                            $output = modApiFunc('Location','getState',$attr_value);
                        else
                            $output = $attr_value;
                        break;
                    default:
                        $output = $attr_value;
                        break;
                };
            }
            else
            {
                $filter = array(
                    'type' => 'quick'
                   ,'order_status' => ORDER_STATUS_ALL
                );

                $obj->setOrdersHistoryFilter($filter);

                switch(_ml_strtolower($attr))
                {
                    case 'quantity':
                        $output = $obj->getOrdersCount();
                        break;
                    case 'totalamount':
                        $output = number_format($obj->getOrdersAmount(), 2, '.', '');
                        break;
                    case 'totalfullypaidamount':
                        $output = number_format($obj->getOrdersFullyPaidAmount(), 2, '.', '');
                        break;
                };
            };

            $filtered[$tag] = $output;
        };
        unset($obj);

        return $filtered;
    }

    function loadWork()
    {
        foreach($this->_own_vars as $var_name)
        {
            $this->$var_name = modApiFunc('Session','is_set',__CLASS__.$var_name) ?  modApiFunc('Session','get',__CLASS__.$var_name) : null;
        };
    }

    function clearWork()
    {
        foreach($this->_own_vars as $var_name)
        {
            modApiFunc('Session','un_set',__CLASS__.$var_name);
            $this->$var_name = null;
        };
    }

    function saveWork()
    {
        foreach($this->_own_vars as $var_name)
        {
            if($this->$var_name !== null)
            {
                modApiFunc('Session','set',__CLASS__.$var_name,$this->$var_name);
            }
            elseif(modApiFunc('Session','is_set',__CLASS__.$var_name))
            {
                modApiFunc('Session','un_set',__CLASS__.$var_name);
            };
        };
    }

    var $_settings;
    var $_own_vars = array('_settings');
};

?>