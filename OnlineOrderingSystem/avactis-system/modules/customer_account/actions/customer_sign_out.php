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

class customer_sign_out extends AjaxAction
{
    function customer_sign_out()
    {}

    function onAction()
    {
        global $application;

        $account_name = modApiFunc('Customer_Account','getCurrentSignedCustomer');

        if($account_name != null)
        {
            $customer_obj = &$application->getInstance('CCustomerInfo',$account_name);
            $customer_obj->SignOut();

            if (modApiFunc('Session', 'is_set','AffiliateID'))
                modApiFunc('Session', 'un_set','AffiliateID');

            $sess_obj = $application->getInstance("Session");
            $sess_obj->un_Set('PrerequisitesValidationResults');
            modApiFunc('Checkout', 'ProcessNewStepID', 1);
            modApiFunc('Checkout', 'clearNotMetPrerequisitesValidationResultsDataForAllPosteriorSteps',1);
        }

        $request = new Request();
        $r = new Request();
        $r->setView(CURRENT_REQUEST_URL);
        $r->setKeyValList(modApiFunc('Request', 'getGETArray'));
        $application->redirect($r);
    }
};

?>