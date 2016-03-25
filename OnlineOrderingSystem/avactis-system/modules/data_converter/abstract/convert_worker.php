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
 *       Convert_Worker
 *       .
 *
 * @package DataConverter
 * @author Oleg F. Vlasenko, Egor V. Derevyankin
 */
class Convert_Worker
{


//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------


	function Convert_Worker()
	{

	}


	/**
	 *               -
	 *
	 * $settings
	 * data reader, filter   data writer.
	 *  $settings = array (
	 *  	'datareader_classname'	=> <           datareader>
	 * 		'filter_classname'		=> <           filter>
	 * 		'datawriter_classname'	=> <           datawriter>
	 *                                  data reader, filter   data writer -
	 *                         .
	 *  )
	 *
	 * @param array $settings -        settings
	 */
	function init($settings)
	{
		$this->_clearState();

		$this->_settings = $settings;
        $this->_settings['script_definition'] = modApiFunc('Data_Converter','getScriptDefinition',$this->_settings['script_code']);
        $this->_createWorkers('reader','filter','writer');
        $this->_initWorkers('reader','filter','writer');
        $this->_getWorkersStatus('reader','filter','writer');
        $this->_analizeWorkStatus();

		$this->_saveState();

		return;
	}

    /**
     *                               .
     *                                                    .
     * EXPORTER_TIME_OUT       .
     *
     * @return code of finishing  - EXPORTER_RUN_COMPLETE - export has been finished succesfully
     *                              EXPORTER_RUN_TIME_OUT - export has been interrupted because of time out.
     *                              EXPORTER_RUN_ERROR - errors occured while export session
     */
    function run()
    {
        $this->_loadState();
        $start_time = microtime_float();
        while ((microtime_float() - $start_time) < DC_WORKER_TIME_OUT)
        {
            if($this->_process_info['status']=='INITED' or $this->_process_info['status']=='PROCESSING')
            {
                $this->_doWork();
            };

            if($this->_process_info['status']=='PRE_COMPLETED')
            {
                $this->_finishWork();
                break;
            };

            if($this->_process_info['status']=='ERRORS_HAPPENED')
            {
                $this->_breakWork();
                break;
            };
        }

        $this->_saveState();
        return;
    }

	/**
	 *
	 * array (
 	 * 			'count_items' =>                           ,         data_reader
 	 *          'executed_items' =>                     ,        data_reader
 	 * 		)
	 */

	function getErrors()
	{
		return $this->_errors;
	}

	function getWarnings()
	{
		return $this->_warnings;
	}

    function getMessages()
    {
        return $this->_messages;
    }

    function getProcessInfo()
    {
        return $this->_process_info;
    }

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    function _createWorkers()
    {
        global $application;
        $nicknames = func_get_args();
        for($i=0;$i<count($nicknames);$i++)
        {
            $script_step = $this->_settings['script_step'];
            $this->_workers[$nicknames[$i]] = &$application->getInstance($this->_settings['script_definition']['steps'][$script_step][$nicknames[$i]]);
            $this->_process_info[$nicknames[$i]]=array();
        };
    }

    function _initWorkers()
    {
        $nicknames = func_get_args();
        $bw_init_result = null;
        for($i=0;$i<count($nicknames);$i++)
        {
            $bw = &$this->_workers[$nicknames[$i]];
            $bw_init_result = $bw->initWork($this->_settings,$bw_init_result);
        };
    }

    function _saveWorkers()
    {
        $nicknames = func_get_args();
        for($i=0;$i<count($nicknames);$i++)
        {
            $bw = &$this->_workers[$nicknames[$i]];
            $bw->saveWork();
        };
    }

    function _loadWorkers()
    {
        $nicknames = func_get_args();
        for($i=0;$i<count($nicknames);$i++)
        {
            $bw = &$this->_workers[$nicknames[$i]];
            $bw->loadWork();
        };
    }

    function _fireWorkers()
    {
        $nicknames = func_get_args();
        for($i=0;$i<count($nicknames);$i++)
        {
            $bw = &$this->_workers[$nicknames[$i]];
            $bw->finishWork();
        };
    }

    function _getWorkersStatus()
    {
        $nicknames = func_get_args();
        for($i=0;$i<count($nicknames);$i++)
        {
            $bw = &$this->_workers[$nicknames[$i]];
            $this->_errors=array_merge($this->_errors,$bw->getErrors());
            $this->_warnings=array_merge($this->_warnings,$bw->getWarnings());
            $this->_messages=array_merge($this->_messages,$bw->getMessages());
            $this->_process_info[$nicknames[$i]]=array_merge($this->_process_info[$nicknames[$i]],$bw->getProcessInfo());
        };
    }

    function _analizeWorkStatus()
    {
        $this->_process_info['items_count'] = $this->_process_info['reader']['items_count'];
        $this->_process_info['items_processing'] = $this->_process_info['reader']['items_processing'];

        if(isset($this->_process_info['reader']['global']))
            $this->_process_info = array_merge($this->_process_info,$this->_process_info['reader']['global']);

        if(isset($this->_process_info['filter']['global']))
            $this->_process_info = array_merge($this->_process_info,$this->_process_info['filter']['global']);

        if(isset($this->_process_info['writer']['global']))
            $this->_process_info = array_merge($this->_process_info,$this->_process_info['writer']['global']);

        if(count($this->_errors)>0)
        {
            $this->_process_info['status']='ERRORS_HAPPENED';
            return;
        };
        if($this->_process_info['reader']['status']=='INITED'
            and $this->_process_info['filter']['status']=='INITED'
            and $this->_process_info['writer']['status']=='INITED')
        {
            $this->_process_info['status']='INITED';
            return;
        };
        if($this->_process_info['reader']['status']=='HAVE_MORE_DATA')
        {
            $this->_process_info['status']='PROCESSING';
            return;
        };

        if($this->_process_info['reader']['status']=='NO_MORE_DATA')
        {
            $this->_process_info['status']='PRE_COMPLETED';
            return;
        };
    }

    function _doWork()
    {
        $bw_reader = &$this->_workers['reader'];
        $bw_filter = &$this->_workers['filter'];
        $bw_writer = &$this->_workers['writer'];

        $data = $bw_reader->doWork();
        $filtered = $bw_filter->doWork($data);
        $bw_writer->doWork($filtered);

        $this->_getWorkersStatus('reader','filter','writer');
        $this->_analizeWorkStatus();
    }

    function _finishWork()
    {
        $this->_fireWorkers('reader','filter','writer');
        $this->_process_info['status'] = 'COMPLETED';
        //$this->_saveState();
    }

    function _breakWork()
    {
        //$this->_saveState();
    }

	/**
	 *                 -
	 */
	function _clearState()
	{
        modApiFunc('Session', 'un_set', 'ConvertWorkerSettings');
        modApiFunc('Session', 'un_set', 'ConvertWorkerProcessInfo');
        $this->_errors=array();
        $this->_warnings=array();
        $this->_messages=array();
        $this->_process_info=array();
        $this->_settings=null;
	}


 	/**
	 *                                             (   time-out  )
	 */
	function _saveState()
	{
        $this->_saveWorkers('reader','filter','writer');

        //:            _savekWorkers();
        unset($this->_process_info['reader'], $this->_process_info['writer'], $this->_process_info['filter']);

    	if (!($this->_settings === NULL))
	    {
            modApiFunc('Session', 'set', 'ConvertWorkerSettings', $this->_settings);
            modApiFunc('Session', 'set', 'ConvertWorkerProcessInfo', $this->_process_info);
        }
        elseif (modApiFunc('Session', 'is_set', 'ConvertWorkerSettings'))
        {
            modApiFunc('Session', 'un_set', 'ConvertWorkerSettings');
            modApiFunc('Session', 'un_set', 'ConvertWorkerProcessInfo');
        }
	}

    /**
     *                                            -                          .
     */
    function _loadState()
    {
        if(modApiFunc('Session', 'is_Set', 'ConvertWorkerSettings'))
        {
            $this->_settings = modApiFunc('Session', 'get', 'ConvertWorkerSettings');
            $this->_process_info = modApiFunc('Session', 'get', 'ConvertWorkerProcessInfo');
        }
        else
        {
            $this->_settings = NULL;
            $this->_process_info = array();
        }

        $this->_createWorkers('reader','filter','writer');

        $this->_errors = array();
        $this->_warnings = array();
        $this->_messages = array();

        $this->_loadWorkers('reader','filter','writer');
    }

	/**
	 *                           ,
	 * @param ref array of strings $warnings -                      ,
     * @param ref array of strings $errors -              ,
	 */
	function _checkState()
	{
	}

	var $_errors;
    var $_warnings;
    var $_messages;
    var $_process_info;

	var $_settings;
    var $_workers;

}


?>