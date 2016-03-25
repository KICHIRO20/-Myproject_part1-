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

class update_reg_form extends AjaxAction
{
    function update_reg_form()
    {}

    function onAction()
    {
        global $application;
        $request = new Request();

        $sets = null;
        $attrs = array();

        foreach($_POST as $var_name => $var_value)
        {
            switch($var_name)
            {
                case 'sets':
                    $sets = $var_value;
                    break;
                default:
                    $attrs[$var_name] = $var_value;
                    break;
            };
        };

        foreach ($attrs as $i => $v)
        {
            if($i == '__ASC_FORM_ID__') continue;
        	if (!array_key_exists('disabled',$v))
        	{
        	    if(!array_key_exists('is_visible',$v))
                {
                    $attrs[$i]['is_visible'] = "N";
                }
                if(!array_key_exists('is_required',$v))
                {
                    $attrs[$i]['is_required'] = "N";
                }


                if ($attrs[$i]['is_required'] == "Y")
                {
                  	$attrs[$i]['is_visible'] = "Y";
                }

            	if ($attrs[$i]['is_visible'] == "N")
                {
                    $attrs[$i]['is_required'] = "N";
                }
        	}
            if (array_key_exists('disabled',$v))
            {
                unset($attrs[$i]['disabled']);
            }
        }

        if($sets != null)
        {
            if(array_key_exists('AUTO_CREATE_ACCOUNT',$sets))
                $sets['AUTO_CREATE_ACCOUNT'] = 'Y';
            else
                $sets['AUTO_CREATE_ACCOUNT'] = 'N';

            if(array_key_exists('MERGE_ORDERS_BY_EMAIL',$sets))
                $sets['MERGE_ORDERS_BY_EMAIL'] = 'Y';
            else
                $sets['MERGE_ORDERS_BY_EMAIL'] = 'N';

            modApiFunc('Customer_Account','updateSettings',$sets);
        };

        if(!empty($attrs))
        {
        	modApiFunc('Customer_Account','updateGroupAttrsInfo','Customer',$attrs);
        };

        modApiFunc('Session','set','ResultMessage','MSG_REG_FORM_UPDATED');
        $request->setView('RegisterFormEditor');
        $application->redirect($request);
    }
};

?>