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
 * @package ProductOptions
 * @author Egor V. Derevyankin
 *
 */

class update_options_settings extends AjaxAction
{
    function update_options_settings()
    {
    }

    function onAction()
    {
        global $application;
        $request = &$application->getInstance('Request');
        $os = $request->getValueByKey('os');

        $data = ($os != null ? $os : array());

        if(array_key_exists('LL_NTF',$data))
        {
            if($data['LL_NTF'] != '')
            {
                $data['LL_NTF'] = intval($data['LL_NTF']);
            };
        };

        $is_ajax = $request->getValueByKey('is_ajax');
        if($is_ajax == 'yes')
            global $_RESULT;

        if(modApiFunc("Product_Options","updateOptionsSettingsForEntity",$_POST["parent_entity"],$_POST['entity_id'],$data))
        {
            if($is_ajax!='yes')
                modApiFunc("Session","set","ResultMessage","MSG_SETTINGS_UPDATED");
            else
                $_RESULT['result'] = 'updated';
        }
        else
        {
            if($is_ajax!='yes')
                modApiFunc("Session","set","ResultMessage","MSG_SETTINGS_NOT_UPDATED");
            else
                $_RESULT['result'] = 'not_updated';
        };

        if($request->getValueByKey('redirect_not_needed')!='yes')
        {
            $redirect_view = (isset($_POST['fromView']))?$_POST['fromView']:'PO_OptionsList';

            $request = new Request();
            $request->setView($redirect_view);
            $request->setKey('parent_entity',$_POST["parent_entity"]);
            $request->setKey('entity_id',$_POST["entity_id"]);
            $application->redirect($request);
        };
    }

};

?>