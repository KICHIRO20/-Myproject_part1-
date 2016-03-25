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
 * @package core
 * @author Vadim Lyalikov
 */
class update_pm_sm
{
    function update_pm_sm()
    {
    }

    function onAction()
    {
        global $application;
        $request = $application->getInstance('Request');

       	$pm_sm_uid = $request->getValueByKey('pm_sm_uid');
       	if($pm_sm_uid !== NULL)
       	{
            $pm_settings = modApiFunc("Checkout", "getSelectedModules", "payment");
            $sm_settings = modApiFunc("Checkout", "getSelectedModules", "shipping");
            $pm_sm_settings = array_merge_recursive($pm_settings, $sm_settings);
            $module_settings = $pm_sm_settings[$pm_sm_uid];

       	    $rule = $request->getValueByKey('pm_sm_accepted_currencies_rule');
       	    if($rule !== NULL)
       	    {
       	    	foreach($module_settings['currency_acceptance_rules'] as $id => $info)
       	    	{
       	    		$info =& $module_settings['currency_acceptance_rules'][$id];
       	    		if($info['rule_name'] == $rule)
       	    		{
       	    			$info['rule_selected'] = DB_TRUE;
       	    		}
       	    		else
       	    		{
                        $info['rule_selected'] = DB_FALSE;
       	    		}
       	    		unset($info);
       	    	}

       	    	switch($rule)
       	    	{
       	    		case ACTIVE_AND_SELECTED_BY_CUSTOMER:
       	    	    {
       	    	    	break;
       	    	    }
       	    	    case ACTIVE_AND_ACCEPTED_BY_PM_SM_AND_SELECTED_BY_CUSTOMER:
                    {
                    	$accepted_currencies = $request->getValueByKey('accepted_currencies_checkboxes');
                    	if($accepted_currencies === NULL)
                    	{
                    		$accepted_currencies = array();
                    	}
                    	//
                        foreach($module_settings['accepted_currencies'] as $id => $info)
		                {
		                    $info =& $module_settings['accepted_currencies'][$id];
		                    if(array_key_exists($info['currency_code'], $accepted_currencies))
		                    {
		                        $info['currency_status'] = ACCEPTED;
		                    }
		                    else
		                    {
		                        $info['currency_status'] = NOT_ACCEPTED;
		                    }
		                    unset($info);
		                }
                        break;
                    }
       	    	    case THE_ONLY_ACCEPTED:
                    {
                    	$the_only_acceptable_code = $request->getValueByKey('accepted_currencies_radio');
                        //
                        foreach($module_settings['accepted_currencies'] as $id => $info)
                        {
                            $info =& $module_settings['accepted_currencies'][$id];
                            if($info['currency_code'] == $the_only_acceptable_code)
                            {
                                $info['currency_status'] = THE_ONE_ONLY_ACCEPTED;
                            }
                            else
                            {
                                $info['currency_status'] = NOT_ACCEPTED;
                            }
                            unset($info);
                        }
                        break;
                    }
       	    	    case MAIN_STORE_CURRENCY:
                    {
                        break;
                    }
       	    	}
                modApiStaticFunc("Checkout", "update_pm_sm_currency_settings", $pm_sm_uid, $module_settings);
       	    }
       	}
    }
}

?>