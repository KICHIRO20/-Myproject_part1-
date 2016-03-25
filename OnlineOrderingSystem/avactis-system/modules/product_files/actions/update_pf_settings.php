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
 * @package ProductFiles
 * @author Egor V. Derevyankin
 *
 */

class update_pf_settings extends AjaxAction
{
    function update_pf_settings()
    {
    }

    function onAction()
    {
        global $application;
        $request = &$application->getInstance('Request');
        $pf_sets = $request->getValueByKey('pf_sets');

        $pf_sets['HL_TL'] = intval($pf_sets['HL_TL']);
        if($pf_sets['HL_TL'] < 0)
            $pf_sets['HL_TL'] = 0;

        $pf_sets['HL_MAX_TRY'] = intval($pf_sets['HL_MAX_TRY']);
        if($pf_sets['HL_MAX_TRY'] < 0)
            $pf_sets['HL_MAX_TRY'] = 0;

        modApiFunc('Product_Files','updateSettings',$pf_sets);

        modApiFunc('Session','set','ResultMessage','MSG_PF_SETTINGS_UPDATED');
        $r = new Request();
        $r->setView(CURRENT_REQUEST_URL);
        $r->setKey('page_view','PF_Settings');

        $application->redirect($r);
    }
};

?>