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
 * Bootstrap
 *
 * @package Core
 * @author Alexey Florinsky
 * @access  public
 */
class Bootstrap
{
    function Bootstrap()
    {
    }

    function preboot()
    {
        $this->_preboot_php_ini_set();
        $this->_preboot_check_app_lock();
        $this->_preboot_check_unexpected_output();
        $this->_preboot_prepare_request();
    }

    function includeFile($list)
    {
        if (is_string($list)) $list = array($list);
        foreach ($list as $f)
        {
            $f = prepareFSPath($f);
        	if ($f && ! isset($this->included_file[$f]) && file_exists($f))
            {
                $this->included_file[$f] = true;
                CProfiler::includeStart($f);
                include_once($f);
                CProfiler::includeStop();
            }
        }
    }

    function boot()
    {
        $GLOBALS['__localization_disable_formatting__'] = false;

        $GLOBALS['application'] = new Application();
        $GLOBALS['application']->readAppINI();
        //CTrace::inf('Point 3.1 (after readAppINI)');

        $GLOBALS['__session_db_handler_object'] = new SessionDBHandler();

        // check cache folder
        if(!is_dir_writable($GLOBALS['application']->getAppINI('PATH_CACHE_DIR')))
        {
            _fatal(array( "CODE" => "CORE_040"), $GLOBALS['application']->getAppINI('PATH_CACHE_DIR'));
        }

        $GLOBALS['application']->init();
        //CTrace::inf('Point 3.2 (after application->init)');

        // include custom template file
        if (isset($GLOBALS['__SYSTEM_TPL_DIR__']) && file_exists($GLOBALS['__SYSTEM_TPL_DIR__'].'custom.php'))
        {
            include_once($GLOBALS['__SYSTEM_TPL_DIR__'].'custom.php');
        }
        if (isset($GLOBALS['__TPL_DIR__']) && file_exists($GLOBALS['__TPL_DIR__'].'custom.php'))
        {
            include_once($GLOBALS['__TPL_DIR__'].'custom.php');
        }

        // include functions template file
        if (isset($GLOBALS['__SYSTEM_TPL_DIR__']) && file_exists($GLOBALS['__SYSTEM_TPL_DIR__'].'functions.php'))
        {
            include_once($GLOBALS['__SYSTEM_TPL_DIR__'].'functions.php');
        }
        if (isset($GLOBALS['__TPL_DIR__']) && file_exists($GLOBALS['__TPL_DIR__'].'functions.php'))
        {
            include_once($GLOBALS['__TPL_DIR__'].'functions.php');
        }
    }

    function postboot()
    {
        $this->_postboot_check_configdef();
        $this->_postboot_check_affiliate_id();
        $this->_postboot_check_store_block_cache();
        $this->_postboot_check_map_ini();
        register_shutdown_function('__shutdown__');
    }

    function _preboot_php_ini_set()
    {
        # Support Mode
        if ( (isset($_GET['asc_action']) && strtolower($_GET['asc_action']) == 'setsupportmode') ||
             (defined('DEBUG_MODE') && DEBUG_MODE == 1) /*FORCE_DISPLAY_ERRORS*/)
        {
            if (isset($_GET['asc_action']) && strtolower($_GET['asc_action']) == 'setsupportmode')
            {
                define('SUPPORT_MODE',1);
            }
            ini_set("display_errors", "On");
            ini_set("display_startup_errors", "On");
        }
        else
        {
            ini_set("display_errors", "Off");
            ini_set("display_startup_errors", "Off");
        }
		//ini_set("log_errors",true);
		//ini_set("error_log",dirname(dirname(dirname(__FILE__)))."/error_log");
		//error_reporting(E_ERROR);


        # Memory
        @set_time_limit(0);
        $memory_limit = @(int)ini_get('memory_limit');
        if ($memory_limit < 128)
        {
            @ini_set('memory_limit', "128M");
        }
    }

    function _preboot_check_app_lock()
    {
        //                                    .
        //                                   .
        $lock = dirname(dirname(dirname(__FILE__))).'/avactis-conf/cache/.full_application_lock';
        $lock_duration = 30;
        if (file_exists($lock) and is_readable($lock) and is_writable($lock))
        {
             $content = trim(@file_get_contents($lock));
             //                                         timestamp
             //                               , timestamp           1 214 482 824
             //             ,
             //                  :              - 10         ,             -      .
            if (strlen($content)>=10 and preg_match("/^[0-9]*$/",$content) and ($content+$lock_duration) > time())
            {
                $template_fname = dirname(__FILE__) . "/../" . "application_locked.tpl.html";
                $text = file_get_contents($template_fname);
                echo $text;
                flush();
                exit;
            }
            else
            {
                @unlink($lock);
            }
        }
    }

    function _preboot_check_unexpected_output()
    {
        /*
         *          ,                        .         -
         *           "                                       -               -  ,
         *           ,             ,             php       AVACTIS
         *        (   html,    php,                                         )"
         *               ,
         * header' .
         */

        /*
         *                            (              php.net):
         *  kamermans at teratechnologies dot net
         *  22-Aug-2006 12:24
         *  If you are using output buffering and you use the flush() command ANYWHERE headers_sent() will return true - even if the buffer is seemingly empty.
         */

        /*
         * Note that in IIS (or at least the version that comes with W2K server), the server seems to do some buffering, so even if you output someting or cause a warning, the value of headers_sent() may be false because the headers haven't been sent yet.
         * So it's not a safe way to know if warnings have been encountered in your script.
         */

        /*
         * php.ini:
         * output_buffering = Off
         */
        if (headers_sent($filename, $linenum))
        {
            //ob_start();
            //echo "<br>inside output buffer<br>";
            //flush();
            ob_get_contents();
            ob_get_clean();

            $msg = "To operate properly, Avactis needs its initialization tags, such as &lt;?php include ('path/avactis-system/store.php'); ?&gt;, to be placed at the very beginning of your e-store source files. No empty spaces are allowed in this string (e.g. blank spaces, tabs, carriage returns). Please check the file<br>"
                  . $filename
                  . "<br>"
                  . "at line<br>"
                  . $linenum
                  . "<br><br>"
                  . "In the example given above, \"path\" is the absolute path to the Avactis Shopping Cart installation directory."
                  . "<br><br>"
                  . "If the error persists, please refer to Avactis Shopping Cart documentation.";

            $template_fname = dirname(__FILE__) . "/../" . "cz_headers_sent.tpl.html";
            $text = file_get_contents($template_fname);
            $text = str_replace("___Errors", $msg, $text);
            echo $text;
            flush();
            exit;
        }
        else
        /* php.ini:
         * output_buffering = On
         * headers_sent() returns false even if html is already in the buffer
         */
        if(strlen(ob_get_contents()) > 0)
        {
            ob_get_clean();
            $msg = "To operate properly, Avactis needs its initialization tags, such as &lt;?php include ('path/avactis-system/store.php'); ?&gt;, to be placed at the very beginning of your e-store source files. No empty spaces are allowed in this string (e.g. blank spaces, tabs, carriage returns)."
                  . "<br><br>"
                  . "In the example given above, \"path\" is the absolute path to the Avactis Shopping Cart installation directory."
                  . "<br><br>"
                  . "If the error persists, please refer to Avactis Shopping Cart documentation.";

            $template_fname = dirname(__FILE__) . "/../" . "cz_headers_sent.tpl.html";
            $text = file_get_contents($template_fname);
            $text = str_replace("___Errors", $msg, $text);
            echo $text;
            flush();
            exit;
        }
    }

    function _preboot_prepare_request()
    {
        #                                  PHP_SELF                 GET
        $pos = strpos($_SERVER["PHP_SELF"], '?');
        if (!($pos === false))
        {
            $_SERVER["PHP_SELF"] = substr($_SERVER["PHP_SELF"], 0, $pos);
        }
    }

    function _postboot_check_configdef()
    {
    }

    function _postboot_check_map_ini()
    {
    }

    function _postboot_check_affiliate_id()
    {
        global $application;
        # Affilite ID tracking
        $aff_id = $application->getAppINI('AFFILIATE_ID_PARAM');
        $setting_update_affiliate = modApiFunc("Settings","getParamValue","AFFILIATE_SETTINGS","UPDATE_AFFILIATE_ID");

        if (modApiFunc("Session","is_set","AffiliateID") && isset($_GET[$aff_id]) && modApiFunc("Session","get","AffiliateID") != $_GET[$aff_id] && $setting_update_affiliate == "CURRENT" )
        {
            unset($_GET[$aff_id]);
        }
        if (isset($_GET[$aff_id]) && !empty($_GET[$aff_id]))
        {
            # affiliate id has to be alphanumeric and its length must not exceed 255 symbols
            if (preg_match("/^[0-9a-zA-Z_\-]+$/i",$_GET[$aff_id]) && strlen($_GET[$aff_id]) <= 255)
                modApiFunc("Session","set","AffiliateID",$_GET[$aff_id]);
            else
                unset($_GET[$aff_id]);
        }
        else
            unset($_GET[$aff_id]);
    }

    function _postboot_check_store_block_cache()
    {
    }

    function preloadCorePHP()
    {
        $this->preloadPHP(CConf::get('preload_core_php'));
    }

    function preloadModulesPHP()
    {
        $this->preloadPHP(CConf::get('preload_modules_php'));
    }

    function preloadModulesViewsCzPHP()
    {
        $this->preloadPHP(CConf::get('preload_modules_views_cz_php'));
    }

    function preloadPHP($preload_php)
    {
        $sys_dir = dirname(dirname(__FILE__)).'/';
        $add_modules_dir = CConf::get('add_modules_dir');
        $modules_dir = CConf::get('modules_dir');

        if (is_readable($preload_php['combined_file'])) {
            global $include_combined_php; // emergency flag
            $include_combined_php = $preload_php['combined_file'];
            CTrace::dbg('Preload combined: '.$preload_php['combined_file']);
            CProfiler::includeStart($include_combined_php);
            include_once $include_combined_php;
            CProfiler::includeStop();
            CTrace::dbg('Done');
            unset($include_combined_php);
        }
        else {
		if(strpos($preload_php['combined_file'],'core')===false){
			foreach ($preload_php['files'] as $php) {
				if(is_file(realpath($add_modules_dir. $php ))){
					$this->includeFile($add_modules_dir. $php);
				}else{
					$this->includeFile($modules_dir . $php);
				}
			}
		}else{

			foreach ($preload_php['files'] as $php) {
				$this->includeFile($sys_dir.$php);
			}
		}
	}
    }

    /**
     * Use avactis-system/admin/index.php?asc_action=combine_php
     */
    function combinePreloadedPHP()
    {
        global $application;
        $mm = $application->getInstance('Modules_Manager');
        $sys_dir = CConf::get('system_dir');
        $modules_dir = CConf::get('modules_dir');
        $add_modules_dir = CConf::get('add_modules_dir');
        $conf_core = CConf::get('preload_core_php');
        $core_files = array();
        foreach ($conf_core['files'] as $file) {
            $core_files[] = realpath($sys_dir . $file);
        }

        if ($php = fopen($conf_core['combined_file'], 'w')) {
            $this->addPhpFiles($php, array_filter($core_files));

            $file = new CFile($application->appIni['PATH_TAGS_FILE']);
            $tags = $file->getLines();
            if (is_array($tags)) {
                foreach ($tags as $tag) {
                    fwrite($php, "if (! function_exists('$tag')) {\n".$application->getTagFunction($tag)."\n} else { \nCTrace::inf('Registering tag: function \\'$tag\\' is already defined.'); }\n");
                    fwrite($php, "if (! function_exists('get$tag')) {\n".$application->getTagGetFunction($tag)."\n} else { \nCTrace::inf('Registering tag: function \\'get$tag\\' is already defined.'); }\n");
                    fwrite($php, "if (! function_exists('getVal$tag')) {\n".$application->getTagGetValFunction($tag)."\n} else { \nCTrace::inf('Registering tag: function \\'getVal$tag\\' is already defined.'); }\n");
                }
                fwrite($php, "\ndefine('GLOBAL_TAGS_REGISTERED', 'yes');\n");
            }
            fclose($php);
        }


        $modules_core = CConf::get('preload_modules_php');
        $modules_files = array();
        foreach ($modules_core['files'] as $file) {
	   if(is_file(realpath($add_modules_dir. $file))){
                $modules_files[] = realpath($add_modules_dir . $file);
            }else{
            	$modules_files[] = realpath($modules_dir. $file);
            }
        }

        foreach ($mm->moduleList as $module_name => $moduleInfo) {
            // see using of COMPILED_MODULES_LOADED constant
            if (isset($moduleInfo->constantsFile)) {
                $const_file = realpath($modules_dir . $moduleInfo->directory . $moduleInfo->constantsFile);
                if (! in_array($const_file, $modules_files) && ! in_array($const_file, $core_files)) {
                    $modules_files[] = $const_file;
                }
            }
            $queries_file = realpath($modules_dir . $moduleInfo->directory . 'dbqueries/common.php');
            if (! in_array($queries_file, $modules_files) && ! in_array($queries_file, $core_files)) {
                $modules_files[] = $queries_file;
            }
        }

        if ($php = fopen($modules_core['combined_file'], 'w')) {
            $this->addPhpFiles($php, array_filter($modules_files));
            fwrite($php, "\ndefine('COMPILED_MODULES_LOADED', 'yes');\n");
            fclose($php);
        }


        $conf_modules_views_cz = CConf::get('preload_modules_views_cz_php');
        $modules_files = array();
        foreach ($conf_modules_views_cz['files'] as $file) {
   	    if(is_file(realpath($add_modules_dir . $file))){
                $modules_files[] = realpath($add_modules_dir . $file);
            }else{
            	$modules_files[] = realpath($modules_dir. $file);
            }
        }

        if ($php = fopen($conf_modules_views_cz['combined_file'], 'w')) {
            $this->addPhpFiles($php, array_filter($modules_files));
//            var_dump($mm->czViewList, $mm->czAliasesList);

            foreach (array_keys($mm->czViewList) as $view) {
                fwrite($php, "
if (! function_exists('$view')) {
    " . $mm->getViewFunction($view) . "
}
else {
    CTrace::inf('Registering module view: function \\'$view\\' is already defined.');
}
if (! function_exists('get$view')) {
    " . $mm->getViewGetFunction($view) . "
}
else {
    CTrace::inf('Registering module view: function \\'get$view\\' is already defined.');
}");
            }

            foreach ($mm->czAliasesList as $alias_name => $view_name) {
                fwrite($php, "
if (! function_exists('$alias_name')) {
    " . $mm->getAliasFunction($alias_name, $view_name) . "
}
else {
    CTrace::inf('Registering module alias: function \\'$alias_name\\' is already defined.');
}
if (! function_exists('get$alias_name')) {
    " . $mm->getAliasGetFunction($alias_name, $view_name) . "
}
else {
    CTrace::inf('Registering module alias: function \\'get$alias_name\\' is already defined.');
}");
            }
            fwrite($php, "\ndefine('MODULES_VIEWS_REGISTERED', 'yes');\n");
            fclose($php);
        }
    }

    function addPhpFiles($php, $files)
    {
        $paths_arr = array();
        foreach ($files as $file) {
            $paths_arr[] = "    '" . $file . "',\n";
        }

        fwrite($php, "<?php\nglobal \$bootstrap;\n\$bootstrap->markPHPLoaded(array(\n".implode('', $paths_arr)."));\n");
        foreach ($files as $file) {
            fwrite($php, "\n/* ".$file." */\n");
            $code = php_strip_whitespace($file);
            $code = preg_replace(array(
//                '/^[\x20\t]+/m',
//                '/^\/\/.*\n/m',
//                '/\/\*.*\*\/\n*/sU',
                '/\s*\?><\?php\s*/',
                '/^\s*<\?php\s*/',
                '/\s*\?>\s*$/'), '', $code);
            fwrite($php, $code);
            fwrite($php, "CTrace::dbg('Combined component loaded: ".$file."');\n");
        }
    }

    function markPHPLoaded($files)
    {
        foreach ($files as $file) {
            $this->included_file[$file] = true;
        }
    }

    var $included_file = array();
}

?>