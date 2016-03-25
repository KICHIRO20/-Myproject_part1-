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

/**
 * @package Look & Feel
 * @author Sergey Kulitsky
 *
 */

/**
 * Definition of SkinList viewer
 * The viewer is used to manage storefront skins in the store
 */
class SkinList
{
    /**
     * Constructor
     */
    function SkinList()
    {
        // initializing the template engine
        $this -> mTmplFiller = new TmplFiller();
        loadCoreFile('html_form.php');
    }

    /**
     * The main function to output the viewer content.
     */
    function output()
    {
        global $application;

        $current_skin = modApiFunc('Look_Feel', 'getCurrentSkin');
        $skins = modApiFunc('Look_Feel', 'getDetailedSkinList', $current_skin);
        if ($current_skin)
            $current_skin = modApiFunc('Look_Feel', 'getSkinInfo',
                                       $current_skin);

        $template_contents = array(
            'CurrentSkinInfo' => $this -> outputSkinInfo($current_skin),
            'SkinList'        => $this -> outputSkins($skins),
            'AdminZoneURL'    => $this -> getAdminZoneURL(),
            'AdminZonePath'   => $this -> getAdminZonePath(),
        );
        $this -> _Template_Contents = $template_contents;
        $application -> registerAttributes($this -> _Template_Contents);
        return $this -> mTmplFiller -> fill('look_feel/skin_list/',
                                            'container.tpl.html', array());
    }

    /**
     * Returns the URL to admin zone
     */
    function getAdminZoneURL()
    {
        global $application;

        if (isset($application -> appIni['HTTPS_URL'])
            && $application -> getCurrentProtocol() == 'https')
            return $application -> appIni['SITE_AZ_HTTPS_URL'];

        return $application -> appIni['SITE_AZ_URL'];
    }

    /**
     * Returns the path to admin zone
     */
    function getAdminZonePath()
    {
        global $application;

        if (isset($application -> appIni['HTTPS_URL'])
            && $application -> getCurrentProtocol() == 'https')
            $path = $application -> appIni['SITE_AZ_HTTPS_URL'];
        else
            $path = $application -> appIni['SITE_AZ_URL'];

        $path = parse_url($path);

        return $path['path'];
    }

    /**
     * Outputs skin detailed info
     */
    function outputSkinInfo($skin_info)
    {
        global $application;

        if (!is_array($skin_info) || empty($skin_info))
            return '';

	 $skin_path = $application -> appIni['PATH_THEMES'] . $skin_info['skin'] . '/';
	 	$active_skin = modApiFunc('Look_Feel','getActiveSkin');
        $permissions_warning = $this->getPermissionsWarning($skin_info);
        $template_contents = array(
            'S_Thumbnail' => ((isset($skin_info['thumbnail']))
                              ? $skin_info['thumbnail'] : ''),
            'S_ThumbnailPath' => $application -> appIni['URL_THEMES'] .
                                 $skin_info['skin'] . '/' .
                                 @$skin_info['thumbnail'],
            'S_Image' => ((isset($skin_info['image']))
                          ? $skin_info['image'] : ''),
            'S_ImagePath' => $application -> appIni['URL_THEMES'] .
                             $skin_info['skin'] . '/' . @$skin_info['image'],
            'S_Name' => ((isset($skin_info['name'])) ? $skin_info['name']
                                                     : $skin_info['skin']),
            'S_Skin' => $skin_info['skin'],
			'S_Active' => $active_skin,
            'S_Version' => ((isset($skin_info['version']))
                         ? getMsg('LF', 'LF_SKIN_VERSION') . ' ' . $skin_info['version']
                         : ''),
            'S_Description' => ((isset($skin_info['description']))
                                ? $skin_info['description'] : ''),
            'S_Location' => _ml_substr($application -> appIni['PATH_THEMES'],
                            _ml_strlen($application -> appIni['PATH_ASC_ROOT']))
                            . $skin_info['skin'] . '/',
            'Local_isMultipleThemes' => (bool) modApiStaticFunc('Look_Feel', 'getThemeList'),
            'Local_ThemeSwitcher' => $this->getThemeSwitcher(),
            'Local_LaunchEditorButton' => $this->getLaunchEditorButton(),
            'Local_PermissionsWarning' => $permissions_warning,

        );
        $this -> _Template_Contents = $template_contents;
        $application -> registerAttributes($this -> _Template_Contents);
        return $this -> mTmplFiller -> fill('look_feel/skin_list/',
                                            'current_skin.tpl.html', array());
    }

    function getThemeSwitcher()
    {
        $switcher = '';
        $themes = modApiStaticFunc('Look_Feel', 'getThemeList');
        if ($themes) {
            $values = array(array('value' => '', 'contents' => getXMsg('LF', 'LF_THEME_DEFAULT')));
            foreach ($themes as $theme) {
                $values[] = array('value' => $theme['name'], 'contents' => $theme['name']);
            }
            $switcher = HtmlForm::genDropdownSingleChoice(array(
                'select_name' => 'active_theme',
                'class' => 'input-medium',
                'selected_value' => modApiFunc('Look_Feel', 'getActiveTheme'),
                'values' => $values,
                'onChange' => 'setActiveTheme(this)',
            ));
        }
        return $switcher;
    }

    /**
     * Outputs skin list
     */
    function outputSkins($skins)
    {
        global $application;

        if (!is_array($skins) || empty($skins))
            return $this -> mTmplFiller -> fill('look_feel/skin_list/',
                                                'no_skins.tpl.html', array());

        $template_contents = array(
            'Skins' => $this -> outputSkinDetails($skins)
        );
        $this -> _Template_Contents = $template_contents;
        $application -> registerAttributes($this -> _Template_Contents);
        return $this -> mTmplFiller -> fill('look_feel/skin_list/',
                                            'active_skins.tpl.html', array());
    }

    /**
     * Outputs skin list details
     */
    function outputSkinDetails($skins)
    {
        global $application;

        if (!is_array($skins) || empty($skins))
            return $this -> mTmplFiller -> fill('look_feel/skin_list/',
                                                'no_skins.tpl.html', array());
	 	$active_skin = modApiFunc('Look_Feel','getActiveSkin');
        $result = '';
        foreach($skins as $skin)
        {
            if(isset($skin['hidden']) && $skin['hidden'] == 'true') continue;
            $template_contents = array(
                'S_Thumbnail' => ((isset($skin['thumbnail']))
                                  ? $skin['thumbnail'] : ''),
                'S_ThumbnailPath' => $application -> appIni['URL_THEMES'] .
                                     $skin['skin'] . '/' . @$skin['thumbnail'],
                'S_Image' => ((isset($skin['image']))
                              ? $skin['image'] : ''),
                'S_ImagePath' => $application -> appIni['URL_THEMES'] .
                                 $skin['skin'] . '/' . @$skin['image'],
                'S_Name' => ((isset($skin['name'])) ? $skin['name']
                                                     : $skin['skin']),
                'S_Version' => ((isset($skin['version']))
                                ? getMsg('LF', 'LF_SKIN_VERSION') . ' ' .
                                  $skin['version']
                                : '&nbsp;'),
                'S_Skin' => urlencode($skin['skin']),
				'S_Active' => $active_skin,
            );
            $this -> _Template_Contents = $template_contents;
            $application -> registerAttributes($this -> _Template_Contents);
            $result .= $this -> mTmplFiller -> fill('look_feel/skin_list/',
                                                    'skin.tpl.html', array());
        }

        return $result;
    }

    function renderWarning($message, $tpl = 'warning.tpl.html')
    {
        return $this -> mTmplFiller -> fill('look_feel/skin_list/', $tpl, array('WarningMessage' => $message));
    }

    function getLaunchEditorButton()
    {
        return $this->enable_editing
                ? $this -> mTmplFiller -> fill('look_feel/skin_list/', 'launch_editor.tpl.html', array())
                : '';
    }

    function getPermissionsWarning($skin_info)
    {
        global $application;
        $skin_path = $application -> appIni['PATH_THEMES'] . $skin_info['skin'] . '/';
        $warnings = array();

        $themes_dir = $skin_path . 'themes';
        if (! is_dir($themes_dir)) {
            $warnings[] = $this -> renderWarning(getXMsg('LF', 'WRN_NO_THEMES_DIR', $themes_dir));
        }
        elseif (! is_writable($themes_dir)) {
            $warnings[] = $this -> renderWarning(getXMsg('LF', 'WRN_THEMES_DIR_NOT_WRITABLE', $themes_dir));
        }

        $images_dir = $skin_path . 'images/upload';
        if (! is_dir($images_dir)) {
        	if (! mkdir($images_dir,0755))
            	$warnings[] = $this -> renderWarning(getXMsg('LF', 'WRN_NO_IMAGES_DIR', $images_dir));
        }
        elseif (! is_writable($images_dir)) {
            $warnings[] = $this -> renderWarning(getXMsg('LF', 'WRN_IMAGES_DIR_NOT_WRITABLE', $images_dir));
        }

        $themes_warnings = array();
        $themes = modApiStaticFunc('Look_Feel', 'getThemeList');
        foreach ($themes as $theme) {
            if (! $theme['editable']) {
                $themes_warnings[] = $this -> renderWarning($theme['path']);
            }
        }
        if ($themes_warnings) {
            $warnings[] = $this -> mTmplFiller -> fill('look_feel/skin_list/', 'warning_list_themes.tpl.html',
                    array('Warnings' => implode('', $themes_warnings)));
        }

        if (false && Look_Feel::getEditorRules() == '') {
            $warnings[] = $this -> renderWarning(getXMsg('LF', 'WRN_CSS_RULES_NOT_DEFINED', $images_dir));
        }
        elseif (sizeof($themes_warnings) < sizeof($themes) || is_writable($themes_dir)) {
            $this->enable_editing = true;
        }

        return $warnings
            ? $this -> mTmplFiller -> fill('look_feel/skin_list/', 'warning_list.tpl.html',
                    array('Warnings' => implode('', $warnings)))
            : '';
    }

    /**
     * Processes local tags
     */
    function getTag($tag)
    {
        return getKeyIgnoreCase($tag, $this -> _Template_Contents);
    }

    var $mTmplFiller;
    var $_Template_Contents;
    var $enable_editing = false;

}