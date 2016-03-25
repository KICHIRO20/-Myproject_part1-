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
?><?php include('../admin.php');
?>
<HTML>
<HEAD>
<TITLE>Avactis Storefront Editor</TITLE>
<?php
    if (! modApiFunc('Users', 'checkCurrentUserPermission', PERMISSION_DESIGN)) {
        echo '<div class="no_access">Sorry, You have no access to this content.</div>';
        die;
    }
    loadCoreFile('JSON.php');
    $json = new Services_JSON();
?>
<script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="js/avactis-jquery_post_extend.js"></script>
<script type="text/javascript" src="js/jquery-migrate-1.1.1.min.js"></script>

<script>
$(function() {
	window.editor = new FrameCSSEditor({
		panel_frame_id: 'panel',
		storefront_frame_id: 'storefront',
		theme_filename_head: '<?php echo escapeJSString(THEME_FILENAME_HEAD) ?>',
		theme_filename_tail: '<?php echo escapeJSString(THEME_FILENAME_TAIL) ?>',
		labels: {
			associated_style: '<?php echo escapeJSString(getXMsg('LF', 'LF_ASSOC_STYLE')) ?>',
            associated_styles: '<?php echo escapeJSString(getXMsg('LF', 'LF_ASSOC_STYLES')) ?>',
            selected_element: '<?php echo escapeJSString(getXMsg('LF', 'LF_SELECTED_ELEMENT')) ?>',
            underlying_element: '<?php echo escapeJSString(getXMsg('LF', 'LF_UNDERLYING_ELEMENT')) ?>',
            underlying_elements: '<?php echo escapeJSString(getXMsg('LF', 'LF_UNDERLYING_ELEMENTS')) ?>',
            show_this_element: '<?php echo escapeJSString(getXMsg('LF', 'LF_SHOW_THIS_ELEMENT')) ?>',
            show_elements_used_style: '<?php echo escapeJSString(getXMsg('LF', 'LF_SHOW_ELEMENTS_USED_STYLE')) ?>',
            editing_element_style: '<?php echo escapeJSString(getXMsg('LF', 'LF_EDITING_ELEMENT_STYLE')) ?>',
            saving_failed_network: '<?php echo escapeJSString(getXMsg('LF', 'LF_TEXT_SAVING_FAILED_WTD')) ?>',
            alert_not_saved_theme: '<?php echo escapeJSString(getXMsg('LF', 'ALERT_NOT_SAVED_THEME')) ?>'
		},
		form_id: '<?php echo modApiFunc('Session', 'get', '__ASC_FORM_ID__') ?>',
		editables_text: '<?php echo escapeJSString(Look_Feel::getEditorRules()) ?>',
		edited_theme: <?php echo $json->encode(Look_Feel::getEditedThemeObj()) ?>,
		active_theme_name: '<?php echo escapeJSString(Look_Feel::getActiveTheme()) ?>'
	});
});
</script>
<?php
    global $application;
    echo $application->combineAdminJS(array(
        'js/css_editor_util.js',
        'js/css_editor.js',
    ));
?>
</HEAD>
<FRAMESET cols="320,*">
  <FRAME frameborder="0" id="panel" name="css_editor_panel" src="edit_panel.php" scrolling="no">
  <FRAME frameborder="0" id="storefront" name="css_editor_storefront" src="<?php echo urlStorefrontBase() ?>">
  <NOFRAMES>
      <P><?php echo getXMsg('LF', 'LF_FRAMES_NOT_SUPPORTED') ?></P>
  </NOFRAMES>
</FRAMESET>
</HTML>