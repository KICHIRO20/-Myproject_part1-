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

require_once( dirname(__FILE__) . '/app_init.php' );
require_once( dirname(__FILE__) . '/admin/includes/admin.php');

include('menu.php');

do_action( 'admin_init' );
$hook_suffix = '';

if ( isset($page_hook) )
	$hook_suffix = $page_hook;
else if ( isset($plugin_page) )
	$hook_suffix = $plugin_page;
else if ( isset($pagenow) )
	$hook_suffix = $pagenow;

set_current_screen();
$usr = &$application->getInstance( 'users' );

$usr->loadState();
$usr->setZone($zone);

modApiFunc('EventsManager','throwEvent','ApplicationStarted');

$session = &$application->getInstance('session');

if ( $usr->isUserSignedIn() == FALSE )
{
    $current_file = basename($_SERVER['PHP_SELF']);
    $request = new Request();
    switch ($current_file)
    {
        case $application->getPagenameByViewname('AdminPasswordRecovery'):
            if ($usr->isBlocked())
            {
                $request->setView  ( 'AdminZoneBlocked' );
                $application->redirect($request);
            }
            break;
        case $application->getPagenameByViewname('AdminSignIn'):
            if ($usr->isBlocked())
            {
                $request->setView  ( 'AdminZoneBlocked' );
                $application->redirect($request);
            }
            break;
        case $application->getPagenameByViewname('AdminZoneBlocked'):
            if (!$usr->isBlocked())
            {
                $request->setView  ( 'AdminSignIn' );
                $application->redirect($request);
            }
            break;
        default:
            $URL = ($application->getCurrentProtocol() == "https"? "https://":"http://").$_SERVER["HTTP_HOST"];
            if (isset($_SERVER['QUERY_STRING']))
            {
                $URL.= $_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
            }
            elseif (isset($_SERVER['REQUEST_URI']))
            {
                $URL.= $_SERVER['REQUEST_URI'];
            }
            else
            {
                $URL.= $_SERVER['PHP_SELF'];
            }
            $session->set('URL', $URL);
            if ($usr->isBlocked())
            {
                $request->setView  ( 'AdminZoneBlocked' );
                $application->redirect($request);
            }
            else
            {
                $request->setView  ( 'AdminSignIn' );
                $application->redirect($request);
            }
            break;
    }
}
else
{
    $current_file = basename($_SERVER['PHP_SELF']);
    if ($usr->getPasswordUpdate())
    {
        if ($current_file != $application->getPagenameByViewname('AdminPasswordUpdate'))
        {
            $request = new Request();
            $request->setView  ('AdminPasswordUpdate');
            $application->redirect($request);
        }
    }
    else
    {
        if ($session->is_Set('URL'))
        {
            $URL = $session->get('URL');
            $session->un_Set('URL');
            $request = new Request($URL);
            $application->redirect($request);
        }
    }
}
CProfiler::start('processAction');
$application->processAction();
CProfiler::stop('processAction');

$application->redirectToAnotherProtocol();

CProfiler::start('API & Render');