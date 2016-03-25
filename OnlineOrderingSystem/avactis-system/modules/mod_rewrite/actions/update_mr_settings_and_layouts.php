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

class update_mr_settings_and_layouts extends AjaxAction
{
    function update_mr_settings_and_layouts()
    {}

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

            $mr_act = $request->getValueByKey('mr_act');
            $layouts = $request->getValueByKey('layouts');

            if($mr_act != null)
            {
                foreach($mr_act as $form_id => $mr_stat)
                {
                    $layout_path = base64_decode($layouts[$form_id]);
                    if($mr_stat == 'on')
                    {
                        $hta_content = modApiFunc('Mod_Rewrite','genRewriteBlock',$layout_path);
                        $res = modApiFunc('Mod_Rewrite','saveHTAcontent',$hta_content,$layout_path);

                        if(empty($res))
                        {
                            modApiFunc('Mod_Rewrite','enableMRforLayout',$layout_path);
                        }
                        else
                        {
                            $errors = array_merge($errors, $res);
                        };
                    }
                    elseif($mr_stat == 'off')
                    {
                        modApiFunc('Mod_Rewrite','disableMRforLayout',$layout_path);
                    };
                };
            };
        };

        if(!empty($errors))
        {
            modApiFunc('Session','set','MR_sets',$sets);
            modApiFunc('Session','set','Errors',$errors);
        }
        else
        {
            modApiFunc('Session','set','ResultMessage','MSG_SETTINGS_UPDATED');
        };

        $r = new Request();
        $r->setView(CURRENT_REQUEST_URL);
     //   $r->setKey('page_view','MR_Settings');
        $application->redirect($r);


    }
};

?>