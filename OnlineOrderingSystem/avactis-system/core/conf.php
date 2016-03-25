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

class CConf
{
	static $conf = array();

	static public function init($confdir)
	{
		include($confdir . 'conf.main.php');
        @ include($confdir . 'conf.local.php');
		self::$conf = $conf;
	}

	static public function get($key)
	{
		if (isset(self::$conf[$key]))
		{
			return self::$conf[$key];
		}
		else
		{
			//die('ERROR. CConf::get("'.$key.'") is undefined.');
		}
	}

	static public function set($key, $value)
	{
		self::$conf[$key] = $value;
	}
}