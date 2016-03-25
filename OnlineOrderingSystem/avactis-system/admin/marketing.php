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
	header("location:marketing_manage_promo_codes.php");
	die;
?>
<?php include('../admin.php'); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">
<HTML>
<HEAD>
  <TITLE><?php msg('MRKTNG_TAB_PAGE_NAME'); ?> - <?php Msg('CMN_PAGE_TITLE'); ?></TITLE>
    <?php //
          // Insert HTML Header
          //
	  global $parentGBL;
          $parentGBL='marketing';
          include('part_htmlheader.php'); ?>
</HEAD>
<BODY  class="body_bgimage">

<TABLE height="100%" cellSpacing=0 cellPadding=0 width="100%" border=0 class="MainPageTable">
    <THEAD>
    <TR height="35">
        <TH colspan=2>
                <?php //
                      // Insert Page Header
                      //
                      include('part_header.php'); ?>
        </TH>
    </TR>
    </THEAD>

    <TR>
	<TD class="floatleft">
		<?php //
                 // Insert Tabs Menu
                 //
                include('part_vmenu.php'); ?>
	</TD>
        <TD width="100%">
            <div style="position: relative;">
                <TABLE class="content pageBlock" cellSpacing=0 cellPadding=0 width="100%" border=0>
                        <TR>
                            <TD width="100%" vAlign=top style="padding-top: 5px;">
                                <!-- Tab Navigation Breadcrumb // -->
                                <DIV style="padding-left: 15px;">
                                    <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
                                        <TR class="top">
                                            <TD noWrap width="90%"><?php msg('MRKTNG_TAB_PAGE_NAME'); ?></TD>
                                        <TD noWrap style="padding-right: 8px;"><?php PageHelpLink('marketing_tab'); ?>&nbsp;</TD>
                                        <TD noWrap style="padding-right: 8px;"><?php VideoTutorialLink('marketing_tab'); ?>&nbsp;</TD>
                                        </TR>
                                    </TABLE>
                                </DIV>
                                <!-- // Tab Navigation Breadcrumb -->
                            </TD>
                        </TR>
                        <TR>
                            <TD valign="top" style="padding-top: 0px; padding-left: 3px;">
                                <!-- Page Content // -->
                                <div class="fixed_height_menu_tab_page" style="padding: 3px 7px 0px 7px;">
                                    <TABLE cellSpacing=0 cellPadding=5 width="100%" border=0>
                                        <TR class="title">
                                            <TD><span style="padding-left: 6px;"><?php msg('MRKTNG_TAB_001'); ?></span></td>
                                        </TR>
                                        <TR>
                                            <TD vAlign=top style="padding-top: 3px;">
						<?php printNavCell(null,'MRKTNG_TAB_002','MRKTNG_TAB_003',"go('marketing_manage_discounts.php');"); ?>
						<?php printNavCell(null,'MRKTNG_TAB_004','MRKTNG_TAB_005',"go('marketing_manage_promo_codes.php');"); ?>
						<?php printNavCell('GCT','MARKETING_LINK','MARKETING_DESCRIPTION',"go('marketing_manage_gc.php');"); ?>
						<?php printNavCell('NLT','NEWSLETTER_TITLE','NEWSLETTER_DESCR',"go('newsletter_archive.php');"); ?>
						<?php printNavCell('SUBSCR','SUBSCRIPTIONS_LINK','SUBSCRIPTIONS_DESCR',"go('subscriptions_manage.php');"); ?>
						<?php printNavCell('TT','MARKETING_LINK','MARKETING_DESCRIPTION',"go('transaction_tracking_settings.php');"); ?>
                                            </TD>
                                        </TR>
                                    </TABLE>
                                </div>
                                <!-- // Page Content -->
                            </TD>
                        </TR>
                    </TABLE>
            </div>
        </TD>
    </TR>



    <TFOOT>
    <TR height=100>
        <TH colspan=2><?php //
                            // Insert Page Footer
                            //
                            include('part_footer.php') ?></TH>
    </TR>
    </TFOOT>
</TABLE>

</BODY>
</HTML>