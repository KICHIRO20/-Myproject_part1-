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
 * @package RelatedProducts
 * @author Vadim Lyalikov
 *
 */

define('IMAGE_MEDIA_SERVER_PATH', 'IMAGE_MEDIA_SERVER_PATH');
define('IMAGE_MEDIA_URL', 'IMAGE_MEDIA_URL');
define('IMAGE_MEDIA_THEME_PATH', 'IMAGE_MEDIA_THEME_PATH');
define('DBQUERY_FIELD_TYPE_IMAGE_MEDIA', "ENUM "
   ."('". IMAGE_MEDIA_SERVER_PATH. "',"
    ."'". IMAGE_MEDIA_URL. "',"
    ."'". IMAGE_MEDIA_THEME_PATH. "') " ." NOT NULL");
define('EMPTY_IMAGE_BASENAME', 'no-image.gif');
define('IMAGE_THUMB_SIZE', 100);
define('EMPTY_IMAGE_WIDTH', IMAGE_THUMB_SIZE);
define('EMPTY_IMAGE_HEIGHT', IMAGE_THUMB_SIZE);



?>