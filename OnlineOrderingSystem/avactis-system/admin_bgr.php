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
 * Background Core Initialization
 *
 * @package Core
 */
    global $zone;
    $zone = 'AdminZone';
    include_once( dirname(__FILE__) . '/app_init.php' );

//    modApiFunc('EventsManager','throwEvent','ApplicationStarted');

    function checkCreds($login, $password)
    {
        $retval = false;
        $accountInfo = modApiFunc("Users", "getAcountInfoByEmail", $login);

        if (sizeof($accountInfo) == 1)
        {
            $accountInfo = $accountInfo[0];
            if ($accountInfo['password'] == md5($password))
            {
                $retval = true;
            }
        }
        return $retval;
    }

?>