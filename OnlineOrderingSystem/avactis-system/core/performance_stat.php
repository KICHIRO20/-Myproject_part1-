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

class CPerformanceStat
{
    function CPerformanceStat()
    {
        $this->timer_total = new CTimer();
        $this->timer_total->start();

        $this->timer_lock = new CTimer();
        $this->timer_db = new CTimer();
        $this->timer_include = new CTimer();

        $this->request_url = getCurrentURL();
    }

    function addQueryExecStat($query, $time)
    {
        if (PERFORMANCE_LOG_LEVEL < PERFORMANCE_LOG_LEVEL_TIMELINE_FULL)
        {
            return;
        }

        $k = md5($query);
        if (isset($this->query_list[$k]))
        {
            $this->query_list[$k] = array(
                                            'QUERY' => $query,
                                            'TIME' => $time + $this->query_list[$k]['TIME'],
                                            'CNT'  => 1 + $this->query_list[$k]['CNT']
            );
        }
        else
        {
            $this->query_list[$k] = array(
                                            'QUERY' => $query,
                                            'TIME'  => $time,
                                            'CNT'   => 1
            );
        }

        if (isset($this->stat_stack_list[$this->index]) &&  is_object($this->stat_stack_list[$this->index]))
        {
            $this->stat_stack_list[$this->index]->query_list[$k] = $this->query_list[$k];
        }
    }

    function startStatByName($name)
    {
        if (PERFORMANCE_LOG_LEVEL < PERFORMANCE_LOG_LEVEL_TIMELINE_FULL)
        {
            return;
        }

        if (empty($this->stat_stack_list))
        {
            $this->stat_stack_list[$this->index] = new CPerformanceStatItem($this, $name);
        }
        else
        {
            $this->stat_stack_list[$this->index]->stop();
            $this->index++;
            $this->stat_stack_list[$this->index] = new CPerformanceStatItem($this, $name);
        }
        $this->stat_stack_list[$this->index]->start();
    }

    function stopStatByName()
    {
        if (PERFORMANCE_LOG_LEVEL < PERFORMANCE_LOG_LEVEL_TIMELINE_FULL)
        {
            return;
        }

        $this->stat_stack_list[$this->index]->stop();
        $this->stat_list[] = $this->stat_stack_list[$this->index];
        unset($this->stat_stack_list[$this->index]);
        if ($this->index > 0)
        {
            $this->index--;
            $this->stat_stack_list[$this->index]->start();
        }
    }

    var $timer_total = null;
    var $timer_lock = null;
    var $timer_db = null;
    var $timer_include = null;
    var $query_list = array();
    var $cnt_queries_got_from_cache = 0;
    var $cnt_queries_saved_to_cache = 0;
    var $cnt_queries_removed_from_cache = 0;
    var $cnt_queries_executed = 0;
    var $memory = 0;
    var $memory_peak = 0;

    var $stat_list = array();
    var $stat_stack_list = array();
    var $index = 0;

    var $request_url = '';
}

class CPerformanceStatItem
{
    function CPerformanceStatItem(&$stat_obj, $name)
    {
        $this->timer = new CTimer();
        $this->stat_obj = &$stat_obj;
        $this->name = $name;
    }

    function start()
    {
        $this->timer->start();
        $this->cnt_queries_got_from_cache -= $this->stat_obj->cnt_queries_got_from_cache;
        $this->cnt_queries_saved_to_cache -= $this->stat_obj->cnt_queries_saved_to_cache;
        $this->cnt_queries_removed_from_cache -= $this->cnt_queries_removed_from_cache;
        $this->cnt_queries_executed -= $this->stat_obj->cnt_queries_executed;
        if (function_exists('memory_get_usage'))
        {
            $this->delta_memory -= memory_get_usage(true);
        }
    }

    function stop()
    {
        $this->timer->stop();
        $this->cnt_queries_got_from_cache += $this->stat_obj->cnt_queries_got_from_cache;
        $this->cnt_queries_saved_to_cache += $this->stat_obj->cnt_queries_saved_to_cache;
        $this->cnt_queries_removed_from_cache += $this->cnt_queries_removed_from_cache;
        $this->cnt_queries_executed += $this->stat_obj->cnt_queries_executed;
        if (function_exists('memory_get_usage'))
        {
            $this->delta_memory += memory_get_usage(true);
        }
    }

    var $timer = null;
    var $cnt_queries_got_from_cache = 0;
    var $cnt_queries_saved_to_cache = 0;
    var $cnt_queries_removed_from_cache = 0;
    var $cnt_queries_executed = 0;
    var $stat_obj = null;
    var $name = 'Unnamed';
    var $query_list = array();
    var $delta_memory = 0;
}

?>