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
 *                                ,                       -      ,
 *                                         TEMPLATE_CHARSET = "CP-1251"
 *         config.def.php.
 */
class reinstall_module extends AjaxAction
{
    function onAction()
    {
        global $application;

        $request = &$application->getInstance('Request');
        $moduleName = $request->getValueByKey('module_name');
        if(!empty($moduleName))
        {
            $moduleInfo = modApiFunc('Modules_Manager', 'getModuleInfoByName', $moduleName);
            modApiFunc('Modules_Manager', 'uninstallModule', $moduleInfo);

            CCacheFactory::clearAll();
        }

        $request = new Request();
        $request->setView(CURRENT_REQUEST_URL);
        $application->redirect($request);
    }
}