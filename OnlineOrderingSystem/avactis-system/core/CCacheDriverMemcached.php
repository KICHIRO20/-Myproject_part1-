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

class CCacheDriverMemcached extends CCacheDriverRAM implements ICacheDriver
{
    protected $_memcache;

    public function __construct($host, $port)
    {
        parent::__construct();
        $this->_stat['io_unlink'] = 0;
        $this->_stat['io_read'] = 0;
        $this->_stat['io_write'] = 0;
        $this->_stat['io_read_time'] = 0;
        $this->_stat['io_write_time'] = 0;
        $this->_stat['io_unlink_time'] = 0;
        $this->_memcache = new Memcache;
        if ($this->_memcache->connect($host, $port) === false) {
            throw new Exception('Failed to connect to the memcached server');
        }
    }

    public function save(CCacheItem $cache_item)
    {
        $this->_stat['io_write']++;
        $start = microtime(true);
        parent::save($cache_item);
        $res = $this->_memcache->set(sha1($cache_item->id), $this->_encodeData($cache_item), MEMCACHE_COMPRESSED, $cache_item->expired);
        $end = microtime(true);
        $this->_stat['io_write_time'] += ($end - $start);
    }

    public function load($id)
    {
        if (!isset($this->_data[$id])) { //      check errors
            $this->_stat['io_read']++;
            $start = microtime(true);
            $res = $this->_memcache->get(sha1($id));
            $end = microtime(true);
            $this->_stat['io_read_time'] += ($end - $start);
            if ($res !== false) {
                $this->_data[$id] = $this->_decodeData($res);
            }
        }
        return parent::load($id);
    }

    public function delete($id)
    {
        $this->_stat['io_unlink']++;
        $start = microtime(true);
        $res = $this->_memcache->delete(sha1($id), 0);
        $end = microtime(true);
        $this->_stat['io_unlink_time'] += ($end - $start);
        parent::delete($id);
    }

    protected function _encodeData($data)
    {
        return serialize($data);
    }

    protected function _decodeData($data)
    {
        return unserialize($data);
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
            return $pstat .$str."\n";
        }
        else {
            return $this->_stat;
        }
    }

}


