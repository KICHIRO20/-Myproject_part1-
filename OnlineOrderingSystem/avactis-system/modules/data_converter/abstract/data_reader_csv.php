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
 * @package Data_Converter
 * @author Egor V. Derevyankin
 *
 */

loadClass('DataReaderDefault');

class DataReaderCSV extends DataReaderDefault
{
    function DataReaderCSV()
    {
        global $application;
        $this->MR = &$application->getInstance('MessageResources','data-converter-messages','AdminZone');
    }

    function initWork($settings)
    {
        $this->_settings = array(
            'src_file' => $settings['src_file']
           ,'csv_delimiter' => isset($settings['csv_delimiter']) ? $settings['csv_delimiter'] : 'DETECT'
           ,'header_rx' => isset($settings['header_rx']) ? $settings['header_rx'] : CSV_HEADER_RX
           ,'file_position' => 0
           ,'lines_count' => 0
           ,'items_processing' => 0
        );

        $this->_open_src_file();
        if($this->_settings['csv_delimiter']=='DETECT')
            $this->_detect_delimiter();
        $this->_calc_lines_count();
        $this->_fetch_headers();

        $this->_process_info['status'] = 'INITED';
        $this->_process_info['items_count'] = $this->_settings['lines_count'] - 2;
        $this->_process_info['items_processing'] = 0;

        return $this->_csv_headers;
    }

    function doWork()
    {
        $data = $this->_fetch_data();
        $this->_process_info['items_count'] = $this->_settings['lines_count'] - 2;

        if($data != null)
            $this->_settings['items_processing']++;

        $this->_process_info['items_processing'] = $this->_settings['items_processing'];

        if($this->_settings['items_processing'] == ($this->_settings['lines_count'] - 2))
        {
            $this->_process_info['status'] = 'NO_MORE_DATA';
        }

        return array(
            'item_number' => $this->_settings['items_processing']
           ,'item_data' => $data
        );

    }

    function finishWork()
    {
        $this->_close_src_file();
    }

    function saveWork()
    {
        modApiFunc('Session','set','DataReaderCSVSettings',$this->_settings);
        modApiFunc('Session','set','DataReaderCSVHeaders',$this->_csv_headers);
        $this->_close_src_file();
    }

    function loadWork()
    {
        if(modApiFunc('Session','is_set','DataReaderCSVSettings'))
            $this->_settings = modApiFunc('Session','get','DataReaderCSVSettings');
        else
            $this->_settings = null;

        if(modApiFunc('Session','is_set','DataReaderCSVHeaders'))
            $this->_csv_headers = modApiFunc('Session','get','DataReaderCSVHeaders');
        else
            $this->_csv_headers = null;

        $this->_open_src_file();
	}

    function clearWork()
    {
        modApiFunc('Session','un_set','DataReaderCSVSettings');
        modApiFunc('Session','un_set','DataReaderCSVHeaders');
        $this->_settings = null;
        $this->_src_file_handler = null;
        $this->_csv_headers = null;
    }

    function _open_src_file()
    {
        if($this->_settings != null)
        {
            if(!is_string($this->_settings['src_file']) or $this->_settings['src_file']=='')
            {
                $this->_errors = $this->MR->getMessage('CANT_OPEN_FILE_01');//'can not open file. source not defined';
                $this->_src_file_handler = null;
                return;
            };
            if(!file_exists($this->_settings['src_file']) or !is_file($this->_settings['src_file']))
            {
                $this->_errors = $this->MR->getMessage('CANT_OPEN_FILE_02');//'can not open file. source is not file';
                $this->_src_file_handler = null;
                return;
            };
            if(!is_readable($this->_settings['src_file']))
            {
                $this->_errors = $this->MR->getMessage('CANT_OPEN_FILE_03');//'can not open file. source is not readable';
                $this->_src_file_handler = null;
                return;
            };
            if(filesize($this->_settings['src_file'])==0)
            {
                $this->_errors = $this->MR->getMessage('CANT_OPEN_FILE_04');//'can not open file. source is empty';
                $this->_src_file_handler = null;
                return;
            };

            if(asc_detect_eol($this->_settings['src_file'])=="\r")
                asc_mac2nix($this->_settings['src_file']);

            $this->_src_file_handler = fopen($this->_settings['src_file'],'r');
            fseek($this->_src_file_handler,$this->_settings['file_position']);
        }
        else
        {
            $this->_errors = $this->MR->getMessage('CANT_OPEN_FILE_01');//'can not open file. source not defined';
            $this->_src_file_handler = null;
        };
    }

    function _close_src_file()
    {
        if($this->_src_file_handler != null)
        {
            fclose($this->_src_file_handler);
            $this->_src_file_handler = null;
        };
    }

    function _fetch_line_as_array()
    {
        if($this->_src_file_handler == null)
        {
            $this->_errors = $this->MR->getMessage('CANT_FETCH_LINE_01');//'can not fetch line. file not opened';
            return null;
        };

        if(feof($this->_src_file_handler))
        {
            $this->_process_info['status'] = 'NO_MORE_DATA';
            return null;
        };

        $data_array = convertImportDataArray(fgetcsv($this->_src_file_handler, 262144, $this->_settings['csv_delimiter']));
        $this->_settings['file_position'] = ftell($this->_src_file_handler);

        if(feof($this->_src_file_handler))
        {
            $this->_process_info['status'] = 'NO_MORE_DATA';
        }
        else
        {
            $this->_process_info['status'] = 'HAVE_MORE_DATA';
        }

        return $data_array;
    }

    function _fetch_headers()
    {
        if($this->_settings['csv_delimiter'] == null)
        {
            $this->_errors = $this->MR->getMessage('CANT_FETCH_HEADERS_01');//'can not fetch headers. csv-delimiter not defined';
            return;
        };

        $this->_csv_headers = $this->_fetch_line_as_array();

        if($this->_csv_headers == null)
        {
            $this->_errors = $this->MR->getMessage('CANT_FETCH_HEADERS_02');//'can not fetch headers.';
            return;
        };

        if(in_array('',$this->_csv_headers))
        {
            $this->_errors = $this->MR->getMessage('DETECTED_EMPTY_HEADERS');//'detect one or more empty record in the headers line.';
        }
    }

    function _fetch_data()
    {
        $raw = $this->_fetch_line_as_array();

        if($raw == null)
            return null;

        $data = array();
        for($i=0;$i<count($this->_csv_headers);$i++)
        {
            if(isset($raw[$i]))
                $data[$this->_csv_headers[$i]]=$raw[$i];
            else
                $data[$this->_csv_headers[$i]]=null;
        };
        return $data;
    }

    function _calc_lines_count()
    {
        if($this->_src_file_handler == null)
        {
            $this->_errors = $this->MR->getMessage('CANT_CALC_LINES_01');//'can not calculate lines count. source not opened';
            $this->_settings['lines_count'] = 0;
            return;
        };
        if($this->_settings['csv_delimiter'] == null)
        {
            $this->_errors = $this->MR->getMessage('CANT_CALC_LINES_02');//'can not calculate lines count. csv-delimiter not defined';
            $this->_settings['lines_count'] = 0;
            return;
        };

        $counter = 0;
        while(!feof($this->_src_file_handler))
        {
            fgetcsv($this->_src_file_handler, 262144, $this->_settings['csv_delimiter']);
            $counter++;
        };

        rewind($this->_src_file_handler);

        $this->_settings['lines_count'] = $counter;
    }

    function _detect_delimiter()
    {
        if($this->_src_file_handler == null)
        {
            $this->_errors = $this->MR->getMessage('CANT_DETECT_DELIMITER');//'can not detect csv-delimiter. source not opened';
            $this->_settings['csv_delimiter'] = null;
            return;
        };

        reset($this->CSV_DELIMITERS);
        do
        {
            $delimiter = current($this->CSV_DELIMITERS);
            $_tmp_arr = convertImportDataArray(fgetcsv($this->_src_file_handler, 262144, $delimiter));
            rewind($this->_src_file_handler);
            if($this->_check_headers_array($_tmp_arr))
            {
                $this->_settings['csv_delimiter'] = $delimiter;
                return;
            };
        }
        while(next($this->CSV_DELIMITERS));

        $this->_errors = $this->MR->getMessage('INVALID_CSV_FORMAT');//'invalid csv file format';
        $this->_settings['csv_delimiter'] = null;
        return;
    }

    function _check_headers_array($arr)
    {
        if(empty($arr))
            return false;

        for($i=0;$i<count($arr);$i++)
            if(!preg_match($this->_settings['header_rx'],$arr[$i]))
                return false;

        return true;
    }

    var $_settings;
    var $_src_file_handler;
    var $_csv_headers;

    var $CSV_DELIMITERS = array(
        ",",";","\t"
    );

};

?>