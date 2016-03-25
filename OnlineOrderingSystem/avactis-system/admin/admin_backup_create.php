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
<TITLE><?php msg('BCP_CREATE_PAGE_TITLE'); ?></TITLE>
<?php include('part_htmlheader.php'); ?>
</HEAD>

<BODY onbeforeunload="CancelBackup();">
  <TABLE cellSpacing=0 cellPadding=0 align=center border=0 width="670">
    <TR>
      <TD>
        <div class="fixed_height_popup_form" style="background-color: #ffffff;">
        <?php BackupCreate(); ?>
        </div>
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