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
 * Look_Feel class
 *
 * Common API class for Look & Feel.
 *
 * @author Sergey Kulitsky
 * @version $Id: look_feel_api.php xxxx 2010-01-13 16:40:47Z azrael $
 * @package Look & Feel
 */
class Look_Feel
{
    function Look_Feel()
    {
    }

    function install()
    {
        include_once(dirname(__FILE__) . '/includes/install.inc');
    }

    function getTables()
    {
    }

    function uninstall()
    {
        global $application;

        $tables = Configuration :: getTables();
        $columns = $tables['store_settings']['columns'];

        $query = new DB_Delete('store_settings');
        $query -> WhereValue($columns['name'], DB_EQ, STOREFRONT_ACTIVE_SKIN);
        $application -> db -> getDB_Result($query);
    }

    /**
     * Checks if the provided skin exists
     */
    function checkSkin($skin)
    {
        global $application;

        if (!$skin)
            return false;

        // skins beginning with a dot ('.') are hidden ones...
        if ($skin{0} == '.')
            return false;

        $skin_dir = $application -> getAppIni('PATH_THEMES') . $skin;

        if (is_dir($skin_dir) && is_readable($skin_dir))
            return true;

        return false;
    }

    /**
     * Returns the current storefront skin
     */
    function getCurrentSkin()
    {
        // reading the current skin
        $current_skin = modApiFunc('Configuration', 'getValue',
                                   STOREFRONT_ACTIVE_SKIN);

        // checking if the current skin is valid
        if ($this -> checkSkin($current_skin))
            return $current_skin;

        // the current skin is invalid
        // trying to change it with the system one...
        $current_skin = 'metro';
        if ($this -> checkSkin($current_skin))
            return $current_skin;

        // Abnormal situation!
        // both current skin and system default skin are invalid
        // trying to set any found skin to be default...
        $skins = $this -> getSkinList();
        if (!empty($skins) && $this -> checkSkin($skins[0]))
            return $skins[0];

        // no skins are found...
        // a good place to throw a fatal error if needed...
        return '';
    }

    /**
     * Returns the list of skins
     */
    function getSkinList($excluded = '')
    {
        global $application;

        $result = array();
        $_root = $application -> getAppIni('PATH_THEMES');
        if (!is_dir($_root) || !is_readable($_root))
            return $result;

        if ($dh = opendir($_root))
        {
            while(($skin = readdir($dh)) !== false)
                if ($skin != $excluded && $this -> checkSkin($skin))
                    $result[] = $skin;

            closedir($dh);
        }

        return $result;
    }

    /**
     * Returns the information for the provided skin
     * Note: function DOES NOT check the skin
     *       use method checkSkin for checking
     */
    function getSkinInfo($skin)
    {
        global $application;

        $skin_dir = $application -> getAppIni('PATH_THEMES') . $skin;
        $result = array(
            'skin' => $skin,
            'path' => $application -> getAppIni('PATH_THEMES') . $skin
        );

        if (is_file($skin_dir . '/info.ini')
                && is_readable($skin_dir . '/info.ini')) {
            CProfiler::ioStart($skin_dir . '/info.ini', 'parse');
            $result = array_merge(parse_ini_file($skin_dir . '/info.ini'),
                                  $result);
            CProfiler::ioStop();
        }

        return $result;
    }

    /**
     * Returns detailed list of skins
     */
    function getDetailedSkinList($excluded = '')
    {
        $result = $this -> getSkinList($excluded);
        foreach($result as $k => $v)
            $result[$k] = $this -> getSkinInfo($v);

        usort($result, array($this, '_cmpSkinNames'));

        return $result;
    }

    function _cmpSkinNames($a, $b)
    {
        return _ml_strcasecmp($a['name'], $b['name']);
    }

    /**
     * Changes the storefront skin
     */
    function changeSkin($skin)
    {
        if ($this -> checkSkin($skin))
        {
            modApiFunc('Configuration', 'setValue',
                       array(STOREFRONT_ACTIVE_SKIN => $skin));
            global $application;
            $cache = CCacheFactory::getCache('persistent', 'page_manager');
            $cache->erase();
            return $skin;
        }

        return '';
    }

     function isMobile()
     {
            if (!$this -> checkSkin('system_mobile')) return false;
    		global $zone;
    		if($zone == 'CustomerZone')
    		{
    			$mAgents = array('AvantGo','DoCoMo','KDDI','UP.Browser','Vodafone','J-PHONE','DDIPOCKET','PDXGW','ASTEL','Palm','Windows CE','armv','Minimo','OPWV-SDK','Plucker','PDA','Mobile Content Viewer','PlayStation','Xiino','Android','iPad','iPhone','Opera Mobi','T-Mobile','BlackBerry','Opera Mini','Cricket','IEMobile','HTC','Windows Phone','htc','Kindle','Obigo','POLARIS','Teleca','LGE','MIDP','MOT-','Smartphone','Nintendo','Nitro','Nokia','Symbian','EPOC','SAGEM','Samsung','SCH-','SEC-','j2me','SIE-','SonyEricsson','ProxiNet','Elaine');

    			foreach($mAgents as $i=>$m)
    			  $mAgents[$i] = prepareRE($m);

    			//CTrace::err($_SERVER['HTTP_USER_AGENT']);
    		 	if(preg_match('/('.implode('|',$mAgents).')/i',$_SERVER['HTTP_USER_AGENT']))
    				return true;
    		}
    				return false;
    }

    function isFacebook()
    {
		if (!$this -> checkSkin('facebook')) return false;

        global $zone;
        global $application;

        if($zone == 'CustomerZone')
        {
            $request = $application->getInstance('Request');
            $http_referer = isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : '';
            if(preg_match('/(facebook\.com|asc_fb_req=true)/', $http_referer)
                || $application->fb_request
                || $request->getValueByKey('asc_fb_req') != null)
            {
                if($request->getValueByKey('asc_fb_req') == 'no_fb')
                {
                    $application->fb_request = false;
                    return false;
                }
                $application->fb_request = true;
                return true;
            }
        }
        return false;
    }

    function isJoomla()
    {
    	if (!$this -> checkSkin('joomla')) return false;

        global $zone;
    	global $application;

    	if($zone == 'CustomerZone')
    	{
    		$request = $application->getInstance('Request');
    		$http_referer = isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : '';
    		if(preg_match('/(asc_joomla_req=true)/', $http_referer)
    				|| $application->joomla_request
    				|| $request->getValueByKey('asc_joomla_req') != null)
    		{
    			if($request->getValueByKey('asc_joomla_req') == 'no_joomla')
    			{
    				$application->joomla_request = false;
    				return false;
    			}
    			$application->joomla_request = true;
    			if($request->getValueByKey('jm_ret_url') != null)
    				$application->jm_ret_url = $request->getValueByKey('jm_ret_url');

    			return true;
    		}
    	}
    	return false;
    }

    function isWP()
    {
if (!$this -> checkSkin('wp')) return false;

global $zone;
    	global $application;

    	if($zone == 'CustomerZone')
    	{
    		$request = $application->getInstance('Request');
    		$http_referer = isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : '';
    		if(preg_match('/(asc_wp_req=true)/', $http_referer)
    				|| $application->wp_request
    				|| $request->getValueByKey('asc_wp_req') != null)
    		{
    			if($request->getValueByKey('asc_wp_req') == 'no_wp')
    			{
    				$application->wp_request = false;
    				return false;
    			}
    			$application->wp_request = true;
    			if($request->getValueByKey('wp_ret_url') != null)
    				$application->wp_ret_url = $request->getValueByKey('wp_ret_url');

    			return true;
    		}
    	}
    	return false;
    }

function isCSSEditor()
    {
                       if(isset($_COOKIE['edit_skin']))
                   return true;
               return false;
    }

    // themes-related functions

    /**
     * Fetch list of themes in the skin.
     * @param string $theme
     * @return array
     */
    static public function getThemeList($skin = null)
    {
        global $application;
        static $themes_cache = array();

        if (! $skin) {
            $skin = modApiFunc('Look_Feel', 'getCurrentSkin');
        }
        if (isset($themes_cache[$skin])) {
            return $themes_cache[$skin];
        }

        $themes_pattern = $application -> getAppIni('PATH_THEMES') . $skin . '/themes/' . THEME_FILENAME_HEAD . '*' . THEME_FILENAME_TAIL;
        $themes_raw = asArray(glob($themes_pattern));

        $re = '/' . prepareRE($application -> getAppIni('PATH_THEMES') . $skin . '/themes/' . THEME_FILENAME_HEAD) . '(.*)' . prepareRE(THEME_FILENAME_TAIL) . '/';
        $themes = array();
        foreach($themes_raw as $theme_path) {
            if (preg_match($re, $theme_path, $match) && is_file($theme_path)) {
                $path = $application->getAppIni('PATH_THEME') . 'themes/' . THEME_FILENAME_HEAD . $match[1] . THEME_FILENAME_TAIL;
                $themes[ $match[1] ] = array(
                    'name' => $match[1],
                    'url' => $application->getAppIni('URL_THEME') . 'themes/' .
                            THEME_FILENAME_HEAD . $match[1] . THEME_FILENAME_TAIL,
                    'path' => $path,
                    'editable' => is_writable($path),
                );
            }
        }
        $themes_cache[$skin] = $themes;
        return $themes;
    }

    static private function getSkinSettings($skin = null)
    {
        $skins_settings = unserialize(modApiFunc('Configuration', 'getValue', STOREFRONT_SKIN_SETTINGS));
        if (! $skin) {
            $skin = modApiFunc('Configuration', 'getValue', STOREFRONT_ACTIVE_SKIN);
        }
        if (isset($skins_settings[$skin]) && is_array($skins_settings[$skin])) {
            $settings = $skins_settings[$skin];
        }
        else {
            $settings = array(
                'active_theme' => null,
                'edited_theme' => null,
            );
        }
        return $settings;
    }

    static private function setSkinSettings($settings, $skin = null)
    {
        $skins_settings = unserialize(modApiFunc('Configuration', 'getValue', STOREFRONT_SKIN_SETTINGS));

        if (! $skin) {
            $skin = modApiFunc('Configuration', 'getValue', STOREFRONT_ACTIVE_SKIN);
        }
        $skins_settings[$skin] = $settings;

        modApiFunc('Configuration', 'setValue', array( STOREFRONT_SKIN_SETTINGS => serialize($skins_settings)));
    }

    static public function checkTheme($theme, $skin = null)
    {
        $themes = self::getThemeList($skin);
        return isset($themes[$theme]) ? $theme : null;
    }

    static public function getActiveTheme($skin = null)
    {
        $settings = self::getSkinSettings($skin);
        return self::checkTheme(@ $settings['active_theme'], $skin);
    }

    static public function getActiveSkin()
    {
        global $application;
		$skins = array();
        $tables = Configuration :: getTables();
        $columns = $tables['store_settings']['columns'];

        $query = new DB_Select('store_settings');
        $query->addSelectField($columns["variable_value"], "variable_value");
        $query -> WhereValue($columns['name'], DB_EQ, STOREFRONT_ACTIVE_SKIN);
        $skins = $application->db->getDB_Result($query);
		return $skins[0]['variable_value'];
    }

    static public function getActiveThemeURL($skin = null)
    {
        $active_theme = modApiStaticFunc('Look_Feel', 'getActiveTheme');
        $theme_subpath = 'themes/'.THEME_FILENAME_HEAD.$active_theme.THEME_FILENAME_TAIL;
        $time = @filemtime(getTemplateFileExactAbsolutePath($theme_subpath));
        return $time ? getTemplateFileExactURL($theme_subpath).'?v='.$time : '';
    }

    static public function getEditedTheme($skin = null)
    {
        $settings = self::getSkinSettings($skin);
        $edited_theme = isset($settings['edited_theme']) ? $settings['edited_theme'] : 'new_theme';
        return self::checkTheme($edited_theme, $skin);
    }

    static public function getEditedThemeObj($skin = null)
    {
        $settings = self::getSkinSettings($skin);
        $edited_theme = isset($settings['edited_theme']) ? $settings['edited_theme'] : 'new_theme';
        $themes = self::getThemeList($skin);
        return isset($themes[$edited_theme]) ? $themes[$edited_theme] : null;
    }

    static public function getThemesFolderPath($skin = null)
    {
        global $application;
        if (! $skin) {
            $skin = modApiFunc('Configuration', 'getValue', STOREFRONT_ACTIVE_SKIN);
        }
        return $application->getAppIni('PATH_THEMES') . $skin . '/themes';
    }

    static public function getThemePath($theme_name, $skin = null)
    {
        global $application;
        if (! $skin) {
            $skin = modApiFunc('Configuration', 'getValue', STOREFRONT_ACTIVE_SKIN);
        }
        return self::getThemesFolderPath($skin) . '/' . THEME_FILENAME_HEAD . $theme_name . THEME_FILENAME_TAIL;
    }

    const FAIL = 0;
    const OK = 1;

    static public function addNewTheme($theme_name, $skin = null)
    {
        $theme_path = self::getThemePath($theme_name, $skin);

        if (is_file($theme_path)) {
            return array(
                'result' => self::FAIL,
                'message' => getXMsg('LF', 'LF_THEME_ALREADY_EXISTS'),
            );
        }

        if (@ file_put_contents($theme_path, '') === false) {
            $themes_path = self::getThemesFolderPath($skin);
            if (is_writable($themes_path)) {
                //                                                                                                                                         :                          ,                                                       ,                                                    .
                return array(
                    'result' => self::FAIL,
                    'message' => getXMsg('LF', 'LF_CANNOT_CREATE_THEME'),
                );
            }
            return array(
                'result' => self::FAIL,
                'message' => getXMsg('LF', 'LF_UNABLE_WRITE_THEME'),
                'what_to_do' => strtr(getXMsg('LF', 'LF_THEMES_PERMISSIONS'), array('%themes_folder%' => $themes_path)),
            );
        }

        return array(
            'result' => self::OK,
            'message' => getXMsg('LF', 'LF_THEME_CREATED'),
        );
    }

    static public function setActiveTheme($theme_name, $skin = null)
    {
        $settings = self::getSkinSettings($skin);
        $settings['active_theme'] = $theme_name;
        self::setSkinSettings($settings);
    }

    static public function setEditedTheme($theme_name, $skin = null)
    {
        $settings = self::getSkinSettings($skin);
        $settings['edited_theme'] = $theme_name;
        self::setSkinSettings($settings);
    }

    static public function removeTheme($theme_name, $skin = null)
    {
        $theme_path = self::getThemePath($theme_name);

        if (is_file($theme_path)) {
            if (unlink($theme_path) && ! file_exists($theme_path)) {
                return array(
                    'result' => self::OK,
                    'message' => '',
                );
            }
            else {
                return  array(
                    'result' => self::FAIL,
                    'message' => getXMsg('LF', 'LF_CANNOT_REMOVE_THEME'),
                    'what_to_do' => strtr(getXMsg('LF', 'LF_CANNOT_REMOVE_THEME_WTD'), array('%theme_file%' => $theme_path)),
                );
            }
        }
        else {
            return  array(
                'result' => self::FAIL,
                'message' => getXMsg('LF', 'LF_THEME_DOESNT_EXIST'),
                'what_to_do' => strtr(getXMsg('LF', 'LF_THEME_DOESNT_EXIST_WTD'), array('%theme_file%' => $theme_path)),
            );
        }
    }

    public static function saveThemeCss($theme_name, $stylesheets, $skin = null)
    {
        if (! $skin) {
            $skin = modApiFunc('Configuration', 'getValue', STOREFRONT_ACTIVE_SKIN);
        }
        $backup_path = CConf::get('themes_backup_dir') . $skin . '.' . $theme_name . date('.Y-m-d.H-i-s') . '.css';
        $file = new CFile($backup_path);
        $file->putContent($stylesheets);

        $theme_path = self::getThemePath($theme_name, $skin);
        $file = new CFile($theme_path);
        if ($file->putContent($stylesheets)) {
            return array(
                'result' => self::OK,
                'message' => '',
            );
        }
        return  array(
            'result' => self::FAIL,
            'message' => getXMsg('LF', 'LF_THEME_CANT_WRITE'),
            'what_to_do' => strtr(getXMsg('LF', 'LF_THEME_PERMISSIONS'), array('%theme_file%' => $theme_path)),
            'path' => $theme_path,
        );
    }

    static public function setPanelSetting($name, $value)
    {
        $panel_settings = unserialize(modApiFunc('Configuration', 'getValue', STOREFRONT_SKIN_PANEL_SETTINGS));
        if (! $panel_settings) {
            $panel_settings = array();
        }

        $panel_settings[$name] = $value;

        modApiFunc('Configuration', 'setValue', array( STOREFRONT_SKIN_PANEL_SETTINGS => serialize($panel_settings)));
    }

    static public function getPanelSetting($name)
    {
        $panel_settings = unserialize(modApiFunc('Configuration', 'getValue', STOREFRONT_SKIN_PANEL_SETTINGS));
        return @ $panel_settings[$name];
    }

    static public function getPanelSettingsJSON()
    {
        global $application;

        $panel_settings = unserialize(modApiFunc('Configuration', 'getValue', STOREFRONT_SKIN_PANEL_SETTINGS));
        if (! $panel_settings) {
            $panel_settings = array();
        }

        $panel_settings['active_theme'] = (string) modApiStaticFunc('Look_Feel', 'getActiveTheme');
        $panel_settings['edited_theme'] = modApiStaticFunc('Look_Feel', 'getEditedTheme');
        $panel_settings['skin_default_theme_title'] = getXMsg('LF', 'LF_SKIN_DEFAULTS_TITLE');
        $panel_settings['themes_folder'] = $application->getAppIni('PATH_THEME_THEMES');
        $panel_settings['labels'] = array(
            'set_active_fail_wtd' => getXMsg('LF', 'LF_STATUS_SET_ACTIVE_FAIL_WTD'),
            'alert_not_saved_theme' => getXMsg('LF', 'ALERT_NOT_SAVED_THEME'),
            'alert_not_saved_theme_nav' => getXMsg('LF', 'ALERT_NOT_SAVED_THEME_NAV'),
        );

        loadCoreFile('JSON.php');
        $json = new Services_JSON();
        return $json->encode($panel_settings);
    }

    static function getEditorRules()
    {
        global $application;
        return trim(preg_replace('/\s+/', ' ', combineFiles($application->getAppIni('PATH_THEME').'css/*editable.txt')));
    }

}
?>