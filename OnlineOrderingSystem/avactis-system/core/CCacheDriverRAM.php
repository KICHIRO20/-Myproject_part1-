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

class CCacheDriverRAM extends CCacheDriverNull implements ICacheDriver
{
    protected $_data = array();
    protected $_stat = array(
        'save' => 0,
        'load' => 0,
        'delete' => 0,
        'hit' => 0,
        'miss' => 0,
    );

    public function __construct()
    {
        $this->_data = array();
    }

    public function save(CCacheItem $cache_item)
    {
/*      assert('is_a($cache_item, "CCacheItem")');
        assert('is_scalar($cache_item->id);');
        assert('is_int($cache_item->expired);');
        assert('($cache_item->expired == 0) || ($cache_item->expired >0 && $cache_item->expired > time())');
        assert('is_int($cache_item->hash) || is_string($cache_item->hash)');
        assert('is_array($cache_item->dependencies)');
        assert('is_int($cache_item->dependencies_hash) || is_string($cache_item->dependencies_hash)');*/
        $this->_stat['save']++;
        $this->_data[$cache_item->id] = $cache_item;
    }

    public function load($id)
    {
        $this->_stat['load']++;
		/*      assert('is_a($cache_item, "CCacheItem")');
        	    assert('is_scalar($cache_item->id);');
            	assert('is_int($cache_item->expired);');
            	assert('is_int($cache_item->hash) || is_string($cache_item->hash)');
            	assert('is_array($cache_item->dependencies)');
            	assert('is_int($cache_item->dependencies_hash) || is_string($cache_item->dependencies_hash)'); */
        if (isset($this->_data[$id]))
        	{
        		$this->_stat['hit']++;
        		$cache_item = $this->_data[$id];
        		return $cache_item;
        	}
        		else
        	{
            	$this->_stat['miss']++;
            	return null;
        	}
    }

    public function delete($id)
    {
        $this->_stat['delete']++;
        unset($this->_data[$id]);
    }

    public function getStat($format='txt')
    {
        return parent::getStat($format);
    }
}