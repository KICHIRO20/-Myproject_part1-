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

class CCacheDriverAPC extends CCacheDriverRAM implements ICacheDriver
{

    public function __construct()
    {
        parent::__construct();
        $this->_stat['io_unlink'] = 0;
        $this->_stat['io_read'] = 0;
        $this->_stat['io_write'] = 0;
        $this->_stat['io_read_time'] = 0;
        $this->_stat['io_write_time'] = 0;
        $this->_stat['io_unlink_time'] = 0;
        if (APC_EXTENSION_LOADED === false) {
            throw new Exception('Failed to cache with APC. Please check if APC is enabled in phpinfo');
        }
    }

    public function save(CCacheItem $cache_item)
    {
        $this->_stat['io_write']++;
        $start = microtime(true);
        parent::save($cache_item);
        apc_store($cache_item->id,$cache_item,$cache_item->expired);
        $end = microtime(true);
        $this->_stat['io_write_time'] += ($end - $start);
    }

    public function load($id)
    {
        if (!isset($this->_data[$id])) { //      check errors
            $this->_stat['io_read']++;
            $start = microtime(true);
            $cache_item = apc_fetch($id,$success);
            $end = microtime(true);
            $this->_stat['io_read_time'] += ($end - $start);

            if ($success===true) {
                $this->_data[$id] = $cache_item;
            }
        }
        return parent::load($id);
    }

    public function delete($id)
    {
        $this->_stat['io_unlink']++;
        $start = microtime(true);
        apc_delete($id);
        $end = microtime(true);
        $this->_stat['io_unlink_time'] += ($end - $start);
        parent::delete($id);
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