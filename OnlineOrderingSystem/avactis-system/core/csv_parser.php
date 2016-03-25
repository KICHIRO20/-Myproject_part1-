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

define('CSV_WRITER_ENABLE_BUFFERING', 'CSV_WRITER_ENABLE_BUFFERING');


/**
 * Class CSV_Parcer provides interface for parsing the .csv files
 *
 * @package Core
 * @access  public
 * @author Egor V. Derevyankin
 */
class CSV_Parser
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    function CSV_Parser()
    {}

    /**
     * Parse .csv file.
     *
     * @example layout and data, relative path
     *      $parser = new CSV_Parser();
     *      list($layout,$data) = $parser->parse_file('example.csv');
     *
     * @example only data, absolute path
     *      $parser = new CSV_Parser();
     *      $data = $parser->parse_file('/home/user/data/example.csv',false);
     *
     * @param $fname - path to the .csv file (absolute or relative)
     * @param $delimetr - CSV delimetr (',' or ';' or '\t')
     * @param $read_layout - true if the first line of file contains the fields names
     * @return array of the data from the .csv file
     *      if $read_layout is true then also return fields layout
     *      if $read_layout is true then return the associative arrays
     *      if $read_layout is false then return the numeric arrays
     */
    function parse_file($fname,$delimetr=";",$read_layout=true)
    {
        if(!file_exists($fname) or !is_readable($fname))
            return false;

        if(!in_array($delimetr,array(",",";","\t")))
            return false;

        $csv_data=array();
        CProfiler::ioStart($fname, 'read');
        $fh=@fopen($fname,"r");
        if($fh!=false)
        {
            if($read_layout)
            {
                $layout=fgetcsv($fh, 262144, $delimetr);
                $layout = convertImportDataArray($layout);
            }
            while(!feof($fh))
            {
                $data=fgetcsv($fh, 262144, $delimetr);
                // skipping empty strings...
                if (empty($data) || (count($data) == 1 && $data[0] === null))
                    continue;
                $data = convertImportDataArray($data);
                $tmp=array();
                if($read_layout)
                    for($i=0;$i<count($layout);$i++)
                    {
                        if (isset($data[$i]))
                            $tmp[_ml_strtolower($layout[$i])]=$data[$i];
                        else
                            $tmp[_ml_strtolower($layout[$i])] = '';
                    }
                else
                    $tmp=$data;
                $csv_data[]=$tmp;
            };
            fclose($fh);
            CProfiler::ioStop();
            if($read_layout)
                return array($layout,$csv_data);
            else
                return $csv_data;

        };
        CProfiler::ioStop();

        return false;
    }

    function parse_import_file($fname, $delimetr = ";")
    {
        if (!file_exists($fname) or !is_readable($fname))
            return false;

        if (!in_array($delimetr, array(",",";","\t")))
            return false;

        CProfiler::ioStart();
        $fh = @fopen($fname,"r");
        CProfiler::ioStop();
        if ($fh == FALSE)
            return false;

        CProfiler::ioStart($fname, 'read');
        $layout = fgetcsv($fh, 262144, $delimetr);
        $layout = convertImportDataArray($layout);
//!!!!!
        foreach($layout as $name => $value)
        {
//            echo("!layout: name = $name; value = $value!<br>");
        }
//!!!!!

        $csv_data = array();
        while(!feof($fh))
        {
            $data = fgetcsv($fh, 262144, $delimetr);
            $data = convertImportDataArray($data);
            foreach($data as $name => $value)
            {
 //               echo("!data: name = $name; value = $value!<br>");
            }

            $tmp = array();
            for ($i = 0; $i < count($layout); $i++)
            {
//                echo("<i>layout[$i] = $layout[$i]</i> - ");
  //              echo("data[$i] = $data[$i]<br>");
                $tmp[$layout[$i]] = $data[$i];
            }
            $csv_data[] = $tmp;
        };

        fclose($fh);
        CProfiler::ioStop();

        return array($layout,$csv_data);
    }


};

/**
 * Class CSV_Writer provides interface for writing data to .csv files
 *
 * @example
 *
 * $data_to_writing=array(
 *      array('key' => 'k1', 'value' => 'v1'),
 *      array('key' => 'k2', 'value' => 'v3'),
 *      array('key' => 'k3', 'value' => 'v3'),
 * );
 *
 * $csv_writer = new CSV_Writer();
 * $csv_writer->setOutFile('example.csv');
 * $csv_writer->setLayout(array_keys($data_to_writing[0]));
 * $csv_writer->setDelimetr(';');
 * $csv_writer->writeLayout();
 * $csv_writer->writeData($data_to_writing);
 * $csv_writer->closeOutFile();
 *
 *
 * $data_to_append=array(
 *      array('key' => 'k4', 'value' => 'v4'),
 *      array('key' => 'k5', 'value' => 'v5'),
 * );
 *
 * $csv_writer->setOutFile('example.csv','a');
 * $csv_writer->writeData($data_to_append);
 * $csv_writer->closeOutFile();
 *
 * @package Core
 * @access public
 * @author Egor V. Derevyankin
 */
class CSV_Writer
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    var $buffering = false;
    var $out_file_name = '';
    var $out_file_mode = 'w';
    var $buffer = '';

    function CSV_Writer($buffering = false)
    {
        $this->buffering = ($buffering === CSV_WRITER_ENABLE_BUFFERING);
        $this->out_file=NULL;
        $this->csv_layout=array();
        $this->csv_delimetr=",";
        $this->_nl="\n";
    }

    /**
     * Sets and opens the output file.
     *
     * @param $file_path path to the output file
     * @param $open_mode mode for file open ('w' - write, 'a' - append)
     * @return TRUE on success or FALSE on failure
     */
    function setOutFile($file_path,$open_mode="w")
    {
        if(!in_array($open_mode,array("w","a")))
            return false;

        if ($this->buffering === true)
        {
            $this->out_file_name = $file_path;
            $this->out_file_mode = $open_mode;
            return true;
        }

        $this->out_file=@fopen($file_path,$open_mode);

        if($this->out_file==false)
        {
            $this->out_file = null;
            return false;
        }
        else
            return true;
    }

    /**
     * Sets the output layout.
     *
     * @param $layout array with the field names
     * @return TRUE on success or FALSE on failure
     */
    function setLayout($layout)
    {
        if(is_array($layout) and !empty($layout))
        {
            $this->csv_layout=array_values($layout);
            return true;
        }
        else
            return false;
    }

    /**
     * Sets the CSV-delimeter.
     *
     * @param $delimetr CSV-delimetr (',' or ';' or '\t')
     * @return TRUE on success or FALSE on failure
     */
    function setDelimetr($delimetr)
    {
        if(!in_array($delimetr,array(",",";","\t")))
            return false;
        else
        {
            $this->csv_delimetr=$delimetr;
            return true;
        }
    }

    /**
     * Sets the new line symbol for the output file.
     * The default value is '\n' (unix systems).
     * Values '\n\r' (windows systems) and '\r\n' (mac systems) are also possible.
     *
     * @param $nls type of the new line symbol ('nix','win' or 'mac')
     */
    function setNewLineType($nls)
    {
        $NLT=array("nix"=>"\n","win"=>"\r\n","mac"=>"\r");
        if(!in_array($nls,array_keys($NLT)))
            return false;

        $this->_nl=$NLT[$nls];
        return true;
    }

    /**
     * Writes layout to the output file.
     */
    function writeLayout()
    {
        $this->writeString($this->prepareCSVstring($this->csv_layout));
    }

    /**
     * Writes an array to the output file.
     * If layout was set up, then the array will be written in accord layout.
     *
     * @param $array array with the values
     */
    function writeArray($array)
    {
        $to_write=array();
        if(!empty($this->csv_layout))
            for($i=0;$i<count($this->csv_layout);$i++)
                $to_write[]=$array[$this->csv_layout[$i]];
        else
            $to_write=$array;

        $this->writeString($this->prepareCSVstring($to_write));
    }

    /**
     * Writes data to the output file.
     *
     * @param $data array with the data
     */
    function writeData($data)
    {
        for($i=0;$i<count($data);$i++)
            $this->writeArray($data[$i]);
    }

    /**
     * Closes the output file.
     */
    function closeOutFile()
    {
        if($this->out_file != null)
        {
            fclose($this->out_file);
            $this->out_file = null;
        };
    }

    /**
     * Converts the array to the CSV-string.
     *
     * @param $array array with values
     * @return CSV-string
     */
    function prepareCSVstring($array)
    {
        for($i=0;$i<count($array);$i++)
        {
            if(is_array($array[$i]))
                $array[$i] = "Array";
            elseif(is_object($array[$i]))
                $array[$i] = "Object";
            else
            {
                $array[$i] = str_replace("\n", ' ', $array[$i]);
                $array[$i] = str_replace("\r", ' ', $array[$i]);

                if (strstr($array[$i], '"') !== false
                    || strstr($array[$i], $this->csv_delimetr) !== false
                    || _ml_substr($array[$i], 0, 1) == ' '
                    || _ml_substr($array[$i], -1) == ' ')
                    $array[$i]='"' . str_replace('"', '""', $array[$i]) . '"';
            }
        }
        return implode($this->csv_delimetr,$array);
    }

    function getBuffer()
    {
        return $this->buffer;
    }

    function flush()
    {
        if ($h = @fopen($this->out_file_name, $this->out_file_mode))
        {
            if (@fwrite($h, convertExportData($this->buffer)))
            {
                @fclose($h);
                $this->buffer = '';
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }

//------------------------------------------------
//               PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    function writeString($string)
    {
        if ($this->buffering === true)
        {
            $this->buffer .= $string.$this->_nl;
        }
        else
        {
            fwrite($this->out_file,convertExportData($string).$this->_nl);
        }
    }

    var $out_file;
    var $csv_layout;
    var $csv_delimetr;
    var $_nl;
}


class CSV_Dynamic_Writer
{
    function CSV_Dynamic_Writer($uid)
    {
        $this->__uid = md5($uid);
        $this->__cache = CCacheFactory::getCache('persistent', $this->__uid);

        if ( null === ($this->__csv_headers = $this->__cache->read('headers')) )
        {
            $this->__csv_headers = array();
        }

        if ( null === ($this->__counter = $this->__cache->read('counter')) )
        {
            $this->__counter = 1;
        }
        $this->csv_delimitr = ';';
    }

    function add($map)
    {
        $this->__csv_headers = array_unique(array_merge($this->__csv_headers, array_keys($map)));
        $this->__cache->write('headers', $this->__csv_headers);

        if (null === ($current_item = $this->__cache->read('data_'.$this->__counter)))
        {
            $this->__cache->write('data_'.$this->__counter, array($map));
        }
        else
        {
            if (count($current_item) >= $this->__chunk_max_size)
            {
                $this->__counter++;
                $this->__cache->write('data_'.$this->__counter, array($map));
                $this->__cache->write('counter', $this->__counter);
            }
            else
            {
                $current_item[] = $map;
                $this->__cache->write('data_'.$this->__counter, $current_item);
            }
        }
    }

    function addArray($mapArray)
    {
        if (!is_array($mapArray))
            return;

        foreach($mapArray as $map)
            $this -> add($map);
    }

    function flush()
    {
        if ($this->__out_file_path == null)
        {
            return false;
        }

        $csv_writer = new CSV_Writer();
        $csv_writer->setOutFile($this->__out_file_path);
        $csv_writer->setLayout($this->__csv_headers);
        $csv_writer->setDelimetr($this->csv_delimetr);
        $csv_writer->writeLayout();

        for ($i=1; $i<=$this->__counter; $i++)
        {
            if ( null === ($data = $this->__cache->read('data_'.$i)))
            {
                continue;
            }
            $this->__fillMap($data);
            $csv_writer->writeData($data);
            $this->__cache->erase('data_'.$i);
        }
        $this->__cache->erase('headers');
        $this->__cache->erase('counter');
        $csv_writer->closeOutFile();
        return true;
    }

    function setOutFile($file_path)
    {
        if ($this->__checkFilePermissions($file_path) == true)
        {
            $this->__out_file_path = $file_path;
            return true;
        }
        else
        {
            return false;
        }
    }

    function clear()
    {
        for ($i=1; $i<=$this->__counter; $i++)
        {
            $this->__cache->erase('data_'.$i);
        }
        $this->__cache->erase('headers');
        $this->__cache->erase('counter');
    }

    /**
     * Sets the CSV-delimeter.
     *
     * @param $delimetr CSV-delimetr (',' or ';' or '\t')
     * @return TRUE on success or FALSE on failure
     */
    function setDelimetr($delimetr)
    {
        if(!in_array($delimetr,array(",",";","\t")))
            return false;
        else
        {
            $this->csv_delimetr=$delimetr;
            return true;
        }
    }

    function __fillMap(&$data_list)
    {
        foreach ($data_list as $k=>$data)
        {
            foreach ($this->__csv_headers as $h)
            {
                if (!isset($data_list[$k][$h]))
                {
                    $data_list[$k][$h] = '';
                }
            }
        }
    }

    function __checkFilePermissions($out_file_path)
    {
        if ($out_file_path == null or empty($out_file_path))
        {
            return false;
        }

        if (file_exists($out_file_path))
        {
            $file_to_check = $out_file_path;
        }
        else
        {
            $file_to_check = dirname($out_file_path);
        }

        if (is_readable($file_to_check) && is_writable($file_to_check))
        {
            return true;
        }
        else
        {
            return false;
        }
    }


    var $__out_file_path = null;
    var $__uid = null;
    var $__csv_headers = array();
    var $__cache = null;
    var $__counter = 1;
    var $__chunk_max_size = 50;
    var $csv_delimetr;
}

?>