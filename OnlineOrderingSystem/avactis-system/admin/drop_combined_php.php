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

include dirname(dirname(__FILE__)).'/conf/conf.main.php';

unlink($conf['preload_core_php']['combined_file']);
unlink($conf['preload_modules_php']['combined_file']);
unlink($conf['preload_modules_views_cz_php']['combined_file']);

echo 'done';