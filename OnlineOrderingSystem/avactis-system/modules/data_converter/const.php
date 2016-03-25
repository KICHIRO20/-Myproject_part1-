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

/**
 * @package DataConverter
 * @author Oleg F. Vlasenko, Egor V. Derevyankin
 *
 */

define('DC_SCRIPTS_DEFINITION_FILE', CConf::get('modules_dir').'data_converter/includes/scripts.xml');
define('DC_WORKER_TIME_OUT', 1);
define('CSV_HEADER_RX','/.*/');

?>