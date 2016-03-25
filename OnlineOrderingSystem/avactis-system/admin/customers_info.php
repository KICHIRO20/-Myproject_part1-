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
?><?php include('../admin.php'); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">
<HTML>
<HEAD>
<TITLE><?php msg('CUSTOMERS_INFO'); ?></TITLE>
<?php include('part_htmlheader.php'); ?>
</HEAD>

<BODY>
  <TABLE cellspacing="0" cellpadding="0" align="center" border="0" width="670">
    <TR>
      <TD>
        <?php CustomerInfo();?>
      </TD>
    </TR>
    <TFOOT>
    <TR height=100>
        <TH><?php //
                  // Insert Page Footer
                  //
                  include('part_footer_popup.php') ?></TH>
    </TR>
    </TFOOT>
  </TABLE>
</BODY>
</HTML>