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
 * @package DataConverter
 * @author Alexey Florinsky
 *
 */

loadClass('DataWriterDefault');
loadCoreFile('csv_parser.php');

class DataWriterDynamicCSV extends DataWriterDefault
{
    function DataWriterDynamicCSV()
    {
    }

    function initWork($settings)
    {
        $this->clearWork();

        $this->_settings = array(
            'out_file' => $settings['out_file']
           ,'csv_delimiter' => $settings['csv_delimiter']
           ,'use_bulks' => ((isset($settings['use_bulks'])) ? $settings['use_bulks'] : false)
        );

        $this->_csv_worker = new CSV_Dynamic_Writer(session_id());
        $this->_csv_worker->setOutFile($this->_settings['out_file']);
        $this->_csv_worker->setDelimetr($this->_settings['csv_delimiter']);
        $this->_csv_worker->clear();

        $this->_process_info['status'] = 'INITED';
    }

    function doWork($data)
    {
        if($this->_csv_worker != null)
            (($this -> _settings['use_bulks']) ? $this->_csv_worker->addArray($data) : $this->_csv_worker->add($data));
    }

    function finishWork()
    {
        $this->_csv_worker->flush();
    }

    function saveWork()
    {
        modApiFunc('Session','set','DataWriterCSVsettings',$this->_settings);
    }

    function loadWork()
    {
        if(modApiFunc('Session','is_set','DataWriterCSVsettings'))
        {
            $this->_settings = modApiFunc('Session','get','DataWriterCSVsettings');
            $this->_csv_worker = new CSV_Dynamic_Writer(session_id());
            $this->_csv_worker->setOutFile($this->_settings['out_file']);
            $this->_csv_worker->setDelimetr($this->_settings['csv_delimiter']);
            return;
        };

        $this->_settings = null;
        $this->_csv_worker = null;
    }

    function clearWork()
    {
        modApiFunc('Session','un_set','DataWriterCSVsettings');
        $this->_settings = null;
        $this->_csv_worker = null;
    }


    var $_settings;
    var $_csv_worker;
};

?>