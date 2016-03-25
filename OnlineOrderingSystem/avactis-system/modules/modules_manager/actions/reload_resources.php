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
class reload_resources extends AjaxAction
{
    function onAction()
    {
        global $application;
        $mm = $application->getInstance( 'Modules_Manager' );
        $lang = _ml_strtolower($application->getAppIni('LANGUAGE'));

        $_path = dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/avactis-themes/system/resources/messages.ini';
        modApiFunc("Resources", "dropMessageGroupByMetaId", 'CZ');
        modApiFunc("Resources", "dropMessageMetaByMetaId", 'CZ');
        modApiFunc("Resources", "addResourceIniToDB", $_path, 'CZ', 'customer_messages', 'CZ');

        modApiFunc("Resources", "dropMessageGroupByMetaId", 'SYS');
        modApiFunc("Resources", "dropMessageMetaByMetaId", 'SYS');
        modApiFunc("Resources", "addResourceIniToDB", $application->getAppIni('PATH_ADMIN_RESOURCES').'system-messages-'.$lang.'.ini', 'SYS', 'system_messages', 'AZ');
        modApiFunc("Resources", "dropMessageGroupByMetaId", 'ML');
        modApiFunc("Resources", "dropMessageMetaByMetaId", 'ML');
        modApiFunc("Resources", "addResourceIniToDB", $application->getAppIni('PATH_ADMIN_RESOURCES').'multilang-messages-'.$lang.'.ini', 'ML', 'MultiLang', 'AZ');
        modApiFunc("Resources", "dropMessageGroupByMetaId", 'CFG');
        modApiFunc("Resources", "dropMessageMetaByMetaId", 'CFG');
        modApiFunc("Resources", "addResourceIniToDB", $application->getAppIni('PATH_ADMIN_RESOURCES').'configuration-messages-'.$lang.'.ini', 'CFG', 'Configuration', 'AZ');

        foreach ($mm->moduleList as $module_name => $moduleInfo) {
            if (isset($mm->modulesResFiles[$moduleInfo->shortName])) {
                modApiFunc("Resources", "dropMessageGroupByMetaId", $moduleInfo->shortName);
                modApiFunc("Resources", "dropMessageMetaByMetaId", $moduleInfo->shortName);
                modApiFunc("Resources", "addResourceIniToDB", $mm->modulesResFiles[$moduleInfo->shortName], $moduleInfo->shortName, $moduleInfo->name, 'AZ');
            }
        }

        CCacheFactory::clearAll();
        die('done');
    }
}