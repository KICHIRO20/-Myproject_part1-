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
 * @author Egor V. Derevyankin
 *
 */

loadClass('DataWriterDefault');
loadCoreFile('csv_parser.php');

class DataWriterCSV extends DataWriterDefault
{
    function DataWriterCSV()
    {
    }

    function initWork($settings)
    {
        $this->clearWork();

        $this->_settings = array(
            'out_file' => $settings['out_file']
           ,'headers'  => $settings['headers']
           ,'csv_delimiter' => $settings['csv_delimiter']
        );

        $this->_csv_worker = new CSV_Writer();
        $this->_csv_worker->setOutFile($this->_settings['out_file'],'w');
        $this->_csv_worker->setLayout($this->_settings['headers']);
        $this->_csv_worker->setDelimetr($this->_settings['csv_delimiter']);
        $this->_csv_worker->writeLayout();
        $this->_csv_worker->closeOutFile();

        $this->_process_info['status'] = 'INITED';
    }

    function doWork($data)
    {
        if($this->_csv_worker != null)
            $this->_csv_worker->writeData(array($data));
    }

    function finishWork()
    {
        $this->_csv_worker->closeOutFile();
    }

    function saveWork()
    {
        modApiFunc('Session','set','DataWriterCSVsettings',$this->_settings);
        $this->_csv_worker->closeOutFile();
    }

    function loadWork()
    {
        if(modApiFunc('Session','is_set','DataWriterCSVsettings'))
        {
            $this->_settings = modApiFunc('Session','get','DataWriterCSVsettings');
            $this->_csv_worker = new CSV_Writer();
            $this->_csv_worker->setOutFile($this->_settings['out_file'],'a');
            $this->_csv_worker->setLayout($this->_settings['headers']);
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