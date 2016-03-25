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

class register_customer extends AjaxAction
{
    function register_customer()
    {}

    function onAction()
    {
        global $application;
        $settings = modApiFunc('Customer_Account','getSettings');

        $request = &$application->getInstance('Request');

        $customer_info = $request->getValueByKey('customer_info');
        $passwd = array(
            'passwd' => $customer_info['Customer']['Password']
           ,'re-type' => $customer_info['Customer']['RePassword']
        );
        unset($customer_info['Customer']['Password'], $customer_info['Customer']['RePassword']);

        $validator = &$application->getInstance('CAValidator');
        $invalid_fields = array();

        $errors = array();
        if(!$validator->isValid('passwd',$passwd))
        {
            $errors[] = 'E_INVALID_PASSWD';
            $invalid_fields[] = 'Password';
            $invalid_fields[] = 'RePassword';
        };

        $required = array();
        $visible_names = array();
        $rg_info = modApiFunc('Customer_Account','getPersonInfoGroupInfoByName','Customer');
        $rg_attrs = modApiFunc('Customer_Account','getPersonInfoGroupAttrs',$rg_info['group_id']);
        foreach($rg_attrs as $a_info)
        {
            if($a_info['is_required'] == 'Y')
            {
                $required[] = $a_info['attr_name'];
            };
            $visible_names[$a_info['attr_name']] = $a_info['visible_name'];
        };

        $country = null;
        $state = null;
        $state_text = null;

        foreach($customer_info['Customer'] as $attr_name => $attr_value)
        {
            if(!in_array($attr_name, $required) and $attr_value == '')
                continue;

            $customer_info['Customer'][$attr_name] = $attr_value = trim($attr_value);
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
                default:
                    if(!$validator->isValid($attr_name, $attr_value) or (in_array($attr_name, $required) and $attr_value == ''))
                    {
                        $errors[] = cz_getMsg('E_INVALID_FIELD',$visible_names[$attr_name]);
                        $invalid_fields[] = $attr_name;
                    };
                    break;
            };
            $customer_info['Customer'][$attr_name] = $attr_value;
        };

        if($country != null)
        {
            if($validator->isValid('Country', $country))
            {
                if($state != null or $state_text != null)
                {
                    $tmp_arr = array(
                        'country_id' => $country
                       ,'state_id' => $state
                       ,'state_text' =>  $state_text
                     );
                    if(!$validator->isValid('country_state', $tmp_arr))
                    {
                        $errors[] = cz_getMsg('E_INVALID_FIELD',$visible_names['State']);;
                        $invalid_fields[] = 'State';
                    };
                };
            }
            else
            {
                $errors[] = cz_getMsg('E_INVALID_FIELD',$visible_names['Country']);
                $invalid_fields[] = 'Country';
            };
        };

        if(empty($errors))
        {
            $account_name = $settings['AUTH_SCHEME'] == AUTH_SCHEME_BY_LOGIN ? $customer_info['Customer']['AccountName'] : $customer_info['Customer']['Email'];

            if(modApiFunc('Customer_Account','doesAccountExists',$account_name))
            {
                $errors[] = 'E_ACCOUNT_EXISTS';
                $invalid_fields[] = $settings['AUTH_SCHEME'] == AUTH_SCHEME_BY_LOGIN ? 'AccountName' : 'Email';
            }
            else
            {
                if(!modApiFunc('Customer_Account','registerAccount',$account_name,$passwd['passwd'],$customer_info['Customer']))
                {
                    $errors[] = 'E_NOT_REGISTERED';
                };
            };
        };

        $target_view = 'Registration';
        $target_url = '';

        if(!empty($errors))
        {
            modApiFunc('Session','set','RegisterErrors',$errors);
            modApiFunc('Session','set','customer_info',$customer_info);
            modApiFunc('Session','set','invalid_fields',$invalid_fields);
        }
        else
        {
            $obj = &$application->getInstance('CCustomerInfo',$account_name);

            $reg_data = array(
                'account' => $account_name
               ,'info' => $customer_info['Customer']
            );
            modApiFunc('EventsManager','throwEvent','CustomerRegistered',$reg_data);

            switch($settings['ACCOUNT_ACTIVATION_SCHEME'])
            {
                case ACCOUNT_ACTIVATION_SCHEME_NONE:
                    modApiFunc('Customer_Account','dropActivationKey',$account_name,'customer_account');
                    $obj->SignIn();
                    if(modApiFunc('Session','is_set','toCheckoutAfterSignIn'))
                    {
                        modApiFunc('Session','un_set','toCheckoutAfterSignIn');
                        $target_view = 'CheckoutView';
                    }
                    elseif (modApiFunc('Session', 'is_set', 'toURLAfterSignIn'))
                    {
                        $target_url = modApiFunc('Session', 'get', 'toURLAfterSignIn');
                        modApiFunc('Session', 'un_set', 'toURLAfterSignIn');
                    }
                    else
                    {
                        modApiFunc('Session','set','ResultMessage','MSG_REGISTERED');
                        $target_view = 'CustomerAccountHome';
                    };
                    break;
                case ACCOUNT_ACTIVATION_SCHEME_BY_ADMIN:
                    modApiFunc('EventsManager','throwEvent','AdminShouldActivateCustomer',$account_name);
                    modApiFunc('Session','set','ResultMessage','MSG_REGISTERED_NEED_ACTIVATE_BY_ADMIN');
                    $target_view = 'AccountActivation';
                    break;
                case ACCOUNT_ACTIVATION_SCHEME_BY_CUSTOMER:
                    modApiFunc('EventsManager','throwEvent','CustomerShouldActivateSelf',$account_name);
                    modApiFunc('Session','set','ResultMessage','MSG_REGISTERED_NEED_ACTIVATE_BY_CUSTOMER');
                    $target_view = 'AccountActivation';
                    break;
            };
        };

        if ($target_url != '')
        {
            $r = new Request($target_url);
        }
        else
        {
            $r = new Request();
            $r->setView($target_view);
        }
        $application->redirect($r);
    }
};

?>