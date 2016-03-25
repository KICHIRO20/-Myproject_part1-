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

class CCacheStorageWrapperNamespace implements ICacheStorage
{
    protected $obj;
    protected $space;
    protected $space_id;

    function __construct($obj, $space)
    {
        $this->obj = $obj;
        $this->space = $space;
        $this->space_id = "$space:master_dependence";
        if (null == ($v = $this->obj->read($this->space_id))) {
            $this->obj->write($this->space_id, rand());
        }
    }

    public function test($key)
    {
        return $this->obj->test($this->_decorateKey($key));
    }

    public function read($key)
    {
        return $this->obj->read($this->_decorateKey($key));
    }

    public function write($key, $data, $ttl=null, $dependencies=array())
    {
        $d= $this->_decorateKey($dependencies);
        $d[] = $this->space_id;
        return $this->obj->write($this->_decorateKey($key), $data, $ttl, $d);
    }

    public function add($key, $data, $ttl=null, $dependencies=array())
    {
        $d= $this->_decorateKey($dependencies);
        $d[] = $this->space_id;
        return $this->obj->add($this->_decorateKey($key), $data, $ttl, $d);
    }

    public function erase($key=null)
    {
        if ($key == null) {
            $this->obj->write($this->space_id, rand());
        }
        else {
            return $this->obj->erase($this->_decorateKey($key));
        }
    }

    public function getStat($format = 'txt')
    {
        return $this->obj->getStat($format);
    }

    public function shutdown()
    {
        $this->obj->shutdown();
    }

    protected function _decorateKey($key)
    {
        if (is_array($key)) {
            $k = array();
            foreach ($key as $v) {
                $k[] = $this->space .':'. $v;
            }
        }
        else {
            $k = $this->space .':'. $key;
        }
        return $k;
    }


}