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

class CCustomerInfo
{
    function CCustomerInfo($account='')
    {
        if(!modApiFunc('Customer_Account','doesAccountExists',$account))
        {
            $this->account = null;
            $this->base_info = null;
            $this->full_info = null;
            $this->affiliate_id = null;
        }
        else
        {
            $this->account = $account;
            $this->__loadBaseInfo();
        };

        $this->orders_filter = null;
        $this->orders_ids = null;
        $this->orders_info = array();
        $this->orders_summary = null;
        $this->person_info_attrs_type = PERSON_INFO_GROUP_ATTR_ALL;
        $this->ca_attrs_list = null;
    }

    function changeAccountName($new_account_name)
    {
        $this->__changeAccount($this->account, $new_account_name);
        modApiFunc('Session','set','SignedCustomer', $new_account_name);

        $this->account = $new_account_name;
        $this->base_info = null;
        $this->full_info = null;
        $this->__loadBaseInfo();

        $this->orders_filter = null;
        $this->orders_ids = null;
        $this->orders_info = array();
        $this->orders_summary = null;
        $this->person_info_attrs_type = PERSON_INFO_GROUP_ATTR_ALL;
        $this->ca_attrs_list = null;
    }

    function getDisplayAccountName()
    {
        return $this->getDisplayAccountNameExt($this->account, $this->getPersonInfo('Status'));
    }

    /* static */ function getDisplayAccountNameExt($account_name, $customer_status)
    {
        if($account_name == null)
            return '';

        if($customer_status == 'B')
        {
            $account_name = preg_replace('/'.PSEUDO_CUSTOMER_SUFFIX.'$/i','',$account_name);
            if(preg_match('/^'.PSEUDO_NA_CUSTOMER_PERFIX.'(\d)+/i',$account_name))
            {
                $account_name = 'N/A';
            };
        };

        return $account_name;
    }

    function changePassword($new_password)
    {
        if($this->account == null)
            return false;

        global $application;
        $ca_tables = modApiStaticFunc('Customer_Account','getTables');
        $customers_table = $ca_tables['ca_customers']['columns'];

        $query = new DB_Update('ca_customers');
        $query->addUpdateValue($customers_table['customer_password'], md5($new_password));
        $query->WhereValue($customers_table['customer_account'], DB_EQ, $this->account);
        $application->db->PrepareSQL($query);
        return $application->db->DB_Exec();
    }

    function setAffiliateID($affiliate_id)
    {
        if($this->account == null)
            return false;

        $params = array("affiliate_id"=>$affiliate_id, "customer_account"=>$this->account);

        $result = execQuery("UPDATE_CUSTOMER_AFFILIATE_ID", $params);

        return $result;
    }

    function SignIn()
    {
        if($this->account == null) return;

        modApiFunc('Session','set','SignedCustomer',$this->account);
        $this->refreshCheckoutPrerequisites();
    }

    function SignOut()
    {
        if($this->account == null) return;

        if($this->isSigned())
            modApiFunc('Session','un_set','SignedCustomer');
    }

    function isSigned()
    {
        if($this->account == null) return false;

        return (modApiFunc('Session','is_set','SignedCustomer')
            and modApiFunc('Session','get','SignedCustomer') == $this->account);
    }

    function getPersonInfo($attr, $group='base')
    {
        if($this->account == null)
            return null;

        if(_ml_strtolower($attr) == 'account')
        {
            return $this->account;
        };

        if(array_key_exists(_ml_strtolower($group), $this->_groups_names))
            $group = $this->_groups_names[_ml_strtolower($group)];

        if(array_key_exists(_ml_strtolower($attr), $this->_attrs_names))
            $attr = $this->_attrs_names[_ml_strtolower($attr)];

        switch($group)
        {
            case 'base':
                if(array_key_exists($attr, $this->base_info))
                {
                    return $this->base_info[$attr];
                };
                break;
            default:
                if($this->full_info == null)
                {
                    $this->__loadFullInfo();
                };
                if(array_key_exists($group, $this->full_info))
                {
                    if(array_key_exists($attr, $this->full_info[$group]['attrs']))
                    {
                        return $this->full_info[$group]['attrs'][$attr]['attr_value'];
                    };
                };
                break;
        };

        return null;
    }

    function getPersonInfoGroupsNames()
    {
        if($this->account == null)
            return array();

        if($this->full_info == null)
        {
            $this->__loadFullInfo();
        };

        return array_keys($this->full_info);
    }

    function getPersonInfoGroupAttrsNames($group_name)
    {
        if($this->account == null)
            return array();

        if($this->full_info == null)
        {
            $this->__loadFullInfo();
        };

        if(array_key_exists($group_name, $this->full_info))
        {
            return array_keys($this->full_info[$group_name]['attrs']);
        };

        return array();
    }

    function getPersonInfoGroupInfoByName($group_name)
    {
        if($this->account == null)
            return array();

        if($this->full_info == null)
        {
            $this->__loadFullInfo();
        };

        if(array_key_exists($group_name, $this->full_info))
        {
            return array(
                'id' => $this->full_info[$group_name]['group_id']
               ,'lang_code' => $this->full_info[$group_name]['lang_code']
            );
        };

        return array();
    }

    function getPersonInfoAttrInfoByName($attr_name, $group_name)
    {
        if($this->account == null)
            return array();

        if($this->full_info == null)
        {
            $this->__loadFullInfo();
        };

        if(array_key_exists($group_name, $this->full_info))
        {
            if(array_key_exists($attr_name, $this->full_info[$group_name]['attrs']))
            {
                return $this->full_info[$group_name]['attrs'][$attr_name];
            };
        };

        return array();
    }

    function setPersonInfo($attrs)
    {
        if($this->account == null)
            return false;

        $__updatePersonInfoAttrInDB_data = array();
        foreach($attrs as $attr_info)
        {
            list($attr, $value, $group) = $attr_info;
            switch($group)
            {
                 case 'base':
                    foreach($attrs as $attr_info)
                    {
                        list($attr, $value, $group) = $attr_info;
                        if($attr != 'Status')
                        {
                            //return false;
                        }
                        else
                        {
                            modApiFunc('Customer_Account','setAccountStatus', $this->account, $value);
                        }
                    }
                    break;
                default:
                    if($this->full_info == null)
                    {
                        $this->__loadFullInfo();
                    };
                    if(array_key_exists($group, $this->full_info))
                    {
                        if(array_key_exists($attr, $this->full_info[$group]['attrs']))
                        {
                            $__updatePersonInfoAttrInDB_data[] = array($this->full_info[$group]['group_id'], $this->full_info[$group]['attrs'][$attr]['attr_id'], $value);
                            $this->full_info[$group]['attrs'][$attr]['attr_value'] = $value;
                        };
                    };
            }
        };

        if(sizeof($__updatePersonInfoAttrInDB_data) > 0)
        {
            $this->__updatePersonInfoAttrInDB($__updatePersonInfoAttrInDB_data);
        }
        return;// false;
    }

    function setPersonInfoAttrsType($type = PERSON_INFO_GROUP_ATTR_ALL)
    {
        if(in_array($type,array(
            PERSON_INFO_GROUP_ATTR_ALL
           ,PERSON_INFO_GROUP_ATTR_VISIBLE
           ,PERSON_INFO_GROUP_ATTR_HIDDEN
          )))
        {
            if($this->person_info_attrs_type != $type)
            {
                $this->full_info = null;
            };

            $this->person_info_attrs_type = $type;
        };
    }

    function refreshCheckoutPrerequisites()
    {
        if($this->account == null)
            return;

        $groups = $this->getPersonInfoGroupsNames();

        if(empty($groups))
            return;

        $new_validation_results = modApiFunc('Checkout','getPrerequisitesValidationResults');

        foreach($groups as $group_name)
        {
            if($group_name == 'Customer')
                continue;

            $co_gn = _ml_strtolower($group_name).'Info';
            $pvr = modApiFunc("Checkout", "getPrerequisiteValidationResults", $co_gn);

            foreach($pvr['validatedData'] as $tag_name => $tag_info)
            {
                if($tag_name == 'Statetext')
                {
                    $attr_name = 'state';
                }
                else
                {
                    $attr_name = modApiFunc('Customer_Account','getPersonInfoAttrNameByCOAttrID',$tag_info['id']);
                };

                $attr_value = $tag_info['value'];

                $normal_attr_name = modApiFunc('Customer_Account','getPersionInfoAttrNameByLowcaseName', $attr_name);
                $attr_value = $this->getPersonInfo($normal_attr_name, $group_name);

                switch($tag_name)
                {
                    case 'Statemenu':
                        $attr_value = intval($attr_value) == $attr_value ? $attr_value : 0;
                        break;
                    case 'Statetext':
                        $attr_value = is_string($attr_value) ? $attr_value : '';
                        break;
                };

                $pvr['validatedData'][$tag_name]['value'] = $attr_value;
            };

            $new_validation_results[$co_gn] = $pvr;
        };

        modApiFunc('Checkout','setPrerequisitesValidationResults',$new_validation_results);
    }

    function loadPersonInfoFromCheckout()
    {
        global $application;

        if($this->account == null)
            return;

        $groups = $this->getPersonInfoGroupsNames();

        if(empty($groups))
            return;

        $setPersonInfo_data = array();
        foreach($groups as $group_name)
        {
            if($group_name == 'Customer')
                continue;

            $co_gn = _ml_strtolower($group_name).'Info';
            $pvr = modApiFunc("Checkout", "getPrerequisiteValidationResults", $co_gn);

            $customer_country = null;
            $customer_state_id = null;
            $customer_state_text = null;

            $validator = &$application->getInstance('CAValidator');

            foreach($pvr['validatedData'] as $tag_name => $tag_info)
            {
                switch($tag_name)
                {
                    case 'Country':
                        $customer_country = $tag_info['value'];
                        break;
                    case 'Statemenu':
                        $customer_state_id = $tag_info['value'];
                        break;
                    case 'Statetext':
                        $customer_state_text = $tag_info['value'];
                        break;
                };
                if(in_array($tag_name,array('Country','Statemenu','Statetext')))
                {
                    continue;
                };

                $attr_name = modApiFunc('Customer_Account','getPersonInfoAttrNameByCOAttrID',$tag_info['id']);

                $attr_value = $tag_info['value'];
                $normal_attr_name = modApiFunc('Customer_Account','getPersionInfoAttrNameByLowcaseName', $attr_name);
                $setPersonInfo_data[] = array($normal_attr_name, $attr_value, $group_name);
            };

            if($customer_country != null)
            {
                $setPersonInfo_data[] = array('Country', $customer_country, $group_name);
                if($customer_state_id != null or $customer_state_text != null)
                {
                    $tmp_arr = array(
                        'country_id' => $customer_country
                       ,'state_id' => $customer_state_id
                       ,'state_text' =>  $customer_state_text
                     );
                    if($validator->isValid('country_state', $tmp_arr))
                    {
                        $setPersonInfo_data[] = array('State', $tmp_arr['state_id'], $group_name);
                    };
                };
            };
        };
        if(sizeof($setPersonInfo_data > 0))
        {
            $this->setPersonInfo($setPersonInfo_data);
        }

        return;
    }

    /**
     * @param array $filter
     */
    function setOrdersHistoryFilter($filter)
    {
        //: check filter;

        if($this->orders_filter != $filter)
        {
            $this->orders_filter = $filter;
            $this->search_completed = false;
            $this->orders_summary = null;
        };
    }

    function getOrdersCount()
    {
        return count($this->getOrdersIDs());
    }

    function getOrdersIDs()
    {
        if($this->account == null or $this->orders_filter == null)
        {
            return array();
        };

        if(!$this->search_completed)
        {
            $this->__searchOrders();
        };

        return $this->orders_ids;
    }

    function getBaseOrderInfo($order_id)
    {
        if($this->account == null) return array();

        $order_id = intval($order_id);

        if(isset($this->orders_info[$order_id]['base']))
        {
            return $this->orders_info[$order_id]['base'];
        };

        global $application;
        $co_tables = modApiStaticFunc('Checkout','getTables');
        $orders_table = $co_tables['orders']['columns'];
        $order_prices_table = $co_tables['order_prices']['columns'];

        $currency_code = modApiFunc("Localization", "getCurrencyCodeById", modApiFunc("Localization", "whichCurrencyToDisplayOrderIn", $order_id));
        $query = new DB_Select();
        $query->addSelectTable('orders');
        $query->addSelectField('*');
        $query->WhereValue($orders_table['id'], DB_EQ, $order_id);
        $query->WhereAnd();
        $query->WhereValue($order_prices_table['currency_code'], DB_EQ, $currency_code);
        $query->addLeftJoin('order_prices', $orders_table['id'], DB_EQ, $order_prices_table['order_id']);



        $res = $application->db->getDB_Result($query);

        if(count($res) != 1)
        {
            return array();
        };

        $this->orders_info[$order_id]['base'] = $res[0];

        return $this->orders_info[$order_id]['base'];
    }

    function getFullOrderInfo($order_id)
    {
    }

    function getOrdersMinDate()
    {
        return $this->__getOrdersSummaryTag('min_date');
    }

    function getOrdersMaxDate()
    {
        return $this->__getOrdersSummaryTag('max_date');
    }

    function getOrdersAmount()
    {
        return $this->__getOrdersSummaryTag('amount');
    }

    function getOrdersFullyPaidAmount()
    {
        return $this->__getOrdersSummaryTag('fully_paid_amount');
    }

    function copyPersonInfo($from_group, $to_group, $exclude=array())
    {
        if($this->full_info == null)
        {
            $this->__loadFullInfo();
        };

        if(!array_key_exists($from_group, $this->full_info) or !array_key_exists($to_group, $this->full_info))
            return;

        $setPersonInfo_data = array();
        foreach($this->full_info[$from_group]['attrs'] as $attr_name => $attr_info)
        {
            if(array_key_exists($attr_name, $this->full_info[$to_group]['attrs']) and !in_array($attr_name,$exclude))
            {
                $setPersonInfo_data[] = array($attr_name, $attr_info['attr_value'], $to_group);
            };
        };
        if(sizeof($setPersonInfo_data > 0))
        {
            $this->setPersonInfo($setPersonInfo_data);
        }
    }

    function getPersonPassword()
    {
        if($this->account === null)
            return;

        global $application;
        $tables = Customer_Account::getTables();
        $customers_table = $tables['ca_customers']['columns'];

        $query = new DB_Select();
        $query->addSelectTable('ca_customers');
        $query->addSelectField('*');
        $query->WhereValue($customers_table['customer_account'], DB_EQ, $this->account);

        $res = $application->db->getDB_Result($query);

        $this->base_info['Password'] = $res[0]['customer_password'];
    }

    function __loadBaseInfo()
    {
        if($this->account === null)
            return;

        global $application;
        $tables = Customer_Account::getTables();
        $customers_table = $tables['ca_customers']['columns'];

        $query = new DB_Select();
        $query->addSelectTable('ca_customers');
        $query->addSelectField('*');
        $query->WhereValue($customers_table['customer_account'], DB_EQ, $this->account);

        $res = $application->db->getDB_Result($query);

        $this->affiliate_id =  $res[0]['affiliate_id'];

        $this->base_info = array(
            'ID'     => $res[0]['customer_id']
           ,'Account'  => $res[0]['customer_account']
           ,'Status' => $res[0]['customer_status']
           ,'Lng'    => $res[0]['customer_lng']
           ,'Notification_Lng' => (($res[0]['notification_lng'])
                                    ? $res[0]['notification_lng']
                                    : $res[0]['customer_lng'])
        );
    }

    function __loadFullInfo()
    {
        if($this->account === null)
            return;

        global $application;


        $res = execQuery('SELECT_CUSTOMER_ACCOUNT_FULL_INFO', array('customer_id' => $this->base_info['ID']));

        $this->full_info = array();
        foreach($res as $attr)
        {
            //                  PersonInfoType -
            if(!isset($this->full_info[$attr['group_name']]))
            {
                $this->full_info[$attr['group_name']] = array
                (
                     'group_id'   => $attr['group_id']
                    ,'lang_code'  => $attr['capig_lang_code']
                    ,'sort_order' => $attr['sort_order']
                    ,'attrs'      => Array()
                );
            }

            $group_name = $attr['group_name'];
            //            "         "                   Checkout
            // 1)             "Customer" -        person info type'
            //    Checkout           ,
            //            "          ".
            // 2)                                           "         ":
            //    PERSON_INFO_GROUP_ATTR_ALL
            //            "         "          Customer_Account                         .
            $attr_vis = $this->person_info_attrs_type;
            $b_skip = false;
            if($attr_vis != PERSON_INFO_GROUP_ATTR_ALL)
            {
                switch($group_name)
                {
                    case 'Customer':
                    {
                        //                    Customer_Account
                        if(($attr_vis == PERSON_INFO_GROUP_ATTR_VISIBLE && $attr['is_visible'] != 'Y') ||
                           ($attr_vis == PERSON_INFO_GROUP_ATTR_HIDDEN && $attr['is_visible'] != 'N'))
                        $b_skip = true;
                        break;
                    }
                    default:
                    {
                        //             Customer_Account   Checkout
                        if(($attr_vis == PERSON_INFO_GROUP_ATTR_VISIBLE && (($attr['is_visible'] != 'Y') || !modApiFunc("Customer_Account", "__isCOAttrVisible", modApiFunc("Customer_Account", "detectCOPITypeID", $group_name), modApiFunc("Customer_Account", "detectCOAttrID", $attr['attr_name'])))) ||
                           ($attr_vis == PERSON_INFO_GROUP_ATTR_HIDDEN  && (($attr['is_visible'] != 'N') || !modApiFunc("Customer_Account", "__isCOAttrHidden", modApiFunc("Customer_Account", "detectCOPITypeID", $group_name), modApiFunc("Customer_Account", "detectCOAttrID", $attr['attr_name'])))))
                        $b_skip = true;
                        break;
                    }
                }
            }
            if($b_skip === false)
            {
                //
                $this->full_info[$attr['group_name']]['attrs'][$attr['attr_name']] = array
                (
                     'attr_id'        => $attr['attr_id']
                    ,'lang_code'      => $attr['capia_lang_code']
                    ,'visible_name'   => $attr['visible_name']
                    ,'is_visible'     => $attr['is_visible']
                    ,'is_required'    => $attr['is_required']
                    ,'attr_value'     => $attr['data_value']
                );
            }
        }
    }

    function __searchOrders()
    {
        global $application;
        $tables = modApiStaticFunc('Checkout','getTables');
        $orders_table = $tables['orders']['columns'];

        $query = new DB_Select();
        $query->addSelectTable('orders');
        $query->addSelectField($orders_table['id'], 'order_id');
        $query->WhereValue($orders_table['person_id'], DB_EQ, $this->base_info['ID']);

        if($this->orders_filter['type'] == 'id')
        {
            $query->WhereAND();
            $query->WhereValue($orders_table['id'], DB_EQ, $this->orders_filter['order_id']);
        };

        $query->SelectGroup($orders_table['id']);
        $query->SelectOrder($orders_table['id'], 'DESC');

        $oids_wo_filter = array();

        $res = $application->db->getDB_Result($query);
        for($i=0;$i<count($res);$i++)
        {
            $oids_wo_filter[] = $res[$i]['order_id'];
        };

        if($this->orders_filter['type'] != 'custom'
            and ($this->orders_filter['order_status'] == ORDER_STATUS_ALL or empty($oids_wo_filter)))
        {
            $this->__setOrdersIDs($oids_wo_filter);
            return;
        };

        $query = new DB_Select();
        $query->addSelectTable('orders');
        $query->addSelectField($orders_table['id'], 'order_id');

        if($this->orders_filter['type'] == 'quick')
        {
            $query->WhereValue($orders_table['status_id'], DB_EQ, $this->orders_filter['order_status']);
        };

        if($this->orders_filter['type'] == 'custom')
        {
            $from_date = implode("-",array(
                $this->orders_filter['year_from']
               ,$this->orders_filter['month_from']
               ,$this->orders_filter['day_from']
            )) . ' 00:00:00';

            $to_date = implode("-",array(
                $this->orders_filter['year_to']
               ,$this->orders_filter['month_to']
               ,$this->orders_filter['day_to']
            )) . ' 23:59:59';

            $query->WhereValue($orders_table['date'], DB_GTE, $from_date);
            $query->WhereAND();
            $query->WhereValue($orders_table['date'], DB_LTE, $to_date);

            if($this->orders_filter['order_status'] != ORDER_STATUS_ALL)
            {
                $query->WhereAND();
                $query->WhereValue($orders_table['status_id'], DB_EQ, $this->orders_filter['order_status']);
            };

            if($this->orders_filter['order_payment_status'] != ORDER_PAYMENT_STATUS_ALL)
            {
                $query->WhereAND();
                $query->WhereValue($orders_table['payment_status_id'], DB_EQ, $this->orders_filter['order_payment_status']);
            };
        };

        $query->WhereAND();
        $query->Where($orders_table['id'], DB_IN, "('".implode("','",$oids_wo_filter)."')");

        $oids_with_filter = array();

        $res = $application->db->getDB_Result($query);
        for($i=0;$i<count($res);$i++)
        {
            $oids_with_filter[] = $res[$i]['order_id'];
        };

        $this->__setOrdersIDs($oids_with_filter);
    }

    function __loadOrdersSummary()
    {
        if(!$this->search_completed or empty($this->orders_ids))
        {
            return;
        };

        //                        :
        //                                                               ,
        //               main_store_currency                            .
        //                               main_store_currency,
        //           .
        //                                 main_store_currency.
        global $application;
        $co_tables = modApiStaticFunc('Checkout','getTables');
        $orders_table = $co_tables['orders']['columns'];
        $order_prices_table = $co_tables['order_prices']['columns'];

        $query = new DB_Select();
        $query->addSelectTable('orders');
        $query->addSelectField($order_prices_table['order_total'], 'order_total');
        $query->addSelectField($orders_table['payment_status_id'], 'payment_status_id');
        $query->addSelectField($order_prices_table['currency_code'], 'currency_code');
        $query->WhereValue($order_prices_table['currency_type'], DB_EQ, CURRENCY_TYPE_MAIN_STORE_CURRENCY);
        $query->WhereAnd();
        $query->Where($orders_table['id'], DB_IN, "('".implode("','",$this->orders_ids)."')");
        $query->addLeftJoin('order_prices', $orders_table['id'], DB_EQ, $order_prices_table['order_id']);


        $rows = $application->db->getDB_Result($query);

        $amount = 0.0;
        $fully_paid_amount = 0.0;
        $main_store_currency = modApiFunc("Localization", "getCurrencyCodeById", modApiFunc("Localization", "getMainStoreCurrency"));
        foreach($rows as $order_info)
        {
            $order_main_currency = $order_info['currency_code'];
            $order_total = $order_info['order_total'];
            if($order_main_currency != $main_store_currency)
            {
            	$order_total = modApiFunc('Currency_Converter','convert',$order_total, $order_main_currency, $main_store_currency);
            }
            $amount += $order_total;
            if($order_info['payment_status_id'] == ORDER_PAYMENT_STATUS_FULLY_PAID)
            {
            	$fully_paid_amount += $order_total;
            }
        }

        $query = new DB_Select();
        $query->addSelectTable('orders');
        $query->addSelectField($query->fMax($orders_table['date']), 'max_date');
        $query->addSelectField($query->fMin($orders_table['date']), 'min_date');
        $query->Where($orders_table['id'], DB_IN, "('".implode("','",$this->orders_ids)."')");
        $res = $application->db->getDB_Result($query);

        $this->orders_summary = array
        (
             'amount' => $amount
            ,'max_date' => $res[0]['max_date']
            ,'min_date' => $res[0]['min_date']
            ,'fully_paid_amount' => $fully_paid_amount
        );
    }

    function __getOrdersSummaryTag($tag)
    {
        if($this->orders_summary == null)
        {
            $this->__loadOrdersSummary();
        };

        return $this->orders_summary[$tag];
    }

    function __setOrdersIDs($oids)
    {
        $this->orders_ids = $oids;
        $this->search_completed = true;
        $this->orders_summary = null;
    }

    function __getCAAttrsList()
    {
        global $application;
        if($this->ca_attrs_list == null)
        {
            $res = execQuery('SELECT_CUSTOMER_ACCOUNT_ATTRIBUTES_LIST', array());

            $this->ca_attrs_list = array();
            foreach($res as $attr)
            {
                //                  PersonInfoType -
                if(!isset($this->ca_attrs_list[$attr['group_id']]))
                {
                    $this->ca_attrs_list[$attr['group_id']] = array();
                }

                //
                $this->ca_attrs_list[$attr['group_id']][$attr['attr_id']] = array
                (
                     'attr_id'  => $attr['attr_id']
                    ,'group_id' => $attr['group_id']
                    ,'ag_id'    => $attr['ag_id']
                );
            }
        };
        return $this->ca_attrs_list;
    }

    function __updatePersonInfoAttrInDB($attrs)
    {
        global $application;
        $tables = Customer_Account::getTables();
        $data_table = $tables['ca_person_info_data']['columns'];

        $ca_attrs_list = $this->__getCAAttrsList();

        loadCoreFile('db_multiple_replace.php');

        $tables = Customer_Account::getTables();

        $query = new DB_Multiple_Replace('ca_person_info_data');
        $query->setReplaceFields(array('customer_id','ag_id','data_value'));

        foreach($attrs as $attr_info)
        {
            list($group_id, $attr_id, $attr_value) = $attr_info;
            $r_arr = array(
                'customer_id' => $this->base_info['ID']
               ,'ag_id'       => $ca_attrs_list[$group_id][$attr_id]['ag_id']
               ,'data_value'  => $attr_value
            );
            $query->addReplaceValuesArray($r_arr);
        }
        $application->db->PrepareSQL($query);
        return $application->db->DB_Exec();
    }

    function __changeAccount($account, $new_account)
    {
        global $application;
        $tables = Customer_Account::getTables();
        $customers_table = $tables['ca_customers']['columns'];

        $query = new DB_Update('ca_customers');
        $query->addUpdateValue($customers_table['customer_account'], $new_account);
        $query->WhereValue($customers_table['customer_account'], DB_EQ, $account);
        $application->db->PrepareSQL($query);
        return $application->db->DB_Exec();
    }


    var $account;
    var $affiliate_id;
    var $base_info;
    var $full_info;
    var $orders_filter;
    var $orders_ids;
    var $search_completed;
    var $orders_info;
    var $orders_summary;
    var $person_info_attrs_type;

    var $_attrs_names = array(
        'firstname'   => 'FirstName'
       ,'lastname'    => 'LastName'
       ,'email'       => 'Email'
       ,'phone'       => 'Phone'
       ,'country'     => 'Country'
       ,'state'       => 'State'
       ,'zipcode'     => 'ZipCode'
       ,'postcode'    => 'ZipCode'
       ,'city'        => 'City'
       ,'streetline1' => 'Streetline1'
       ,'streetline2' => 'Streetline2'
    );

    var $_groups_names = array(
        'customer'  => 'Customer'
       ,'billing'   => 'Billing'
       ,'shipping'  => 'Shipping'
    );
};

?>