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
  <TITLE><?php msg('CMN_PAGE_TITLE'); ?></TITLE>
    <?php //
          // Insert HTML Header
          //
          $item = 'admin';
          include('part_htmlheader.php'); ?>
</HEAD>
<BODY  class="body_bgimage">

<TABLE height="100%" cellSpacing=5 cellPadding=0 width="100%" border=0 class="MainPageTable">
    <THEAD>
    <TR height="41">
        <TH colspan=2>
                <?php //
                      // Insert Page Header
                      //
                      include('part_header.php'); ?>
        </TH>
    </TR>
    </THEAD>

    <TR>
        <TD class="pageBlock" vAlign=top style="padding: 0px; margin: 0px;">
        <?php //
              // Insert Left Page Column
              //
              include('part_left_box.php') ?>
        </TD>

        <TD width="100%">
            <div style="position: relative;">
                <?php //
                      // Insert Tabs Menu
                      //
                      include('part_tabs_menu.php'); ?>

                <div style="position: relative">
                <TABLE class="content pageBlock" cellSpacing=0 cellPadding=0 width="100%" border=0>
                        <TR>
                            <TD width="100%" vAlign=top style="padding-top: 5px;">
                                <!-- Tab Navigation Breadcrumb // -->
                                <DIV style="padding-left: 15px;">
                                    <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
                                        <TR class="top">
                                        <TD noWrap width="90%"><a href="admin.php" style="font-weight: bold; font-size: 10pt; margin: 0px; color: #000000; font-family: Tahoma, sans-serif;"><?php Msg('ADMIN_MENU_PAGE_NAME'); ?></a><span class="categorySeparatorCharacter">&nbsp;&gt;&gt;&nbsp;</span><?php Msg('ADMIN_CZ_LAYOUTS_PAGE_NAME'); ?></nobr></TD>
                                        <TD noWrap style="padding-right: 8px;"><?php PageHelpLink('admin_cz_layouts_info'); ?>&nbsp;</TD>
                                        <TD noWrap style="padding-right: 8px;"><?php VideoTutorialLink('admin_cz_layouts_info'); ?>&nbsp;</TD>
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
                                    <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
                                        <TR>
                                            <TD><?php CZLayoutsList(); ?></td>
                                        </TR>
                                    </TABLE>
                                </div>
                                <!-- // Page Content -->
                            </TD>
                        </TR>
                    </TABLE>
                </div>
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