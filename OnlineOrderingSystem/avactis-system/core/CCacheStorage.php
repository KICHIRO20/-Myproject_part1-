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


class CCacheStorage implements ICacheStorage
{
    protected $_driver;
    protected $_stat = array();

    public function __construct(ICacheDriver $driver)
    {
        $this->_driver = $driver;

        $this->_stat['test_succ'] = 0;
        $this->_stat['test_fail'] = 0;
        $this->_stat['read_succ'] = 0;
        $this->_stat['read_fail'] = 0;
        $this->_stat['write_succ'] = 0;
        $this->_stat['write_fail'] = 0;
        $this->_stat['add_succ'] = 0;
        $this->_stat['add_fail'] = 0;
        $this->_stat['erase'] = 0;
        $this->_stat['err_checkHash'] = 0;
        $this->_stat['err_checkExpire'] = 0;
        $this->_stat['err_checkDependencies'] = 0;
        $this->_stat['log'] = array();
        $this->_stat['read_time'] = 0;
    }

    public function test($id)
    {
        if (null == ($cache_item = $this->_driver->load($id))) {
            $this->_stat['test_fail']++;
            return false;
        }

        if ($this->_checkHash($cache_item) && $this->_checkExpire($cache_item) && $this->_checkDependencies($cache_item)) {
            $this->_stat['test_succ']++;
            return true;
        }
        else {
            $this->erase($id);
            $this->_stat['test_fail']++;
            return false;
        }
    }

    public function read($id)
    {
        $start = microtime(true);
        if ($this->test($id)) {
            $this->_stat['read_succ']++;
            $cache_item = $this->_driver->load($id);
            $value = $cache_item->value;
        }
        else {
            $this->_stat['read_fail']++;
            $value = null;
        }
        $this->_stat['read_time'] += microtime(true) - $start;
        return $value;
    }

    public function write($id, $value, $ttl=null, $dependencies=array())
    {
        $this->_write($id, $value, $ttl, $dependencies);
    }

    public function add($id, $value, $ttl=null, $dependencies=array())
    {
        if (false == $this->test($id)) {
            $this->_stat['add_succ']++;
            $this->_write($id, $value, $ttl, $dependencies);
        }
        else {
            $this->_stat['add_fail']++;
        }
    }

    public function erase($id=null)
    {
        $this->_stat['erase']++;
        if ($id === null) {
            throw new Exception(__CLASS__ . ' does not support erasing all cache storage. Use CacheStorageNamespace.');
        }
        elseif (is_string($id)) {
            $this->_driver->delete($id);
        }
        elseif (is_array($id)) {
            foreach ($id as $k) {
                $this->_driver->delete($k);
            }
        }
    }

    public function getStat($format='txt')
    {
        if ($format == 'txt') {
            $str  = "S: ";
            $str .= sprintf("test: %d/%d\t",     $this->_stat['test_succ'],     $this->_stat['test_fail']);
            $str .= sprintf("read: %d/%d\t",     $this->_stat['read_succ'],     $this->_stat['read_fail']);
            $str .= sprintf("write: %d/%d\t", $this->_stat['write_succ'],    $this->_stat['write_fail']);
            $str .= sprintf("add: %d/%d\t",     $this->_stat['add_succ'],     $this->_stat['add_fail']);
            $str .= sprintf("erase: %d\t",         $this->_stat['erase']);
            $str .= sprintf("errHash: %d\t errExpire: %d\t errDepend: %d\trt: %.3f\n", $this->_stat['err_checkHash'], $this->_stat['err_checkExpire'], $this->_stat['err_checkDependencies'], $this->_stat['read_time']);
            $str .= $this->_driver->getStat($format);
            return $str;
        }
        else {
            return array(
                'storage' => $this->_stat,
                'driver'  => $this->_driver->getStat($format),
            );
        }
    }

    public function shutdown()
    {
        $this->_driver->shutdown();
    }


    protected function _write($id, $value, $ttl, $dependencies)
    {
        $cache_item = $this->_createCacheItem($id, $value, $ttl, $dependencies);
        if (null !== $cache_item) {
            $this->_stat['write_succ']++;
            $this->_driver->save($cache_item);
        }
        else {
            $this->_stat['write_fail']++;
        }
    }

    protected function _createCacheItem($id, $value, $ttl, $dependencies)
    {
        if ( null === ($dep_hash = $this->_calcDependenciesHash($dependencies)) ) {
            return null;
        }
        $cache_item = new CCacheItem();
        $cache_item->id = $id;
        $cache_item->value = $value;
        $cache_item->expired = ($ttl == null ? 0 : time() + $ttl);
        $cache_item->dependencies = $dependencies;
        $cache_item->dependencies_hash = $dep_hash;
        $cache_item->hash = $this->_calcHash($cache_item);
        return $cache_item;
    }

    protected function _calcHash($cache_item)
    {
        $cache_item = clone $cache_item;
        $cache_item->hash = null;
        return $this->_hash($cache_item);
    }

    protected function _calcDependenciesHash($dependencies) //                                                                                         test               ,                                     
    {
        $dependencies_hashs = array();
        foreach ($dependencies as $d_key) {
            if ($this->test($d_key)) {
                $dependency_cache_item = $this->_driver->load($d_key);
                $dependencies_hashs[] = $dependency_cache_item->hash;
            }
            else {
                return null;
            }
        }
        return $this->_hash($dependencies_hashs);
    }

    protected function _checkExpire($cache_item)
    {
        if ($cache_item->expired > 0 && $cache_item->expired < time()) {
            $this->_stat['err_checkExpire']++;
            return false;
        }
        else {
            return true;
        }
    }

    protected function _checkHash($cache_item)
    {
        $hash = $cache_item->hash;
        $new = $this->_calcHash($cache_item);
        if ($hash == $new) {
            return true;
        }
        else {
            $this->_stat['err_checkHash']++;
            return false;
        }
    }

    protected function _checkDependencies($cache_item)
    {
        $d_hash = $cache_item->dependencies_hash;
        $new = $this->_calcDependenciesHash($cache_item->dependencies);
        if ($d_hash == $new) {
            return true;
        }
        else {
            $this->_stat['err_checkDependencies']++;
            return false;
        }
    }

    protected function _hash($mixed)
    {
        return crc32(serialize($mixed));
    }

    protected function _log_begin($action, $id)
    {
        if ($this->_log_disabled) return;

        $this->_log_index++;
        $this->_log_list[$this->_log_index] = array();
        $str = "$action($id)";
        $this->_log_list[$this->_log_index][] = $str;
    }


}


