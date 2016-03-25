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

loadCoreFile('JSON.php');
loadClass('RESTResponse');
loadClass('REST_Errors');

class REST_Request_Action extends AjaxAction
{
    function onAction()
    {

        global $zone;
        if ($zone == 'AdminZone')
        {
            $request_method = $this->_getRequestedMethod();
            if ($request_method == null)
            {
            	$e = new REST_Errors();
            	$e->UndefinedRequestType();
                $e->send();
                return;
            }

            $rest_query = $this->_getRESTQuery();
            if ($rest_query == false)
            {
            	$e = new REST_Errors();
            	$e->UndefinedRESTQuery();
                $e->send();
                return;
           	}

            $handler = modApiFunc('RESTManager', 'getHandler', $request_method, $rest_query);
            if ($handler == null)
            {
            	$e = new REST_Errors();
            	$e->UndefinedHandler();
                $e->send();
                return;
            }

            $h_class = $handler['handler'][0];
            $h_method = $handler['handler'][1];
            loadClass($h_class);
            if (!class_exists($h_class))
            {
            	$e = new REST_Errors();
            	$e->UndefinedHandlerClass();
                $e->send();
                return;
            }

            $o = new $h_class();
           	$o->$h_method($handler['param']);
           	$o->send();

           	exit(0);
        }
    }

    protected function _getRESTQuery()
    {
        $php_self = $_SERVER['REQUEST_URI'];
        $php_self = substr( $php_self, 0, strpos($php_self, '?') );
        $marker = '.php';
        $rest_query = substr( $php_self, strpos($php_self, $marker)+strlen($marker)+1 );
        if(substr($rest_query,-1,1) == '/') $rest_query = substr($rest_query,0,-1);
        return $rest_query;
    }

    protected function _getRequestedMethod()
    {
		$request_method = trim(strtolower($_SERVER['REQUEST_METHOD']));
        if (in_array(strtolower($request_method), array('get', 'post')))
        {
        	return $request_method;
        }
        else
        {
        	return null;
        }
    }

}
