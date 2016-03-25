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
 	require_once('../admin.php');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">

<html>
	<head>
		<title><?php xMsg('SYS','ORDERS_INVOICE'); ?></title>
		<link rel='stylesheet' href='styles/style.invoice.css' type='text/css'/>
		<script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>
		<script type="text/javascript" src="js/avactis-jquery_post_extend.js"></script>
		<script type="text/javascript" src="js/jquery-migrate-1.1.1.min.js"></script>


		<!--[if lt IE 7]>
		<style type="text/css">
		.transparent_block {
		  background-image: none;
		  filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='images/halftranspixel.png', sizingMethod='scale');
		}
		</style>
		<![endif]-->

		<SCRIPT language="JavaScript">
		var window_onload_timeout = 0;
		</SCRIPT>
	</head>
<BODY style="background-color: #FFFFFF;">
  <TABLE cellspacing="0" cellpadding="0" align="center" border="0" width="100%" style="background-color: #FFFFFF;">
    <TR>
      <TD>
        <?php OrderInvoice();?>
      </TD>
    </TR>
    <TFOOT>
    <TR><TD>&nbsp;
    </TFOOT>
  </TABLE>
</BODY>
</html>