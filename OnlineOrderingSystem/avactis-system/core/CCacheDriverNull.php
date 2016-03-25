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

class CCacheDriverNull implements ICacheDriver
{
    protected $_stat = array(
        'save' => 0,
        'load' => 0,
        'delete' => 0,
        'hit' => 0,
        'miss' => 0,
    );

    public function __construct()
    {
    }

    public function save(CCacheItem $cache_item)
    {
        assert('is_a($cache_item, "CCacheItem")');
        assert('is_scalar($cache_item->id);');
        assert('is_int($cache_item->expired);');
        assert('($cache_item->expired == 0) || ($cache_item->expired >0 && $cache_item->expired > time())');
        assert('(is_scalar($cache_item->value) || is_array($cache_item->value))');
        assert('is_int($cache_item->hash) || is_string($cache_item->hash)');
        assert('is_array($cache_item->dependencies)');
        assert('is_int($cache_item->dependencies_hash) || is_string($cache_item->dependencies_hash)');
        $this->_stat['save']++;
    }

    public function load($id)
    {
        $this->_stat['load']++;
        $this->_stat['miss']++;
        return null;
    }

    public function delete($id)
    {
        $this->_stat['delete']++;
    }

    public function getStat($format = 'txt')
    {
        if ($format == 'txt') {
            $str  = "D: ";
            $str .= sprintf("save: %s\t", str_pad($this->_stat['save'], 3, ' ', STR_PAD_RIGHT) );
            $str .= sprintf("load: %s\t", str_pad($this->_stat['load'], 3, ' ', STR_PAD_RIGHT));
            $str .= sprintf("hit: %s\t", str_pad($this->_stat['hit'], 3, ' ', STR_PAD_RIGHT));
            $str .= sprintf("miss: %s\t", str_pad($this->_stat['miss'], 3, ' ', STR_PAD_RIGHT));
            $str .= sprintf("delete: %s\n", str_pad($this->_stat['delete'], 3, ' ', STR_PAD_RIGHT));
            return $str;
        }
        else {
            return $this->_stat;
        }
    }

    public function shutdown()
    {

    }

}


