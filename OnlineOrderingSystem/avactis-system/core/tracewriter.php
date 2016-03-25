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
 *                                        trace-log-                             
 *                 .
 *                                                  .
 *
 * @author af
 * @package Avactis_Core
 */
class CTraceWriter
{
	protected $_filter;
	protected $_file;
	protected $_tpl_prefix;
	protected $_tpl_line;
	protected $_last_time;
	protected $_last_mem;

	/**
	 *
	 * @param $filepath                      -     
	 * @param $template                            -               
	 * @param $filter        CTraceFilter                            -         
	 */
	function __construct($filepath, $template, CTraceFilter $filter)
	{
		$this->_file = new CFile($filepath);
		$this->_filter = $filter;
		// "[@id] [@level] [@date @time.@microtime] [@delta_time] [@mem] [@delta_mem] [@filepath] [@file:@line] [@class@func()]";
		$this->_tpl_prefix = $template;
		$this->_last_mem = memory_get_usage(true);
		$this->_last_time = microtime_float();
	}

	function write($trace_struct)
	{
		if ($this->_filter->match($trace_struct) == true)
		{
			$prepared_struct = $this->_prepareTraceStruct($trace_struct);

			$search = array(
				'@id',
				'@level',
				'@date',
				'@time',
				'@microtime',
				'@mem',
				'@line',
				'@filepath',
				'@file',
				'@func',
				'@class',
				'@delta_time',
				'@delta_mem',
			    '@total_time',
			);

			$replace = array(
				$prepared_struct['id'],
				$prepared_struct['level'],
				$prepared_struct['date'],
				$prepared_struct['time'],
				$prepared_struct['microtime'],
				$prepared_struct['mem'],
				$prepared_struct['line'],
				$prepared_struct['filepath'],
				$prepared_struct['file'],
				$prepared_struct['func'],
				$prepared_struct['class'],
				$prepared_struct['delta_time'],
				$prepared_struct['delta_mem'],
                $prepared_struct['total_time'],
			);

			$log_line_prefix = str_replace($search, $replace, $this->_tpl_prefix);
			// $trace_struct['msg'] -                  
			$msg = $this->_prepareMsg($trace_struct['msg']);
			$this->_file->putContent($log_line_prefix ."\n".$msg."\n\n", 'a');
		}
	}

	protected function _prepareMsg($msg)
	{
		$str = array();
		foreach($msg as $m)
		{
			if (!is_scalar($m) || is_bool($m))
			{
				ob_start();
				var_dump($m);
				$m = ob_get_clean();
				$m = trim(str_replace("=>\n ", '=>', $m));
			}
			$str[] = $m;
		}
		return implode("\n", $str);
	}

	protected function _prepareTraceStruct($trace_struct)
	{
	    $total_time = $trace_struct['time'] - CTrace::getStartTime();
		$microtime = round($trace_struct['time'] - floor($trace_struct['time']), 3) * 1000;
		$date = date('Y-m-d', floor($trace_struct['time']));
		$time = date('H:i:s', floor($trace_struct['time']));

		$delta_time = round($trace_struct['time'] - $this->_last_time, 3);
		$delta_time = ($delta_time <= 0) ? $delta_time : '+'.$delta_time;

		$delta_mem = round(($trace_struct['mem']-$this->_last_mem)/(1024*1024), 2);
		$delta_mem = ($delta_mem <= 0) ? $delta_mem : '+'.$delta_mem;

		$this->_last_time = $trace_struct['time'];
		$this->_last_mem = $trace_struct['mem'];

		$trace_struct['microtime'] = sprintf('%03d', $microtime);
		$trace_struct['date'] = $date;
		$trace_struct['time'] = $time;
		$trace_struct['delta_time'] = $delta_time;
		$trace_struct['delta_mem'] = $delta_mem;
		$trace_struct['mem'] = sprintf('%.3f', $trace_struct['mem']/(1024*1024));
		$trace_struct['filepath'] = $trace_struct['file'];
		$trace_struct['file'] = basename($trace_struct['file']);
		$trace_struct['func'] = ($trace_struct['func']==null ? '' : $trace_struct['func']);
		$trace_struct['class'] = ($trace_struct['class']==null ? '' : $trace_struct['class'].$trace_struct['type']);
		$trace_struct['total_time'] = sprintf('%.3f', $total_time);

		return $trace_struct;
	}

}

