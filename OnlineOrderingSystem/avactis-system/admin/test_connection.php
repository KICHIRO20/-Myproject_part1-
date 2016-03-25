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
if (isset($_GET['request']) && $_GET['request'] == "is_connection_available" && ((isset($_SERVER["HTTPS"]) && strtolower($_SERVER["HTTPS"]) == "on" || $_SERVER["HTTPS"] == 1 || $_SERVER["HTTPS"] == true) || (isset($_SERVER["SERVER_PORT"]) && ($_SERVER["SERVER_PORT"] == "443"))))
{
    echo <<<END
<center>
<div style="display: none;">YES</div>
<div style="color: green; font-size: 14px; font-family: verdana, tahoma; font-weight: bold; padding-top: 20px;">The URL you entered is correct.</div>
</center>
END;
}
else
{
    echo '';
}
?>