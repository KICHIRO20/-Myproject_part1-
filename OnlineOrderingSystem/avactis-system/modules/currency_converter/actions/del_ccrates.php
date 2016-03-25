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

class del_ccrates extends AjaxAction
{
    function del_ccrates()
    {}

    function onAction()
    {
        $r = new Request();
        $rates_ids = $r->getValueByKey('rates_ids');
        if($rates_ids !== null)
        {
            $rates_ids = explode('|',$rates_ids);
        };

        if(modApiFunc('Currency_Converter','delManualRates',$rates_ids))
        {
            modApiFunc('Session','set','ResultMessage','MSG_MAN_RATES_DELETED');
        }
        else
        {
            modApiFunc('Session','set','Errors',array('E_MAN_RATES_NOT_DELETED'));
        };

        $r->setView('PopupWindow');
        $r->setKey('page_view','CurrencyRateEditor');
        global $application;
        $application->redirect($r);
    }
};

?>