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
	include('../admin.php');
    if (! modApiFunc('Users', 'checkCurrentUserPermission', PERMISSION_DESIGN)) {
        echo '<div class="no_access">Sorry, You have no access to this content.</div>';
        die;
    }

	loadCoreFile('JSON.php');

	$r = new Request();

	switch ($r->getValueByKey('request')) {
        case 'get_theme_list':
            $themes = modApiStaticFunc('Look_Feel', 'getThemeList');
            $json = new Services_JSON();
            echo $json->encode($themes);
            exit;

        case 'add_theme':
            $result = modApiStaticFunc('Look_Feel', 'addNewTheme', $r->getValueByKey('name'));
            $json = new Services_JSON();
            echo $json->encode($result);
            exit;

        case 'set_active_theme':
            modApiStaticFunc('Look_Feel', 'setActiveTheme', $r->getValueByKey('name'));
            modApiFunc('Configuration', 'resetCache');
            $active = modApiFunc('Look_Feel', 'getActiveTheme');
            echo $active == $r->getValueByKey('name') ? 'ok' : 'fail('.$r->getValueByKey('name').':'.$active.')';
            exit;

        case 'set_edited_theme':
            modApiStaticFunc('Look_Feel', 'setEditedTheme', $r->getValueByKey('name'));
            exit;

        case 'remove_theme':
            $result = modApiStaticFunc('Look_Feel', 'removeTheme', $r->getValueByKey('name'));
            $json = new Services_JSON();
            echo $json->encode($result);
            exit;

	    case 'save_theme':
	        $name = $r->getValueByKey('name');
	        $css = $r->getValueByKey('css');
	        $result = modApiStaticFunc('Look_Feel', 'saveThemeCss', $name, $css);
            $json = new Services_JSON();
            echo $json->encode($result);
	        exit;

	    case 'set_panel_setting':
	        modApiStaticFunc('Look_Feel', 'setPanelSetting', $r->getValueByKey('name'), $r->getValueByKey('value'));
	        echo 'done';
	        exit;

	}