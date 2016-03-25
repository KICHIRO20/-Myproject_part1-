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
 * Application class is a main class of the system
 *
 * @package Core
 * @author Alexey Florinsky
 * @access  public
 */
class Application
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Application constructor.
     */
    function Application()
    {
        $this->pFactory = new Factory();
        $this->appIni = array();
        $this->TagFatalErrors = array();
        $this->TagWarnings = array();
        $this->protocol = ((isset($_SERVER["HTTPS"]) && (strtolower($_SERVER["HTTPS"]) == "on" || $_SERVER["HTTPS"] == 1 || $_SERVER["HTTPS"] === true)) || (isset($_SERVER["SERVER_PORT"]) && ($_SERVER["SERVER_PORT"] == "443")))? "https":"http";
    }

    // $file_list may contain a pattern
    function combineCSS($file_list)
    {
        $combined_file_dir = 'css/';
        $combined_file_name_prefix = 'style';
        $combined_file_name_extension = 'css';
        CTrace::inf("Trying to combine CSS files:", $file_list);

        // resolve all pathnames patterns
        $file_list = $this->getThemePathnamesByPatterns($file_list);
        CTrace::inf("Trying to combine CSS files (pattern resolved list):", $file_list);

        $files_to_link = $this->__combineThemeFiles($file_list, $combined_file_dir, $combined_file_name_prefix, $combined_file_name_extension);
        CTrace::inf("Combined CSS file:", $files_to_link);

        $html = '';
        foreach($files_to_link as $f)
        {
            $html .= $this->__getHTMLCSSLink(getTemplateFileURL($f));
        }
        return $html;
    }

    // $file_list may contain a pattern
    function combineJS($file_list)
    {
        $combined_file_dir = 'js/';
        $combined_file_name_prefix = 'js';
        $combined_file_name_extension = 'js';
        CTrace::inf("Trying to combine JS files:", $file_list);

        // resolve all pathnames patterns
        $file_list = $this->getThemePathnamesByPatterns($file_list);
        CTrace::inf("Trying to combine JS files (pattern resolved list):", $file_list);

        $files_to_link = $this->__combineThemeFiles($file_list, $combined_file_dir, $combined_file_name_prefix, $combined_file_name_extension);
        CTrace::inf("Combined JS file:", $files_to_link);

        $html = '';
        foreach($files_to_link as $f)
        {
            $html .= $this->__getHTMLJSLink(getTemplateFileURL($f));
        }
        return $html;
    }

    /**
     * $file_list may contain:
     *  - file path (relative to theme directory), for example, 'css/common.css'
     *  - file pattern (relative to theme directory), for example, 'css/style.*.css'
     *
     * The function will find all pathsnames by all patterns and returns an array wich will
     * contain only relative file pathes.
     *
     * @param $file_list The list of file names and patterns relative to theme directory
     */
    function getThemePathnamesByPatterns($file_list)
    {
        $prepared_file_list = array();
        foreach($file_list as $file)
        {
        	if (strpos($file, '*') !== false)
        	{
        		// getTemplateFileByPattern function returns file pathes relative to user skin dir and system skin dir.
        		$prepared_file_list = array_merge($prepared_file_list, getTemplateFileByPattern($file));
        	}
        	else
        	{
				$prepared_file_list[] = $file;
        	}
        }
        return $prepared_file_list;
    }

    // $file_list may contain a pattern
    function __combineThemeFiles($file_list, $combined_file_dir, $combined_file_name_prefix = '', $combined_file_name_extension = '')
    {
        global $application;

        // prepare file list
        $files = array();
        $mtime = '';
        reset($file_list);
        foreach($file_list as $file)
        {
            $file_path = getTemplateFileAbsolutePath($file);
            if (file_exists($file_path) && is_readable($file_path))
            {
                $files[] = $file_path;
                $mtime .= filemtime($file_path);
            }
            else
            {
            	CTrace::wrn("The file '$file_path' does not exist or is not readable, skip it.");
            }
        }

        // determine combined file version
        $version = md5($mtime);
        if ($combined_file_name_prefix != '') $combined_file_name_prefix .= '.';
        if ($combined_file_name_extension != '') $combined_file_name_extension = '.' . $combined_file_name_extension;
        $combined_file_name = $combined_file_name_prefix . 'combined.'.$version .$combined_file_name_extension;
        $combined_file_rel_path = $combined_file_dir . $combined_file_name;
        $combined_file_abs_path = getTemplateFileExactAbsolutePath($combined_file_rel_path);

        if ($application->getAppIni('DEBUG_JS_CSS') == 'yes')
        {
        	CTrace::wrn('DEBUG_JS_CSS mode enabled, combining css/js files is disabled.');
            return $file_list;
        }
        elseif (file_exists($combined_file_abs_path))
        {
            return array($combined_file_rel_path);
        }
        elseif (is_writable(getTemplateFileExactAbsolutePath($combined_file_dir)))
        {
            // create new one
            $content = '';
            reset($files);
            foreach($files as $f)
            {
                $content .= file_get_contents($f) . "\n";
            }
            asc_file_put_contents($combined_file_abs_path, $content);
            if (file_exists($combined_file_abs_path))
            {
                return array($combined_file_rel_path);
            }
        }

        // return as is
        return $file_list;
    }

    function combineAdminCSS($file_list)
    {
        $files = $this->__combineAdminFiles($file_list, 'styles/', 'styles.combined.version.css');
        $admin_url = $this->getAppIni('SITE_AZ_URL');
        if (isset($this->appIni['HTTPS_URL'])
            && $this->getCurrentProtocol() == 'https')
            $admin_url = $this->appIni['SITE_AZ_HTTPS_URL'];
        $html = '';
        foreach ($files as $f) {
            $html .= $this->__getHTMLCSSLink($admin_url.$f);
        }
        return $html;
    }

    function combineAdminJS($file_list)
    {
        $files = $this->__combineAdminFiles($file_list, 'js/', 'scripts.combined.version.js');
       	$admin_url = $this->getAppIni('SITE_AZ_URL');
        if (isset($this->appIni['HTTPS_URL'])
            && $this->getCurrentProtocol() == 'https')
            $admin_url = $this->appIni['SITE_AZ_HTTPS_URL'];
        $html = '';
        foreach ($files as $f) {
            $html .= $this->__getHTMLJSLink($admin_url.$f);
        }
        return $html;
    }

    function __combineAdminFiles($file_list, $dir, $combined_file_name)
    {
        global $application;
        $admin_path = $this->getAppIni('PATH_ADMIN_DIR');
        if ($application->getAppIni('DEBUG_JS_CSS') != 'yes')
        {
            $mtime = '';
            $files = array();
            foreach ($file_list as $file) {
                $file_path = $admin_path.$file;
                if (file_exists($file_path) && is_readable($file_path)) {
                    $files[] = $file_path;
                    $mtime .= filemtime($file_path);
                }
            }

            $combined_file_name = str_replace('version', md5($mtime), $combined_file_name);
            $combined_file_abs_path = $admin_path.$dir.$combined_file_name;

            if (file_exists($combined_file_abs_path)) {
                return array($dir.$combined_file_name);
            }
            elseif (is_writable($admin_path.$dir)) {
                $content = '';
                foreach ($files as $f) {
                    $content .= file_get_contents($f) . "\n";
                }
                asc_file_put_contents($combined_file_abs_path, $content);
                if (file_exists($combined_file_abs_path)) {
                    return array($dir.$combined_file_name);
                }
            }
        }
        else {
            foreach (array_keys($file_list) as $i) {
                $file_path = $admin_path . $file_list[$i];
                if (file_exists($file_path) && is_readable($file_path)) {
                    $file_list[$i] = $file_list[$i].'?v='.filemtime($file_path);
                }
            }
        }
        return $file_list;
    }

    function __getHTMLCSSLink($url)
    {
        return '<link href="'.$url.'" rel="stylesheet" type="text/css" />'."\n";
    }

    function __getHTMLJSLink($url)
    {
        return '<script type="text/javascript" src="'.$url.'"></script>'."\n";
    }

    function get_microtime()
    {
        list($usec, $sec) = explode(" ",microtime());
        return (float)($usec + $sec);
    }

    function init_db()
    {
        # Create an object and connect to a database.
        $this->db = &$this->getInstance( 'DB_MySQL' );
        $this->db->DB_Connect();
    }

    function enterCriticalSection($lock_name)
    {
        CProfiler::lockStart();
        CFileLock::lock(LOCK_EX, $lock_name);
        CProfiler::lockStop();
    }

    function leaveCriticalSection()
    {
        CFileLock::unlock();
    }

    function getTplCache()
    {
        global $zone;
        if ($zone == 'AdminZone') {
        	return CCacheFactory::getCache('templatesAZ');
        }
        else {
        	return CCacheFactory::getCache('templatesCZ');
        }
    }

    function getMMCache()
    {
        global $zone;
        if ($zone == 'AdminZone') {
        	return CCacheFactory::getCache('modulesAZ');
        }
        else {
        	return CCacheFactory::getCache('modulesCZ');
        }
    }

    function getIniCache()
    {
        return CCacheFactory::getCache('inifiles');
    }

    function getAttrIdsCache()
    {
        return CCacheFactory::getCache('attr_ids');
    }

    /**
     * Initialization.
     */
    function init()
    {
        $this->checkCrossSiteScripting();
        # Create a session class and start with the current session.
        $session = &$this->getInstance( 'Session' );
        $request = &$this->getInstance( 'Request' );
        $request->importHTTPRequestData();
        $sid = ($request->getValueByKey('CZSESSID')? $request->getValueByKey('CZSESSID'):($request->getValueByKey('AZSESSID')? $request->getValueByKey('AZSESSID'):""));
        $session->start($sid);
        $this->checkCrossSiteRequest();


        //                                       ,                   POST       .
        //                                 PageView::getViewCacheKey()
        //                   ,                        POST       ,
        //                 Full Page Cache.
        if (!empty($_POST))
        {
            modApiFunc('Session', 'set', '__POST_DATA_SENT__', 1);
        }


        if (Configuration::getSupportMode(ASC_S_DISPLAY_ERRORS))
        {
            error_reporting(E_ALL);
            ini_set("display_errors", "On");
            ini_set("display_startup_errors", "On");
        }

        #                                    !
        #                 GET   POST       ,                                         .
        #                   (20      2007),                                                    .
        $request->restoreGETPOSTData();

        # Parse a current query string for further usage, and
        # POST data are parsed in Request::parseQueryString().
        $request->prepareGetPostData();

        # ModuleManager - has already been loaded, an object can be created.
        # MM will load all the main module classes and create their objects, after
        # that getInstance([ModuleName]) can be called for any module.
        # Besides, MM must load an action file, based on the information
        # from Request. Warning: don't create its object and not to direct
        # the control to it, this will be done later in Application::ProcessAction().
        # View files will be loaded in Application::output(), if necessary,
        # that will increase productivity.
        $mm = &$this->getInstance( 'Modules_Manager' );
        $mm->initModules(); # to load and create objects of all modules


        # redirect to hide session id in URL
        if ($sid) {
            //                           ,            URL      session id,
            //              .        Request                          URL session id,
            //                              .
            CTrace::inf('Prepare to redirect to hide session ID in URL');
        	unset($_GET[session_name()]);
            unset($_POST[session_name()]);
            $request->saveCurrentGETPOSTData();
            $request->setView(CURRENT_REQUEST_URL);
            $this->redirect($request);
        }


        # getting the current zone
        global $zone;

        # Sets the current language
        modApiFunc('MultiLang', 'initLanguages');

        # initializing the Users module
        #                                              Users.                                    init().
        #                      -       ,                                           .
        #                          ,                       Factory                                        .

        $users = &$this->getInstance('Users');
        $users->init();


        #setting the current skin
        $skin = modApiFunc('Look_Feel', 'getCurrentSkin');
        $this->appIni['PATH_THEME']        = $this->appIni['PATH_THEMES'].$skin.'/';
        $this->appIni['PATH_THEME_CSS']    = $this->appIni['PATH_THEME'].'.css/';
        $this->appIni['PATH_THEME_IMAGES'] = $this->appIni['PATH_THEME'].'.images/';
        $this->appIni['PATH_THEME_JS']     = $this->appIni['PATH_THEME'].'.js/';

        $this->appIni['URL_THEME']        = $this->appIni['URL_THEMES'].$skin.'/';
        $this->appIni['URL_THEME_CSS']    = $this->appIni['URL_THEME'].'.css/';
        $this->appIni['URL_THEME_IMAGES'] = $this->appIni['URL_THEME'].'.images/';
        $this->appIni['URL_THEME_JS']     = $this->appIni['URL_THEME'].'.js/';

        $GLOBALS['__TPL_URL__'] = $this->appIni['URL_THEME'];
        $GLOBALS['__TPL_DIR__'] = $this->appIni['PATH_THEME'];

        #Load a resource file of system messages
        $this->MessageResources = &$this->getInstance('MessageResources');

        # register information tags
        $this->registerTags();

        if ($zone == 'CustomerZone')
        {
            # check tags
            $res = LayoutConfigurationManager::static_checkLayoutFile();
            //                        -        -               .
            if(!empty($res["MAIN_ERROR_PARAMETERS"]))
            {
                $_fatal_params = array();
                //                                            _fatal
                $_fatal_params[] = $res["MAIN_ERROR_PARAMETERS"];
                unset($res["MAIN_ERROR_PARAMETERS"]);
                foreach($res as $value)
                {
                    $_fatal_params[] = $value;
                }

                call_user_func_array("_fatal", $_fatal_params);
            }


            //Copy 3 3ini of the ini-file to the temporary folder convert them
            //  to the old format.
            $this->ini_copy_3_ini_and_convert_them_to_old_ini_format_3ini();

            # read the configuration file layouts-config.ini
            $this->readLayoutsINI();

            #                  layout   Mod_Rewrite
            modApiFunc('Mod_Rewrite','setCurrentLayout',$this->getAppIni('PATH_LAYOUTS_CONFIG_FILE'));
        }
    }

    /**
     * Adds prefix to the field names in the meta description of the module
     * database list.
     *
     * @author Alexandr Girin
     * @
     * @param reference $tables the reference to the array of the meta
     * description list
     * @return the changed array of the meta discription
     */
    function addTablePrefix(&$tables)
    {
        $table_prefix = $this->getAppIni("DB_TABLE_PREFIX");
        if ($table_prefix == NULL)
        {
            return $tables;
        }
        foreach ($tables as $table => $table_structure)
        {
            foreach ($tables[$table]['columns'] as $col_name => $column)
            {
                $tables[$table]['columns'][$col_name] = $table_prefix.$column;
            }
        }
        return $tables;
    }

    /**
     * Output the view by name.
     * $Viewname is the first required parameter. It is the name of the view
     * class, which the method output() will be invoked. After the $Viewname
     * parameter the unlimitted number of parameters can follow. All of them
     * will be passed in to the method output() being invoked at
     * the view class.
     *
     * An example of using:
     * <code>
     * $html = $application->output( 'Catalog', $par1, $par2);
     *
     * $html = modApiFunc('application', 'output', 'Catalog', $par1, $par2);
     * </code>
     *
     * @param string $Viewname The name of view class
     * @return generated HTML code of view
     */
    function output( $Viewname )
    {
        # Get a list of the passed parameters
        $arg_list = func_get_args();

        # Remove the first parameter from the list
        array_shift($arg_list);

        # To get an object from the representation name, enable the Modules_Manager
        # object. It is the only one that has metainformation
        # about modules.
        $mm = &$this->getInstance( 'Modules_Manager' );
        $viewObj = &$mm->getViewObject( $Viewname );

        $HTMLCode = call_user_func_array( array( &$viewObj, 'output' ), $arg_list);
        return $HTMLCode;
    }


    /**
     * Gets a pointer to object.
     *
     * It takes an unlimited number of parameters.
     * The required parameter is the clas name. Others will be sent to
     * the constructor "as is".
     *
     * @param string $Classname Name of class
     * @return &object Pointer to object of the class
     */
    function &getInstance($Classname)
    {
        // parameters for the constructor
        $_args_list = func_get_args();
        array_shift($_args_list);

        // if the file of the class hasn't been loaded yet, then call
        // Modules Manager to load the required file
        if ( !class_exists($Classname) )
        {
            $mm = &$this->getInstance('Modules_Manager');
            $mm->includeAPIFileOnce($Classname);
        }

        //                              ,
        if ( !class_exists($Classname) )
        {
            //           CustomerZone                                ,             prepareStorefrontBlockTag
            //                              -                 application
            //                   .                             TMPL_001 (missing template description ...)
            global $zone;
            if ($zone == 'CustomerZone')
            {
                $this->prepareStorefrontBlockTag($Classname);
            }
            else
            {
                $mm = &$this->getInstance('Modules_Manager');
                $mm->includeViewFileOnce($Classname);
            }
        }

        // if the object has the loadState method, then call it
        // only if the object has been created
        $obj = &$this->pFactory->getInstance($Classname, $_args_list, 'loadState');

        return $obj;
    }


    /**
     * Saves the state of all loaded system modules/classes.
     * It calls the function saveState for each of the module, if
     * such function is declared.
     */
    function saveStateofInstancedClasses()
    {
    	$this->pFactory->callMethodToAllObjects('saveState');
    }

    function getBlocksRelatedToAction($asc_action=null)
    {
        if($asc_action!=null)
        {
            $ini = array();
            $dir = $this->appIni['PATH_SYSTEM_DIR'].'/ajax_actions_maps/';
            if($files=scandir($dir))
                foreach($files as $file)
                    if(preg_match('/^.+\.ini$/',$file))
                        $ini = array_merge($ini, @_parse_ini_file($dir.$file));

            return empty($ini[$asc_action]) ? array() : explode(',', $ini[$asc_action]);
        }

        return array();
    }

    /**
     * Calls the controller to the process current action.
     * It has to use the Request object to read action and other parameters
     * from Query String.
     */
    function processAction()
    {
        global $application;
        $request = $this->getInstance( 'Request' );
        $asc_actionName = $request->getCurrentAction();

        #
        # IE high security mode fix. This is "The Platform for Privacy Preferences"
        # see http://www.w3.org/TR/P3P/ for details
        # compact policy is used
        header('P3P: CP="NON CUR ADMa DEVa CONi OUR DELa BUS PHY ONL UNI PUR COM NAV DEM STA"');

        if (modApiFunc('Users', 'getZone') == 'AdminZone' &&
            ! modApiFunc('Users', 'checkCurrentUserAction', $asc_actionName)) {
        	echo file_get_contents($application->getAppIni('PATH_CORE_DIR') . '/block_no_action.tpl');
        	exit;
        }

        if ($asc_actionName === NULL)
        {
            $asc_actionName = "ActionIsNotSetAction";
        }

        # To get an object from the asc_action'  name, enable the Modules_Manager
        # object. It's the only one that has metainformation
        # about modules.
        $mm = &$this->getInstance('Modules_Manager');
        loadCoreFile('ajax_action.php');
        $asc_actionObj = &$mm->getActionObject($asc_actionName);

        if (is_object($asc_actionObj))
        {
            $this->processActionStarted = true;

            $this->pushTag('NullView');
            #                                   -        return               ,                   .
            #                     ,                                                  -
            #                         Ajax'  .
            #       $application->_exit();
            $content = $asc_actionObj->onAction();

            if ($content !== null and !empty($content))
            {
                echo $content;
            }

            # Query MM about the list of hook-classes names of the current action,
            # create their objects and direct the control.
            $hooksList = $mm->getHooksClassesList($asc_actionName);
            if ($hooksList)
            {
                foreach ($hooksList as $hookClass)
                {
                    $hookObj = $this->getInstance( $hookClass );
                    $hookObj->onHook($asc_actionObj);
                }
            }
            $application->popTag();

            // Save the module state (only for loaded modules)
            $this->saveStateofInstancedClasses();

            if ($this->haveToExit)
            {
                exit;
            }

            // Process Ajax Request, convert to JSON, print to browser and exit
            if ($request->getValueByKey('asc_ajax_req') == '1')
            {
                if(method_exists($asc_actionObj, 'generateResponse'))
                {
            	    $res = $this->processAjaxRequest($request, $asc_actionName, $asc_actionObj->generateResponse());
                }
                else {
                    $base_action = new AjaxAction();
                    $base_action->setStatusError();
                    $base_action->setMessage("<b>System error.</b> The action '$asc_actionName' has just been called by AJAX request, but it is not compatible with this type of requests.");
                    $res = $base_action->generateResponse();
                }
		        loadCoreFile('JSON.php');
		        $json = new Services_JSON();
                if ($request->getValueByKey('asc_ajax_upload') == '1')
		            echo '<body><script type="text/javascript"> var resp='.$json->encode($res).'; </script></body>';
                else
                    echo $json->encode($res);
		        exit;
            }

            if($request->getValueByKey('asc_fb_req') != null)
            {
                $this->fb_request = true;
            }

            $this->processActionStarted = false;
            if ($this->redirectURL)
            {
                $request = new Request($this->redirectURL);
                $application->redirect($request);
            }

            if ($this->js_redirectURL)
            {
                $js_redirect = new Request($this->js_redirectURL);
                $application->jsRedirect($js_redirect);
            }
        }
    }

    function processAjaxRequest(&$request, $asc_actionName, &$ajax_response)
    {
    	$block_list = $this->getBlocksRelatedToAction($asc_actionName);
        $block_list = array_unique(array_map('strtolower',array_filter(array_map("trim",$block_list))));
        $block_list_ext = $request->getValueByKey('ajax_get_block');
        if(is_array($block_list_ext))
        {
            $block_list = array_unique(array_merge(
                $block_list,
                array_unique(array_map('strtolower',array_filter(array_map("trim",$block_list_ext))))));
        }

        foreach($block_list as $block)
        {
            $f = "get".trim($block);
            //$ajax_response['data'][$block] = preg_replace('/[\n\r]/','',$f());
            $ajax_response['data'][$block] = $f();
        }

        return $ajax_response;
    }

      function isImageFileValid($filename)
      {
                      $full_filename = $this->getAppIni('PATH_IMAGES_DIR').$filename;
                      return (@getimagesize($full_filename) != FALSE);
      }

    /**
     * Method for recalculation the height and the width of the image.
     *
     * @ the method is not finished. Return an empty image, if the file
     * doesn't exist
     * @param string $filename the image filename
     * @param int $max_img_height max image heght
     * @param int $max_img_width max image width
     * @return array image size - the structure of the array matches the array
     * returned PHP by the getimagesize() function
     * @ !!! Delete this method
     */
    function img_size($filename, $max_img_height=50, $max_img_width=50)
    {
        if (is_readable($filename))
        {
            $img_size = getimagesize($filename);
            if ($img_size[0] > $max_img_width)
            {
                $kof = $max_img_width/$img_size[0];
                $img_size[0] = $max_img_width;
                $img_size[1] = $kof*$img_size[1];
            }
            if ($img_size[1] > $max_img_height)
            {
                $kof = $max_img_height/$img_size[1];
                $img_size[1] = $max_img_height;
                $img_size[0] = $kof*$img_size[0];
            }
            $img_size[3] = 'width="'.$img_size[0].'" height="'.$img_size[1].'"';
        }
        else
        {
            $img_size = array($max_img_width, $max_img_height, '3', 'width="'.$max_img_width.'" height="'.$max_img_height.'"', '8', 'image/png');
        }
        return $img_size;
    }

    /**
     * Defines the possibility of uploading images by file type.
     *
     * @param $file The array consists of the $_FILES variable, for
     * the current file.
     * @return boolean
     */
    function isAllowedImageType($file)
    {
/*        $type = _ml_strtolower($file['type']);
        switch ($type)
        {
            case 'image/gif':
            case 'image/jpeg':
            case 'image/jpg':
            case 'image/jpe':
            case 'image/jfif':
            case 'image/pjpeg':
            case 'image/pjp':
            case 'image/png':
            case 'image/x-png':
                return true;
            default:
                return false;
        }
*/
        return true;
    }

    /**
     * Defines the extension of the image file of the file type to be used in the system.
     *
     * @param $file - The array consists of the $_FILES variable, for
     * the current file.
     * @return mixed false - this image type can't be used in the system.
     */
    function getImageTypeExtension($file)
    {
        $type = _ml_strtolower($file['type']);
        $ext = false;
        switch ($type)
        {
            case 'image/gif':
                $ext = "gif";
                break;
            case 'image/jpeg':
            case 'image/jpg':
            case 'image/jpe':
            case 'image/jfif':
            case 'image/pjpeg':
            case 'image/pjp':
                $ext = "jpg";
                break;
            case 'image/png':
            case 'image/x-png':
                $ext = "png";
                break;
            default:
                $ext = false;
                break;
        }
        return $ext;
    }

    function _img_doesnot_exist_and_creatable($path)
    {
        $result = false;
        if(! file_exists($path))
        {
            CProfiler::ioStart($path, 'test');
            $fh = fopen($path, "w");
            if($fh)
            {
                fclose($fh);
                $result = @unlink($path);
            }
            CProfiler::ioStop();
        }
        return $result;
    }

    function _img_path($basename)
    {
        return $this->getAppIni('PATH_IMAGES_DIR') . $basename;
    }

    /**
     * Defines a unique filename and creates it for temporary storing in
     * the image catalog.
     *
     * @param $prefix string the filename prefix.
     * @param $postfix string the file extension.
     * @return mixed false - the file can't be created.
     */
    function getUploadImageName($orig_path, $subfolder = '')
    {
        $path_parts = pathinfo($orig_path);
        $basename = $path_parts['basename'];
        $ext = $path_parts['extension'];

        //
        //http://tools.ietf.org/html/rfc1630
        $safe = "a-zA-Z0-9\$\-\_\@\.\&";
        $basename = preg_replace("/[^" . $safe. "]/","", $basename);

        $basename_without_ext = _ml_substr($basename, 0, -_ml_strlen("." . $ext));
        if($basename_without_ext == "")
        {
        	$basename_without_ext = "image";
        }

        $path = $this->getAppIni('PATH_IMAGES_DIR') . $subfolder . $basename_without_ext . "." . $ext;
        $tries = 2;

        while (file_exists($path)) {
            $path = $this->getAppIni('PATH_IMAGES_DIR') . $subfolder . $basename_without_ext . "_" . $tries++ . "." . $ext;
        }
        return $this->_img_doesnot_exist_and_creatable($path) ? $path : false;
    }

    /**
     * Saves the temporary image file to the catalog as a permanent one.
     * If the names match, appends a number "_XXXXXXXXX" to the end of the filename.
     *
     * @param $prefix string the permanent filename prefix.
     * @param $tmp_file string the temporary filename.
     * @return Array (['name'], ['width'], ['height'])
     */
    function saveUploadImage($prefix, $tmp_file)
    {
        $dir = $this->getAppIni('PATH_IMAGES_DIR');

        if(is_file($dir . $tmp_file))
        {
            $image_info = getimagesize($dir . $tmp_file);
            return array(
                'name' => $tmp_file,
                'width' => $image_info[0],
                'height' => $image_info[1]
            );
        }
        else
        {
            return array(
                'name' => '',
                'width' => 0,
                'height' => 0
            );
        };
    }


    /**
     * Returns the section name based on the current filename.
     */
    function getSectionByCurrentPagename()
    {
        $zone = modApiFunc('Users', 'getZone');

        $sections = array();
        if ($zone == "CustomerZone")
        {
            foreach ($this->Configs_array["Layouts"] as $section => $params)
            {
                if (_ml_strtoupper($section) == "SITE" || _ml_strtoupper($section) == "TEMPLATES")
                {
                    continue;
                }
                foreach ($params as $key => $val)
                {
                    if (_ml_strtoupper($key)!="HTTPS" && $val != "")
                    {
                        if (!(_ml_strpos($_SERVER["PHP_SELF"], $val) === false))
                        {
                            $sections[] = $section;
                        }
                    }
                }
            }
        }
        elseif ($zone == "AdminZone")
        {

            $r = &$this->getInstance('Request');

            $current_file = basename($_SERVER["PHP_SELF"]);

            if ($current_file == 'popup_window.php' && $r->getValueByKey('page_view') != null)
            {
                $current_file = $r->getValueByKey('page_view');
            }

            switch ($current_file)
            {
                case "signin.php":
                case "signin_password_update.php":
                case "signin_password_recovery.php":
                case "admin_members.php":
                case "admin_member_info.php":
                case "admin_member_add.php":
                case "admin_member_edit.php":
                case "admin_member_passwd_reset.php":
                case "admin_member_delete.php":
                    $sections[] = "SignIn_AdminMembers";
                    break;
                case "orders.php":
                case "checkout_delete_orders.php":
                case "orders_info.php":
                case "customers.php":
                case "orders_invoice.php":
                case "orders_packing_slip.php":
                case "customers_info.php":

                case "CustomerAccountInfo":

                    $sections[] = "Orders_Customers";
                    break;
                case "payment_modules.php":
                case "checkout_payment_module_settings.php":
                case "shipping_modules.php":
                case "checkout_shipping_module_settings.php":
                case "scc_settings.php":
                case "shipping_tester_window.php":
                    $sections[] = "Payment_Shipping";
                    break;
                default:
                    break;
            }
        }

        return $sections;
    }


    /**
     * Returns the protocol, by which the section must be loaded.
     */
    function getSectionProtocol($section)
    {
        $section_orig = $section;
        if(!isset($this->SectionProtocol))
        {
            $this->SectionProtocol = array();
        }
        if(!isset($this->SectionProtocol[$section_orig]))
        {
            $section = _ml_strtoupper($section);
            $layout = array_change_key_case($this->Configs_array["Layouts"], CASE_UPPER);
            foreach ($layout as $key => $sectionInfo)
            {
                $layout[$key] = array_change_key_case($sectionInfo, CASE_UPPER);
            }
            $res = 'http';
            if (isset($this->appIni['SITE_HTTPS_URL']))
            {

                $settings = modApiFunc('Configuration', 'getLayoutSettings',
                                       $this->cz_layout_config_ini_file);
                if (modApiFunc('Configuration', 'getCZHTTPS',
                               'whole_cz', $settings) == 'YES')


                {
                    $res = 'https';
                }

                elseif (isset($layout[$section]["HTTPS"]))
                {
                    if (_ml_strtolower($layout[$section]["HTTPS"]) == 'yes'
                        || $layout[$section]['HTTPS'] == 1)
                    $res = 'https';
                }

            }
            $this->SectionProtocol[$section_orig] = $res;
        }
        return $this->SectionProtocol[$section_orig];
    }

    /**
     * Returns the protocol, by which the current page is loaded.
     */
    function getCurrentProtocol()
    {
        return $this->protocol;
    }

    /**
     * Redirects to another protocol, if necessary.
     * The method is called in 'admin.php' and 'store.php', after
     * executing the current action.
     *
     *                                              ,
     *                             action' .
     */
    function redirectToAnotherProtocol()
    {
        $sections = $this->getSectionByCurrentPagename();

        $zone = modApiFunc('Users', 'getZone');

        if ($zone == "CustomerZone")
        {
            // for Ajax-requests no redirection...
            if (basename($_SERVER["PHP_SELF"]) == 'js_http_request_frontend.php')
            {
                CTrace::inf('Ajax-request, no redirection...');
                return;
            }

            /*
                                                                  storefront
                [           ,      https                   storefront'              , ]
                [                            ,                                 HTTPS  ]

                ----------------------------------------------------------------
                |                   |                                          |
                |                   |------------------------------------------|
                |                   |     HTTP           |     HTTPS           |
                ----------------------------------------------------------------
                |                   |                    |                     |
                |                   |                    |                 HTTP|
                |layout.ini         |                    |                     |
                ----------------------------------------------------------------
                |                   |                    |                     |
                |                   |                    |                 HTTP|
                |layout.ini         |                    |                     |
                |           HTTP    |                    |                     |
                ----------------------------------------------------------------
                |                   |                    |                     |
                |                   |                    |                     |
                |layout.ini         |        HTTPS       |                     |
                |           HTTPS   |                    |                     |
                ----------------------------------------------------------------
                |                   |                    |                     |
                |                   |                    |                     |
                |layout.ini         |        HTTPS       |                     |
                |      HTTPS,       |                    |                     |
                |      HTTP         |                    |                     |
                |                   |                    |                     |
                ----------------------------------------------------------------

            */


            /*
                                      HTTPS                          layout
                           ,                       HTTP.
                 . .     "       "         ,                           HTTP.
            */
            if (!sizeof($sections))
            {
            	$settings = modApiFunc("Configuration", "getLayoutSettings", $this->cz_layout_config_ini_file);
            	$needed_protocol = (modApiFunc("Configuration", "getCZHTTPS", "whole_cz", $settings) == "YES") ? "https" : "http";
            	if($this->protocol != $needed_protocol)
            	{
                        CTrace::inf('Redirecting to another protocol...');
	                $request = new Request();
	                $request->setView(CURRENT_REQUEST_URL);
	                $request->setKey(session_name(), session_id());
	                $request->saveCurrentGETPOSTData();
	                $this->redirect($request, $needed_protocol);
            	}
            }
            else
            {
	            $redirect_to_http_protocol = true;
	            $settings = modApiFunc("Configuration", "getLayoutSettings", $this->cz_layout_config_ini_file);
                /*
	                                           ,                          .
	            */
	            foreach ($sections as $section)
	            {
	                /*
	                                          HTTP,
	                                              HTTPS -                       HTTPS.
	                     . .           ,
	                            HTTPS -                                     .
	                */
                    $needed_protocol = (modApiFunc("Configuration", "getCZHTTPS", $section, $settings) == "YES") ? "https" : "http";
	                if ($needed_protocol == "https" && $this->protocol == "http")
	                {
                            CTrace::inf('Redirecting: https needed while http is used...');
	                    $redirect_to_http_protocol = false;
	                    $request = new Request();
	                    $request->setView($this->getViewBySection($section));
	                    $request->setKey(session_name(), session_id());
	                    $request->saveCurrentGETPOSTData();
                        
                            if (!$request->getKey('category_id'))
                                $request->setCategoryID(1);
                        
	                    $this->redirect($request, "https");
	                }


	                /*
	                                          HTTPS,
	                                          HTTP           -                       HTTP.
	                                   ,
	                                     HTTPS           -                      ,
	                    HTTPS         .
	                */
	                if ($needed_protocol == "https" && $this->protocol == "https")
	                {
	                    $redirect_to_http_protocol = false;
	                }
	            }

	            if ($redirect_to_http_protocol && $this->protocol == "https")
	            {
                        CTrace::inf('Redirecting: http needed while https is used...');
	                $request = new Request();
	                $request->setView($this->getViewBySection($sections[0]));
	                $request->setKey(session_name(), session_id());
	                $request->saveCurrentGETPOSTData();
                    
                        if (!$request->getKey('category_id'))
                            $request->setCategoryID(1);
                    
	                $this->redirect($request, "http");
	            }
            }
        }
        elseif ($zone == "AdminZone")
        {
            if (!empty($this->appIni["AllAdminArea"]))
            {
                if ($this->protocol == "http")
                {
                    CTrace::inf('Redirecting (admin): https needed while http is used...');
                    $request = new Request();
                    $request->setView(CURRENT_REQUEST_URL);
                    $request->setKey(session_name(), session_id());
                    $request->saveCurrentGETPOSTData();
                    $this->redirect($request, "https");
                }
            }
            elseif (!sizeof($sections) && $this->protocol == "https")
            {
                CTrace::inf('Redirecting (admin): no https sections is found, forcing to use http...');
                $request = new Request();
                $request->setView(CURRENT_REQUEST_URL);
                $request->setKey(session_name(), session_id());
                $request->saveCurrentGETPOSTData();
                $this->redirect($request, "http");
            }
            elseif (isset($sections[0]))
            {
                if ($this->appIni[$sections[0]] && $this->protocol == "http")
                {
                    CTrace::inf('Redirecting (admin): https needed for the section while http is used...');
                    $request = new Request();
                    $request->setView(CURRENT_REQUEST_URL);
                    $request->setKey(session_name(), session_id());
                    $request->saveCurrentGETPOSTData();
                    $this->redirect($request, "https");
                }
                if (!$this->appIni[$sections[0]] && $this->protocol == "https")
                {
                    CTrace::inf('Redirecting (admin): http needed for the section while http is used...');
                    $request = new Request();
                    $request->setView(CURRENT_REQUEST_URL);
                    $request->setKey(session_name(), session_id());
                    $request->saveCurrentGETPOSTData();
                    $this->redirect($request, "http");
                }
            }

        }
    }

    /**
     * Returns the filename (a web site page) for the specified view.
     *
     * @ method isn't finished. There must be a selection from the database.
     * What kind of module must work on it?
     */
    function getPagenameByViewname($viewName, $category = -1, $product = -1, $zone = '')
    {
        $file = '';
        if ($zone == '')
        {
            $zone = modApiFunc('Users', 'getZone');
        }

        if ($zone == "CustomerZone")
        {
            return $this->getViewLayoutPage($viewName, $category, $product);
        }
        switch ($viewName)
        {
            case 'AdminSignIn':
                $file = "signin.php";
                break;
            case 'AdminZoneBlocked':
                $file = "signin_blocked.php";
                break;
            case 'AdminPasswordUpdate':
                $file = "signin_password_update.php";
                break;
            case 'AdminPasswordRecovery':
                $file = "signin_password_recovery.php";
                break;
            # Commented by AF:
            # I think, it should be removed in the release
            case 'Maximize':
                $file = "maximize.html";
                break;
            case 'HomeTab':
                $file = "index.php";
                break;
            case 'NavigationBar':
                $file = "catalog_manage_categories.php";
                break;
            case 'CustomerZoneBlocked':
                $file = "customer_blocked.php";
                break;
            case 'ProductList':
                $file = "catalog_manage_products.php";
                break;
            case 'AddCategory':
                $file = "catalog_add_category.php";
                break;
            case 'MoveCategory':
                $file = "catalog_move_category.php";
                break;
            case 'DeleteCategory':
                $file = "catalog_del_category.php";
                break;
            case 'EditCategory':
                $file = "catalog_edit_category.php";
                break;
            case 'ViewCategory':
                $file = "catalog_view_category.php";
                break;
            case 'SortCategories':
                $file = "catalog_sort_category.php";
                break;
            case 'Catalog_AddProduct':
                $file = "catalog_addproduct.php";
                break;
            case 'Catalog_EditProduct':
                $file = "catalog_editproduct.php";
                break;
            case 'MoveProducts':
                $file = "catalog_move_product.php";
                break;
            case 'CopyProducts':
                $file = "catalog_copy_product.php";
                break;
            case 'DeleteProducts':
                $file = "catalog_delete_products.php";
                break;
            case 'SortProducts':
                $file = "catalog_sort_products.php";
                break;
            case 'Catalog_ProdInfo':
                $file = "catalog_product.php";
                break;
            case 'SelectProductType':
                $file = "catalog_select_type.php";
                break;
            case 'ManageProductTypes':
                $file = "catalog_manage_product_types.php";
                break;
            case 'AddProductType':
                $file = "catalog_new_product_type.php";
                break;
            case 'EditProductType':
                $file = "catalog_edit_product_type.php";
                break;
            case 'EditCustomAttribute':
                $file = "catalog_edit_custom_attribute.php";
                break;
            case 'DeleteProductType':
                $file = "catalog_delete_product_type.php";
                break;
            case 'CheckoutPaymentModulesList':
                $file = "payment_modules.php";
                break;


            case 'CheckoutInfoList':
                $file = "checkout_info_list.php";
                break;
            case 'CheckoutInfoSortGroup':
                $file = "checkout-info-sort-group.php";
                break;
            case 'CheckoutInfoAttributeEdit':
                $file = "checkout-info-attribute-edit.php";
                break;

            case 'CheckoutPaymentModuleSettings':
                $file = "checkout_payment_module_settings.php";
                break;
			case 'StoreSettingsPage':
				$file = "store_settings.php";
				break;
            case 'CheckoutShippingModulesList':
                $file = "shipping_modules.php";
                break;
            case 'CheckoutShippingModuleSettings':
                $file = "checkout_shipping_module_settings.php";
                break;

            case 'AddFsRule':
                $file = "scc_add_fs_rule.php";
                break;
            case 'EditFsRule':
                $file = "scc_edit_fs_rule.php";
                break;
            case 'EditFsRuleArea':
                $file = "scc_edit_fs_rule_area.php";
                break;

            case 'CountriesList':
                $file = "store_settings_countries.php";
                break;
            case 'StatesList':
                $file = "store_settings_states.php";
                break;

            case 'LanguageList':
                $file = 'store_settings_languages.php';
                break;

            case 'ChangeDefaultLanguage':
                $file = 'store_settings_change_default_language.php';
                break;

            case 'PHPInfo':
                $file = 'admin_php_info.php';
                break;

            case 'AdminMembersList':
                $file = 'admin_members.php';
                break;
            case 'AdminMemberInfo':
                $file = 'admin_member_info.php';
                break;
            case 'AdminMemberAdd':
                $file = 'admin_member_add.php';
                break;
            case 'AdminMemberEdit':
                $file = 'admin_member_edit.php';
                break;
            case 'AdminPasswordChange':
            case 'AdminMemberPasswordReset':
                $file = 'admin_member_passwd_reset.php';
                break;
            case 'AdminMemberDelete':
                $file = 'admin_member_delete.php';
                break;

            case 'DateTimeFormat':
                $file = 'store_settings_local_date.php';
                break;

            case 'NumberFormat':
                $file = 'store_settings_local_number.php';
                break;
            case 'CurrencyFormat':
                $file = 'store_settings_local_currency.php';
                break;
            case 'WeightUnit':
                $file = 'store_settings_local_weight.php';
                break;

            case 'ProductTaxClassSettings':
                $file = 'store_settings_prod_tax_classes.php';
                break;

            case 'TaxSettings':
                $file = 'store_settings_taxes.php';
                break;

            case 'AddTaxName':
                $file = 'store_settings_tax_add_name.php';
                break;

            case 'EditTaxName':
                $file = 'store_settings_tax_edit_name.php';
                break;

            case 'AddTaxDisplayOption':
                $file = 'store_settings_tax_add_display.php';
                break;

            case 'EditTaxDisplayOption':
                $file = 'store_settings_tax_edit_display.php';
                break;

            case 'AddTaxClass':
                $file = 'store_settings_tax_add_class.php';
                break;

            case 'EditTaxClass':
                $file = 'store_settings_tax_edit_class.php';
                break;

            case 'AddTaxRate':
                $file = 'store_settings_tax_add_rate.php';
                break;

            case 'EditTaxRate':
                $file = 'store_settings_tax_edit_rate.php';
                break;

            case 'TaxCalculator':
                $file = 'store_settings_tax_calculator.php';
                break;

            case 'Hint':
                $file = 'hint.php';
                break;

            case 'ShippingTaxes':
                $file = 'store_settings_tax_edit_shipping_modules.php';
                break;

            case 'Orders':
                $file = 'orders.php';
                break;

            case 'DeleteOrders':
                $file = 'checkout_delete_orders.php';
                break;

            case 'OrderInfo':
                $file = 'orders_info.php';
                break;

            case 'OrderInvoice':
                $file = 'orders_invoice.php';
                break;

            case 'OrderPackingSlip':
                $file = 'orders_packing_slip.php';
                break;

            case 'Customers':
                $file = 'customers.php';
                break;

            case 'CustomerInfo':
                $file = 'customers_info.php';
                break;

            case 'AdminBackup':
                $file = 'admin_backup.php';
                break;

            case 'AdminBackupInfo':
                $file = 'admin_backup_info.php';
                break;

            case 'AdminBackupCreate':
                $file = 'admin_backup_create.php';
                break;

            case 'AdminBackupProgress':
                $file = 'admin_backup_progress.php';
                break;

            case 'AdminBackupDelete':
                $file = 'admin_backup_delete.php';
                break;

            case 'AdminBackupDeleteProgress':
                $file = 'admin_backup_delete_progress.php';
                break;

            case 'AdminRestoreProgress':
                $file = 'admin_restore_progress.php';
                break;

            case 'Downdoad':
                $file = 'download.php';
                break;

            case 'Downdoad_CSV':
                $file = 'download_csv.php';
                break;

            case 'AdminBackupRestore':
                $file = 'admin_backup_restore.php';
                break;

            case 'Notifications':
                $file = 'store_settings_notifications.php';
                break;

            case 'NotificationInfo':
                $file = 'store_settings_notific_info.php';
                break;

            case 'SearchResult':
                $file = 'catalog_search_result.php';
                break;

            case 'SystemUpdate':
                $file = 'update.php';
                break;

            case 'SystemUpdateProgress':
                $file = 'update_progress.php';
                break;

            case 'SystemUpdateProgressDownloadFile':
                $file = 'download_update_file_start.php';
                break;

            case 'LicenseInfo':
                $file = 'license_info.php';
                break;

            case 'HTTPSSettings':
                $file = 'https_settings.php';
                break;

            // begin vews for ProductOptions module
            case 'PO_OptionsList':
                $file = 'po_options_list.php';
                break;

            case 'PO_AddOption':
                $file = 'po_new_option.php';
                break;

            case 'PO_EditOption':
                $file = 'po_edit_option.php';
                break;

            case 'PO_EditExs':
                $file = 'po_edit_exs.php';
                break;

            case 'PO_CRulesEditor':
                $file = 'po_crules_editor.php';
                break;

            case 'PO_InvEditor':
                $file = 'po_inv_editor.php';
                break;
            // end views for ProductOptions module

            case 'CSVImportProducts':
                $file = "csv_import_products.php";
                break;

            case 'CSVImportNextStepValidation':
                $file = "csv_import_product_next_step_validation.php";
                break;

            case 'CSVImportStartValidation':
                $file = "csv_import_product_validation.php";
                break;

            case 'AdminCSVExport':
                $file = "csv_export_products_exporting.php";
                break;

            case 'AdminCSVInfo':
                $file = 'admin_csv_info.php';
                break;

            case 'AdminCSVDelete':
                $file = 'admin_csv_delete.php';
                break;

            case 'AdminCSVDeleteProgress':
                $file = 'admin_csv_delete_progress.php';
                break;

            case 'CSVExportSetParamsExporting':
                $file = "csv_export_set_params.php";
                break;

            case 'discounts_manage_global_discounts_az':
                $file = "marketing_manage_discounts.php";
                break;

            case 'AddPromoCode':
                $file = "marketing_add_promo_code.php";
                break;

            case 'EditPromoCode':
                $file = "marketing_edit_promo_code.php";
                break;

            case "EditPromoCodeArea":
                $file = "marketing_edit_promo_code_area.php";
                break;

            case 'PromoCodesNavigationBar':
                $file = "marketing_manage_promo_codes.php";
                break;

            //views for Product Files module
            case 'PF_FilesList':
                $file = 'pf_files_list.php';
                break;
            //end views for Product Files module

            //views for Product Images module
            case 'PI_ImagesList':
                $file = 'pi_images_list.php';
                break;

			case 'PI_ColorSwatch':
            	$file = 'pi_color_swatch_list.php';
            	break;

            case 'manage_quantity_discounts_az':
                $file = 'manage_quantity_discounts_az.php';
                break;

            case 'related_products':
                $file = 'related_products.php';
                break;
            //end views for Product Files module

            case 'MngProductCats':
                $file = 'mng_product_cats.php';
                break;

            case 'RegisterFormEditor':
                $file = 'register_form_editor.php';
                break;

            case 'CustomersList':
                $file = 'customers.php';
                break;

            case 'CustomerGroups':
                $file = 'customer_groups.php';
                break;

            case 'PopupWindow':
                $file = 'popup_window.php';
                break;

            case 'CreditCardSettings':
                $file = 'credit_cards_editor.php';
                break;

            case 'CreditCardAttributes':
                $file = 'credit_card_attributes.php';
                break;

            case 'SortCreditCardTypes':
                $file = 'sort_credit_card_types.php';
                break;

            case 'ManufacturersList':
                $file = 'mnf_manufacturers.php';
                break;

            case 'AddManufacturer':
                $file = 'mnf_add_manufacturer.php';
                break;

            case 'EditManufacturer':
                $file = 'mnf_edit_manufacturer.php';
                break;

            case 'TransactionTrackingSettings':
            	$file = 'transaction_tracking_settings.php';
            	break;

            case 'AcceptedCurrencies':
                $file = 'store_settings_accepted_currencies.php';
                break;

            case 'SortManufacturers':
            	$file = 'mnf_sort_manufacturers.php';
            	break;

            case 'TaxRateByZip_Sets':
                $file = 'tax_zip_sets.php';
                break;

            case 'TimelineView':
                $file = 'timeline.php';
                break;

            case 'ManageCustomerReviews':
                $file = 'customer_reviews.php';
                break;

            case 'LabelEditor':
                $file = 'label_editor.php';
                break;

            case 'CatalogProductGroupEdit':
                $file = 'catalog_product_group_edit.php';
                break;

            case 'Subscriptions_Manage':
                $file = 'subscriptions_manage.php';
                break;
            case 'Subscriptions_AddTopic':
                $file = 'subscriptions_addtopic.php';
                break;
            case 'Subscriptions_EditTopic':
                $file = 'subscriptions_edittopic.php';
                break;
            case 'Subscriptions_SortTopics':
                $file = 'subscriptions_sort.php';
                break;
            case 'Subscriptions_DeleteTopics':
                $file = 'subscriptions_deltopics.php';
                break;
            case 'Subscriptions_Subscribe':
                $file = 'subscriptions_subscribe.php';
                break;
            case 'Subscriptions_Unsubscribe':
                $file = 'subscriptions_unsubscribe.php';
                break;
            case 'Subscriptions_Export':
                $file = 'subscriptions_export.php';
                break;
            case 'Subscriptions_Signature':
                $file = 'subscriptions_signature.php';
                break;
            case 'CurrencyRateEditor':
            	$file = 'currency_rate_editor.php';
            	break;


            case 'CMS_Pages':
                $file = 'cms_pages.php';
                break;
            case 'CMS_Menu':
                $file = 'cms_menus.php';
                break;

            case 'Look_Feel':
                $file = 'look_feel.php';
                break;

            # Commented by AF: Is the page CartContent really contained in the Admin Zone??
            #case 'CartContent':
            #    $file = "cart.php";
            #    break;

            case 'GiftCertificateListView':
                $file = 'marketing_manage_gc.php';
                break;
            case 'GiftCertificateEditView':
                $file = 'marketing_edit_gc.php';
                break;
            case 'GiftCertificateAddView':
                $file = 'marketing_add_gc.php';
                break;

            case 'Layout_CMS':
                $file = 'layout_cms.php';
                break;

            default:
                $file = 'index.php';
                break;
        }
        return $file;
    }

    /**
     * Determines the dependence between a block tag and a filename, which contains
     * this tag. Depending on the passed parameters $category and $product and
     * on the settings in layouts-config.ini, replaces the file with the
     * proper configurations.
     */
    function getViewLayoutPage($viewName, $category = -1, $product = -1)
    {
        $section = "";
        $section = $this->getSectionByViewName($viewName);
        if ($section == 'UNDEFINED')
            return 'index.php';

        if ($product != -1 && array_key_exists('products', $this->layout[$section]))
        {
            if (array_key_exists($product, $this->layout[$section]['products']))
            {
                return $this->layout[$section]['products'][$product];
            }
        }
        if ($category != -1 && array_key_exists('categories', $this->layout[$section]))
        {
            if (array_key_exists($category, $this->layout[$section]['categories']))
            {
                return $this->layout[$section]['categories'][$category];
            }
        }
        return $this->layout[$section]['default'];
    }

    function getSectionByViewName($viewName)
    {
        if (empty($this -> SectionByView))
        {
            $this -> SectionByView = array(
                'NavigationBar'              => 'productlist',
                'Subcategories'              => 'productlist',
                'ProductList'                => 'productlist',
                'ProductInfo'                => 'productinfo',
                'CartContent'                => 'cart',
                'CheckoutView'               => 'checkout',
                'OrderPlaced'                => 'orderplaced',
                'Closed'                     => 'closed',
                'SearchResult'               => 'searchresult',
                'Download'                   => 'download',
                'Registration'               => 'registration',
                'AccountActivation'          => 'accountactivation',
                'CustomerPersonalInfo'       => 'customerpersonalinfo',
                'CustomerOrdersHistory'      => 'customerordershistory',
                'CustomerOrderInfo'          => 'customerorderinfo',
                'CustomerOrderInvoice'       => 'customerorderinvoice',
                'CustomerOrderDownloadLinks' => 'customerorderdownloadlinks',
                'CustomerSignIn'             => 'customersignin',
                'CustomerNewPassword'        => 'customernewpassword',
                'CustomerChangePassword'     => 'customerchangepassword',
                'CustomerForgotPassword'     => 'customerforgotpassword',
                'CustomerAccountHome'        => 'customeraccounthome',
                'CMSPage'                    => 'cmspage',
                'CustomerSubscription'       => 'customersubscription',
                'GiftCertificate'            => 'giftcertificate',
                'Wishlist'                   => 'wishlist',
                'CategorySheet'              => 'categorysheet'
            );
            $this -> SectionByView = array_merge(
                $this -> SectionByView,
                modApiFunc('Modules_Manager', 'getSectionByView')
            );
        }

        $section = 'UNDEFINED';
        if (isset($this -> SectionByView[$viewName]))
            $section = $this -> SectionByView[$viewName];

        return $section;
    }

    /**
     * Determines the dependence between a filename and a block tag.
     * The inverse function to Application::getViewLayoutPage()
     */
    function getViewBySection($section)
    {
        if (empty($this -> ViewBySection))
        {
            $this -> ViewBySection = array(
                'productlist'                => 'ProductList',
                'productinfo'                => 'ProductInfo',
                'cart'                       => 'CartContent',
                'checkout'                   => 'CheckoutView',
                'orderplaced'                => 'CheckoutView',
                'closed'                     => 'Closed',
                'searchresult'               => 'SearchResult',
                'download'                   => 'Download',
                'registration'               => 'Registration',
                'accountactivation'          => 'AccountActivation',
                'customerpersonalinfo'       => 'CustomerPersonalInfo',
                'customerordershistory'      => 'CustomerOrdersHistory',
                'customerorderinfo'          => 'CustomerOrderInfo',
                'customerorderinvoice'       => 'CustomerOrderInvoice',
                'customerorderdownloadlinks' => 'CustomerOrderDownloadLinks',
                'customersignin'             => 'CustomerSignIn',
                'customernewpassword'        => 'CustomerNewPassword',
                'customerchangepassword'     => 'CustomerChangePassword',
                'customerforgotpassword'     => 'CustomerForgotPassword',
                'customeraccounthome'        => 'CustomerAccountHome',
                'customersubscription'       => 'CustomerSubscription',
                
                'cmspage'                    => 'CMSPage',
                
                'giftcertificate'            => 'GiftCertificate',
                'wishlist'                   => 'Wishlist',
                'categorysheet'              => 'CategorySheet'

            );
            $this -> ViewBySection = array_merge(
                $this -> ViewBySection,
                modApiFunc('Modules_Manager', 'getViewBySection')
            );
        }

        $view = 'Index';
        $section = _ml_strtolower($section);
        if (isset($this -> ViewBySection[$section]))
            $view = $this -> ViewBySection[$section];

        return $view;
    }

    /**
     * Activates redirect if necessary.
     *
     * @ finish the functions on this page
     */
    function redirect($request, $force_redirect_to = "")
    {
        $url = $request->getURL($force_redirect_to);

        if($force_redirect_to != "")
        {
	        if(_ml_strcasecmp($force_redirect_to . "://", _ml_substr($url, 0, _ml_strlen($force_redirect_to . "://"))) !== 0)
	        {
                //      :       ,                              $force_redirect_to,    url                        .
                _fatal(array( "CODE" => "CORE_059"), $force_redirect_to, $url);
	        }
        }

        if ($this->processActionStarted)
        {
            $this->redirectURL = $url;
        }
        elseif ($url)
        {
            Header("Location: " . $url);
            exit;
        }
    }

    function jsRedirect($request, $force_redirect_to = "")
    {
        $url = $request->getURL($force_redirect_to);

        if ($this->processActionStarted)
        {
            $this->js_redirectURL = $url;
        }
        elseif ($url)
        {
//            Header("Location: " . $url);
        	$admin_path = $this->getAppIni('PATH_ADMIN_DIR');
        	echo str_replace("%NEW_LOCATION%",$url,file_get_contents($admin_path.'/js_redirect.html'));
            exit;
        }
    }

    function _exit()
    {
        $this->haveToExit = true;
    }

    function closeChild_UpdateParent()
    {
        echo file_get_contents("close_child_update_parent.html");
        exit;
    }


    function closeChild_UpdateTop()
    {
        echo file_get_contents("close_child_update_top.html");
        exit;
    }


    function updateParent()
    {
        echo file_get_contents("update_parent.html");
    }

    function updateParentsParent()
    {
        echo file_get_contents("update_parents_parent.html");
    }

    function closeChild()
    {
        echo file_get_contents("close_child.html");
        exit;
    }

    /**
     * Reads system settings from config.php.
     *
     *
     * All paths are returned with the slash at the end.
     *
     * The parameter values can be got by calling Application::getAppIni($key).
     * An example:
     * <code>
     *  $core_path = $application->getAppIni('PATH_MODULES_DIR');
     *  $db_server = $application->getAppIni('DB_SERVER');
     *  $db_user   = $application->getAppIni('DB_USER');
     *  $db_paswd  = $application->getAppIni('DB_PASSWORD');
     * </code>
     *
     * @see Application::getAppIni()
     */
    function readAppINI()
    {
   	    global $zone;
        # Predefined Application directories and files
        $_web_root = dirname( dirname( dirname(__FILE__) ) );
        $_web_root = strtr($_web_root,'\\','/');

      //========================================================
      // CZ messages hack
        $this->appIni["PATH_USERS_RESOURCES"] = $_web_root.'/avactis-themes/system/resources/';
      //========================================================

        $this->appIni["PATH_ASC_ROOT"]         = $_web_root.'/';
        # Path to license key file
        $this->appIni['PATH_LICENSE_KEY_FILE'] = $_web_root.'/avactis-conf/license.key';
        # Path to license certificate file
        $this->appIni['PATH_CERTIFICATE_KEY_FILE'] = $_web_root.'/avactis-conf/license.cert';

        //  current theme path
        $current_theme = 'system';

        $this->appIni['PATH_COMPONENTS']       = $_web_root.'/avactis-components/';
        $this->appIni['PATH_THEMES']           = $_web_root.'/avactis-themes/';
        $this->appIni['PATH_THEME']            = $this->appIni['PATH_THEMES'].$current_theme.'/';
        $this->appIni['PATH_THEME_CSS']        = $this->appIni['PATH_THEME'].'.css/';
        $this->appIni['PATH_THEME_THEMES']     = $this->appIni['PATH_THEME'].'.themes/';
        $this->appIni['PATH_THEME_IMAGES']     = $this->appIni['PATH_THEME'].'.images/';
        $this->appIni['PATH_THEME_JS']         = $this->appIni['PATH_THEME'].'.js/';

        $this->appIni['PATH_CORE_DIR']         = $_web_root.'/avactis-system/core/';
        $this->appIni['PATH_MODULES_DIR']      = $_web_root.'/avactis-system/modules/';
        $this->appIni['PATH_ADD_MODULES_DIR']  = $_web_root.'/avactis-extensions/';
        $this->appIni['PATH_CONF_DIR']         = $_web_root.'/avactis-conf/';
        $this->appIni['PATH_CACHE_DIR']        = $_web_root.'/avactis-conf/cache/';
        $this->appIni['PATH_BACKUP_DIR']       = $_web_root.'/avactis-conf/backup/';
        $this->appIni['PATH_SYSTEM_DIR']       = $_web_root.'/avactis-system/';
        $this->appIni['PATH_LAYOUTS_DIR']      = $_web_root.'/avactis-layouts/';
        $this->appIni['PATH_EXPORT_DIR']       = $_web_root.'/avactis-exports/';
        $this->appIni['PRODUCT_FILES_DIR']     = $_web_root.'/avactis-downloads/';
        $this->appIni['UPLOAD_FILES_DIR']      = $_web_root.'/avactis-uploads/';

        $this->appIni['PATH_ADMIN_DIR']        = $_web_root.'/avactis-system/admin/';
        $this->appIni['PATH_ADMIN_RESOURCES']  = $_web_root.'/avactis-system/admin/templates/resources/';
        $this->appIni['PATH_ADMIN_TPLS_VIEWS'] = $_web_root.'/avactis-system/admin/templates/modules/';

        # Path to TAGS file
        # : should be deprecated
        $this->appIni['PATH_TAGS_FILE']        = $_web_root.'/avactis-system/tags';

        # Path to temporary config files
        # : should be deprecated
        $this->appIni['PATH_BLOCK_CONFIG']     = $_web_root.'/avactis-system/admin/blocks_ini/';

        # Path to update files
        # : Should be changed
        $this->appIni['PATH_UPDATES_DIR']      = $_web_root.'/updates/';

        $this->appIni['PATH_CONFIG_FILE']      = $_web_root.'/avactis-conf/config.php';


		if ( file_exists($_web_root.'/avactis-system/license.cert.php') )
			rename($_web_root.'/avactis-system/license.cert.php',$this->appIni['PATH_CERTIFICATE_KEY_FILE'].'.php');
		if ( file_exists($_web_root.'/avactis-system/license.key.php') )
			rename($_web_root.'/avactis-system/license.key.php',$this->appIni['PATH_LICENSE_KEY_FILE'].'.php');

        # Reading system settings
        $cfg_file = $this->appIni['PATH_CONFIG_FILE'];
        $old_cfg_file = $_web_root.'/avactis-system/config.php';
		if ( file_exists($old_cfg_file) )
			rename($old_cfg_file,$cfg_file);

        if ( is_readable($cfg_file) )
        {
            $ini_cache = $this->getIniCache();
            $ini_mtime = filemtime($cfg_file);
            if ($ini_mtime == $ini_cache->read($cfg_file.'-mtime')) {
                $ini_array = $ini_cache->read($cfg_file);
            }
            else {
                $ini_array = @_parse_ini_file( $cfg_file );
                $ini_cache->write($cfg_file.'-mtime', $ini_mtime);
                $ini_cache->write($cfg_file, $ini_array);
            }
            $this->appIni = array_merge( $this->appIni,  $ini_array);

            if (file_exists($this->appIni['PATH_SYSTEM_DIR']."restore"))
            {
                $this->appIni['DB_TABLE_PREFIX'] = "restore_".$this->appIni['DB_TABLE_PREFIX'];
            }
        }
        else
        {
            _fatal( array( "CODE"      => "CORE_030",
                           "FILE"      => $cfg_file   ) );
        }

        $this->appIni['PATH_HTTPS_CONFIG_FILE']      = $_web_root.'/avactis-conf/https_config.php';

        if (!isset($this->appIni["HTTP_URL"]) && isset($this->appIni["WWWADDRESS"]))
        {
            $this->appIni["HTTP_URL"] = $this->appIni["WWWADDRESS"];
        }
        $this->appIni["HTTP_URL_CONFIG.PHP"] = $this->appIni["HTTP_URL"];
        $http_url = $this->appIni["HTTP_URL"];
        # Reading system settings
        $https_cfg_file = $this->appIni['PATH_HTTPS_CONFIG_FILE'];

        $old_https_cfg_file = $_web_root.'/avactis-system/https_config.php';
		if ( file_exists($old_https_cfg_file) )
			rename($old_https_cfg_file,$https_cfg_file);

        if ( is_readable($https_cfg_file) )
        {
            $ini_cache = $this->getIniCache();
            $https_mtime = filemtime($https_cfg_file);
            if ($https_mtime == $ini_cache->read($https_cfg_file.'-mtime')) {
                $ini_array = $ini_cache->read($https_cfg_file);
            }
            else {
                $ini_array = @_parse_ini_file( $https_cfg_file );
                $ini_cache->write($https_cfg_file.'-mtime', $https_mtime);
                $ini_cache->write($https_cfg_file, $ini_array);
            }
            $this->appIni = array_merge( $this->appIni,  $ini_array);
        }
        else
        {
            $this->appIni["HTTPS_URL"] = $this->appIni["HTTP_URL"];
            $this->appIni["AllAdminArea"] = "";
            $this->appIni["SignIn_AdminMembers"] = "";
            $this->appIni["Orders_Customers"] = "";
            $this->appIni["Payment_Shipping"] = "";
        }

        # The variable $layout_file_path must be defined in Page Init String
        global $layout_file_path;

        if (!isset($layout_file_path))
        {
            if($zone != 'AdminZone')
            {
                _fatal( array( "CODE" => "CORE_056") );
            }
        }

        #            $layout_file_path                                 :
        # 1)                 $layout_file_path                          ,
        #                                                       avactis-layouts.
        # 2)                 $layout_file_path                             ,
        #                                  .
        #
        # NOTE:                 $layout_file_path                             ,
        #                             ,
        #                   avactis-layouts         .
        #       ,                        .

        #                                      avactis-layouts,
        #                                            ,
        #                    .
        #             ,              1.5.3           1.6.0
        #             layout        avactis-layouts,
        # storefront/init.php                                  layout      .
        $new_layout_file_path = $this->appIni['PATH_LAYOUTS_DIR'] . basename($layout_file_path);
        if (file_exists($new_layout_file_path) && is_readable($new_layout_file_path) && is_file($new_layout_file_path))
        {
            $layout_file_path = $new_layout_file_path;
        }
        #              avactis-layouts                ,
        #                   layout                    1.5.3
        elseif (file_exists($layout_file_path) && is_readable($layout_file_path) && is_file($layout_file_path))
        {
            #                          ,
            #            $layout_file_path
        }
        else
        {
            if($zone != 'AdminZone')
            {
                _fatal( array( "CODE" => "CORE_056") );
            }
        }

        #
        # Read config.def.php
        # It must be before LayoutConfigurationManager::static_parse_layout_config_file(...)
        $this->appIni['PATH_LOCAL_CONFIG_FILE'] = $_web_root.'/avactis-system/config.def.php';
        $cfg_def_file = $this->appIni['PATH_LOCAL_CONFIG_FILE'];
        if (is_readable($cfg_def_file))
        {
            $ini_cache = $this->getIniCache();
            $ini_mtime = filemtime($cfg_def_file);
            if ($ini_mtime == $ini_cache->read($cfg_def_file.'-mtime')) {
                $ini_array = $ini_cache->read($cfg_def_file);
            }
            else {
                $ini_array = @_parse_ini_file( $cfg_def_file );
                $ini_cache->write($cfg_def_file.'-mtime', $ini_mtime);
                $ini_cache->write($cfg_def_file, $ini_array);
            }
            $this->appIni = array_merge( $this->appIni,  $ini_array);
        }

        #
        # Creating MultiLang_Core object
        $this -> multilang_core = new MultiLang_Core();
        # getting SQL Character Set for page charset
        if ($zone == 'AdminZone')
            $charset = $this -> appIni['ADMIN_ZONE_CHARSET'];
        else
            $charset = $this -> appIni['STOREFRONT_CHARSET'];
        # configuring mb_string if possible
        $this -> multilang_core -> configureMBSettings($charset);
        # saving SQL character set
        $this -> appIni['SQL_CHARACTER_SET'] = $this -> multilang_core -> _codepage;
        # if error throw the fatal message
        if (!$this -> appIni['SQL_CHARACTER_SET'])
        {
            $error = $this -> multilang_core -> getSQLCharacterSetError();
            if ($error == -1)
                _fatal(array('CODE' => 'CORE_ML_001'));
            if ($error == -2)
                _fatal(array('CODE' => 'CORE_ML_002'));
            # the thing that should not be...
            _fatal(array('CODE' => 'CORE_ML_003'));
        }

        $this->init_db();

        $layout_config_info = LayoutConfigurationManager::static_parse_layout_config_file($layout_file_path);

        //                        -        -               .
        if(!empty($layout_config_info["MAIN_ERROR_PARAMETERS"]))
        {
        	$_fatal_params = array();
            //                                            _fatal
            $_fatal_params[] = $layout_config_info["MAIN_ERROR_PARAMETERS"];
            unset($layout_config_info["MAIN_ERROR_PARAMETERS"]);
            foreach($layout_config_info as $value)
            {
            	$_fatal_params[] = $value;
            }

        	call_user_func_array("_fatal", $_fatal_params);
        }
        else
        {
        	//Merge parse results with appIni
            $this->appIni = array_merge($this->appIni, $layout_config_info);
        }

        # Path to Installer
        $this->appIni['INSTALLER_FILE_DAT'] = $_web_root . '/install.dat';
        $this->appIni['INSTALLER_FILE_PHP'] = $_web_root . '/install.php';

        # Path and URL to images directory
        $this->appIni['PATH_IMAGES_DIR'] = $_web_root.'/avactis-images/';
        $this->appIni['URL_IMAGES_DIR']  = $http_url.'avactis-images/';
        $this->appIni['HTTP_URL_IMAGES_DIR'] = $this->appIni['URL_IMAGES_DIR'];
        if (isset($this->appIni['HTTPS_URL']))
        {
            $this->appIni['HTTPS_URL_IMAGES_DIR']  = $this->appIni['HTTPS_URL'].'avactis-images/';
            // forcing all images to use https protocol to load...
            if ($this -> protocol == 'https')
                $this->appIni['URL_IMAGES_DIR'] = $this->appIni['HTTPS_URL_IMAGES_DIR'];
        }

        $this->appIni['SHOW_NO_PICTURE_IMAGE'] = 'DISABLED';
        $this->appIni['PATH_NO_PICTURE_IMAGE'] = $this->appIni['URL_IMAGES_DIR'].'no_picture.gif';
        $this->appIni['NO_PICTURE_IMAGE']      = 'no_picture.gif';

        /**
         * URL to AZ like http[s]://domain/path/
         *  E.g. http://www.avactis.com/avactis-system/admin/
         */
        $this->appIni['SITE_AZ_URL'] = $this->appIni['HTTP_URL'].'avactis-system/admin/';
        $this->appIni['SITE_AZ_CURRENT_URL'] = $this->appIni['SITE_AZ_URL'];
        if (isset($this->appIni['HTTPS_URL']))
        {
            $this->appIni['SITE_AZ_HTTPS_URL']  = $this->appIni['HTTPS_URL'].'avactis-system/admin/';
            if ($this->protocol == 'https') {
                $this->appIni['SITE_AZ_CURRENT_URL'] = $this->appIni['SITE_AZ_HTTPS_URL'];
            }
        }


        $this->appIni['URL_THEMES'] = $this->appIni['HTTP_URL'].'avactis-themes/';
        if (isset($this->appIni['HTTPS_URL']) && $this->getCurrentProtocol() == 'https')
        {
            $this->appIni['URL_THEMES']  = $this->appIni['HTTPS_URL'].'avactis-themes/';
        }

        $this->appIni['URL_COMPONENTS'] = $this->appIni['HTTP_URL'].'avactis-components/';
        if (isset($this->appIni['HTTPS_URL']) && $this->getCurrentProtocol() == 'https')
        {
            $this->appIni['URL_COMPONENTS']  = $this->appIni['HTTPS_URL'].'avactis-components/';
        }

        $this->appIni['URL_THEME'] = $this->appIni['HTTP_URL'].'avactis-themes/'.$current_theme.'/';
        if (isset($this->appIni['HTTPS_URL']) && $this->getCurrentProtocol() == 'https')
        {
            $this->appIni['URL_THEME']  = $this->appIni['HTTPS_URL'].'avactis-themes/'.$current_theme.'/';
        }

        $this->appIni['URL_THEME_CSS'] = $this->appIni['HTTP_URL'].'avactis-themes/'.$current_theme.'/.css/';
        if (isset($this->appIni['HTTPS_URL']) && $this->getCurrentProtocol() == 'https')
        {
            $this->appIni['URL_THEME_CSS']  = $this->appIni['HTTPS_URL'].'avactis-themes/'.$current_theme.'/.css/';
        }

        $this->appIni['URL_THEME_IMAGES'] = $this->appIni['HTTP_URL'].'avactis-themes/'.$current_theme.'/.images/';
        if (isset($this->appIni['HTTPS_URL']) && $this->getCurrentProtocol() == 'https')
        {
            $this->appIni['URL_THEME_IMAGES']  = $this->appIni['HTTPS_URL'].'avactis-themes/'.$current_theme.'/.images/';
        }

        $this->appIni['URL_THEME_JS'] = $this->appIni['HTTP_URL'].'avactis-themes/'.$current_theme.'/.js/';
        if (isset($this->appIni['HTTPS_URL']) && $this->getCurrentProtocol() == 'https')
        {
            $this->appIni['URL_THEME_JS']  = $this->appIni['HTTPS_URL'].'avactis-themes/'.$current_theme.'/.js/';
        }

        if ($zone == 'CustomerZone')
        {
            $storefront_templates_dir = $this->appIni['PATH_TEMPLATES'];
            $storefront_templates_dir_url = $this->appIni['URL_TEMPLATES'];
            if ($this->getCurrentProtocol() == "https" && $this->appIni['HTTPS_URL_TEMPLATES'])
            {
                $storefront_templates_dir_url = $this->appIni['HTTPS_URL_TEMPLATES'];
            }
            $GLOBALS['__TPL_URL__'] = $storefront_templates_dir_url;
            $GLOBALS['__TPL_DIR__'] = $storefront_templates_dir;

            $storefront_templates_dir = $this->appIni['SYSTEM_PATH_TEMPLATES'];
            $storefront_templates_dir_url = $this->appIni['SYSTEM_URL_TEMPLATES'];
            if ($this->getCurrentProtocol() == "https" && $this->appIni['SYSTEM_HTTPS_URL_TEMPLATES'])
            {
                $storefront_templates_dir_url = $this->appIni['SYSTEM_HTTPS_URL_TEMPLATES'];
            }
            $GLOBALS['__SYSTEM_TPL_URL__'] = $storefront_templates_dir_url;
            $GLOBALS['__SYSTEM_TPL_DIR__'] = $storefront_templates_dir;
        }
    }

    /**
     * Checks whether the ini file syntax is correct, in comparison with regular
     * expressions.
     *
     * @author Alexander Girin
     * @ it might be necessary to make changes in defining regular
     * expressions
     *
     * @param string $ini_file_name - ini filename
     * @return array - the array of errors found in the format:
     *                      Array (
     *                             Array ("OBJ_XXX" => "line_num"),
     *                             ...
     *                            )
     *                            )
     */
    function isIniFileCorrect($ini_file_name)
    {
        $ini_cache = $this->getIniCache();
        $ini_mtime = @filemtime($ini_file_name);
        if ($ini_mtime == $ini_cache->read($ini_file_name.'-errors-mtime')) {
            return $ini_cache->read($ini_file_name.'-errors');
        }

        $Errors = array();
        $file = new CFile($ini_file_name);
        $lines = $file->getLines();
        $str_num = 0;
        foreach ($lines as $str)
        {
            $str = trim($str);
            $str_num++;
            //If the string is a comment, then it is skipped
            if (empty($str) || $str[0] == ";")
            {
                continue;
            }
            //If there is character ";" in the string, everything has left after it
            //is truncated
            if ($pos = _ml_strpos($str, ";"))
            {
                $str = _ml_substr($str, 0, $pos)."\n";
            }
            //Check whether the rest of the strings match regular expressions
            //If the first string character is "[", then check whether the
            //the section declaration is valid
            if (!(($pos = _ml_strpos($str, "[")) === false))
            {
                # the name of the section must be well-defined
                if (!preg_match('/^\[[a-z]+[a-z0-9]*\]/i', $str))
                {
                    array_push($Errors, array("CORE_020" => $str_num));      // Mismatching with the regular expression of the
                                                                             // [<Section Name>] type
                }
                continue;
            }



            # if the directive contains "ProductType", check the syntax by the special template {1[,2,3]}
            if (preg_match('/^\s*producttype.*/', _ml_strtolower($str)) && !preg_match('/^TemplateFile-[0-9a-z]+-ProductType\s*{\s*\d+\s*(,\s*\d+\s*?)*\}\s*=\s*/i', _ml_strtolower($str)))
            {
                array_push($Errors, array("CORE_022_3INI" => $str_num));      // Mismatching with the regular expression of the type
                                                                         // <directive> [1[,2,3]] = <value>
            }
            # if the directive contains "Categories", check it by the special template {1[+][,2,3[+]]}
            elseif (preg_match('/^\s*categories.*/', _ml_strtolower($str)) && !preg_match('/^categories\s*\{\s*\d+\+{0,1}(\s*,\s*\d+\+{0,1}\s*?)*\}\s*=\s*/i', _ml_strtolower($str)))
            {
                array_push($Errors, array("CORE_021" => $str_num));      //  Mismatching with the regular expression of the type
                                                                         // <directive> [{1[+][, 2+, 3]}] = <value>
            }
            # if the directive contains "Products", check it by the special template {1[+][,2,3[+]]}
            elseif (preg_match('/^\s*products.*/', _ml_strtolower($str)) && !preg_match('/^products\s*\{\s*\d+\+{0,1}(\s*,\s*\d+\+{0,1}\s*?)*\}\s*=\s*/i', _ml_strtolower($str)))
            {
                array_push($Errors, array("CORE_021" => $str_num));      //   Mismatching with the regular expression of the type
                                                                         // <directive> [{1[+][, 2+, 3]}] = <value>
            }
        }
        $ini_cache->write($ini_file_name.'-errors-mtime', $ini_mtime);
        $ini_cache->write($ini_file_name.'-errors', $Errors);
        return $Errors;
    }

    /**
     * Emulation of the PHP function file_put_contents().
     */
    function asc_file_put_contents($filename, $text)
    {
        $f = new CFile($filename);
        $f->putContent($text);
    }
    /**
     * Copies three new ini files to the temporary folder. It converts them
     * to the old format. It returns the number of the strings,
     * appended to the beginning of the file - 2. It is used for error
     * mapping. While building, no error can exist in the first two strings.
     *
     * @author Alexander Girin
     *
     */
    function ini_copy_3_ini_and_convert_them_to_old_ini_format_3ini()
    {
        global $application;

        $from = $application->getAppIni("PATH_THEMES").('/system/catalog/product-info/default/product-info-config.ini');
        $to = $application->getAppIni('PATH_CACHE_DIR') . 'product-info-config.ini';
        if (@filemtime($from) > @filemtime($to)) {
            $text = @file_get_contents($from);
            if($text !== FALSE)
            {
                $text = str_replace("Template", "TemplateFile-Item", $text);
                $text = str_replace("ProductType{default}", "Default", $text);
                $text = str_replace("TemplateFile-Item-OutOfStock-", "TemplateFile-ItemOutOfStock-", $text);

                $text =
                    "[ProductInfo]" . "\n" .
                    "TemplateDirectory = catalog/product-info/default" . "\n" .
                    $text;
                $this->asc_file_put_contents($to, $text);
            }
        }

        $from = $application->getAppIni("PATH_THEMES").('/system/catalog/product-list/default/product-list-config.ini');
        $to = $application->getAppIni('PATH_CACHE_DIR') . 'product-list-config.ini';
        if (@filemtime($from) > @filemtime($to)) {
            $text = @file_get_contents($from);
            if($text !== FALSE)
            {
                $text = str_replace("Template", "TemplateFile", $text);
                $text = str_replace("Container-Empty", "ContainerEmpty", $text);
                $text = str_replace("ProductType{default}", "Item-Default", $text);
                $text = str_replace("ProductType", "Item-ProductType", $text);
                $text = str_replace("TemplateFile-OutOfStock-Item-", "TemplateFile-ItemOutOfStock-", $text);

                $text =
                    "[ProductList]" . "\n" .
                    "TemplateDirectory = catalog/product-list/default" . "\n" .
                    $text;
                $this->asc_file_put_contents($to, $text);
            }
        }

        $from = $application->getAppIni("PATH_THEMES").('/system/catalog/subcategory-list/default/subcategory-list-config.ini');
        $to = $application->getAppIni('PATH_CACHE_DIR') . 'subcategory-list-config.ini';
        if (@filemtime($from) > @filemtime($to)) {
            $text = @file_get_contents($from);
            if($text !== FALSE)
            {
                $text = str_replace("Template", "TemplateFile", $text);
                $text = str_replace("Container-Empty", "ContainerEmpty", $text);
                $text =
                    "[Subcategories]" . "\n" .
                    "TemplateDirectory = catalog/subcategory-list/default" . "\n" .
                    $text;
                $this->asc_file_put_contents($to, $text);
            }
        }

        $from = $application->getAppIni("PATH_THEMES").('/system/catalog/shopping-cart/default/shopping-cart-config.ini');
        $to = $application->getAppIni('PATH_CACHE_DIR') . 'cart-content-config.ini';
        if (@filemtime($from) > @filemtime($to)) {
            $text = @file_get_contents($from);
            if($text !== FALSE)
            {
                $text = str_replace("Template", "TemplateFile", $text);
                $text = str_replace("Container-Empty", "ContainerEmpty", $text);
                $text = str_replace("ProductType{default}", "Item-Default", $text);
                $text = str_replace("ProductType", "Item-ProductType", $text);
                $text =
                    "[ShoppingCart]" . "\n" .
                    "TemplateDirectory = catalog/shopping-cart/default" . "\n" .
                    $text;

                $text .= "\n[MiniCart]\n";
                $text .= "TemplateDirectory = catalog/shopping-cart/minicart\n";
                $text .= "TemplateFile-Container = shopping-cart-container.tpl.html\n";
                $text .= "TemplateFile-ContainerEmpty = shopping-cart-container-empty.tpl.html\n";
                $text .= "TemplateFile-Item-Default = shopping-cart-item-general.tpl.html\n";
                $text .= "TemplateFile-PriceItem = shopping-cart-price-item.tpl.html\n";
                $text .= "TemplateFile-PriceItemSeparator = shopping-cart-price-item-separator.tpl.html\n";
                $text .= "Option-Columns=1 \n";

                $text .= "\n[CartPreview]\n";
                $text .= "TemplateDirectory = catalog/shopping-cart/cartpreview\n";
                $text .= "TemplateFile-Container = shopping-cart-container.tpl.html\n";
                $text .= "TemplateFile-ContainerEmpty = shopping-cart-container-empty.tpl.html\n";
                $text .= "TemplateFile-Item-Default = shopping-cart-item-general.tpl.html\n";
                $text .= "TemplateFile-PriceItem = shopping-cart-price-item.tpl.html\n";
                $text .= "TemplateFile-PriceItemSeparator = shopping-cart-price-item-separator.tpl.html\n";
                $text .= "Option-Columns=1 \n";
                $this->asc_file_put_contents($to, $text);
            }
        }
    }

    /**
     * If one of the three ini files name is passed, replaces it with the name
     * of the matching copy in the temporary folder.
     * (See ini_copy_3_ini_and_convert_them_to_old_ini_format_3ini).
     * It returns without changes otherwise.
     *
     * @author Alexander Girin
     *
     */
    function ini_filename_old_to_copy_3ini($path_config, $ini_filename)
    {
        switch($ini_filename)
        {
            case "product-info-config.ini":
            case "product-list-config.ini":
            case "subcategory-list-config.ini":
            case "cart-content-config.ini":
            {
                return   $this->getAppIni('PATH_CACHE_DIR') . $ini_filename;
                break;
            }
            default:
            {
            	if(strpos($ini_filename,"/")===FALSE){
                return $path_config.$ini_filename;
            	}else{
     				return $ini_filename;
		    	}
            }
        }
    }

    /**
     * If one of the three ini files name is passed, replaces it with the name
     * of the new corresponding file.
     * (See ini_copy_3_ini_and_convert_them_to_old_ini_format_3ini).
     * It returns without changes otherwise.
     *
     * If in the converted configuration file an error occurs,
     * the message must specify a not converted file yet.
     * The nature of conversions and checkings allows at the same time to save
     * valid the error message semantic.
     *
     * @author Alexander Girin
     */
    function ini_filename_old_to_new_3ini($ini_filename)
    {
        if(strpos($ini_filename,"product-info-config.ini")>0)
            {
        	return modApiFunc('application','getAppIni','PATH_THEMES')."/system/catalog/product-info/default/product-info-config.ini";
                break;
        }else if(strpos($ini_filename,"product-list-config.ini")>0){
        	return modApiFunc('application','getAppIni','PATH_THEMES')."/system/catalog/product-list/default/product-list-config.ini";

                break;
            }
        else if(strpos($ini_filename,"cart-content-config.ini")>0){
             return modApiFunc('application','getAppIni','PATH_THEMES')."/system/catalog/shopping-cart/default/" . "shopping-cart-config.ini";
                break;
        }else
            {
                return $ini_filename;
            }
        }

    /**
     * Checks all kinds of errors connected with defining tags in the
     * customer area at the stage of initializing the application.
     *
     * @author Alexander Girin
     * @
     * @return
     */
    function checkTags($view_name,$view_file)
    {
        $path_config = $this->getAppIni('PATH_BLOCK_CONFIG');

            // if in the view the getTemplateFormat function isn't defined, then skip it.
            if(!is_callable(array($view_name, 'getTemplateFormat'))) return;

            // get information about configuration file format
            //$format = eval("return $view_name::getTemplateFormat();");
            //$format = $view_name::getTemplateFormat();
            $format = call_user_func(array($view_name, 'getTemplateFormat'));

            $format['layout-file'] = $this->ini_filename_old_to_new_3ini ($format['layout-file']);

            //: If at the template viewing stage the configuration file
            //  for ViewClassName was found, then it's better use it, rather than
            //  the default file in system/admin/blocks_ini

            $new_config_file = $path_config . $this->ini_filename_old_to_new_3ini ($format['layout-file']);
            $config_file     = $this->ini_filename_old_to_copy_3ini($path_config, $format['layout-file']);
            //2 lines were added in the beginning of a file while converging NEW configs. 0 - otherwise.
            $config_is_3ini = ($config_file == ($path_config . $format['layout-file']) ? true : false);
            $line_shift = $config_is_3ini ? -2 : 0;


            #check if the configuration file exists
            if (! is_readable($config_file))
            {
				$config_file = dirname(dirname(dirname(__FILE__))).str_replace(basename($view_file),'',$view_file).'/blocks_ini/'.$format['layout-file'];
            }
            if (! is_readable($config_file))
            {
                $err_mes = new ActionMessage(array("CORE_001"));
                $err_params = array(
                                    "CODE"    => "CORE_001",
                                    "FILE"    => $config_file,
                                    "MESSAGE" => $this->MessageResources->getMessage($err_mes)
                                    );
                $this->addErrorWarningMessage($view_name, "Error", $err_params);
                return; // continue the next view
            }

            $mtime = filemtime($config_file);
            $ini_cache = $this->getIniCache();
            if ($ini_cache->read($view_name.'-mtime') == $mtime) {
                $this->Configs_array[$view_name] = $ini_cache->read($view_name);
            }
            else {
                #check the configuration file syntax
                if (sizeof($ini_errors = $this->isIniFileCorrect($config_file))>0)
                {
                    foreach ($ini_errors as $error)
                    {
                        list($error_code, $error_line) = each($error);
                        $err_mes = new ActionMessage(array($error_code));
                        $err_params = array(
                                            "CODE"    => $error_code,
                                            "FILE"    => $new_config_file, //$config_file,
                                            "LINE"    => $error_line + $line_shift,
                                            "MESSAGE" => $this->MessageResources->getMessage($err_mes)
                                            );
                        $this->addErrorWarningMessage($view_name, "Error", $err_params);
                    }
                    return; //  continue the next view
                }

                //: see the comments to parse_ini_file above.
                $this->Configs_array[$view_name] = @_parse_ini_file($config_file, true);

                $ini_cache->write($view_name.'-mtime', $mtime);
                $ini_cache->write($view_name, $this->Configs_array[$view_name]);
            }

            foreach ($this->Configs_array[$view_name] as $section_name => $section_array)
            {
                $reserved_words = array("if", "else", "elseif", "while", "do", "for", "foreach", "break", "continue",
                                        "switch", "case", "declare", "return", "require", "include", "require_once",
                                        "include_once", "and", "or", "xor", "exit", "echo", "print", "die", "list",
                                        "as", "array", "function", "class", "var", "eval", "layouts", "");
                # check the matching reserved words
                if (array_search(_ml_strtolower($section_name), $reserved_words))
                {
                    $err_mes = new ActionMessage(array('CORE_006'));
                    $err_params = array(
                                        "CODE"    => 'CORE_006',
                                        "FILE"    => $new_config_file, //$config_file,
                                        "SECTION" => $section_name,
                                        "MESSAGE" => $this->MessageResources->getMessage($err_mes)
                                        );
                    _fatal($err_params);
                }

                # check the template description
                # check if the required directive "TemplateDirectory" exists
                if (($template_directory = getKeyIgnoreCase('TemplateDirectory', $section_array)) == null)
                {
                    $err_mes = new ActionMessage(array('CORE_015'));
                    $err_params = array(
                                        "CODE"      => 'CORE_015',
                                        "FILE"      => $new_config_file, //$config_file,
                                        "SECTION"   => $section_name,
                                        "DIRECTIVE" => "TemplateDirectory",
                                        "MESSAGE"   => $this->MessageResources->getMessage($err_mes)
                                        );
                    $this->addErrorWarningMessage($view_name, "Error", $err_params);
                }


                # check if all the described directives in the format exist
                foreach ($format['files'] as $template_file => $template_file_type)
                {
                    if ($template_file_type == TEMPLATE_FILE_SIMPLE && getKeyIgnoreCase('TemplateFile-'.$template_file, $section_array) == null)
                    {
                        $err_mes = new ActionMessage(array('CORE_0151', 'TemplateFile-'.$template_file));
                        $err_params = array(
                                            "CODE"      => 'CORE_0151',
                                            "FILE"      => $new_config_file,//$config_file,
                                            "SECTION"   => $section_name,
                                            "DIRECTIVE" => 'TemplateFile-'.$template_file,
                                            "MESSAGE"   => $this->MessageResources->getMessage($err_mes)
                                            );
                        $this->addErrorWarningMessage($view_name, "Error", $err_params);
                        continue;
                    }
                    elseif ($template_file_type == TEMPLATE_FILE_PRODUCT_TYPE)
                    {
                        # The description must contain several directives for the specified file:
                        # TemplateFile-<name>-Default - one directive
                        # TemplateFile-<name>-ProductType{1,2,3} - 0, 1 or a few for different product types
                        $default = false;
                        foreach ($section_array as $directive => $value)
                        {
                            if (_ml_strpos(_ml_strtolower($directive), _ml_strtolower('templatefile-'.$template_file.'-Default')) === 0)
                            {
                                $default = true;
                            }
                            # if it is a ProductType description, it must conform with the regulation
                            elseif (_ml_strpos(_ml_strtolower($directive), _ml_strtolower('templatefile-'.$template_file.'-ProductType')) === 0)
                            {

                                if (!preg_match('/{\s*-{0,1}\d+\s*(,\s*-{0,1}\d+\s*?)*\}/', $directive))
                                {
                                    $error_code = $config_is_3ini ? 'CORE_0154_3INI' : 'CORE_0154';
                                    $err_mes = new ActionMessage(array($error_code, $directive));
                                    $err_params = array(
                                                        "CODE"      => $error_code,
                                                        "FILE"      => $new_config_file, //$config_file,
                                                        "SECTION"   => $section_name,
                                                        "DIRECTIVE" => $directive,
                                                        "MESSAGE"   => $this->MessageResources->getMessage($err_mes)
                                                        );
                                    $this->addErrorWarningMessage($view_name, "Error", $err_params);
                                }
                            }
                        }
                        if (!$default)
                        {
                            # if the Default directive did not occur, then output an error
                            $err_mes = new ActionMessage(array('CORE_0155', 'TemplateFile-'.$template_file.'-Default'));
                            $err_params = array(
                                                "CODE"      => 'CORE_0155',
                                                "FILE"      => $new_config_file, //$config_file,
                                                "SECTION"   => $section_name,
                                                "DIRECTIVE" => 'TemplateFile-'.$template_file.'-Default',
                                                "MESSAGE"   => $this->MessageResources->getMessage($err_mes)
                                                );
                            $this->addErrorWarningMessage($view_name, "Error", $err_params);
                        }

                        continue;
                    }
                    # check if the described file exists
                    $template_file = getKeyIgnoreCase('TemplateFile-'.$template_file, $section_array);
                    $template_file = getTemplateFileAbsolutePath($template_directory . '/' . $template_file,$this->appIni["PATH_ASC_ROOT"].dirname($view_file));
                    #check if the files declared in the directives exist
                    if (!file_exists($template_file))
                    {
                        $err_mes = new ActionMessage(array('CORE_017', $template_file));
                        $err_params = array(
                                            "CODE"      => 'CORE_017',
                                            "FILE"      => $new_config_file, //$config_file,
                                            "SECTION"   => $section_name,
                                            "DIRECTIVE" => '', //$directive." = ".$value,
                                            "MESSAGE"   => $this->MessageResources->getMessage($err_mes)
                                            );
                        $this->addErrorWarningMessage($view_name, "Error", $err_params);
                    }
                }

                # check if all the described options in the format exist
                foreach ($format['options'] as $option => $option_type)
                {
                    # check the required options only
                    if ($option_type == TEMPLATE_OPTION_REQUIRED && getKeyIgnoreCase('Option-'.$option, $section_array) == null)
                    {
                        $err_mes = new ActionMessage(array('CORE_0152', 'Option-'.$option));
                        $err_params = array(
                                            "CODE"      => 'CORE_0152',
                                            "FILE"      => $new_config_file, //$config_file,
                                            "SECTION"   => $section_name,
                                            "DIRECTIVE" => 'Option-'.$option,
                                            "MESSAGE"   => $this->MessageResources->getMessage($err_mes)
                                            );
                        $this->addErrorWarningMessage($view_name, "Error", $err_params);
                    }
                }

                # check the settings of the references to other templates
                foreach ($section_array as $directive => $value)
                {
                    # skip all directives except 'Categories' and 'Products'
                    if ((_ml_strpos(_ml_strtolower($directive), 'categories') === false && _ml_strpos(_ml_strtolower($directive), 'products') === false) || !(_ml_strpos(_ml_strtolower($directive), 'subcategories') === false)) continue;
                    #check if the declaration of other directives is valid
                    if (! preg_match("/categories|products([\t\ ]*)(\{[0-9]+\+?(,?[\t\ ]*[0-9]\+?)*\})?/i", $directive))
                    {
                        $err_mes = new ActionMessage(array('CORE_013', $directive));
                        $err_params = array(
                                            "CODE"      => 'CORE_013',
                                            "FILE"      => $new_config_file,
                                            "SECTION"   => $section_name,
                                            "DIRECTIVE" => $directive." = ".$value,
                                            "MESSAGE"   => $this->MessageResources->getMessage($err_mes)
                                            );
                        $this->addErrorWarningMessage($view_name, "Warning", $err_params);
                    }
                    #check whether the sections declared in the directives exist in the section[layouts]
                    if (getKeyIgnoreCase($value, $this->Configs_array[$view_name]) == null)
                    {
                        $err_mes = new ActionMessage(array('CORE_012', $value));
                        $err_params = array(
                                            "CODE"      => 'CORE_012',
                                            "FILE"      => $new_config_file,
                                            "SECTION"   => $section_name,
                                            "DIRECTIVE" => $directive." = ".$value,
                                            "MESSAGE"   => $this->MessageResources->getMessage($err_mes)
                                            );
                        $this->addErrorWarningMessage($view_name, "Warning", $err_params);
                    }
                    continue;
                }
            }
        return ;
    }

    /**
     * Checks if the "Default" directive exists in each of the sections,
     * declared in the layouts-config.ini file and if matching it template files
     * exist. It also checks the files, defined for other directives.
     *
     * @author Alexander Girin
     * @param string $section_name - the name of checked section
     * @param array $section_arr - the array of values, defined in this section
     * @param array $necessary_checks - reference to the array of necessary
     * checkings
     * @return
     * @ do the tag registration where the error occurred
     */
    function checkDefaultFileAndOther ($section_name, $section_arr, &$necessary_checks, $SITE_PATH = NULL, $PATH_LAYOUTS_CONFIG_FILE = NULL)
    {
        if($SITE_PATH === NULL)
        {
            $SITE_PATH = $this->getAppIni('SITE_PATH');
        }

        if($PATH_LAYOUTS_CONFIG_FILE === NULL)
        {
            $PATH_LAYOUTS_CONFIG_FILE = $this->getAppIni('PATH_LAYOUTS_CONFIG_FILE');
        }

        $necessary_checks[$section_name]["section"] = true;
        foreach ($section_arr as $param => $value)
        {
            if (_ml_strtolower(trim($param)) == 'https')
            {
                continue;
            }
            if (_ml_strtolower($param) == 'default')
            {
                $necessary_checks[$section_name]["default"] = true;
                #check if the file declared in the "default" directive exists
                if (file_exists($SITE_PATH.$value))
                {
                    $necessary_checks[$section_name]["template_file"] = true;
                }
                else
                {
                    $necessary_checks[$section_name]["template_file"] = $SITE_PATH.$value;
                }
            }
            else
            {
                switch ($section_name)
                {
                    case "productlist":
                        #check if the directive declaration in the section [Category] is valid
                        if (! preg_match("/categories([\t\ ]*)(\{[0-9]+\+?(,?[\t\ ]*[0-9]\+?)*\})?/i", $param))
                        {
                            $err_mes = new ActionMessage(array('CORE_007', $param));
                            $err_params = array(
                                                "CODE"      => 'CORE_007',
                                                "FILE"      => $PATH_LAYOUTS_CONFIG_FILE,
                                                "SECTION"   => "ProductList",
                                                "DIRECTIVE" => $param." = ".$value,
                                                "MESSAGE"   => $this->MessageResources->getMessage($err_mes)
                                                );
                            $this->addErrorWarningMessage("Core", "Warning", $err_params);
                        }
                        break;
                    case "productinfo":
                        #check if the directive declaration in the section [ProductInfo] is valid
                        if (! preg_match("/categories|products([\t\ ]*)(\{[0-9]+\+?(,?[\t\ ]*[0-9]\+?)*\})?/i", $param))
                        {
                            $err_mes = new ActionMessage(array('CORE_008', $param));
                            $err_params = array(
                                                "CODE"      => 'CORE_008',
                                                "FILE"      => $PATH_LAYOUTS_CONFIG_FILE,
                                                "SECTION"   => "ProductInfo",
                                                "DIRECTIVE" => $param." = ".$value,
                                                "MESSAGE"   => $this->MessageResources->getMessage($err_mes)
                                                );
                            $this->addErrorWarningMessage("Core", "Warning", $err_params);
                        }
                        break;
                    case "cart":
                    case "closed":
                        #check if the directive declaration in the section [cart], [checkout], [closed] is valid
                        $err_mes = new ActionMessage(array('CORE_009', $param));
                        $err_params = array(
                                            "CODE"      => 'CORE_009',
                                            "FILE"      => $PATH_LAYOUTS_CONFIG_FILE,
                                            "SECTION"   => $section_name,
                                            "DIRECTIVE" => $param." = ".$value,
                                            "MESSAGE"   => $this->MessageResources->getMessage($err_mes)
                                            );
                        $this->addErrorWarningMessage("Core", "Warning", $err_params);
                        break;
                    default:

                        break;
                }
                #check if the files declared in the directives exist
                if (!file_exists($SITE_PATH.$value))
                {
                    $err_mes = new ActionMessage(array('CORE_005', $SITE_PATH.$value));
                    $err_params = array(
                                        "CODE"      => 'CORE_005',
                                        "FILE"      => $PATH_LAYOUTS_CONFIG_FILE,
                                        "SECTION"   => $section_name,
                                        "DIRECTIVE" => $param,
                                        "MESSAGE"   => $this->MessageResources->getMessage($err_mes)
                                        );
                    $this->addErrorWarningMessage("Core", "Warning", $err_params);
                }
            }
        }
        if ($necessary_checks[$section_name]["default"] == false)
        {
            $necessary_checks[$section_name]["template_file"] = true;
        }
    }

    /**
     * Adds fatal error or warning message into the proper arrays $this->TagFatalErrors,
     * $this->TagWarnings.
     *
     * @author Alexander Girin
     * @param string $message - error or warning message
     * @param string $message_level - error level: the core or the block
     * @param string $message_type - message type: the error or warning
     * @return
     * @ make the tag registration where the error occurred
     */
    function addErrorWarningMessage($message_level="Core", $message_type="Error", $params=array())
    {
        #register the fatal errors of core and block level
        if ($message_type=="Error")
        {
            if ($message_level=="Core")
            {
                if (!isset($this->TagFatalErrors['Core']))
                {
                    $this->TagFatalErrors['Core'] = array();
                }
                $this->TagFatalErrors['Core'][] = $params;
            }
            else
            {
                if (!isset($this->TagFatalErrors[$message_level]))
                {
                    $this->TagFatalErrors[$message_level] = array();
                }
                $this->TagFatalErrors[$message_level][] = $params;
            }
        }
        #register the warnings of core and block level
        elseif ($message_type=="Warning")
        {
            if ($message_level=="Core")
            {
                if (!isset($this->TagWarnings['Core']))
                {
                    $this->TagWarnings['Core'] = array();
                }
                $this->TagWarnings['Core'][] = $params;
            }
            else
            {
                if (!isset($this->TagWarnings[$message_level]))
                {
                    $this->TagWarnings[$message_level] = array();
                }
                $this->TagWarnings[$message_level][] = $params;
            }
        }
    }

    /**
     * Checks if fatal errors of the block level exist.
     *
     * @author Alexandr Girin
     * @param string $block - the block name
     * @return bool Returns true if fatal errors of the block level have been
     * detected, false otherwise
     */
    function issetBlockTagFatalErrors($block)
    {
        if (isset($this->TagFatalErrors[$block]))
        {
            return true;
        }
        return false;
    }

    /**
     * Gets the array read from the *.ini file.
     *
     * @author Alexander Girin
     * @param string $ini_array - the array name, e.g. "Layouts" or the
     * representation name
     * @return array - the array of values, read from the *.ini file
     */
    function getConfigArray($ini_array)
    {
        return getKeyIgnoreCase($ini_array, $this->Configs_array);
    }

    /**
     * Outputs the list of errors and warnings, occurred during the process of
     * checkings, connected with user Tags.
     *
     * @author Alexander Girin
     * @return
     */
    function outputTagErrors($in_block = "", $block_name = "", $type = "")
    {
        global $application;
        $session = &$application->getInstance("Session");
        if ((!$session->is_Set("DEBUG_MODE")) &&  (Configuration::getSupportMode(ASC_S_DISPLAY_ERRORS)))
        {
            return;
        }
        if (sizeof($this->TagFatalErrors)==0 && sizeof($this->TagWarnings)==0)
        {
            if (file_exists("TagErrors.html"))
            {
                @unlink("TagErrors.html");
            }
            return;
        }
        $link = "";
        if ($in_block)
        {
            $URL = $this->getAppIni("HTTP_URL")."avactis-system/TagErrors.html";
            if ($type == "Errors" && isset($this->TagFatalErrors[$block_name]))
            {
                $link = "<span style=\"font-family: verdana; font-size: 10px; font-weight: bold;\">";
                $link.= "<a href=\"$URL\" target=_blank>The are Fatal Errors in Block: \"$block_name\"</a></span><br>";
            }
            if ($type == "Warnings" && isset($this->TagWarnings[$block_name]))
            {
                $link = "<span style=\"font-family: verdana; font-size: 10px; font-weight: bold\">";
                $link.= "<a href=\"$URL\" target=_blank>The are Warnings in Block: \"$block_name\"</a></span><br>";
            }
            if ($link)
            {
                echo $link;
            }
        }
        $output = "";
        $header = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\"\n";
        $header.= "\"http://www.w3.org/TR/html4/loose.dtd\">\n";
        $header.= "<HTML><HEAD><TITLE>Tags Errors and Warnings</TITLE>";
        $header.= "<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=windows-1251\">\n";
        $header.= "<LINK HREF=\"style/style.css\" TYPE=\"text/css\" REL=\"stylesheet\">\n";
        $header.= "<script src=\"javascript/menu.js\" type=\"text/javascript\"></SCRIPT>\n";
        $header.= "</HEAD><BODY>\n";
        $header.= "<style type=\"text/css\">\n";
        $header.= "<!--\n";
        $header.= ".title{background-color: #C0C0C0; font-size: 12px; font-family: verdana; font-weight: bold; \n";
        $header.= "       border-right: solid #505050 1px; border-bottom: solid #505050 1px; \n";
        $header.= "       border-left: solid #FFFFFF 1px; border-top: solid #FFFFFF 1px;}\n";
        $header.= ".subtitle{background-color: #E0E0E0; font-size: 11px; font-family: verdana; font-weight: bold; \n";
        $header.= "       border-right: solid #C0C0C0 1px; border-bottom: solid #C0C0C0 1px; \n";
        $header.= "       border-left: solid #FFFFFF 1px; border-top: solid #FFFFFF 1px;}\n";
        $header.= ".message{background-color: #FAFAFA; font-size: 10px;\n";
        $header.= "       border-right: solid #E0E0E0 1px; border-bottom: solid #E0E0E0 1px; \n";
        $header.= "       border-left: solid #FFFFFF 1px; border-top: solid #FFFFFF 1px;}\n";
        $header.= "-->\n";
        $header.= "</style>\n";
        $header.= "<TABLE width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">\n";

        $subtitle = "<tr><td width=\"100%%\" class=\"title\">%s</td></tr>\n";
        $subtitle.= "<tr><td>\n<table width=\"100%%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">\n";
        $subtitle.= "<tr><td class=\"subtitle\"";
        $subtitle.= "width=\"10%%\">Error Code</td>\n";
        $subtitle.= "<td class=\"subtitle\"";
        $subtitle.= "width=\"90%%\">Error Source</td></tr>%s</table></td></tr>\n";

        $tablecontent = "<tr><td class=\"subtitle\" style=\"background-color: #FAFAFA;\">%s</td>\n";
        $tablecontent.= "<td class=\"subtitle\">";
        $tablecontent.= "<table width=\"100%%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">%s</table></td></tr>\n";

        $source_line = "<tr><td class=\"message\" width=\"10%%\">%s</td>\n";
        $source_line.= "<td class=\"message\" style=\"font-weight: normal;\" width=\"90%%\">%s</td></tr>\n";

        $output = $header;
        if (isset($this->TagFatalErrors['Core']))
        {
            $content = "";
            foreach ($this->TagFatalErrors['Core'] as $error_source)
            {
                $content.= $this->outputErrorSource($tablecontent, $source_line, $error_source);
            }
            $output.= sprintf($subtitle, "FATAL Core Errors", $content);
        }
        foreach ($this->TagFatalErrors as $block => $block_errors)
        {
            if ($block != "Core")
            {
                $content = "";
                foreach ($this->TagFatalErrors[$block] as $error_source)
                {
                    $content.= $this->outputErrorSource($tablecontent, $source_line, $error_source);
                }
                $output.= sprintf($subtitle, $block." Block: Fatal Errors", $content);
            }
        }
        if (isset($this->TagWarnings['Core']))
        {
            $content = "";
            foreach ($this->TagWarnings['Core'] as $error_source)
            {
                $content.= $this->outputErrorSource($tablecontent, $source_line, $error_source);
            }
            $output.= sprintf($subtitle, "Core: Warnings", $content);
        }
        foreach ($this->TagWarnings as $block => $block_errors)
        {
            if ($block != "Core")
            {
                $content = "";
                foreach ($this->TagWarnings[$block] as $error_source)
                {
                    $content.= $this->outputErrorSource($tablecontent, $source_line, $error_source);
                }
                $output.= sprintf($subtitle, $block." Block: Warnings", $content);
            }
        }

        $footer = "</table></BODY></HTML>";
        $output.= $footer;
        $fp = new CFile($this->getAppIni("PATH_SYSTEM_DIR")."TagErrors.html");
        $fp->putContent($output);

        $javascript = "<SCRIPT LANGUAGE=\"JavaScript\">\n";
        $javascript.= "<!--\n";
        $javascript.= "var newwin;\n";
        $javascript.= "var w = 800;\n";
        $javascript.= "var h = 600;\n";
        $javascript.= "var bars_width = 47;\n";
        $javascript.= "var winl = (screen.width - w) / 2;\n";
        $javascript.= "var wint = ((screen.height - h) - bars_width) / 2;\n";
        $javascript.= "var params = 'top='+wint+',left='+winl+',width='+w+',height='+h+',directories=no,location=no,menubar=no,scrollbars=yes,status=no,toolbar=no,resizable=yes';\n";
        $javascript.= "newwin = window.open('".$this->getAppIni("HTTP_URL")."avactis-system/TagErrors.html', 'Errors', params);\n";
        $javascript.= "newwin.focus();\n";
        $javascript.= "//-->\n";
        $javascript.= "</SCRIPT>\n";
        echo $javascript;
    }

    /**
     * Outputs the error or warning message.
     *
     * @author Alexander Girin
     * @param string $tablecontent - HTML template of Table content
     * @param string $source_line - HTML string template of Error source
     * @param array $error_source - Array of Error Source
     * @return string HTML code
     */
    function outputErrorSource($tablecontent, $source_line, $error_source)
    {
        $source = "";
        if (isset($error_source["FILE"]))
        {
            $source.= sprintf($source_line, "File:", $error_source["FILE"]);
        }
        if (isset($error_source["LINE"]))
        {
            $source.= sprintf($source_line, "Line:", $error_source["LINE"]);
        }
        if (isset($error_source["SECTION"]))
        {
            $source.= sprintf($source_line, "Section:", "[".$error_source["SECTION"]."]");
        }
        if (isset($error_source["DIRECTIVE"]))
        {
            $source.= sprintf($source_line, "Directive:", $error_source["DIRECTIVE"]);
        }
        if (isset($error_source["MESSAGE"]))
        {
            $source.= sprintf($source_line, "Message:", $error_source["MESSAGE"]);
        }
        return sprintf($tablecontent, $error_source["CODE"], $source);
    }

    /**
     * Reads out the settings from the layouts-config.ini file.
     */
    function readLayoutsINI()
    {
        # read the configuration file
        $ini_array = $this->getConfigArray('Layouts');

        $ini_array = array_merge($ini_array, modApiFunc('Modules_Manager', 'getStorefrontLayout'));

        $this->row_layout_ini_array = $ini_array;

        $layout = array();
        $direct_categories = array();
        foreach ($ini_array as $section_name => $section_arr)
        {
            $section_name = _ml_strtolower($section_name);
            $layout[$section_name] = $this->readLayout($section_arr);
        }
        $this->layout = $layout;
    }

    function _getChildrenCategories($arr, $cid)
    {
        $parent = $arr[$cid];
        $children = array();
        foreach ($arr as $cat) {
            if ($cat['c_left'] >= $parent['c_left'] && $cat['c_right'] <= $parent['c_right'])
                $children[] = $cat;
        }
        return $children;
    }

    /**
     * Reads out the block settings from configuration files.
     */
    function readBlocksINI($block)
    {
        if (!isset($this->Configs_array[$block]))
        {
            return;
        }
        $block_arr = $this->Configs_array[$block];

            if (_ml_strtolower($block) == 'layouts') return;
            # read the configuration file for this block
            $config = $this->readBlockINI($block);
            # register functions for derived blocks.
            foreach ($config as $custom_tag => $template)
            {
                # skip the special tag layouts
                if ($custom_tag == "layout") continue;
                # skip the tags, which are representation names
                if (class_exists($custom_tag) && ($custom_tag != "Checkout")) continue;
                if(function_exists($custom_tag)) continue;
                $func = '
                function '. $custom_tag .'() {
                    global $application;
                    $application->setBlockOverride(\''.$block.'\', \''.$custom_tag.'\');
                    $arg0 = @func_get_arg(0);
                    if ($arg0 === false) {
                        $out = '. $block .'();
                    } else {
                        $out = '. $block .'($arg0);
                    }
                    $application->resetBlockOverride(\''.$block.'\');
                    return $out;
                }';
                eval($func);
            }
            # save information.
            $this->block_config[$block] = $config;
        }

    /**
     * Reads out the block settings from the given file.
     * @return array
     */
    function readBlockINI($viewname)
    {
        #  read the configuration file
        $ini_array = $this->getConfigArray($viewname);

        $block = array();
        # parse settings for each section
        foreach ($ini_array as $section_name => $section_arr)
        {
            # it is the block description.
            $block[$section_name] = array('template' => array(), 'options' => array(), 'layout' => array( 'categories' => array(), 'products' => array()));
            $block_section = &$block[$section_name];
            # parse every option for this block.
            foreach ($section_arr as $key => $value)
            {
                // keyword: TemplateDirectory, TemplateFile, Option
                $_key = _ml_strtolower($key);
                // describe the references to the other templates
                if (_ml_strpos($_key, 'categories') === 0 || _ml_strpos($_key, 'products') === 0)
                {
                    $layout_line = $this->readLayoutLine($_key, $value);
                    # add the references to the array
                    if (is_array($layout_line) && array_key_exists('categories', $layout_line))
                    {
                        $block_section['layout']['categories'] = $block_section['layout']['categories'] + $layout_line['categories'];
                    }
                    elseif (is_array($layout_line) && array_key_exists('products', $layout_line))
                    {
                        $block_section['layout']['products'] = $block_section['layout']['products'] + $layout_line['products'];
                    }
                }
                // the keyword variation, that may contain branches: ProductType
                $_subkey = "";
                if (_ml_strpos($_key, '-') > 0)
                {
                    $_subkey = _ml_substr($_key, _ml_strpos($_key, '-') + 1);
                    $_key = _ml_substr($_key, 0, _ml_strpos($_key, '-'));
                }
                # if the option has branches, then parse it.
                if (_ml_strpos($_subkey, '-') > 0)
                {
                    $_vary = _ml_substr($_subkey, _ml_strpos($_subkey, '-') + 1);
                    $_subkey = _ml_substr($_subkey, 0, _ml_strpos($_subkey, '-'));
                    if ($_vary == "default")
                    {
                        $value = array('default' => $value);
                    }
                    else
                    {
                        $value = $this->readLayoutLine($_vary, $value);
                    }
                }
                switch ($_key)
                {
                    case 'templatedirectory':
                        $block_section['template']['directory'] = $value;
                        break;

                    case 'templatefile':
                        if (array_key_exists($_subkey, $block_section['template']) && is_array($block_section['template'][$_subkey]))
                        {
                            $block_section['template'][$_subkey] = _array_merge_recursive($block_section['template'][$_subkey], $value);
                        }
                        else
                        {
                            $block_section['template'][$_subkey] = $value;
                        }
                        break;

                    case 'option':
                        $block_section['options'][$_subkey] = $value;
                        break;

                    default:
                        break;
                }
            }
        }
        return $block;
    }

    /**
     * Determines the layout name, depending on the current PHP page.
     * @return Array each array key - layout, to which this page refers.
     */
    function getLayout()
    {
        global $application;

        $page_name = $_SERVER["PHP_SELF"]; // the current executing page address from the root of the site
        $parsed_site_address = parse_url($application->getAppIni('SITE_URL')); // parse the base site address

        // remove the path from the root of the site,if it exists
        if (_ml_strpos($page_name, $parsed_site_address['path']) === 0)
        {
            $page_name = _ml_substr($page_name, _ml_strlen($parsed_site_address['path']));
        }

        $result = array();
        foreach ($this->layout as $layout => $arr)
        {
            $result[$layout] = multi_array_search($page_name, $arr);
        }
        return $result;
    }

    /**
     * Returns the current category id and the current product id, if the page
     * displayed at this moment, refers to Category Layout or ProductInfo Layout.
     *
     * @return Array
     */
    function getTemplateParameters()
    {
        $result = array('category_id' => -1, 'product_id' => -1);
        if (modApiFunc('Users', 'getZone') == 'AdminZone')
            return $result;

        $layout_arr = $this->getLayout();

        $category_id = modApiFunc('CProductListFilter','getCurrentCategoryId');
        $product_id = modApiFunc('Catalog', 'getCurrentProductID');

        if ($layout_arr['productinfo'])
        {
            $result['category_id'] = $category_id;
            $result['product_id'] = $product_id;
        }
        if ($layout_arr['productlist'])
        {
            $result['category_id'] = $category_id;
            $result['product_id'] = $product_id;
        }
        return $result;
    }

    function prepareStorefrontBlockTag($viewname)
    {
        global $zone;
        static $blocks_read;
        ! isset($blocks_read) && ($blocks_read = array());

        if ($zone == 'CustomerZone') {
            $mm = $this->getInstance( 'Modules_Manager' );
            if (! class_exists($viewname)) {
                $mm->includeViewFileOnce($viewname);
            }
            if (! isset($blocks_read[$viewname])) {
                $blocks_read[$viewname] = true;
                $this->checkTags($viewname, $mm->czViewList[$viewname]);
                $this->readBlocksINI($viewname);
            }
        }
    }


    /**
     * Returns the description of the template for the given block.
     *
     *            $overrie,                                   ,           $block_name
     *         $override.
     */
    function getBlockTemplate($block_name, $override = null)
    {
        $this->prepareStorefrontBlockTag($block_name);

        $params = $this->getTemplateParameters();
        $category = $params['category_id'];
        $product = $params['product_id'];
        # if such block doesn't exist, return null.
        if (!array_key_exists($block_name, $this->block_config))
        {
            return null;
        }

        $block = $this->block_config[$block_name];
        # check if the tag overrides
        if ($override == null)
        {
            $override = $this->getBlockOverride($block_name);
        }
        if ($override != null)
        {
            # if the description for the specified block doesn't exist, return null
            if (getKeyIgnoreCase($override, $block) === null)
            {
                return null;
            }
            # if the overriding of the main tag is specified, extract the template for the overriding tag
            $block = $block[$override];
        }
        else
        {
            # if the description for the basic block doesn't exist, return null
            if (getKeyIgnoreCase($block_name, $block) === null)
            {
                return null;
            }
            # extract the template for the basic tag otherwise
            $block = $block[$block_name];
        }
        $layout = $block['layout'];

        if ($product != -1 && array_key_exists('products', $layout))
        {
            if (array_key_exists($product, $layout['products']))
            {
                $block = $this->block_config[$block_name][$layout['products'][$product]];
            }
        }
        if ($category != -1 && array_key_exists('categories', $layout))
        {
            if (array_key_exists($category, $layout['categories']))
            {
                $block = $this->block_config[$block_name][$layout['categories'][$category]];
            }
        }
        return $block;
    }

    /**
     * Returns the whole path of the specified file in this block template.
     */
    function getBlockTemplateFile($template, $file, $product_type = null,$current_template_path=null)
    {
        if (getKeyIgnoreCase($file, $template['template']) == null)
        {
            return null;
        }
        # define a base folder.
        $dir = $template['template']['directory'] . '/';

        # if the product type is defined, then search one of the variation by product_type
        if ($product_type != null)
        {
            if (is_array($template['template'][_ml_strtolower($file)]))
            {
                if (array_key_exists('producttype', $template['template'][_ml_strtolower($file)]) && is_array($template['template'][_ml_strtolower($file)]['producttype']) && array_key_exists($product_type, $template['template'][_ml_strtolower($file)]['producttype']))
                {
                    # if the variation array is defined, then select a required one.
                    return getTemplateFileAbsolutePath($dir . $template['template'][_ml_strtolower($file)]['producttype'][$product_type]);
                }
                else
                {
                    # if the required variation isn't found or the array isn't specified at all, return the default value.
                    # warning: the default value must be specified!
                    return getTemplateFileAbsolutePath($dir . $template['template'][_ml_strtolower($file)]['default']);
                }
            }
        }
        return getTemplateFileAbsolutePath($dir . $template['template'][_ml_strtolower($file)],$current_template_path);
    }

    /**
     *           md5                                    .
     *       ,                            $block_alias
     *
     * @param string $block_alias
     * @param string $block_name
     * @return string md5 hash
     */
    function getBlockTemplateHash($block_alias, $block_name)
    {
        global $application;
        //                    $template           $block_name,            ,                           $block_alias
        $template = $this->getBlockTemplate($block_name, $block_alias);
        if (!isset($template['template']) || !isset($template['template']['directory']))
        {
            $hash = null;
        }
        else
        {
            $hash = '';
            $tpl_dir = $template['template']['directory'] . '/';
            foreach ($template['template'] as $tpl_file)
            {
                $tpl_path = getTemplateFileAbsolutePath($tpl_dir.$tpl_file);
                if (file_exists($tpl_path))
                {
                    $hash .= md5_file($tpl_path);
                }
            }

            if (isset($template['options']) && is_array($template['options']))
            {
                foreach ($template['options'] as $opt)
                {
                    $hash .= $opt;
                }
            }
        }
        return md5($hash);
    }

    /**
     * Returns the value of the option for this block template.
     */
    function getBlockOption($template, $option)
    {
        if (!array_key_exists(_ml_strtolower($option), $template['options']))
        {
            return null;
        }
        return $template['options'][_ml_strtolower($option)];
    }

    function __getSubcategories($arr, $cid)
    {
        $parent = $arr[$cid];
        $children = array();
        foreach ($arr as $cat) {
            if ($cat['c_left'] >= $parent['c_left'] && $cat['c_right'] <= $parent['c_right'])
                $children[] = $cat;
        }
        return $children;
    }
    /**
     * Reads out the settings from the Layout section of any configuration file.
     */
    function readLayout($section_arr)
    {
        #   create an anonymous function to define children categories
        $layout = array();
        $direct_categories = array();
        foreach ($section_arr as $option => $value)
        {
            $option = _ml_strtolower($option);
            if ($option == "default")
            {
                $layout['default'] = $value;
            }
            elseif ($option == 'https') {}
            else
            {
                # find out a keyword
                $end = _ml_strpos($option, '{');
                $key = trim(_ml_substr($option, 0, $end));

                # parse the ids
                $begin = _ml_strpos($option, '{') + 1;
                $end = _ml_strpos($option, '}');
                $ids = str_replace(" ", "", trim(_ml_substr($option, $begin, $end - $begin)));
                $ids = explode(",", $ids);

                #  create a key, if it hasn't been created yet
                if (!array_key_exists($key, $layout))
                {
                    $layout[$key] = array();
                }
                foreach ($ids as $id)
                {
                    # check if the nesting symbol exists
                    if (_ml_strpos($id, '+'))
                    {
                        $id = _ml_substr($id, 0, _ml_strlen($id) - 1);
                        if ($key == "categories" && ! modApiFunc('Catalog', 'isCorrectCategoryId', $id))
                            continue;
                        $id = modApiFunc('Catalog', 'getSubcategoryIdsWithParent', $id);
                    }
                    else
                    {
                        if ($key == "categories" && ! modApiFunc('Catalog', 'isCorrectCategoryId', $id))
                            continue;
                        $direct_categories[] = $id;
                    }
                    if (is_array($id))
                    {
                        foreach ($id as $_id)
                        {
                            if (!in_array($_id['id'], $direct_categories))
                            {
                                $layout[$key][$_id['id']] = $value;
                            }
                        }
                    }
                    else
                    {
                        $layout[$key][$id] = $value;
                    }
                }
            }
        }
        return $layout;
    }

    /**
     * @ the description for the Application-> function.
     */
    function readLayoutLine($option, $value)
    {
        # create an anonymous function to define children categories
        $layout = array();
        #  find out a keyword
        $end = _ml_strpos($option, '{');
        $key = trim(_ml_substr($option, 0, $end));

        #  parse the ids
        $begin = _ml_strpos($option, '{') + 1;
        $end = _ml_strpos($option, '}');
        $ids = str_replace(" ", "", trim(_ml_substr($option, $begin, $end - $begin)));
        $ids = explode(",", $ids);

        # create a key, if it hasn't been created yet
        if (!array_key_exists($key, $layout))
        {
            $layout[$key] = array();
        }
        $direct_categories = array();
        foreach ($ids as $id)
        {
            #  check if the nesting symbol exists
            if (_ml_strpos($id, '+'))
            {
                $id = _ml_substr($id, 0, _ml_strlen($id) - 1);
                if ($key == "categories" && ! modApiFunc('Catalog', 'isCorrectCategoryId', $id))
                    continue;
                $id = modApiFunc('Catalog', 'getSubcategoryIdsWithParent', $id);
            }
            else
            {
                if ($key == "categories" && ! modApiFunc('Catalog', 'isCorrectCategoryId', $id))
                    continue;
                $direct_categories[] = $id;
            }
            if (is_array($id))
            {
                foreach ($id as $_id)
                {
                    if (!in_array($_id['id'], $direct_categories))
                    {
                        $layout[$key][$_id['id']] = $value;
                    }
                }
            }
            else
            {
                $layout[$key][$id] = $value;
            }
        }
        return $layout;
    }
    /**
     * Gets Application setting value by a key.
     * If the key does not exist, then return NULL.
     *
     * @see Application::readAppINI()
     * @param string $key Key
     * @return mixed Value of key or NULL if key is not exists
     */
    function getAppIni($key)
    {
        $val = '';
        if (array_key_exists($key, $this->appIni))
        {
            $val = $this->appIni[$key];
        }
        else
        {
            $val = NULL;
        }

        return $val;
    }

    /**
     * Registers all the information files in the system/tags file.
     */
    function registerTags()
    {
        if (defined('GLOBAL_TAGS_REGISTERED')) {
            CTrace::inf('Bypass tags registering (use precompiled)');
            return;
        }

        $file = new CFile($this->appIni['PATH_TAGS_FILE']);
        $tags = $file->getLines();
        if(!is_array($tags)) return;

        foreach ($tags as $tag)
        {
            $this->registerTag($tag);
        }
    }

    function registerTag($tag)
    {
        if (! function_exists($tag)) {
            eval($this->getTagFunction($tag));
        }

        if (! function_exists('get'.$tag)) {
            eval($this->getTagGetFunction($tag));
        }

        if (! function_exists('getVal'.$tag)) {
            eval($this->getTagGetValFunction($tag));
        }
    }

    function getTagFunction($tag)
    {
        return "
function $tag() { \$args = func_get_args(); return __info_tag_output('$tag', \$args); }";
    }

    function getTagGetFunction($tag)
    {
        return "
function get$tag()
{
    \$args = func_get_args();
    ob_start();
    __info_tag_output('$tag', \$args);
    return ob_get_clean();
}";
    }

    function getTagGetValFunction($tag)
    {
        return "
function getVal$tag()
{
    global \$__localization_disable_formatting__;
    \$__localization_disable_formatting__ = true;
    ob_start();
    \$args = func_get_args();
    __info_tag_output('$tag', \$args);
    \$ret = ob_get_clean();
    \$__localization_disable_formatting__ = false;
    return \$ret;
}";
    }

    /**
     * Registers all the tags specified in the paramaters.
     * The input array can be the regular or associative one (it is necessary for compatability).
     */
    function registerAttributes($arr,$viewname="")
    {

    	if($viewname != '')
    	{
    		$arr = apply_filters("registerAttributes",$arr,$viewname);
    	}

        if(!is_array($arr)) return;

        // suppose, that tags are keys in the array
        $tags = array_keys($arr);
        // if the first key is a number, then suppose, that tags are values in the array
        if (count($tags) > 0 && is_int($tags[0]))
        {
            $tags = array_values($arr);
        }

        foreach ($tags as $tag) {
            $tag = trim(str_replace('{', '', str_replace('}', '', $tag)));
            // skip the the invalid tag names
            if (!preg_match("/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/", $tag))
            {
                continue;
            }

            $this->registerTag($tag);
        }
    }

    /**
     * Saves the tag in the callstack.
     */
    function pushTag($tag)
    {
        array_push($this->tag_stack, $tag);
    }

    /**
     * Extracts the tag from the callstack.
     *
     * @return string tag name
     */
    function popTag()
    {
        return array_pop($this->tag_stack);
    }

    /**
     * Returns the last tag name from the stack.
     *
     * @return string the last tag name from the stack
     */
    function getLatestTag()
    {
        $cnt = sizeof($this->tag_stack);
        if ($cnt == 0) return null;
        return $this->tag_stack[$cnt - 1];
    }

    /**
     * Searches the tag occurrence in the stack.
     *
     * @return boolean TRUE - if the tag has been already called, FALSE otherwise.
     */
    function isTagInStack($tag)
    {
        return in_array($tag, $this->tag_stack);
    }

    /**
     * Sets the overriding of the block tag at the customer one.
     */
    function setBlockOverride($block, $custom_tag)
    {
        $this->block_override[$block] = $custom_tag;
    }

    /**
     * Resets the overriding of the block tag.
     */
    function resetBlockOverride($block)
    {
        unset($this->block_override[$block]);
    }

    /**
     * Checks the overriding of the block tag.
     */
    function getBlockOverride($block)
    {
        if (array_key_exists($block, $this->block_override))
        {
            return $this->block_override[$block];
        }
        return null;
    }

    function getBlockByAlias($alias)
    {
        global $application;
        $mm = &$application->getInstance('Modules_Manager');
        if (isset($mm->czAliasesList[$alias]))
        {
            return $mm->czAliasesList[$alias];
        }
        return $alias;
    }

    function areInstallerFilesNotRemoved()
    {
        return (@file_exists($this->getAppIni("INSTALLER_FILE_DAT")) ||
                @file_exists($this->getAppIni("INSTALLER_FILE_PHP")));
    }

    /**
     * Checks if the file recording with cached pages is available.
     *
     * @author Vadim Lyalikov
     * @
     * @return
     */
    function isCacheFolderNotWritable()
    {
        global $application;
        $dir_fs_name = $application->getAppIni("PATH_CACHE_DIR");
        return !is_dir_writable($dir_fs_name);
    }

    function checkCrossSiteScripting()
    {
	$get = array();
	foreach($_GET as $k=>$v)
	{
        if(is_array($v))
        {
            if(!empty($v))
            {
                foreach($v as $id=>$val)
                {
                    if(!empty($val) && is_string($val))
                        $v[$id] = htmlentities($val);
                }
            }
            $get[htmlentities($k)] = $v;
        }
        else
        {
            $get[htmlentities($k)] = htmlentities($v);
        }
	}
	$_GET = $get;

        $_SERVER['PHP_SELF'] = htmlentities($_SERVER['PHP_SELF']);
    }

    function checkCrossSiteRequest()
    {
        global $zone;
        if($zone == 'AdminZone')
        {
            if (sizeof($_POST))
            {
                if(!isset($_POST['__ASC_FORM_ID__']))
                {
                    $this->destroyCrossSiteRequest();
                }
                elseif($_POST['__ASC_FORM_ID__'] != modApiFunc('Session', 'get', '__ASC_FORM_ID__'))
                {
                    $this->destroyCrossSiteRequest();
                }
            }
        }
        if (!modApiFunc('Session', 'is_set', '__ASC_FORM_ID__'))
        {
            modApiFunc('Session', 'set', '__ASC_FORM_ID__', md5(uniqid().time()));
        }
    }

    function destroyCrossSiteRequest()
    {
        session_destroy();
        header("Location: index.php");
        exit();
    }

    function applyCustomSkin(&$info)
    {
        global $zone;
        if($zone == 'AdminZone') return;

        // checking if a new skin is set...
        if (isset($_GET['set_custom_skin']) && $_GET['set_custom_skin'] != ''
            && is_dir($info['SITE_PATH'] . 'avactis-themes/' .  $_GET['set_custom_skin']))
        {
            modApiFunc('Configuration', 'setValue',
                array(STOREFRONT_ACTIVE_SKIN => $_GET['set_custom_skin']));
            // helps to detect unexpected skin changes
            CTrace::wrn($_GET['set_custom_skin']);
            setcookie('current_skin', $_GET['set_custom_skin'], time() + 2592000, '/');
        if (!isset($_GET['returnURL'])) $_GET['returnURL'] = 'index.php';
            header('Location: ' . $_GET['returnURL']);
            exit;
        }

        // setting the current skin...
        $this -> currentSkin = 'metro';
        if (isset($_COOKIE['current_skin'])
            && is_dir($info['SITE_PATH'] . 'avactis-themes/' .  $_COOKIE['current_skin']))
            $this -> currentSkin = $_COOKIE['current_skin'];

        if ($this -> currentSkin != 'metro')
        {
            $info['TEMPLATE_DIRECTORY_NAME'] = 'avactis-themes/' .  $this -> currentSkin . '/';
            $info['PATH_TEMPLATES'] = $info['SITE_PATH'] .  $info['TEMPLATE_DIRECTORY_NAME'];
            $info['URL_TEMPLATES'] = $info['SITE_URL'] .  $info['TEMPLATE_DIRECTORY_NAME'];
            $info['PATH_USERS_RESOURCES'] = $info['PATH_TEMPLATES'] .  'resources/';
        }
    }

    var $currentSkin = '';

    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    /**
     * The pointer to Factory object
     */
    var $pFactory;

    /**
     * The array for storing the application settings.
     */
    var $appIni;

    /**
     * @var DB_MySQL
     */
    var $db;

    /**
     * @var Array stack for storing tag callings.
     */
    var $tag_stack = array();

    /**
     * @var Array                                                       layouts
     *                  layouts-config.ini
     */
    var $layout = array();

    var $block_config = array();

    var $block_override = array();


    /**
     * The array of fatal errors, connected with defining tags in the customer area.
     */
    var $TagFatalErrors;
    var $TagWarnings;

    var $Configs_array = array();

    /**
     * System messages.
     */
    var $MessageResources;

    var $processActionStarted = false;

    var $redirectURL = NULL;

    var $js_redirectURL = NULL;

    var $haveToExit = NULL;

    var $row_layout_ini_array = null;

    var $multilang_core = null;

    var $SectionByView = array();
    var $ViewBySection = array();

    var $fb_request = false;
    /**#@-*/
}
?>