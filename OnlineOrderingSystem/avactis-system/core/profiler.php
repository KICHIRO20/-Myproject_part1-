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
 * TODO log each query
 * @author af
 *
 */
class CProfiler
{
    const csv_fields_delimiter = ';';
    const csv_lines_delimiter = "\r\n";

	static function init()
	{
		$settings = CConf::get('profiler');
		if (isset($settings['enabled']) && $settings['enabled'] == 'yes')
		{
			self::$enabled = true;
		}
        if (isset($settings['display_include']) && $settings['display_include'] == 'yes')
        {
            self::$display_include = true;
        }
        if (isset($settings['display_file_io']) && $settings['display_file_io'] == 'yes')
        {
            self::$display_file_io = true;
        }
        if (isset($settings['display_queries']) && $settings['display_queries'] == 'yes')
        {
            self::$display_queries = true;
        }
        if (isset($settings['display_cache']) && $settings['display_cache'] == 'yes')
        {
            self::$display_cache = true;
        }
        if (isset($settings['display_block_tags']) && $settings['display_block_tags'] == 'yes')
        {
            self::$display_block_tags = true;
        }
        if (isset($settings['write_csv']) && $settings['write_csv'] == 'yes')
        {
            self::$write_csv = true;
        }
        self::$profiler = new CProfilerStorage();
		self::$profiler->timer_total->start();
	}

	static function DBLayerStop($query = null)
	{
		self::$enabled and self::$profiler->timer_db_layer->stop();
        if(self::$display_queries and $query)
        {
            self::$profiler->timer_db_layer_queries[$query]->stop();
        }
	}

	static function DBLayerStart($query = null)
	{
		self::$enabled and self::$profiler->timer_db_layer->start();
		if(self::$display_queries and $query)
        {
            if(!isset(self::$profiler->timer_db_layer_queries[$query]))
            {
                self::$profiler->timer_db_layer_queries[$query] = new CTimer();
            }
            self::$profiler->timer_db_layer_queries[$query]->start();
        }
	}

	static function DBExecStop($query=null)
	{
		self::$enabled and self::$profiler->timer_db_exec->stop();
        if(self::$display_queries and $query)
        {
            self::$profiler->timer_mysql_queries[$query]->stop();
        }
	}

	static function DBExecStart($query = null)
	{
		self::$enabled and self::$profiler->timer_db_exec->start();
		self::$display_queries and $query and @ self::$profiler->cnt_mysql_queries[$query]++;
		if(self::$display_queries and $query)
        {
            if(!isset(self::$profiler->timer_mysql_queries[$query]))
            {
                self::$profiler->timer_mysql_queries[$query] = new CTimer();
            }
            self::$profiler->timer_mysql_queries[$query]->start();
        }
	}

	static function DBCacheQuerySaved()
	{
		self::$enabled and self::$profiler->cnt_queries_saved_to_cache++;
	}

	static function DBCacheQueryRemoved()
	{
		self::$enabled and self::$profiler->cnt_queries_removed_from_cache++;
	}

	static function DBCacheQueryRead()
	{
		self::$enabled and self::$profiler->cnt_queries_read_from_cache++;
	}

	static function DBQueryStatistics($query_name, $params, $params_key, $cache_hit)
	{
	    if (self::$display_queries) {
            @ self::$profiler->cnt_queries_executed[$query_name]['all']++;
            @ self::$profiler->cnt_queries_executed[$query_name]['unique'][$params_key]++;
            if (! $cache_hit) {
                @ self::$profiler->cnt_queries_executed[$query_name]['really']++;
            }
	    }
	}

	static function checkCache()
	{
	    self::$enabled and self::$profiler->cnt_cache_checks++;
	}

    static function cacheHit()
    {
        self::$enabled and self::$profiler->cnt_cache_hits++;
    }

    static function loadCache()
    {
        self::$enabled and self::$profiler->cnt_cache_loads++;
    }

    static function includeStart($file = null)
	{
		self::$enabled and self::$profiler->timer_include->start();
		self::$display_include and $file and @ self::$profiler->cnt_include[$file]++;
	}

	static function includeStop()
	{
		self::$enabled and self::$profiler->timer_include->stop();
	}

	static function lockStart()
	{
		self::$enabled and self::$profiler->timer_lock->start();
	}

	static function lockStop()
	{
		self::$enabled and self::$profiler->timer_lock->stop();
	}

	static function ioStart($file = null, $op = null)
	{
		self::$enabled and self::$profiler->timer_io->start();
		self::$enabled and $file and $op and self::$profiler->cnt_file_rw++;
		self::$display_file_io and $file and $op and @ self::$profiler->cnt_file_io[basename($file)][$op]++;
	}

	static function ioStop()
	{
		self::$enabled and self::$profiler->timer_io->stop();
	}

	static function btStart($block_tag_name)
	{
	    if (self::$enabled && self::$display_block_tags) {
	        if (! isset(self::$profiler->timer_block_tags[$block_tag_name])) {
	            self::$profiler->timer_block_tags[$block_tag_name] = new CTimer();
	        }
	        self::$profiler->timer_block_tags[$block_tag_name]->start();
	    }
	}

	static function btStop($block_tag_name)
	{
	    if (self::$enabled && self::$display_block_tags) {
	        if (isset(self::$profiler->timer_block_tags[$block_tag_name])) {
	            self::$profiler->timer_block_tags[$block_tag_name]->stop();
	        }
        }
	}

	static function start($name)
	{
		if (!self::$enabled) return;
		self::$stack[$name] = clone(self::$profiler);
		self::$stack[$name]->fullStop();
	}

	static function stop($name)
	{
		if (self::$enabled && isset(self::$stack[$name]))
		{
			$old = self::$stack[$name];
			$current = clone(self::$profiler);
			$current->fullStop();
			$current->reduce($old);
			self::$stack[$name] = $current;
		}
	}

	static function getProfiler()
	{
		if (!self::$enabled) return '';
		self::$profiler->fullStop();
		$s = "[Profiler:Total]\n";
		$s .= (string)self::$profiler->__toString();
		foreach(self::$stack as $key=>$p)
		{
			$s .= "\n[Profiler:$key]\n";
			$s .= (string)$p->__toString();
		}

		if (self::$display_cache) {
            $s .= "\n[Profiler:Cache Storages Stat]\n";
    		$s .= CCacheFactory::getStat();
		}

        if (self::$display_include) {
            $s .= "\n[Profiler:PHP includes]\n";
            ksort(self::$profiler->cnt_include);
            foreach (self::$profiler->cnt_include as $file => $ctr) {
                $s .= $file.': '.$ctr."\n";
            }

        }

		if (self::$display_queries) {
            $s .= "\n[Profiler:Database queries]\n";
            uasort(self::$profiler->cnt_queries_executed, create_function('$a, $b', 'return $a["all"] < $b["all"];'));
            foreach (self::$profiler->cnt_queries_executed as $query => $counter) {
                if ($counter > 1) {
                    $s .= sprintf("%3d /%3d /%3d / %.3fs: %s\n", @$counter['all'], sizeof(@$counter['unique']),
                            @$counter['really'], self::$profiler->timer_db_layer_queries[$query]->getTime(), $query);
                }
            }

            $s .= "\n[Profiler:Raw MySQL queries]\n";
            //arsort(self::$profiler->cnt_mysql_queries, SORT_NUMERIC);
            uasort(self::$profiler->timer_mysql_queries, create_function('$a, $b', 'return $a->getTime() > $b->getTime();'));
            foreach (self::$profiler->timer_mysql_queries as $query => $time) {
                $s .= sprintf("%f: %3d: %s\n", $time->getTime(), self::$profiler->cnt_mysql_queries[$query], $query);
            }
		}

		if (self::$display_file_io) {
            $s .= "\n[Profiler:File I/O operations]\n";
            ksort(self::$profiler->cnt_file_io);
            foreach (self::$profiler->cnt_file_io as $file => $cntrs) {
                $cnt_arr = array();
                foreach ($cntrs as $cnt => $val) {
                    $cnt_arr[] = $cnt.'='.$val;
                }
                $s .= $file.': '.implode(', ', $cnt_arr)."\n";
            }
		}

		if (self::$display_block_tags) {
            $s .= "\n[Profiler:Block Tags]\n";
		    ksort(self::$profiler->timer_block_tags);
		    foreach (self::$profiler->timer_block_tags as $block_tag => $time) {
                $s .= sprintf("%f: %3d: %s\n", $time->getTime(), $time->getTotalCounter(), $block_tag);
		    }
		}

		return $s;
	}

	static function writeCSV()
	{
	    if (! self::$write_csv) {
	        return;
	    }
	    $profiler_dir = CConf::get('cache_dir');
	    $request_id = substr(md5(microtime(true)), 0, 6);

	    global $zone;
	    $requests_csv = $profiler_dir . 'requests.csv';
	    self::prepareCSV($requests_csv, array('zone', 'script', 'request'));
	    self::writeCSVRecords($requests_csv, $request_id, array(
	            array($zone == 'AdminZone' ? 'AZ' : 'CZ', $_SERVER['SCRIPT_NAME'], $_SERVER['QUERY_STRING'])));

	    $general_csv = $profiler_dir . 'general.total.csv';
	    self::prepareCSV($general_csv, self::$profiler->getFields());
	    self::writeCSVRecords($general_csv, $request_id, array(self::$profiler->getData()));

	    foreach (self::$stack as $key => $p) {
    	    $general_csv = $profiler_dir . 'general.'.self::prepareProfilerFileName($key).'.csv';
    	    self::prepareCSV($general_csv, $p->getFields());
    	    self::writeCSVRecords($general_csv, $request_id, array($p->getData()));
		}

	    if (self::$display_cache) {
	        $cache_stat = CCacheFactory::getStat('array');
	        foreach ($cache_stat as $key => $cache_data) {
	            $cache_csv = $profiler_dir . 'cache.'.strtolower($key).'.csv';
	            unset($cache_data['storage']['log']);
	            $fields_storage = array_keys($cache_data['storage']);
	            $fields_driver = array_keys($cache_data['driver']);
	            array_walk($fields_storage, 'self::addCacheFieldPrefix', 'storage_');
	            array_walk($fields_driver, 'self::addCacheFieldPrefix', 'driver_');
                self::prepareCSV($cache_csv, array_merge($fields_storage, $fields_driver));
                self::writeCSVRecords($cache_csv, $request_id, array(array_merge($cache_data['storage'], $cache_data['driver'])));
	        }
	    }

	    if (self::$display_include) {
	        $include_csv = $profiler_dir . 'include.csv';
	        self::prepareCSV($include_csv, array('counter', 'file_path'));
	        $records = array();
            foreach (self::$profiler->cnt_include as $file => $ctr) {
                $records[] = array($ctr, $file);
            }
	        self::writeCSVRecords($include_csv, $request_id, $records);
	    }

	    if (self::$display_queries) {
	        $queries_csv = $profiler_dir . 'queries.csv';
	        self::prepareCSV($queries_csv, array('total', 'unique', 'executed', 'time_db_layer', 'query_name'));
	        $records = array();
            foreach (self::$profiler->cnt_queries_executed as $query => $counter) {
                if ($counter > 1) {
                    $records[] = array(@$counter['all'], sizeof(@$counter['unique']),
                            @$counter['really'], self::$profiler->timer_db_layer_queries[$query]->getTime(), $query);
                }
            }
	        self::writeCSVRecords($queries_csv, $request_id, $records);
	    }

	    if (self::$display_file_io) {
	        $file_io_csv = $profiler_dir . 'file_io.csv';
	        self::prepareCSV($file_io_csv, array('read', 'write', 'read-write', 'parse', 'test', 'delete', 'file'));
	        $records = array();
            foreach (self::$profiler->cnt_file_io as $file => $cntrs) {
                $records[] = array(
                    (int) @ $cntrs['read'],
                    (int) @ $cntrs['write'],
                    (int) @ $cntrs['read-write'],
                    (int) @ $cntrs['parse'],
                    (int) @ $cntrs['test'],
                    (int) @ $cntrs['delete'],
                    $file,
                );
            }
	        self::writeCSVRecords($file_io_csv, $request_id, $records);
	    }

	    if (self::$display_block_tags) {
	        $block_tags_csv = $profiler_dir . 'block_tags.csv';
	        self::prepareCSV($block_tags_csv, array('counter', 'time', 'block_tag'));
	        $records = array();
		    foreach (self::$profiler->timer_block_tags as $block_tag => $time) {
		        $records[] = array($time->getTotalCounter(), $time->getTime(), $block_tag);
		    }
	        self::writeCSVRecords($block_tags_csv, $request_id, $records);
	    }
	}

	static private function prepareProfilerFileName($name)
	{
	    return preg_replace('/[^a-z\d]+/i', '_', strtolower($name));
	}

	static private function addCacheFieldPrefix(& $field, $i, $prefix)
	{
	    $field = $prefix.$field;
	}

	static private function prepareCSV($file, $fields)
	{
	    if (! file_exists($file)) {
    	    $fields = array_map('strtoupper', $fields);
    	    $f = new CFile($file);
    	    $f->open('w');
    	    $f->write('REQUEST_ID'.self::csv_fields_delimiter);
    	    $f->write(implode(self::csv_fields_delimiter, $fields));
    	    $f->write(self::csv_lines_delimiter);
    	    $f->close();
	    }
	}

	static private function writeCSVRecords($file, $request_id, $records)
	{
	    $f = new CFile($file);
	    $f->open('a');
	    foreach ($records as $fields) {
	        $f->write($request_id.self::csv_fields_delimiter);
    	    $f->write(implode(self::csv_fields_delimiter, $fields));
    	    $f->write(self::csv_lines_delimiter);
	    }
	    $f->close();
	}

	static function isEnabled()
	{
		return self::$enabled;
	}

	static protected $profiler;
	static protected $stack = array();
	static public $enabled = false;
	static protected $display_file_io = false;
    static protected $display_queries = false;
    static protected $display_include = false;
    static protected $display_cache = false;
    static protected $display_block_tags = false;

    static protected $write_csv = false;
}

class CProfilerStorage
{
	function __construct()
	{
		$this->timer_total = new CTimer();
		$this->timer_db_layer = new CTimer();
		$this->timer_db_exec = new CTimer();
		$this->timer_include = new CTimer();
		$this->timer_lock = new CTimer();
		$this->timer_io = new CTimer();
		$this->memory_begin = memory_get_usage(true);
	}

	function fullStop()
	{
		$this->timer_total->stop();
		$this->memory_end = memory_get_usage(true);
	}

	function reduce(CProfilerStorage $profiler)
	{
		$this->cnt_queries_saved_to_cache -= $profiler->cnt_queries_saved_to_cache;
		$this->cnt_queries_removed_from_cache -= $profiler->cnt_queries_removed_from_cache;
		$this->cnt_queries_read_from_cache -= $profiler->cnt_queries_read_from_cache;
        $this->cnt_cache_checks -= $profiler->cnt_cache_checks;
        $this->cnt_cache_hits -= $profiler->cnt_cache_hits;
        $this->cnt_cache_loads -= $profiler->cnt_cache_loads;
        $this->cnt_file_rw -= $profiler->cnt_file_rw;
        foreach (array_keys($this->cnt_queries_executed) as $query) {
            @ $this->cnt_queries_executed[$query]['all'] -= @ $profiler->cnt_queries_executed[$query]['all'];
            @ $this->cnt_queries_executed[$query]['really'] -= @ $profiler->cnt_queries_executed[$query]['really'];
            if (isset($this->cnt_queries_executed[$query]['unique'])) {
                foreach (array_keys($this->cnt_queries_executed[$query]['unique']) as $params_key) {
                    @ $this->cnt_queries_executed[$query]['really'][$params_key] -= @ $profiler->cnt_queries_executed[$query]['really'][$params_key];
                }
            }
        }
        foreach (array_keys($this->cnt_include) as $file) {
            @ $this->cnt_include[$file] -= @ $profiler->cnt_include[$file];
        }
        foreach (array_keys($this->cnt_mysql_queries) as $query) {
            @ $this->cnt_mysql_queries[$query] -= @ $profiler->cnt_mysql_queries[$query];
        }

		$this->memory_begin = $profiler->memory_end;

		$this->timer_total->reduce($profiler->timer_total);
		$this->timer_db_layer->reduce($profiler->timer_db_layer);
		$this->timer_db_exec->reduce($profiler->timer_db_exec);
		$this->timer_include->reduce($profiler->timer_include);
		$this->timer_lock->reduce($profiler->timer_lock);
		$this->timer_io->reduce($profiler->timer_io);
	}

	function __clone()
	{
		$this->timer_total = clone $this->timer_total;
		$this->timer_db_layer = clone $this->timer_db_layer;
		$this->timer_db_exec = clone $this->timer_db_exec;
		$this->timer_include = clone $this->timer_include;
		$this->timer_lock = clone $this->timer_lock;
		$this->timer_io = clone $this->timer_io;
	}

	function __toString()
	{
		$s = '';
		$fs = '%ss';

		$c1s1 = sprintf("Time total: $fs", 			round($this->timer_total->getTime(),2));
		$c1s2 = sprintf("Time DB Layer: $fs", 		round($this->timer_db_layer->getTime(),2));
		$c1s3 = sprintf("Time DB Exec: $fs", 		round($this->timer_db_exec->getTime(),2));
		$c1s4 = sprintf("Time php-include: $fs",	round($this->timer_include->getTime(),2));
		$c1s5 = sprintf("Time lock: $fs", 			round($this->timer_lock->getTime(),2));

		$c2s1 = sprintf("Memory at the begin: %.2fM",	($this->memory_begin)/(1024*1024) );
		$c2s2 = sprintf("Memory at the end: %.2fM", 	($this->memory_end)/(1024*1024) );
		$c2s3 = sprintf("Memory delta: %.2fM", 			($this->memory_end-$this->memory_begin)/(1024*1024) );
		$c2s4 = '';
        $c2s5 = sprintf("Files R/W operations: %d",     $this->cnt_file_rw);

		$c3s1 = sprintf("Queries really executed: %d", 		$this->timer_db_exec->__counter );
		$c3s2 = sprintf("Queries saved to cache: %d", 		$this->cnt_queries_saved_to_cache );
		$c3s3 = sprintf("Queries removed from cache: %d",	$this->cnt_queries_removed_from_cache );
		$c3s4 = sprintf("Queries read from cache: %d", 		$this->cnt_queries_read_from_cache );
		$c3s5 = sprintf("Time I/O: $fs", 			        round($this->timer_io->getTime(),2));

		$s .= sprintf("%-27s %-31s %-30s\n", $c1s1, $c3s1, $c2s1);
		$s .= sprintf("%-27s %-31s %-30s\n", $c1s2, $c3s2, $c2s2);
		$s .= sprintf("%-27s %-31s %-30s\n", $c1s3, $c3s3, $c2s3);
		$s .= sprintf("%-27s %-31s %-30s\n", $c1s4, $c3s4, $c2s4);
		$s .= sprintf("%-27s %-31s %-30s\n", $c1s5, $c3s5, $c2s5);

		return $s;
	}

	function getFields()
	{
	    return array(
            'time_total',
            'time_db_layer',
            'time_db_exec',
            'queries_executed',
            'queries_saved',
            'queries_removed',
            'queries_read',
            'memory_delta',
            'time_php_include',
            'time_lock',
            'time_io',
            'rw_operations',
	    );
	}

	function getData()
	{
	    return array(
	        round($this->timer_total->getTime(), 6),
	        round($this->timer_db_layer->getTime(), 6),
	        round($this->timer_db_exec->getTime(), 6),
	        $this->timer_db_exec->__counter,
	        $this->cnt_queries_saved_to_cache,
	        $this->cnt_queries_removed_from_cache,
	        $this->cnt_queries_read_from_cache,
	        $this->memory_end-$this->memory_begin,
	        round($this->timer_include->getTime(), 6),
	        round($this->timer_lock->getTime(), 6),
	        round($this->timer_io->getTime(), 6),
	        $this->cnt_file_rw,
	    );
	}

	public $timer_total;
	public $timer_db_layer;
	public $timer_db_layer_queries = array();
	public $timer_db_exec;
	public $timer_include;
	public $timer_lock;
	public $timer_io;
	public $timer_block_tags = array();

	public $cnt_queries_saved_to_cache = 0;
	public $cnt_queries_removed_from_cache = 0;
	public $cnt_queries_read_from_cache = 0;
	public $cnt_cache_checks = 0;
    public $cnt_cache_hits = 0;
    public $cnt_cache_loads = 0;
    public $cnt_queries_executed = array();
    public $cnt_mysql_queries = array();
    public $cnt_file_rw = 0;
    public $cnt_file_io = array();
    public $cnt_include = array();

	public $memory_begin = 0;
	public $memory_end = 0;
}