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

/* The script collects general information about the current software
 * and sends it to Google Analytics for Avactis team to view the overall statistics
 */

$adminUrlParts = parse_url($application->getAppIni('SITE_AZ_CURRENT_URL'));
$adminUrlPrefix = $adminUrlParts['path'];
$adminPage = str_replace($adminUrlPrefix, '/', $_SERVER['REQUEST_URI']);
?>

<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-365588-3']);

  _gaq.push(['_setCustomVar', 1, 'Domain', document.location.hostname, 1]);
  _gaq.push(['_setCustomVar', 1, 'Version', '<?=PRODUCT_VERSION_NUMBER?>', 1]);
  _gaq.push(['_setCustomVar', 1, 'Edition', '<?=PRODUCT_VERSION_TYPE?>', 1]);
  _gaq.push(['_setCustomVar', 1, 'PHP', '<?=phpversion()?>', 1]);
  _gaq.push(['_setCustomVar', 1, 'OS', '<?=php_uname('s')?>', 1]);

  _gaq.push(['_trackPageview', '<?=$adminPage?>']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>