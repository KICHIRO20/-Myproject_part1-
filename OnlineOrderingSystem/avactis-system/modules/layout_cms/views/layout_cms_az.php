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
 * @package Layout CMS
 * @author Alexey Astafyev
 *
 */

/**
 * Definition of Layout CMS
 * Layout CMS is used to manage page layouts in the store
 */
class LayoutCMS
{
    /**
     * Constructor
     */
    function LayoutCMS()
    {
        // initializing the template engine
        $this -> mTmplFiller = new TmplFiller();
    }

    /**
     * The main function to output the content.
     */
    function output()
    {
        global $application;

        $themePath = modApiFunc('Layout_CMS', 'getThemePath');
        $layoutsPath = modApiFunc('Layout_CMS', 'getYAMLLayoutsPath');
        if(file_exists($themePath.'map.ini'))
        {
            if(!(is_writable($themePath) && is_writable($themePath.'map.ini')))
            {
                $this->_Template_Contents = array(
                    'PermList' => implode('<br>', array($themePath, $themePath.'map.ini'))
                );
                $application -> registerAttributes($this -> _Template_Contents);
                return $this->mTmplFiller->fill('layout_cms/layout_mgr/', 'error-perms.tpl.html', array());
            }
            if(modApiFunc('Layout_CMS', 'parseMapIni'))
            {
                $this->_Template_Contents = array('MapIni' => $themePath.'map.ini');
                $application -> registerAttributes($this -> _Template_Contents);
                return $this->mTmplFiller->fill('layout_cms/layout_mgr/', 'map-ini-parsing-error.tpl.html', array());
            }
        }

        $pagesList = modApiFunc('Layout_CMS', 'getPagesList');
        $themesList = modApiFunc('Look_Feel', 'getSkinList');
        $checkList = array($layoutsPath);
        $checkList[] = $layoutsPath . 'default.yml';
        foreach($pagesList as $p) $checkList[] = $layoutsPath . "$p.yml";
        foreach($checkList as $i=>$c) if(is_writable($c)) unset($checkList[$i]);
        if(!empty($checkList))
        {
            $this->_Template_Contents = array(
                'PermList' => implode('<br>', $checkList)
            );
            $application -> registerAttributes($this -> _Template_Contents);
            return $this->mTmplFiller->fill('layout_cms/layout_mgr/', 'error-perms.tpl.html', array());
        }

        $settingsList = modApiFunc('Layout_CMS', 'getSettingsList');
        $template_contents = array(
            'PagesList' => $this->outPagesList($pagesList),
            'ThemesList' => $this->outThemesList($themesList),
            'SettingsList' => $this->outSettingsList($settingsList),
            'FirstPage' => array_shift($pagesList)
        );
        $this -> _Template_Contents = $template_contents;
        $application -> registerAttributes($this -> _Template_Contents);
        return $this -> mTmplFiller -> fill('layout_cms/layout_mgr/', 'container.tpl.html', array());
    }

    function outSettingsList($settingsList)
    {
        global $application;

        $html = '';
        foreach($settingsList as $setting)
        {
            $template_contents = array(
                'SettingName' => $setting
            );
            $this->_Template_Contents = $template_contents;
            $application->registerAttributes($this->_Template_Contents);
            $html.= $this->mTmplFiller->fill('layout_cms/layout_mgr/','page-setting.tpl.html', array());
        }
        return $html;
    }

    function outPagesList($pagesList)
    {
        global $application;

        $html = '';
        foreach($pagesList as $page)
        {
            $template_contents = array(
                'PageName' => $page
            );
            $this->_Template_Contents = $template_contents;
            $application->registerAttributes($this->_Template_Contents);
            $html.= $this->mTmplFiller->fill('layout_cms/layout_mgr/', 'page-item.tpl.html', array());
        }
        return $html;
    }

    function outThemesList($themesList)
    {
        global $application;

        $html = '';
        $activeTheme = modApiFunc('Look_Feel', 'getCurrentSkin');
        foreach($themesList as $theme)
        {
            $theme_info = modApiFunc('Look_Feel','getSkinInfo',$theme);
            if(is_array($theme_info) && !empty($theme_info['name']))
                $theme_display_name = $theme_info['name'];
            else
                $theme_display_name = $theme;

            $template_contents = array(
                'ThemeName'      => $theme,
                'ThemeDisplayName' => $theme_display_name,
                'Active'         => ($theme == $activeTheme ? 'selected' : ''),
                'ActiveLabel'    => ($theme == $activeTheme ? ' (current skin)' : ''),
                'ActiveClass'    => ($theme == $activeTheme ? 'active-skin' : '')
            );
            $this->_Template_Contents = $template_contents;
            $application->registerAttributes($this->_Template_Contents);
            $html.= $this->mTmplFiller->fill('layout_cms/layout_mgr/', 'theme-item.tpl.html', array());
        }
        return $html;
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
}