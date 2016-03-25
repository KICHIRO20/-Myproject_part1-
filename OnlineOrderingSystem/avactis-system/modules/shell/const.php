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
 * @package Shell
 * @author Egor V. Derevyankin
 *
 */

if(version_compare(phpversion(),"4.3.0")==-1)
{
    if (!defined('UPLOAD_ERR_OK')) define("UPLOAD_ERR_OK",0);
    if (!defined('UPLOAD_ERR_INI_SIZE')) define("UPLOAD_ERR_INI_SIZE",1);
    if (!defined('UPLOAD_ERR_FORM_SIZE')) define("UPLOAD_ERR_FORM_SIZE",2);
    if (!defined('UPLOAD_ERR_PARTIAL')) define("UPLOAD_ERR_PARTIAL",3);
    if (!defined('UPLOAD_ERR_NO_FILE')) define("UPLOAD_ERR_NO_FILE",4);
};

if((version_compare(phpversion(),"4.3.10")==-1) or (version_compare(phpversion(),"5.0.0")>=0 and version_compare(phpversion(),"5.0.3")==-1))
    if (!defined('UPLOAD_ERR_NO_TMP_DIR')) define("UPLOAD_ERR_NO_TMP_DIR",6);

if(version_compare(phpversion(),"5.1.0")==-1)
    if (!defined('UPLOAD_ERR_CANT_WRITE')) define("UPLOAD_ERR_CANT_WRITE",7);

if(version_compare(phpversion(),"5.2.0")==-1)
    if (!defined('UPLOAD_ERR_EXTENSION')) define("UPLOAD_ERR_EXTENSION",8);

define('UPLOAD_ERR_POSIBLE_ATTACK',20);
define('UPLOAD_ERR_CANT_MOVE_FILE',21);
define('UPLOAD_ERR_CANT_CP_FILE',22);

define('UPLOAD_FILE_IS_NOT_IMAGE',31);

?>