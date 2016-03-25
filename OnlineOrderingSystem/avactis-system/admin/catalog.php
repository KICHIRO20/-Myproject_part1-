<?php

?><?php
	header("location:catalog_manage_products.php");
	die;
?>
<?php include('../admin.php'); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">
<HTML>
<HEAD>
  <TITLE><?php Msg('CTLG_TAB_PAGE_TITLE'); ?> - <?php Msg('CMN_PAGE_TITLE'); ?></TITLE>
    <?php //
          // Insert HTML Header
          //
	  global $parentGBL;
          $parentGBL='catalog';
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
                                            <TD noWrap width="90%"><?php Msg('CTLG_TAB_PAGE_NAME'); ?></TD>
                                        <TD noWrap style="padding-right: 8px;"><?php PageHelpLink('catalog_tab'); ?>&nbsp;</TD>
                                        <TD noWrap style="padding-right: 8px;"><?php VideoTutorialLink('catalog_tab'); ?>&nbsp;</TD>
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
                                            <TD><span style="padding-left: 6px;"><?php Msg('CTLG_TAB_001'); ?></span></td>
                                        </TR>
                                        <TR>
                                            <TD vAlign=top style="padding-top: 3px;">
<table class="clear" cellspacing="4" width="100%">
<tr>
    <td>
        <?php printNavCell(null, // short module name, see xmsg()
                           'CTLG_TAB_002', // header
                           'CTLG_TAB_003', // description
                           "go('catalog_manage_products.php');"); // onClick
        ?>

        <?php printNavCell(null,'CTLG_TAB_004','CTLG_TAB_005',"go('catalog_manage_categories.php');"); ?>

        <?php printNavCell(null,'CTLG_TAB_006','CTLG_TAB_007',"go('catalog_manage_product_types.php');"); ?>

        <?php printNavCell(null,'CTLG_TAB_010','CTLG_TAB_011',"go('mnf_manufacturers.php');"); ?>

    </td>
</tr>
</table>
<BR><BR>
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