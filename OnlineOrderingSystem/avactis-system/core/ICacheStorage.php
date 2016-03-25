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

interface ICacheStorage
{
    public function test($key);
    public function read($key);
    public function write($key, $data, $ttl=null, $dependencies=array());
    public function add($key, $data, $ttl=null, $dependencies=array());
    public function erase($key=null);
    public function shutdown();
    public function getStat();
}