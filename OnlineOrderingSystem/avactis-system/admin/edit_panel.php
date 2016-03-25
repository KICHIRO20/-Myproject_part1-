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
  <TITLE><?php xMsg('LF', 'LF_CSS_EDITOR_PANEL'); Subscriptions_Topic_Name(); ?></TITLE>
  <script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>
  <script type="text/javascript" src="js/avactis-jquery_post_extend.js"></script>
  <script type="text/javascript" src="js/jquery-migrate-1.1.1.min.js"></script>
<?php
    $item = 'css_editor_panel';
//    include('part_htmlheader.php');
    global $application;
    echo $application->combineAdminCSS(array(
        'styles/jquery.ui.core.css',
        'styles/jquery.ui.resizable.css',
        'styles/jquery.ui.accordion.css',
        'styles/jquery.ui.autocomplete.css',
        'styles/jquery.ui.button.css',
        'styles/jquery.ui.dialog.css',
        'styles/jquery.ui.slider.css',
        'styles/jquery.ui.tabs.css',
        'styles/jquery.ui.datepicker.css',
        'styles/jquery.ui.progressbar.css',
        'styles/jquery.ui.theme.css',
    	'styles/css_editor_panel.css',
        'styles/colorpicker.css',
    ));

    if (! modApiFunc('Users', 'checkCurrentUserPermission', PERMISSION_DESIGN)) {
        echo '<div class="no_access">Sorry, You have no access to this content.</div>';
        die;
    }

    $themes_collapsed = modApiStaticFunc('Look_Feel', 'getPanelSetting', 'manage_themes_collapsed');

    $store = urlStorefrontBase();
    $backdoor_href = $store.'?'.modApiFunc('Configuration', 'getValue', 'store_offline_key');
?>
</HEAD>
<body>
<div class="editor_panel_loading"><?php xMsg('LF', 'LF_INITIAL_LOADING') ?></div>

<div class="editor_panel">
<div class="header ui-corner-bottom">
    <a class="exit" title="<?php xMsg('LF', 'LF_RETURN_TO_AZ') ?>" target="_top" href="javascript: window.close()"></a>
    <?php xMsg('LF', 'LF_CSS_EDITOR_HEADER') ?>
</div>

<div class="toolbar top ui-corner-all">
    <div class="btn undo ui-corner-all disabled"><?php xMsg('LF', 'LF_UNDO_BUTTON') ?></div>
    <div class="btn redo ui-corner-all disabled"><?php xMsg('LF', 'LF_REDO_BUTTON') ?></div>
    <div class="btn mode ui-corner-all checked"><?php xMsg('LF', 'LF_MODE_EDIT_BUTTON') ?></div>
</div>

<div class="status top ui-corner-all" style="display:none">
    <div class="working" style="display:none"><?php xMsg('LF', 'LF_STATUS_WORKING') ?></div>
    <div class="loading" style="display:none"><?php xMsg('LF', 'LF_STATUS_LOADING') ?></div>
    <div class="saving_theme" style="display:none"><?php xMsg('LF', 'LF_STATUS_SAVING') ?></div>
    <div class="theme_saved" style="display:none"><?php xMsg('LF', 'LF_STATUS_SAVED') ?></div>
    <div class="saving_failed" style="display:none"><?php xMsg('LF', 'LF_STATUS_NOT_SAVED') ?></div>
    <div class="set_active_ok" style="display:none"><?php xMsg('LF', 'LF_STATUS_SET_ACTIVE_OK') ?></div>
    <div class="set_active_fail" style="display:none"><?php xMsg('LF', 'LF_STATUS_SET_ACTIVE_FAIL') ?></div>
    <div class="status_what_to_do top" style="display:none">
        <div class="text"></div>
        <div class="close"><a><?php xMsg('LF', 'LF_BUTTON_WTD_CLOSE') ?></a></div>
        <div class="save"><a><?php xMsg('LF', 'LF_BUTTON_WTD_SAVE_AGAIN') ?></a></div>
    </div>
</div>

<div class="navigation_mode ui-corner-all">
    <div class="editable">
        <p><?php xMsg('LF', 'NAVIGATION_MODE_TEXT1') ?></p>
        <p><?php xMsg('LF', 'NAVIGATION_MODE_TEXT2') ?></p>
        <p><?php xMsg('LF', 'NAVIGATION_MODE_TEXT3') ?></p>
    </div>
    <div class="readonly">
        <p><?php xMsg('LF', 'NAVIGATION_MODE_TEXT1') ?></p>
        <p><?php xMsg('LF', 'NAVIGATION_MODE_TEXT2') ?></p>
        <p><?php xMsg('LF', 'NAVIGATION_MODE_TEXT4') ?></p>
    </div>
    <div class="https">
        <p><?php xMsg('LF', 'NAVIGATION_MODE_TEXT1') ?></p>
        <p><?php xMsg('LF', 'NAVIGATION_MODE_TEXT2') ?></p>
        <p><?php xMsg('LF', 'NAVIGATION_MODE_TEXT5') ?></p>
        <p><a onclick="openURLinNewWindow('https_settings.php', 'HTTPSSettings')" href="javascript:void(0)"><?php Msg('ADMIN_MENU_HTTPSSETTINGS') ?></a></p>
    </div>
</div>


<div class="accordionResizer">
<div class="accordion">
<h2 class="manage_themes"><a href="#"><?php xMsg('LF', 'LF_THEMES_TITLE') ?></a></h2>
<div class="themes_panel">
<!--
    <div class="no_rules"><?php xMsg('LF', 'WRN_CSS_RULES_NOT_DEFINED') ?></div>
-->
    <div class="add_skin">
        <h3><?php xMsg('LF', 'LF_ADD_NEW_THEME_TITLE') ?></h3>
        <input type="text" /><div class="button submit"><?php xMsg('LF', 'LF_ADD_THEME_BUTTON') ?></div><div class="adding"></div>
        <div class="clear message"></div>
        <div class="what_to_do"></div>
    </div>
    <div class="loading"><?php xMsg('LF', 'LF_LOADING_THEMES') ?></div>
    <div class="loading_failed"><?php xMsg('LF', 'LF_LOADING_THEMES_FAILED') ?></div>
    <div class="existing"><div class="title"><?php xMsg('LF', 'LF_CHOOSE_THEME_TITLE') ?></div>
        <ul>
            <li class="default" skin_name=""><a class="name" href="javascript:void(0)"><?php xMsg('LF', 'LF_THEME_DEFAULT') ?></a> <span class="active"><?php xMsg('LF', 'LF_THEME_ACTIVE_LABEL') ?></span></li>
        </ul>
        <div class="read_only"><?php xMsg('LF', 'LF_READ_ONLY_THEME_CHOSEN') ?></div>
        <div class="default_chosen"><?php xMsg('LF', 'LF_DEFAULT_THEME_CHOSEN') ?></div>
        <div class="button make_theme_active"><?php xMsg('LF', 'LF_MAKE_THEME_ACTIVE') ?></div>
    </div>
<?php
    if (! modApiFunc('Configuration', 'getValue', 'store_online')) {
        ?><div class="store_closed">
            <h3><?php xMsg('LF', 'OPEN_STORE_TITLE') ?></h3>
            <p><?php xMsg('LF', 'STORE_CLOSED_TEXT') ?></p>
            <a class="button backdoor" target="css_editor_storefront" href="<?php echo $backdoor_href ?>"><?php xMsg('LF', 'BACKDOOR_BTN') ?></a>
        </div><?php
    }
?>
    <ul class="theme_item_template" style="display:none">
    <li>
        <div class="remove"></div>
        <a href="javascript:void(0)">%skin_name%</a> <span class="active"><?php xMsg('LF', 'LF_THEME_ACTIVE_LABEL') ?></span>
        <div class="confirm_remove">
            <div class="question"><?php xMsg('LF', 'LF_REMOVE_THEME_CONFIRM') ?></div>
            <div class="yes"></div><div class="no"></div>
        </div>
        <div class="removing"><?php xMsg('LF', 'LF_REMOVING_THEME') ?></div>
        <div class="message"></div>
        <div class="what_to_do"></div>
        <div class="error"><?php xMsg('LF', 'LF_REQUEST_FAILED') ?></div>
    </li>
    </ul>
</div>

<h2 class="customization"><a href="#"><?php xMsg('LF', 'LF_CUSTOMIZE_TITLE') ?></a></h2>
<div class="theme_editor" style="display:none">
    <div class="theme_area">
    <div class="choose_element"><p><?php xMsg('LF', 'LF_CHOOSE_ELEMENT_EXPL1') ?></p><p><?php xMsg('LF', 'LF_CHOOSE_ELEMENT_EXPL2') ?></p><p><?php xMsg('LF', 'LF_CHOOSE_ELEMENT_EXPL3') ?></p></div>
    <div class="element upper" style="display:none">
        <div class="title"><?php xMsg('LF', 'LF_SELECTED_ELEMENT') ?></div>
        <a class="name" href="javascript:void(0)"></a>
        <div class="styles_title"></div>
        <ul class="styles"></ul>
    </div>
    <div class="underlying_elements" style="display:none">
        <div class="title"></div>
        <ul></ul>
    </div>
    </div>
</div>

<h2 class="properties"><a href="#"><?php xMsg('LF', 'LF_EDIT_STYLE_TITLE') ?></a></h2>
<div class="rule_editor" style="display:none">
    <div class="element_style"></div>
    <div class="properties"></div>
</div>

<h2 class="bookmarks"><a href="#"><?php xMsg('LF', 'LF_BOOKMARKS_TITLE') ?></a></h2>
<div class="bookmarks" style="display:none">
    <p><?php xMsg('LF', 'LF_ATTENTION_TEXT') ?></p>
    <h3><?php xMsg('LF', 'LF_PAGES_BOTH_TITLE') ?></h3>
    <ul class="both">
        <li><a href="<?php echo $store ?>index.php" target="css_editor_storefront"><?php xMsg('LF', 'LF_PAGE_INDEX') ?></a></li>
        <li><a href="<?php echo $store ?>product-list.php" target="css_editor_storefront"><?php xMsg('LF', 'LF_PAGE_PRODUCT_LIST') ?></a></li>
        <li><a href="<?php echo $store ?>product-info.php" target="css_editor_storefront"><?php xMsg('LF', 'LF_PAGE_PRODUCT_INFO') ?></a></li>
        <li><a href="<?php echo $store ?>cmspage.php" target="css_editor_storefront"><?php xMsg('LF', 'LF_PAGE_CMS_PAGE') ?></a></li>
        <li><a href="<?php echo $store ?>cart.php" target="css_editor_storefront"><?php xMsg('LF', 'LF_PAGE_CART') ?></a></li>
        <li><a href="<?php echo $store ?>checkout.php" target="css_editor_storefront"><?php xMsg('LF', 'LF_PAGE_CHECKOUT') ?></a></li>
        <li><a href="<?php echo $store ?>search-results.php" target="css_editor_storefront"><?php xMsg('LF', 'LF_PAGE_SEARCH_RESULTS') ?></a></li>
        <li><a href="<?php echo $store ?>unsubscribe.php" target="css_editor_storefront"><?php xMsg('LF', 'LF_PAGE_UNSUBSCRIBE') ?></a></li>
        <li><a href="<?php echo $store ?>store-closed.php" target="css_editor_storefront"><?php xMsg('LF', 'LF_PAGE_CLOSED') ?></a></li>
    </ul>
    <h3><?php xMsg('LF', 'LF_PAGES_REG_TITLE') ?></h3>
    <ul class="reg">
        <li><a href="<?php echo $store ?>home.php" target="css_editor_storefront"><?php xMsg('LF', 'LF_PAGE_HOME') ?></a></li>
        <li><a href="<?php echo $store ?>orders.php" target="css_editor_storefront"><?php xMsg('LF', 'LF_PAGE_ORDERS') ?></a></li>
        <li><a href="<?php echo $store ?>order-info.php" target="css_editor_storefront"><?php xMsg('LF', 'LF_PAGE_ORDER_INFO') ?></a></li>
        <li><a href="<?php echo $store ?>order-invoice.php" target="css_editor_storefront"><?php xMsg('LF', 'LF_PAGE_ORDER_INVOICE') ?></a></li>
        <li><a href="<?php echo $store ?>change-password.php" target="css_editor_storefront"><?php xMsg('LF', 'LF_PAGE_CHANGE_PASSWORD') ?></a></li>
        <li><a href="<?php echo $store ?>personal-info.php" target="css_editor_storefront"><?php xMsg('LF', 'LF_PAGE_PERSONAL_INFO') ?></a></li>
        <li><a href="<?php echo $store ?>order-download-links.php" target="css_editor_storefront"><?php xMsg('LF', 'LF_PAGE_DOWNLOAD_LINKS') ?></a></li>
        <li><a href="<?php echo $store ?>download.php" target="css_editor_storefront"><?php xMsg('LF', 'LF_PAGE_DOWNLOAD') ?></a></li>
        <li><a href="<?php echo $store ?>subscriptions.php" target="css_editor_storefront"><?php xMsg('LF', 'LF_PAGE_SUBSCRIPTION') ?></a></li>
    </ul>
    <p><?php xMsg('LF', 'LF_ATTENTION_REG_TEXT') ?></p>
    <h3><?php xMsg('LF', 'LF_PAGES_UNREG_TITLE') ?></h3>
    <ul class="unreg">
        <li><a href="<?php echo $store ?>sign-in.php" target="css_editor_storefront"><?php xMsg('LF', 'LF_PAGE_SIGN_IN') ?></a></li>
        <li><a href="<?php echo $store ?>register.php" target="css_editor_storefront"><?php xMsg('LF', 'LF_PAGE_REGISTER') ?></a></li>
        <li><a href="<?php echo $store ?>forgot-password.php" target="css_editor_storefront"><?php xMsg('LF', 'LF_PAGE_FORGOT_PASSWORD') ?></a></li>
        <li><a href="<?php echo $store ?>new-password.php" target="css_editor_storefront"><?php xMsg('LF', 'LF_PAGE_NEW_PASSWORD') ?></a></li>
        <li><a href="<?php echo $store ?>activation.php" target="css_editor_storefront"><?php xMsg('LF', 'LF_PAGE_ACTIVATION') ?></a></li>
    </ul>
    <p><?php xMsg('LF', 'LF_ATTENTION_UNREG_TEXT') ?></p>
    <h3><?php xMsg('LF', 'LF_GO_URL_TITLE') ?></h3>
    <div class="button_go"></div><div class="url"><input type="text" /></div>
</div>

</div>
</div>

</div>

<div id="rulesDebug"></div>

<script language="JavaScript">
$(function() {
	window.setTimeout(function() {
	   $('body').SkinsPanel(<?php echo modApiStaticFunc('Look_Feel', 'getPanelSettingsJSON') ?>);
	},
	100);
});
</script>

<?php
echo $application->combineAdminJS(array(
    'js/main.js',
    'js/jquery.ui.widget.js',
    'js/jquery.ui.button.js',
    'js/jquery.ui.accordion.js',
    'colorpicker/js/colorpicker.js',
    'js/css_editor_panel.js',
    'js/css_editor_rule.js',
    'js/css_editor_property.js',
));

?>

</body>
</HTML>