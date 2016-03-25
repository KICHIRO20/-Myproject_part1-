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

class CCacheDriverSingleFile extends CCacheDriverRAM implements ICacheDriver
{
    protected $_dir_storage;
    protected $_file_storage;
    protected $_modified = false;

    public function __construct($dir_storage, $cache_file_label)
    {
        parent::__construct();
        $this->_dir_storage = $dir_storage;
        if ( !file_exists($dir_storage) && !mkdir($dir_storage, 0755, true) ) {
            throw new Exception("Failed to create cache storage directory [$dir_storage].");
        }
        if ( !is_dir($dir_storage) || !is_writable($dir_storage) ) {
	    _fatal(array("CODE"=>"CORE_1002","MESSAGE"=>"The [$dir_storage] is not writable directory. Avactis needs valid and writable directory to store cache items."));
            throw new Exception("The [$dir_storage] is not writable directory. ".__CLASS__.' needs valid and writable directory to store cache items.');
        }
        $this->_file_storage = $dir_storage . '/' . $cache_file_label . '.single.cache';
        $this->_stat['io_read'] = 0;
        $this->_stat['io_write'] = 0;
        $this->_stat['io_read_time'] = 0;
        $this->_stat['io_write_time'] = 0;
        $this->_loadFile();
    }

    public function shutdown()
    {
        $this->_updateFile();
    }

    public function save(CCacheItem $cache_item)
    {
        $this->_modified = true;
        parent::save($cache_item);
    }

    public function delete($id)
    {
        $this->_modified = true;
        parent::delete($id);
    }

    protected function _loadFile()
    {
        $this->_stat['io_read']++;
        $start = microtime(true);
        CProfiler::ioStart($this->_file_storage, 'read');
        if (file_exists($this->_file_storage) && is_readable($this->_file_storage) && false !== ($content=file_get_contents($this->_file_storage))) {
            $this->_data = $this->_decodeData($content);
        }
        CProfiler::ioStop();
        $end = microtime(true);
        $this->_stat['io_read_time'] += ($end - $start);
    }

    protected function _updateFile()
    {
        if ($this->_modified == false) {
            return;
        }

        $this->_stat['io_write']++;
        $start = microtime(true);
        CProfiler::ioStart($this->_file_storage, 'write');
        file_put_contents($this->_file_storage, $this->_encodeData($this->_data), LOCK_EX);
        CProfiler::ioStop();
        $end = microtime(true);
        $this->_stat['io_write_time'] += ($end - $start);
    }

    protected function _encodeData($data)
    {
        $d = serialize($data);
        if (function_exists('gzdeflate')) {
            $d = gzdeflate($d, 2);
        }
        return $d;
    }

    protected function _decodeData($data)
    {
        if (function_exists('gzinflate') && false === ($data = gzinflate($data)) ) {
            return array();
        }
        if ( false !== ($v = @unserialize($data)) ) {
            return $v;
        }
        else {
            return array();
        }
    }

    public function getStat($format = 'txt')
    {
        if ($format == 'txt') {
            $pstat = parent::getStat($format);
            $str  = "D: ";
            $str .= sprintf("io_w: %s\t", str_pad($this->_stat['io_write'], 3, ' ', STR_PAD_RIGHT)    );
            $str .= sprintf("io_r: %s\t", str_pad($this->_stat['io_read'], 3, ' ', STR_PAD_RIGHT)    );
            $str .= sprintf("io_wt: %s\t", number_format(round($this->_stat['io_write_time'],3), 2)    );
            $str .= sprintf("io_rt: %s\t", number_format(round($this->_stat['io_read_time'],3), 2)    );
            return $pstat .$str."\n";
        }
        else {
            return $this->_stat;
        }
    }

}


