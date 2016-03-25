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
<TITLE><?php msg('PAYM_MODULE_SETTINGS_PAGE_TITLE'); ?></TITLE>
<?php include('part_htmlheader.php'); ?>
</HEAD>

<BODY>
  <TABLE cellSpacing=0 cellPadding=0 align=center border=0 width="670">
    <TR>
      <TD>

      <table class="form" cellspacing="1" cellpadding="5" width="100%" align="center">
<!--    <colgroup>
      <col width="20%">
      <col width="0%">
      <col width="75%">-->

    <tr class="title">
        <td class="title popup_dialog_header_left_right_padded" colspan="3">Payment Module: <?php isset($_GET['pm_name']) ? print $_GET['pm_name'] : print 'Module'; ?></td>
    </tr>
    <tr class="subtitle" style="height: 25px;">
        <td class="popup_dialog_header_left_right_padded" colspan="3" style="padding: 0px 0px 0px 4px;">
            <table class="clear" width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td width="80%"><?php isset($_GET['pm_name']) ? print $_GET['pm_name'] : print 'Module'; ?> Details</td>
                </tr>
            </table>
        </td>
    </tr>

<?php FreeVersionRestriction(); ?>


        </table>

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