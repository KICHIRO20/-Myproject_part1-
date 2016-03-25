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
 * @package ModRewrite
 * @author Egor V. Derevyankin
 *
 */

class update_mr_settings extends AjaxAction
{
    function update_mr_settings()
    {
    }

    function onAction()
    {
        global $application;
        $request = &$application->getInstance('Request');
        $sets = $request->getValueByKey('sets');

        $errors = array();

        $sets['CATS_PREFIX'] = preg_replace("/[^a-z0-9_\-]/i","",$sets['CATS_PREFIX']);
        $sets['PRODS_PREFIX'] = preg_replace("/[^a-z0-9_\-]/i","",$sets['PRODS_PREFIX']);
        $sets['CMS_PREFIX'] = preg_replace("/[^a-z0-9_\-]/i","",$sets['CMS_PREFIX']);

        if($sets['CATS_PREFIX']=="")
        {
            $errors[] = 'ERR_INVALID_CATS_PREFIX';
        };
        if($sets['PRODS_PREFIX']=="")
        {
            $errors[] = 'ERR_INVALID_PRODS_PREFIX';
        };
        if($sets['CMS_PREFIX']=="")
        {
            $errors[] = 'ERR_INVALID_CMS_PREFIX';
        };
        if(empty($errors) and ($sets['CATS_PREFIX']==$sets['PRODS_PREFIX'] or $sets['CMS_PREFIX']==$sets['PRODS_PREFIX'] or $sets['CATS_PREFIX']==$sets['CMS_PREFIX']))
	{
            $errors[] = 'ERR_PREFIXES_ARE_SAME';
        };

        if(empty($errors))
        {
            modApiFunc('Mod_Rewrite','updateSettings',$sets);
            modApiFunc('Session','set','ResultMessage','MSG_SETTINGS_UPDATED');
        }
        else
        {
            modApiFunc('Session','set','MR_sets',$sets);
            modApiFunc('Session','set','Errors',$errors);
        };

        $r = new Request();
        $r->setView(CURRENT_REQUEST_URL);
       // $r->setKey('page_view','MR_Settings');
        $application->redirect($r);
    }
};

?>