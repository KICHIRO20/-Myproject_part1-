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
class pm_sm_input_az
{
    function pm_sm_input_az()
    {
    }

    function outputAcceptedCurrenciesRule($options_values, $selected_value)
    {
        $options = array();
        foreach($options_values as $rule)
        {
        	$options[] = array("value" => $rule['rule_name'], "contents" => getMsg('SYS',$rule['rule_name']));
        }

        loadCoreFile('html_form.php');
        $HtmlForm1 = new HtmlForm();
        $value = $HtmlForm1->genDropdownSingleChoice(array
            (
                "onChange"=> "try{accepted_currencies_rule_onchange();} catch(ex) {};",
                "select_name"=> "pm_sm_accepted_currencies_rule",
                "class" => "input-large",
                "values" => $options,
                "selected_value" => $selected_value,
                "id"=>"pm_sm_accepted_currencies_rule"
            )
//           ,'id="pm_sm_accepted_currencies_rule"'
        );

        return $value;
    }

    function outputAcceptedCurrenciesRadio($accepted_currencies)
    {
    	$even = true;
    	$res = "";
    	$b_first = true;
    	foreach($accepted_currencies as $currency)
    	{
    		$code = "<input type='radio' name='accepted_currencies_radio' value='".$currency['currency_code']."' ". (($currency['currency_status'] == THE_ONE_ONLY_ACCEPTED) || $b_first ? " CHECKED " : "") .">" . $currency['currency_code'];
    		if($even)
    		{
    			$res .= "<tr><td>".$code."</td>";
    		}
    		else
    		{
    			$res .= "<td>" . $code . "</td></tr>";
    		}
    		$even = !$even;
    		$b_first = false;
    	}
    	return $res;
    }

    function outputAcceptedCurrenciesCheckboxes($accepted_currencies)
    {
    	$even = true;
        $res = "";
        foreach($accepted_currencies as $currency)
        {
            $code = "<input type='checkbox' name='accepted_currencies_checkboxes[".$currency['currency_code']."]' value='".$currency['currency_code']."' ". (($currency['currency_status'] == ACCEPTED) ? " CHECKED " : "") .">" . $currency['currency_code'];
            if($even)
            {
                $res .= "<tr><td>".$code."</td>";
            }
            else
            {
                $res .= "<td>" . $code . "</td></tr>";
            }
            $even = !$even;
        }
        return $res;
    }

    function outputAcceptedCurrencies()
    {
        global $application;
    	$pm_sm_module_id = modApiFunc("Checkout", "getCurrentPaymentShippingModuleSettingsUID");
        $pm_settings = modApiFunc("Checkout", "getSelectedModules", "payment");
        $sm_settings = modApiFunc("Checkout", "getSelectedModules", "shipping");
        $pm_sm_settings = array_merge_recursive($pm_settings, $sm_settings);
        $module_settings = $pm_sm_settings[$pm_sm_module_id];

        //                                                  MAIN_STORE_CURRENCY,
        //                               .
        if(sizeof($module_settings['currency_acceptance_rules']) == 1)
        {
        	$rule = $module_settings['currency_acceptance_rules'][0];
        	if($rule['rule_name'] == MAIN_STORE_CURRENCY &&
        	   $rule['rule_selected'] == DB_TRUE)
        	{
        		return "";
        	}
        }

        //find selected rule
        $selected_rule = NULL;
        foreach($module_settings['currency_acceptance_rules'] as $rule)
        {
        	if($rule['rule_selected'] == DB_TRUE)
        	{
        		$selected_rule = $rule;
        		break;
        	}
        }

        //                                  :
        $accepted_currencies_sorted = array();
        foreach($module_settings['accepted_currencies'] as $info)
        {
        	$accepted_currencies_sorted[$info['currency_code']] = $info;
        }
        ksort($accepted_currencies_sorted);

        //                             :
        // -
        // -
        // -                    ,           -

        //                             ,                   ,
        //         ,                 .               -
        //  THE_ONLY_ACCEPTED.
        //
        //  ACTIVE_AND_ACCEPTED_BY_PM_SM_AND_SELECTED_BY_CUSTOMER.
        //              MAIN_STORE_CURRENCY   ACTIVE_AND_SELECTED_BY_CUSTOMER                                      .
        $MessageResources = &$application->getInstance('MessageResources');
        $Hints = &$application->getInstance('Hint');

        $attrs = array
        (
//            "PmSmAcceptedCurrencies" => ""
            "ModuleAcceptedCurrenciesFieldName" => $MessageResources->getMessage('MODULE_ACCEPTED_CURRENCIES_FIELD_NAME')
           ,"ModuleAcceptedCurrenciesFieldHint" => $Hints->getHintLink(array('MODULE_ACCEPTED_CURRENCIES_FIELD_NAME', 'system-messages'))
           ,"AcceptedCurrenciesRule" => $this->outputAcceptedCurrenciesRule($module_settings['currency_acceptance_rules'], $selected_rule["rule_name"])
           ,"MAIN_STORE_CURRENCY" => MAIN_STORE_CURRENCY
           ,"THE_ONLY_ACCEPTED" => THE_ONLY_ACCEPTED
           ,"ACTIVE_AND_ACCEPTED_BY_PM_SM_AND_SELECTED_BY_CUSTOMER" => ACTIVE_AND_ACCEPTED_BY_PM_SM_AND_SELECTED_BY_CUSTOMER
           ,"ACTIVE_AND_SELECTED_BY_CUSTOMER" => ACTIVE_AND_SELECTED_BY_CUSTOMER
           ,"accepted_currencies_radio" => $this->outputAcceptedCurrenciesRadio($accepted_currencies_sorted)
           ,"accepted_currencies_checkboxes" => $this->outputAcceptedCurrenciesCheckboxes($accepted_currencies_sorted)
           ,"pm_sm_uid" => $pm_sm_module_id
        );
//        print_r($attrs); die("96");
        $application->registerAttributes($attrs);
        $this->_Template_Contents = array_merge($this->_Template_Contents, $attrs);
          $filename = '../../avactis-extensions/checkout/payment_module_settings';
          if (!file_exists($filename))
            return modApiFunc('TmplFiller', 'fill', "checkout/","payment_module_settings/accepted_currencies.tpl.html", array());
          else
        return modApiFunc('TmplFiller', 'fill', "checkout/payment_module_settings/", "accepted_currencies.tpl.html", array());
    }
}
?>