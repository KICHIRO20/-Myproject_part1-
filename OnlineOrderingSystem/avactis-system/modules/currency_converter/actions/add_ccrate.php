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
 * @package CurrencyConverter
 * @author Egor V. Derevyankin
 *
 */

class add_ccrate extends AjaxAction
{
    function add_ccrate()
    {}

    function onAction()
    {
        $r = new Request();
        $to_cc = $r->getValueByKey('to_currency_code');
        $from_cc = $r->getValueByKey('from_currency_code');
        $base = $r->getValueByKey('base_rate');

        $rate = trim($base);

        $errors = array();
        if(modApiFunc('Currency_Converter','doesManRateExists',$from_cc,$to_cc))
        {
            $errors[] = 'E_MAN_RATE_EXISTS';
        };

        #
        # base rate validation. should be integer or decimal value
        #

        if ($this->validateDecimal($rate) != true)
        {
        	$errors[] = 'E_MAN_INVALID_RATE';
        }

        if(empty($errors))
        {
            if(!modApiFunc('Currency_Converter','addManualRate',$from_cc,$to_cc,$rate))
            {
                $errors[] = 'E_MAN_RATE_NOT_ADDED';
            };
        };

        if(empty($errors))
        {
            modApiFunc('Session','set','ResultMessage','MSG_MAN_RATE_ADDED');
        }
        else
        {
            modApiFunc('Session','set','Errors',$errors);
        };

        $r->setView('PopupWindow');
        $r->setKey('page_view','CurrencyRateEditor');
        global $application;
        $application->redirect($r);
    }

    function validateDecimal($value)
	{
        //$validate = preg_match ("/^([0-9]*\.[0-9]+(\.[0-9]*)?)$/" ,$value) == true;
        $validate = Validator::isValidFloat($value);
        return $validate;
   }
};

?>