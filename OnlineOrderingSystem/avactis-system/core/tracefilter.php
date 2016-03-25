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
 *                                            trace-log-         .
 *                                           ,             .
 *                                         ,                        .
 *
 * @author af
 * @package Avactis_Core
 */
class CTraceFilter
{
	protected $settings = array();

	/**
	 *                                              trace-log-         .
	 *        trace-log-                                                    .
	 *              ,                                                                   
	 *           ,                                                                               
	 *                                                .
	 *
	 *                   (09 Sept 2010),        trace-log-                       :
	 * $trace_struct = array(
	 *		'id' 	=> string,
	 *		'msg' 	=> string | array,
	 *		'level'	=> err | wrn | inf | dbg,
	 *		'time'  => float,
	 *		'mem' 	=> int,
	 *		'line' 	=> string,
	 *		'file' 	=> string (full file path),
	 *		'func' 	=> string,
	 *		'class' => string,
	 *	);
	 *
	 *                                                           $settings                 
	 *            .                                                     ,                         
	 *                              .
	 *
	 *         :
	 * $settings = array(
	 * 		'level' => array('err', 'wrn'),
	 * 		'class' => array('Catalog', 'Checkout')
	 * );
	 *
	 *             ,                                   trace-log-         , level        
	 *      err,      wrn,                                Catalog      Checkout.
	 *
	 *               (      )                      ,                  -            .
	 *
	 * @param $settings
	 */
	function __construct($settings = null)
	{
		if (is_array($settings) && !empty($settings))
		{
			$this->settings = $settings;
		}
	}

	function match($trace_truct)
	{
		foreach($this->settings as $field=>$values)
		{
			if (isset($trace_truct[$field]))
			{
				$result = false;
				$test_string = $trace_truct[$field];
				if (is_array($test_string))
				{
					$test_string = implode(' ', $test_string);
				}
				foreach($values as $v)
				{
					//                                        -                 
					if (strpos($test_string, $v)!==false)
					{
						$result = true;
						break;
					}
				}
				//                                                           ,
				//                                  trace_struct
				if ($result == false) return false;
			}
		}
		return true;
	}
}
