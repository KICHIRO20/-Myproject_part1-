<?php

?><!-- footer -->
<style>
.footer { color:#ffffff; }
.footer a { color:#dddddd; }
.footer hr { width:90%; max-width:600px; }
</style>
<div class="footer margin-top-20 margin-bottom-20" align="center">
	<div class="links">
		<A href="index.php"><?php msg('MENU_HOME'); ?></A>&nbsp;
        <A href="catalog.php"><?php msg('MENU_CATALOG'); ?></A>&nbsp;
        <A href="orders.php"><?php msg('MENU_ORDERS'); ?></A>&nbsp;
        <A href="marketing.php"><?php msg('MENU_MARKETING'); ?></A>&nbsp;
        <A href="customers.php"><?php msg('MENU_USERS'); ?></A>&nbsp;
        <A href="store_settings.php"><?php msg('MENU_STORE_SETTINGS'); ?></A>&nbsp;
        <A href="http://wiki.avactis.com/" target="_blank"><?php msg('MENU_HELP'); ?></A>&nbsp;
    	<!--REMOVE IN WHITE LABEL (BEGIN)-->
		<A href="http://www.avactis.com/forums/" class="menulink" target="_blank"><?php msg('MENU_COMMUNITY_FORUMS'); ?></A>&nbsp;
        <A href="http://www.avactis.com/contact-avactis-support/" target="_blank"><?php msg('MENU_SUPPORT'); ?></A>&nbsp;
    <!--REMOVE IN WHITE LABEL (END)-->
	</div>
	<hr />
	<div class="copyright">
		<p>Copyright 2015. All Rights Reserved.</p>
	</div>

	<div class="agreement_links">
	<!--REMOVE IN WHITE LABEL (BEGIN)-->
		<A href="http://www.avactis.com/license/" target="_blank"></A>
       <p>Online Ordering System for Appliances Store with Stock Management</p>
	<!--REMOVE IN WHITE LABEL (END)-->
	</div>
</div>
<?php include 'part_footer_scripts.php'; ?>
<?php include('stat/stat.php'); ?>
<!-- //footer -->