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

class save_personal_info extends AjaxAction
{
    function save_personal_info()
    {}

    function onAction()
    {
        global $application;

        $ca_msg_obj = &$application->getInstance('MessageResources','customer-account-messages','AdminZone');

        $invalid_fields = array();

        $email = modApiFunc('Customer_Account','getCurrentSignedCustomer');
        $new_email = $email;

        if($email == null)
        {
            $r = new Request();
            $r->setView('Index');
            $application->redirect($r);
        }
        else
        {
            $result_messages_array = array();
            $errors_array = array();

            $request = new Request();
            $customer_info = $request->getValueByKey('customer_info');

            $customer_obj = &$application->getInstance('CCustomerInfo',$email);
            $validator = &$application->getInstance('CAValidator');

            foreach($customer_info as $group_name => $group_attrs)
            {
                $country = null;
                $state = null;
                $state_text = null;

                $passwd = array(
                    'passwd' => null
                   ,'re-type' => null
                );

                $required = array();

                if($group_name == 'Customer')
                {
                    $rg_info = modApiFunc('Customer_Account','getPersonInfoGroupInfoByName','Customer');
                    $rg_attrs = modApiFunc('Customer_Account','getPersonInfoGroupAttrs',$rg_info['group_id']);
                    foreach($rg_attrs as $a_info)
                    {
                        if($a_info['is_required'] == 'Y')
                        {
                            $required[] = $a_info['attr_name'];
                        };
                    };
                };

                foreach($group_attrs as $attr_name => $attr_value)
                {
                    if(!preg_match('/password/i',$attr_name) and $attr_value == '' and in_array($attr_name, $required))
                    {
                        $attr_info = $customer_obj->getPersonInfoAttrInfoByName($attr_name, $group_name);
                        $group_info = $customer_obj->getPersonInfoGroupInfoByName($group_name);
                        $errors_array[] = cz_getMsg('ERROR_MESSAGE_INVALID_FIELD_VALUE', $attr_info['visible_name'], $ca_msg_obj->getMessage($group_info['lang_code']));
                        $invalid_fields[$group_name][] = $attr_name;
                        continue;
                    };

                    switch($attr_name)
                    {
                        case 'Country':
                            $country = $attr_value;
                            break;
                        case 'State':
                            $state = $attr_value;
                            break;
                        case 'State_text':
                            $state_text = $attr_value;
                            break;
                        case 'Password':
                            $passwd['passwd'] = $attr_value;
                            break;
                        case 'RePassword':
                            $passwd['re-type'] = $attr_value;
                            break;
                        default:
                            if($validator->isValid($attr_name, $attr_value))
                            {
                                //                email          Customer,                  account name
                                if ($group_name == 'Customer' && $attr_name == 'Email' && $attr_value !== $email)
                                {
                                    $new_customer_email = $attr_value;
                                    //                 $new_customer_email                           ,
                                    //             ,                                   .             ,
                                    //          $new_customer_email          .
                                    //               ,                          :
                                    if (modApiFunc('Customer_Account','doesAccountExists',$new_customer_email) == true)
                                    {
                                        //
                                        $errors_array[] = cz_getMsg('ERROR_MESSAGE_EMAIL_ALREADY_USED', $new_customer_email);
                                        $invalid_fields[$group_name][] = $attr_name;
                                    }
                                    else
                                    {
                                        //            account name,            email          Customer
                                        $customer_obj->changeAccountName($new_customer_email);
                                        $customer_obj->setPersonInfo(array(array($attr_name, $attr_value, $group_name)));
                                    }
                                }
                                else
                                {
                                    $customer_obj->setPersonInfo(array(array($attr_name, $attr_value, $group_name)));
                                }
                            }
                            else
                            {
                                if($attr_value != '')
                                {
                                    $attr_info = $customer_obj->getPersonInfoAttrInfoByName($attr_name, $group_name);
                                    $group_info = $customer_obj->getPersonInfoGroupInfoByName($group_name);
                                    $errors_array[] = cz_getMsg('ERROR_MESSAGE_INVALID_FIELD_VALUE', $attr_info['visible_name'], $ca_msg_obj->getMessage($group_info['lang_code']));
                                    $invalid_fields[$group_name][] = $attr_name;
                                }
                            }
                            break;
                    };
                };

                if($country != null)
                {
                    if($validator->isValid('Country', $country))
                    {
                        $customer_obj->setPersonInfo(array(array('Country',$country, $group_name)));

                        if($state != null or $state_text != null)
                        {
                            $tmp_arr = array(
                                'country_id' => $country
                               ,'state_id' => $state
                               ,'state_text' =>  $state_text
                             );
                            if($validator->isValid('country_state', $tmp_arr))
                            {
                                $customer_obj->setPersonInfo(array(array('State', $tmp_arr['state_id'], $group_name)));
                            };
                        };
                    };
                };

                $_tmp = array_filter($passwd);
                if(!empty($_tmp))
                {
                    if($validator->isValid('passwd',$passwd))
                    {
                        if($customer_obj->changePassword($passwd['passwd']))
                        {
                            $result_messages_array[] = 'MSG_PASSWD_UPDATED';
                        }
                        else
                        {
                            $errors_array[] = 'E_PASSWD_NOT_UPDATED';
                        };
                    }
                    else
                    {
                        $errors_array[] = 'E_INVALID_PASSWD';
                    };
                };

            };

            $customer_obj->refreshCheckoutPrerequisites();

            if(empty($errors_array))
                $result_messages_array[] = 'MSG_PERSONAL_INFO_SAVED';

            if(!empty($result_messages_array))
                modApiFunc('Session','set','ResultMessage',$result_messages_array);
            if(!empty($errors_array))
                modApiFunc('Session','set','RegisterErrors',$errors_array);

            modApiFunc('Session','set','InvalidFields',$invalid_fields);

            $r = new Request();
            $r->setView('CustomerPersonalInfo');
            $application->redirect($r);
        };
    }
};

?>