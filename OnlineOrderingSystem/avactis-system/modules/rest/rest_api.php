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
 * Module RESTManager
 *
 * @package Timeline
 * @author Alexey Florinsky
 */
class RESTManager
{
    function RESTManager()
    {
    	$this->rules_GET = array(
           'test/ok' => array('REST_Test', 'test_ok'),

           'test/errors/UndefinedRequestType'  => array('REST_Errors', 'UndefinedRequestType'),
           'test/errors/UndefinedRESTQuery'    => array('REST_Errors', 'UndefinedRESTQuery'),
           'test/errors/UndefinedHandler'      => array('REST_Errors', 'UndefinedHandler'),
           'test/errors/UndefinedHandlerClass' => array('REST_Errors', 'UndefinedHandlerClass'),

           'events' => array('REST_Events', 'getEventList'),
           'orders/[int:oid]' => array('REST_Orders', 'getOrderInfo'),
        );
    }

    function getHandler($request_method, $rest_query)
    {
    	// find a rule by REST query
    	if ($request_method == 'get')
    	{
    		$rules = $this->rules_GET;
    	}
    	else
    	{
    		$rules = $this->rules_POST;
    	}
    	foreach($rules as $r=>$handler)
    	{
    		// store the rule string in two variables
    		$rule = $r;

    		// remove variables from the rule
    		// [int:order_id] -> [int]
    		$var_pattern = '/\:(\w+)\]/';
	    	$var_replacement = ']';
	    	$r = preg_replace($var_pattern, $var_replacement, $r);

    		// make regexp from the rule string
    		// 'orders/[int]' -> '/^orders\/(\d+)$/i'
	    	$r = str_replace('/','\/', $r);
    		$r = str_replace('[int]','(\d+)', $r);
    		$r = '/^'.$r.'$/i';

			// check REST query by the rule regexp
    		$mathes = array();
        	if ( preg_match($r, $rest_query, $mathes) === 1 ) // we find the rule
        	{
				$parameters = array();
				array_shift($mathes);
        		$values = $mathes; // [int] values
        		if (is_array($values) && !empty($values))
        		{
	        		// grub parameter names from the rule string
	        		$keys = array();
					$var_mathes = array();
	        		if ( preg_match_all($var_pattern, $rule, $var_mathes) >0 )
		        	{
		        		array_shift($var_mathes);
		        		$keys = $var_mathes[0]; // parameter names
		        	}
        		}
        		if (empty($keys))
        		{
        			$parameters = $values;
        		}
        		else
        		{
        			$parameters = array_combine($keys,$values);
        		}
	        	return array('handler' => $handler, 'param' => $parameters);
        	}
        }

        return null;
    }

    function install()
    {
    }

    function getTables()
    {
    }

    function uninstall()
    {
    }

    protected $rules_GET;
    protected $rules_POST;
}
