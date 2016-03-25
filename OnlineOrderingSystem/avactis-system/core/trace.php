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
 *                                                                                               .
 *
 *                                                                    :
 * CTrace::err('message');
 * CTrace::wrn('message');
 * CTrace::inf('message');
 * CTrace::dbg('message');
 *
 *                                                                       ,                .
 *
 *       -                                                         .
 *         :
 * CTrace::dbg('GET:', $_GET, 'POST:', $_POST);
 *
 *                                                :
 * CTrace::dbg('GET:');
 * CTrace::dbg($_GET);
 * CTrace::dbg('POST:');
 * CTrace::dbg($_POST);
 *
 *                    ,          -                                                 .
 *
 *                                                   (backtrace).
 *                          : CTrace::backtrace();
 *                                                            ,                   .
 *
 *                                                              .
 *                              :
 * CTrace::inf('Object', CTrace::var2str($object));
 *
 *         ,                                           ,                                  .
 * 1.                   id                       .
 * 2.                CTraceWriter' ,                             -                .
 * 3.                      CTraceWriter,                           CTraceFilter,                   
 *                      .
 *
 *       :
 * CTrace::setId(getmypid());
 * $filter = new CTraceFilter($filter_settings);
 * $writer = new CTraceWriter($file, $template, $filter);
 * CTrace::registerWriter($writer);
 *
 *                                                             -         .              ,    
 *                                            ,                                          -         
 *               .
 *
 *          :               CTrace                                       ,                       
 *           conf.main.php.
 *
 * @author af
 * @package Avactis_Core
 */
class CTrace
{
	const ERR = 'err';
	const WRN = 'wrn';
	const INF = 'inf';
	const DBG = 'dbg';
	const MAX_CHARS = 80;
	const HELLIP = '...';

	protected static $_script_start;
	protected static $_writers = array();
	protected static $_id;

	public static function registerWriter(CTraceWriter $writer)
	{
		self::$_writers[] = $writer;
	}

	public static function startScript()
	{
	    if (! isset(self::$_script_start)) {
	       self::$_script_start = microtime_float();
	    }
	}

	public static function setId($id)
	{
		self::$_id = $id;
	}

	public static function err()
	{
		self::trace(func_get_args(), CTrace::ERR);
	}

	public static function wrn()
	{
		self::trace(func_get_args(), CTrace::WRN);
	}

	public static function inf()
	{
		self::trace(func_get_args(), CTrace::INF);
	}

	public static function dbg()
	{
		self::trace(func_get_args(), CTrace::DBG);
	}

	public static function backtrace()
	{
		$systrace = debug_backtrace();
		$trace = array();
		foreach($systrace as $num=>$call)
		{
			$msg = '';
			$msg .= "#$num: ";

			if (isset($call['class']))
			{
				$msg .= $call['class'] . $call['type'] . $call['function'];
			}
			else
			{
				$msg .= $call['function'];
			}

			if (isset($call['args']))
			{
			    if (! is_array($call['args']))
			    {
    				$call['args'] = array($call['args']);
    			}
			}
            else
            {
                $call['args'] = array();
            }

			$args = array();
			foreach($call['args'] as $a)
			{
				$args[] = self::var2str($a);
			}
			$msg .= '('.implode(', ', $args).')';

			if (isset($call['file']))
			{
				$msg .= "\n" . $call['file'] . ':' . $call['line'];
			}
			$trace[] = $msg;
		}
		self::trace($trace, self::DBG);
	}

    public static function var2str($var)
    {
    	$str = $var;
    	if (is_array($var))
    	{
    		if (empty($var))
    			$str = 'array()';
    		else
    			$str = 'array:' . count($var) . self::arr2str($var);
    	}
    	if (is_object($var))
    	{
    		$str = 'o:'.get_class($var);
    	}
    	if (is_float($var))
    	{
    		$str = $var;
    	}
    	if (is_int($var))
    	{
    		$str = $var;
    	}
    	if (is_bool($var))
    	{
    		$str = ($var === true) ? 'true' : 'false';
    	}
    	if (is_null($var))
    	{
    		$str = 'null';
    	}
    	if (is_resource($var))
    	{
    		$str = 'r:' . get_resource_type($var);
    	}
    	if (is_string($var))
    	{
    		$str = self::formatString($var);
    	}

        return $str;
    }

    public static function arr2str($arr)
    {
    	$str = array();
    	foreach($arr as $key=>$item)
    	{
    		$str[] = $key .'=>'. self::var2str($item);
    	}
    	return '('.implode(', ', $str).')';
    }

    public static function formatString($str)
    {
		$pl = ceil(self::MAX_CHARS / 2) - ceil(strlen(self::HELLIP) / 2);
    	if(extension_loaded('mbstring') && ini_get('mbstring.internal_encoding') == 'UTF-8')
    	{
    		$len = mb_strlen($str);
		    if($len > self::MAX_CHARS)
		    	$str = mb_substr($str,0,$pl).self::HELLIP.mb_substr($str,(-1)*$pl);
    	}
    	else
    	{
    		$len = strlen($str);
		    if($len >= self::MAX_CHARS)
		    	$str = substr($str,0,$pl).self::HELLIP.substr($str,(-1)*$pl);
    	}
    	return "\"".str_replace("\n",'\n',$str)."\"";
    }

	/*
	 *          2   3 -                        debug_backtrace,                     
	 *                                                        .                               ,
	 *                                      .
	 */
	public static function trace($msg, $level, $backtrace_offset_place=2, $backtrace_offset_callfrom=3)
	{
		if (empty(self::$_writers)) return;
		if (!is_array($msg)) $msg = array($msg);
		$trace_struct = self::_makeTraceStruct($msg, $level, $backtrace_offset_place, $backtrace_offset_callfrom);
		foreach(self::$_writers as $w)
		{
			$w->write($trace_struct);
		}
	}

	protected static function _makeTraceStruct($msg, $level, $backtrace_offset_place, $backtrace_offset_callfrom)
	{
		$trace_struct = array(
			'id' 	=> self::$_id,
			'msg' 	=> $msg,
			'level'	=> $level,
			'time'  => microtime_float(),
			'mem' 	=> memory_get_usage(true),
			'line' 	=> null,
			'file' 	=> null,
			'func' 	=> null,
			'class' => null,
			'type'  => null,
		);

		$systrace = debug_backtrace();
		if (is_array($systrace) && isset($systrace[$backtrace_offset_place]) && isset($systrace[$backtrace_offset_callfrom]))
		{
			$place = $systrace[$backtrace_offset_place];
			$call_from = $systrace[$backtrace_offset_callfrom];

			$trace_struct['line'] = @$place['line'];
			$trace_struct['file'] = @$place['file'];
			$trace_struct['func'] = @$call_from['function'];
			$trace_struct['class']= @$call_from['class'];
			$trace_struct['type']= @$call_from['type'];
		}
		return $trace_struct;
	}

	static function getStartTime()
	{
	    return self::$_script_start;
	}

}
