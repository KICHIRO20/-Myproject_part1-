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

define('CTIMER_STATUS_INPROGRESS', 'CTIMER_STATUS_INPROGRESS');
define('CTIMER_STATUS_STOPPED', 'CTIMER_STATUS_STOPPED');


class CTimer
{
    function CTimer()
    {

    }

    function start()
    {
        if ($this->__status == CTIMER_STATUS_STOPPED)
        {
            $this->__status = CTIMER_STATUS_INPROGRESS;
            $this->__start_time = microtime(true);
            $this->__counter++;
        }
        $this->__start_counter++;
        $this->__total_counter++;
    }

    function stop()
    {
        if ($this->__status == CTIMER_STATUS_INPROGRESS)
        {
            $this->__start_counter--;
            if ($this->__start_counter <= 0) {
                $this->__status = CTIMER_STATUS_STOPPED;
                $this->__stop_time = microtime(true);
                $this->__delta_time += ($this->__stop_time - $this->__start_time);
            }
        }
    }

    function getTime()
    {
        if ($this->__status == CTIMER_STATUS_STOPPED)
        {
            return $this->__delta_time;
        }
        else
        {
            $current_time = microtime(true);
            return ($current_time - $this->__start_time);
        }
    }

    function getTotalCounter()
    {
        return $this->__total_counter;
    }

    function reset()
    {
        $this->__start_time = 0;
        $this->__stop_time = 0;
        $this->__delta_time = 0;
        $this->__status = CTIMER_STATUS_STOPPED;
    }

    function reduce(CTimer $timer)
    {
    	if ($this->__status == CTIMER_STATUS_STOPPED &&
    	    $timer->__status == CTIMER_STATUS_STOPPED &&
    	    $this->__stop_time >= $timer->__stop_time)
    	{
    		$this->__start_time = 0;
    		$this->__stop_time = 0;
    		$this->__delta_time -= $timer->__delta_time;
    		$this->__counter -= $timer->__counter;
    		$this->__total_counter -= $timer->__total_counter;
    	}
    	else
    	{
    		die('ERROR: Cannot reduce a timer.');
    	}
    }

    var $__start_counter = 0;
    var $__start_time = 0;
    var $__stop_time = 0;
    var $__delta_time = 0;
    var $__status = CTIMER_STATUS_STOPPED;
    var $__counter = 0;
    var $__total_counter = 0;
}

?>