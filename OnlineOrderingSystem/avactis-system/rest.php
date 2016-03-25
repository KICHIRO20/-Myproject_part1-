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
 * Core Initialization
 *
 * @package Core
 */

global $zone;
$zone = 'AdminZone';

include_once( dirname(__FILE__) . '/app_init.php' );

$usr = &$application->getInstance( 'users' );

$usr->loadState();
$usr->setZone($zone);

modApiFunc('EventsManager','throwEvent','ApplicationStarted');

$session = &$application->getInstance('session');

loadClass('RESTResponse');
loadClass('REST_Errors');

if ($application->getCurrentProtocol() !== "https")
{
	$e = new REST_Errors();
	$e->IncorrectRestProtocol();
	$e->send();
    exit();
}

if (!isset($_GET['alogin']) || !isset($_GET['apassword']))
{
	$e = new REST_Errors();
	$e->UndefinedLoginPassword();
	$e->send();
    exit();
}

loadActionClass('SignIn');
$signin = new SignIn();
$acountInfo = null;
if( $signin->isValidAcount($_GET['alogin'], md5($_GET['apassword']), $acountInfo) )
{
    modApiFunc("Users", "setCurrentUserID", $acountInfo['id']);
}
else
{
	$e = new REST_Errors();
	$e->IncorrectLoginPassword();
	$e->send();
    exit();
}


if ( $usr->isUserSignedIn() == FALSE )
{

}

$_GET['asc_action'] = 'REST_Request_Action';

CProfiler::start('processAction');
$application->processAction();
CProfiler::stop('processAction');
