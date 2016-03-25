<?php

?><?php
/**
 * Core Initialization
 *
 * @package Core
 */

global $zone;
$zone = 'CustomerZone';

include_once( dirname(__FILE__) . '/app_init.php' );

$usr = &$application->getInstance( 'users' );

$usr->setZone('CustomerZone');

modApiFunc('EventsManager','throwEvent','ApplicationStarted');

$session =  &$application->getInstance( 'Session' );
$current_file = basename($_SERVER['PHP_SELF']);
if ((bool)!modApiFunc("Configuration", "getValue", "store_online"))
{
    if ((isset($_SERVER["QUERY_STRING"]) && $_SERVER["QUERY_STRING"] == modApiFunc("Configuration", "getValue", "store_offline_key")) ||
        (isset($_SERVER["REQUEST_URI"]) && _ml_substr($_SERVER["REQUEST_URI"], _ml_strpos($_SERVER["REQUEST_URI"], "?")+1) == modApiFunc("Configuration", "getValue", "store_offline_key")))
    {
        if (!$session->is_Set("DEBUG_MODE"))
        {
            $session->set("DEBUG_MODE", "");
        }
    }
    if (!$session->is_Set("DEBUG_MODE"))
    {
        if ($current_file != basename($application->getPagenameByViewname('Closed')))
        {
            $request = new Request();
            $request->setView('Closed');
            $application->redirect($request);
        }
    }
    else
    {
        if ($current_file == $application->getPagenameByViewname('Closed'))
        {
            $request = new Request();
            $request->setView('Index');
            $application->redirect($request);
        }
    }
}
else
{
    if ($session->is_Set("DEBUG_MODE"))
    {
        $session->un_Set("DEBUG_MODE");
    }
    if ($current_file == basename($application->getPagenameByViewname('Closed')))
    {
        $request = new Request();
        $request->setView('Index');
        $application->redirect($request);
    }
}

$application->outputTagErrors();

if ( $usr->isUserSignedIn() == FALSE )
{
    # redirect to Users_CustomerLogin
    # exit();
}
CProfiler::start('processAction');
$application->processAction();
CProfiler::stop('processAction');

$application->redirectToAnotherProtocol();


CProfiler::start('API & Render');