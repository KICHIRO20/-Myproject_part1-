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


class CCacheFactory
{
    static protected $_cache_list = array();
    static protected $_cache_storages = array();

    static public function getCache($name, $custom_space = '')
    {
        if (!isset(self::$_cache_list[$name.$custom_space])) {
            self::$_cache_list[$name.$custom_space] = self::_createCache($name, $custom_space);
        }
        return self::$_cache_list[$name.$custom_space];
    }

    static public function clearAll()
    {
        $cache_storages = array_keys(CConf::get('cache_storages'));
        foreach ($cache_storages as $storage) {
            $storage_obj = self::_getCacheStorage($storage);
            $storage_obj->erase();
        }
    }

    static public function shutdown()
    {
        $cache_storages = array_keys(CConf::get('cache_storages'));
        foreach ($cache_storages as $storage) {
            $storage_obj = self::_getCacheStorage($storage);
            $storage_obj->shutdown();
        }
    }

    static public function getStat($format='txt')
    {
        $stat = array();
        $cache_storages = array_keys(CConf::get('cache_storages'));
        foreach ($cache_storages as $storage) {
            $storage_obj = self::_getCacheStorage($storage);
            $stat[$storage] = $storage_obj->getStat($format);
        }
        if ($format == 'txt') {
            $str = '';
            foreach ($stat as $key => $val) {
                $str .= $key . "\n" . $val . "\n";
            }
            $stat = $str;
        }
        return $stat;
    }

    static protected function _createCache($cache_name, $custom_space)
    {
        $cache_list = CConf::get('cache');
        if (is_array($cache_list) && isset($cache_list[$cache_name])) {
            $cache_storage_obj = self::_getCacheStorage($cache_list[$cache_name]);
            $space = $cache_name . ( ($custom_space=='') ? '' : '-'.$custom_space );
            return new CCacheStorageWrapperNamespace($cache_storage_obj, $space);
        }
        else {
            throw new Exception("Failed to create cache object! Specified cache name '$cache_name' does not described in the configuration (conf.main.php) file.");
        }
    }

    static protected function _getCacheStorage($cache_storage_name)
    {
        if (!isset(self::$_cache_storages[$cache_storage_name])) {
            self::$_cache_storages[$cache_storage_name] = self::_createCacheStorage($cache_storage_name);
        }
        return self::$_cache_storages[$cache_storage_name];
    }

    static protected function _createCacheStorage($cache_storage_name)
    {
        $cache_storages = CConf::get('cache_storages');
        if (is_array($cache_storages) && isset($cache_storages[$cache_storage_name])) {
            $cache_storage_descr = $cache_storages[$cache_storage_name];
            if (!isset($cache_storage_descr['driver'])) {
                throw new Exception("Failed to create cache storage object '$cache_storage_name', because the configuration (conf.main.php) file does not contain the driver record for this cache storage.");
            }
            $driver = $cache_storage_descr['driver'];
            $args = (isset($cache_storage_descr['args']) ? $cache_storage_descr['args'] : array());
            $driver_obj = null;
            switch (count($args)) {
                case 0:
                    $driver_obj = new $driver();
                    break;
                case 1:
                    $driver_obj = new $driver($args[0]);
                    break;
                case 2:
                    $driver_obj = new $driver($args[0], $args[1]);
                    break;
                default:
                    throw new Exception("Failed to create cache storage driver!");
                    break;
            }
            $store_uid = 'global_' . sha1(CConf::get('base_dir'));
            return new CCacheStorageWrapperNamespace(new CCacheStorage($driver_obj), $store_uid);
        }
        else {
            throw new Exception("Failed to create cache storage object! Request cache storage to create is '$cache_storage_name'. The configuration (conf.main.php) file does not contain the creation description.");
        }
    }

}


