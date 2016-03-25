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

class CCacheDriverFile extends CCacheDriverRAM implements ICacheDriver
{
    protected $_dir_storage;

    public function __construct($dir_storage)
    {
        parent::__construct();
        $this->_dir_storage = $dir_storage;
        if ( !file_exists($dir_storage) && !mkdir($dir_storage, 0755, true) ) {
            throw new Exception("Failed to create cache storage directory [$dir_storage].");
        }
        if ( !is_dir($dir_storage) || !is_writable($dir_storage) ) {
            throw new Exception("The [$dir_storage] is not writable directory. ".__CLASS__.' needs valid and writable directory to store cache items.');
        }
        $s = "abcdef0123456789";
        for ($i=0; $i< strlen($s); $i++) {
            $subdir = $dir_storage . $s[$i];
            if (!file_exists($subdir) && !mkdir($subdir)) {
                throw new Exception("Failed to create subdirectory [$subdir].");
            }
        }
        $this->_stat['io_unlink'] = 0;
        $this->_stat['io_read'] = 0;
        $this->_stat['io_write'] = 0;
        $this->_stat['io_read_time'] = 0;
        $this->_stat['io_write_time'] = 0;
        $this->_stat['io_unlink_time'] = 0;
        $this->_stat['err_file_get_contents'] = 0;
        $this->_stat['err_file_put_contents'] = 0;
        $this->_stat['err_gzinflate'] = 0;
        $this->_stat['err_unserialize'] = 0;
    }

    public function save(CCacheItem $cache_item)
    {
        parent::save($cache_item);
        $this->_saveFile($this->_getFilenameByKey($cache_item->id), $cache_item);
    }

    public function load($id)
    {
        $filename = $this->_getFilenameByKey($id);
        if ( !isset($this->_data[$id]) && file_exists($filename) && null !== ($cache_item=$this->_loadFile($filename)) ) {
            $this->_data[$id] = $cache_item;
        }
        return parent::load($id);
    }

    public function delete($id)
    {
        $this->_stat['io_unlink']++;
        CProfiler::ioStart($this->_getFilenameByKey($id), 'delete');
        $start = microtime(true);
        @unlink($this->_getFilenameByKey($id));
        $end = microtime(true);
        $this->_stat['io_unlink_time'] += ($end - $start);
        CProfiler::ioStop();
        parent::delete($id);
    }

    protected function _getFilenameByKey($id)
    {
        $name = sha1($id);
        $subdir = $name[0];
        return $this->_dir_storage . $subdir . '/' . sha1($id) . '.cache';
    }

    protected function _loadFile($filename)
    {
        $this->_stat['io_read']++;
        $start = microtime(true);
        CProfiler::ioStart($filename, 'read');
        if (false !== ($content=file_get_contents($filename))) {
            $result = $this->_decodeData($content);
        }
        else {
            $this->_stat['err_file_get_contents']++;
            $result = null;
        }
        CProfiler::ioStop();
        $end = microtime(true);
        $this->_stat['io_read_time'] += ($end - $start);
        return $result;
    }

    protected function _saveFile($filename, $data)
    {
        $this->_stat['io_write']++;
        $start = microtime(true);
        CProfiler::ioStart($filename, 'write');
        if (false === file_put_contents($filename, $this->_encodeData($data), LOCK_EX)) {
            $this->_stat['err_file_put_contents']++;
        }
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
            $this->_stat['err_gzinflate']++;
            return null;
        }
        if ( false !== ($v = @unserialize($data)) ) {
            return $v;
        }
        else {
            $this->_stat['err_unserialize']++;
            return null;
        }
    }

    public function getStat($format = 'txt')
    {
        if ($format == 'txt') {
            $pstat = parent::getStat($format);
            $str  = "D: ";
            $str .= sprintf("io_w: %s\t", str_pad($this->_stat['io_write'], 3, ' ', STR_PAD_RIGHT)    );
            $str .= sprintf("io_r: %s\t", str_pad($this->_stat['io_read'], 3, ' ', STR_PAD_RIGHT)    );
            $str .= sprintf("io_u: %s\t", str_pad($this->_stat['io_unlink'], 3, ' ', STR_PAD_RIGHT)    );
            $str .= sprintf("io_wt: %s\t", number_format(round($this->_stat['io_write_time'],3), 2)    );
            $str .= sprintf("io_rt: %s\t", number_format(round($this->_stat['io_read_time'],3), 2)    );
            $str .= sprintf("io_ut: %s\t", number_format(round($this->_stat['io_unlink_time'],3), 2)    );
            $str .= "\n";
            $str .= "D: ";
            $str .= sprintf("err_r: %d\t", $this->_stat['err_file_get_contents']);
            $str .= sprintf("err_w: %d\t", $this->_stat['err_file_put_contents']);
            $str .= sprintf("err_gzinflate: %d\t", $this->_stat['err_gzinflate']);
            $str .= sprintf("err_unserialize: %d\t", $this->_stat['err_unserialize']);
            return $pstat .$str."\n";
        }
        else {
            return $this->_stat;
        }
    }

}


