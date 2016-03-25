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
 * LayoutConfigurationManager class.
 *                                template layout'                       .
 *
 *         ,                                                     .
 *
 * @package Core
 * @author  Vadim Lyalikov
 * @access  public
 */

class LayoutConfigurationManager
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Object constructor
     */
    function LayoutConfigurationManager()
    {
    }

    function add_https_settings($layout_array, $layout_file_path)
    {
    	//                       SiteHTTPSURL    [Site]
    	//          https                            1.8.2
        if(isset($layout_array['Site']))
        {
        	$new_vals = array();
        	foreach($layout_array['Site'] as $key => $value)
        	{
        		if(_ml_strcasecmp($key, 'SiteHTTPSURL') != 0)
        		{
        			$new_vals[$key] = $value;
        		}
        	}
        	$layout_array['Site'] = $new_vals;
        }
        //                       HTTPS = YES/NO
        //          https                            1.8.2
        foreach($layout_array as $layout_section_name => $vals)
        {
        	$new_vals = array();
        	foreach($vals as $key => $value)
            {
        		if(_ml_strcasecmp($key, 'HTTPS') != 0)
        		{
        			$new_vals[$key] = $value;
        		}
        	}
        	$layout_array[$layout_section_name] = $new_vals;
        }

		//                SITE_HTTPS_URL
		if(isset($layout_array['Site']) &&
		   isset($layout_array['Site']['SiteURL']))
		{
			$SiteHTTPSURL = modApiStaticFunc("Configuration", "getCZSiteHTTPSURL", $layout_array['Site']['SiteURL']);
			if($SiteHTTPSURL !== NULL)
			{
                $layout_array['Site']['SiteHTTPSURL'] = $SiteHTTPSURL;
			}
		}

		//                   HTTPS
		$cz_https_settings = modApiStaticFunc("Configuration", "getLayoutSettings");
		//Search for current layout path entry
		$target_layout_file_path = NULL;
		foreach($cz_https_settings as $fpath => $info)
		{
			if(file_path_cmp($fpath, $layout_file_path) === 0)
			{
				$target_layout_file_path = $fpath;
				 break;
			}
		}

		if($target_layout_file_path !== NULL)
		{
            $map = modApiStaticFunc("Configuration", "getLayoutSettingNameByCZLayoutSectionNameMap");
	        foreach($map as $layout_section_name => $settings_section_name)
	        {
	            if(isset($layout_array[$layout_section_name]))
	            {
	                $layout_array[$layout_section_name]['HTTPS'] = modApiStaticFunc("Configuration", "getCZHTTPS", $layout_section_name, $cz_https_settings[$target_layout_file_path]);
	            }
	        }
		}

		return $layout_array;
    }
    /**
     *            CZ layout config     .                                   .
     */
    function static_parse_layout_config_file($layout_file_path)
    {
    	global $zone;
        global $application;

        $app_root_path = $application->getAppIni("PATH_ASC_ROOT");

    	$config = array();
        $error = array();

        # Use $layout_file_path If it exists and readable
        if ( file_exists($layout_file_path) &&
             is_readable($layout_file_path) &&
             is_file($layout_file_path) )
        {
            $config['PATH_LAYOUTS_CONFIG_FILE'] = $layout_file_path;
            $config['PATH_LAYOUTS_CONFIG_FILE'] = strtr($config['PATH_LAYOUTS_CONFIG_FILE'],'\\','/');
            $isDefaultLayoutFileUsed = false;

            # Defining layout.ini directory location
            $config['DIR_LAYOUTS_CONFIG_FILE'] = dirname($config['PATH_LAYOUTS_CONFIG_FILE']).'/';
            $config['DIR_LAYOUTS_CONFIG_FILE'] = strtr($config['DIR_LAYOUTS_CONFIG_FILE'],'\\','/');
        }
        else
        {
            if($zone != 'AdminZone')
            {
                $error = array( "MAIN_ERROR_PARAMETERS" => array( "CODE" => "CORE_056") );
                return $error;
            }
        }

        if(isset($config['DIR_LAYOUTS_CONFIG_FILE']))
        {
            # Defining SITE_URL and SITE_PATH
            $layout_array = @_parse_cz_layout_ini_file( $config['PATH_LAYOUTS_CONFIG_FILE'], true );
            # convert all array keys to upper case
            $layout_array = array_change_key_case($layout_array, CASE_UPPER);
            foreach($layout_array as $layout_section => $layout_value)
            {
                $layout_array[$layout_section] = array_change_key_case($layout_value, CASE_UPPER);
            }

            # Read [Site] section from layout.ini
            if ( isset($layout_array['SITE']) )
            {
                if ( isset($layout_array['SITE']['SITEURL']) )
                {
                    # Defining SITE_URL
                    $config['SITE_URL'] = $layout_array['SITE']['SITEURL'];

                    # check if SITE_URL is empty or ./ then set to current
                    if ($config['SITE_URL']=='' || $config['SITE_URL']=='./')
                    {
                        $config['SITE_URL'] = "http://".$_SERVER['SERVER_NAME']._ml_substr($_SERVER['PHP_SELF'], 0, (_ml_strrpos($_SERVER['PHP_SELF'], '/')+1));
                    }
                }
                else
                {
                    $error = array( "MAIN_ERROR_PARAMETERS" => array( "CODE"      => "CORE_032",
                                   "FILE"      => $config['PATH_LAYOUTS_CONFIG_FILE'],
                                   "SECTION"   => 'Site',
                                   "DIRECTIVE" => "SiteURL"  ) );
                    return $error;
                }

                if ( isset($layout_array['SITE']['SITEPATH']) )
                {
                    # Defining SITE_PATH
                    $config['SITE_PATH'] = $layout_array['SITE']['SITEPATH'];
                    $config['SITE_PATH'] = strtr($config['SITE_PATH'],'\\','/');
                }
                elseif ( isset($application->appIni["PATH_ASC_ROOT"]) )
                {
                    # Defining SITE_PATH
                    $config['SITE_PATH'] = $application->appIni["PATH_ASC_ROOT"];
                    $config['SITE_PATH'] = strtr($config['SITE_PATH'],'\\','/');
                }
                else
                {
                    $error = array( "MAIN_ERROR_PARAMETERS" => array( "CODE"      => "CORE_033",
                                   "FILE"      => $config['PATH_LAYOUTS_CONFIG_FILE'],
                                   "SECTION"   => 'Site',
                                   "DIRECTIVE" => "SitePATH"  ) );
                    return $error;
                }

                if ( isset($layout_array['SITE']['SITEHTTPSURL']))
                {
                    $config['SITE_HTTPS_URL'] = $layout_array['SITE']['SITEHTTPSURL'];

                    if ( $config['SITE_HTTPS_URL'] == "" || $config['SITE_HTTPS_URL'] == "./" )
                    {
                        $config['SITE_HTTPS_URL'] = "https://".$_SERVER['SERVER_NAME']._ml_substr($_SERVER['PHP_SELF'], 0, (_ml_strrpos($_SERVER['PHP_SELF'], '/')+1));
                    }
                    $config['SITE_HTTPS_URL'].= $config['SITE_HTTPS_URL'][_byte_strlen($config['SITE_HTTPS_URL'])-1] != "/"? "/":"";
                }
                /*
                else
                {
                    $config['SITE_HTTPS_URL'] = $config['SITE_URL'];
                }
                */
            }
            else
            {
                $error = array( "MAIN_ERROR_PARAMETERS" => array( "CODE"      => "CORE_034",
                               "FILE"      => $config['PATH_LAYOUTS_CONFIG_FILE'],
                               "SECTION"   => 'Site'  ) );
                return $error;
            }

            # Check if SITE_PATH and SITE_URL have '/' in the line end
            $config['SITE_PATH'] = _ml_substr($config['SITE_PATH'],-1,1) <> '/' ? $config['SITE_PATH'].'/' : $config['SITE_PATH'];
            $config['SITE_URL']  = _ml_substr($config['SITE_URL'],-1,1)  <> '/' ? $config['SITE_URL'].'/'  : $config['SITE_URL'];

            # Check if SITE_PATH exists
            if (!file_exists($config['SITE_PATH']))
            {
                $error = array( "MAIN_ERROR_PARAMETERS" => array( "CODE"      => "CORE_038",
                               "FILE"      => $config['PATH_LAYOUTS_CONFIG_FILE'],
                               "SECTION"   => 'Site',
                               "DIRECTIVE" => "SitePath = ".$config['SITE_PATH']) );
                return $error;
            }

            # Define LAYOUT_TEMPLATE from layout.ini
            if ( isset($layout_array['TEMPLATES']) && isset($layout_array['TEMPLATES']['TEMPLATEDIRECTORY']) )
            {
                # Defining LAYOUT_TEMPLATE
                $config['TEMPLATE_DIRECTORY_NAME'] = $layout_array['TEMPLATES']['TEMPLATEDIRECTORY'];
                # Check if there is '/' at the end of TEMPLATE_DIRECTORY_NAME path
                $config['TEMPLATE_DIRECTORY_NAME'] = _ml_substr($config['TEMPLATE_DIRECTORY_NAME'],-1,1)  <> '/' ? $config['TEMPLATE_DIRECTORY_NAME'].'/'  : $config['TEMPLATE_DIRECTORY_NAME'];

                # Check template directory exists relative to SitePath directive
                if (file_exists($config['SITE_PATH'].$config['TEMPLATE_DIRECTORY_NAME']))
                {
                    # Path to current templates directory, based on layout.ini directive
                    $config['PATH_TEMPLATES']        = $config['SITE_PATH'].$config['TEMPLATE_DIRECTORY_NAME'];
                    $config['URL_TEMPLATES']         = $config['SITE_URL'].$config['TEMPLATE_DIRECTORY_NAME'];
                    $config['PATH_USERS_RESOURCES']  = $config['PATH_TEMPLATES'].'resources/';
                    if (isset($config['SITE_HTTPS_URL']))
                    {
                        $config['HTTPS_URL_TEMPLATES'] = $config['SITE_HTTPS_URL'].$config['TEMPLATE_DIRECTORY_NAME'];
                    }
                }
                else
                {
                    $error = array( "MAIN_ERROR_PARAMETERS" => array( "CODE"      => "CORE_035",
                                   "FILE"      => $config['PATH_LAYOUTS_CONFIG_FILE'],
                                   "SECTION"   => 'Templates',
                                   "DIRECTIVE" => "TemplateDirectory = ".$config['TEMPLATE_DIRECTORY_NAME'])
                           ,$config['TEMPLATE_DIRECTORY_NAME']
                           ,$config['SITE_PATH']);
                    return $error;
                }
            }
            else
            {
                # If TEMPLATEDIRECTORY directive is undefined then use system templates
                $config['TEMPLATE_DIRECTORY_NAME'] = 'avactis-themes/system';
                $config['PATH_TEMPLATES']        = $app_root_path.'/'.$config['TEMPLATE_DIRECTORY_NAME'].'/';
                $config['URL_TEMPLATES']         = $application->getAppIni('HTTP_URL').$config['TEMPLATE_DIRECTORY_NAME'].'/';
                $config['PATH_USERS_RESOURCES']  = $config['PATH_TEMPLATES'].'resources/';
                if (isset($config['SITE_HTTPS_URL']))
                {
                    $config['HTTPS_URL_TEMPLATES'] = $application->getAppIni('HTTPS_URL').$config['TEMPLATE_DIRECTORY_NAME'].'/';
                }
            }

            # Hard-coding system templates
            $config['SYSTEM_TEMPLATE_DIRECTORY_NAME'] = 'avactis-themes/system';
            $config['SYSTEM_PATH_TEMPLATES']        = str_replace('//', '/', $app_root_path.'/'.$config['SYSTEM_TEMPLATE_DIRECTORY_NAME'].'/');
            $config['SYSTEM_URL_TEMPLATES']         = $application->getAppIni('HTTP_URL').$config['SYSTEM_TEMPLATE_DIRECTORY_NAME'].'/';
            $config['SYSTEM_PATH_USERS_RESOURCES']  = $config['SYSTEM_PATH_TEMPLATES'].'resources/';
            if (isset($config['SITE_HTTPS_URL']))
            {
                $config['SYSTEM_HTTPS_URL_TEMPLATES'] = $application->getAppIni('HTTPS_URL').$config['SYSTEM_TEMPLATE_DIRECTORY_NAME'].'/';
            }
        }

        return $config;
    }

    function static_checkLayoutFile($layout_config = NULL, $SITE_PATH = NULL, $PATH_LAYOUTS_CONFIG_FILE = NULL)
    {
        global $application;

        if($layout_config === NULL)
        {
            $layout_config =  $application->getAppIni('PATH_LAYOUTS_CONFIG_FILE');
        }

        #check if the layouts-config.ini file exists
        if (! is_readable($layout_config))
        {
            $error = array( "MAIN_ERROR_PARAMETERS" => array(
                                "CODE"    => "CORE_001",
                                "FILE"    => $layout_config
                               ));
            return $error;
        }
        #check the ini file syntax
        else
        {
            if (sizeof($ini_errors = $application->isIniFileCorrect($layout_config))>0)
            {
                foreach ($ini_errors as $error)
                {
                    list($error_code, $error_line) = each($error);
                    $error = array( "MAIN_ERROR_PARAMETERS" => array(
                                        "CODE"    => $error_code,
                                        "FILE"    => $layout_config,
                                        "LINE"    => $error_line
                                        ));
                    return $error;
                }
            }
            else
            {
                $layout_array = @_parse_cz_layout_ini_file($layout_config, true);
                $application->Configs_array['Layouts'] = $layout_array;
                $application->cz_layout_config_ini_file = $layout_config;
            }
        }

        if (isset($application->Configs_array['Layouts']))
        {
            # If the declared sections: [Category], [ProductInfo], [Cart], [Checkout], [Closed] exist and are valid
            # If the Default variable and the template file matching it exist in every block
            $necessary_checks = array(
                                    "productlist"    => array("section" => false, "default" => false, "template_file" => false),
                                    "productinfo" => array("section" => false, "default" => false, "template_file" => false),
                                    "cart"        => array("section" => false, "default" => false, "template_file" => false),
                                    "closed"      => array("section" => false, "default" => false, "template_file" => false)
                                     );
            foreach ($application->Configs_array['Layouts'] as $section_name => $section_arr)
            {
                $section_name = _ml_strtolower($section_name);
                switch ($section_name)
                {
                    case "productlist":
                    case "productinfo":
                    case "cart":
                    case "closed":
                        $application->checkDefaultFileAndOther($section_name, $section_arr, $necessary_checks, $SITE_PATH, $PATH_LAYOUTS_CONFIG_FILE);
                        break;
                    default:

                          break;
                }
            }
            //Output the fatal errors, if errors were detected in the layouts-config.ini file
            foreach ($necessary_checks as $section => $valid)
            {
                //If one of the [Category], [ProductInfo], [Cart], [Checkout], [Closed] sections is declared invalid
                if (!$valid['section'])
                {
                    $error = array( "MAIN_ERROR_PARAMETERS" => array(
                                        "CODE"    => "CORE_002",
                                        "FILE"    => $layout_config,
                                        "SECTION" => $section
                                        ), $section);
                    return $error;
                }
                //If the 'default' directive is declared invalid in one of the [Category],
                //[ProductInfo],[Cart],[Checkout],[Closed] sections
                if (!$valid['default'])
                {
                    $error = array( "MAIN_ERROR_PARAMETERS" => array(
                                        "CODE"    => "CORE_003",
                                        "FILE"    => $layout_config,
                                        "SECTION" => $section,
                                        "DIRECTIVE" => "Default"
                                        ));
                    return $error;
                }
                //If the file, declared in the 'default' directive doesn't exist in one of the
                //[Category],[ProductInfo],[Cart],[Checkout],[Closed] sections
                if (!($valid['template_file']===true))
                {
                    $error = array( "MAIN_ERROR_PARAMETERS" => array(
                                        "CODE"    => "CORE_004",
                                        "FILE"    => $layout_config,
                                        "SECTION" => $section,
                                        "DIRECTIVE" => "Default = ".$valid['template_file']
                                        ), $valid['template_file']);
                    return $error;
                }
            }
        }
        return array();
    }

    /**
     *                                                              .
     *                                       (
     *                       )        ,                         .
     *                        /avactis-layouts
     */
    function static_get_cz_layouts_list()
    {
        global $application;
        $res = array();
        $avactis_layouts_path = $application->getAppIni("PATH_LAYOUTS_DIR");
        $dir = dir($avactis_layouts_path);
        while(false !== ($file = $dir->read()))
        {
            if($file != '.' && $file != '..' && pathinfo($file, PATHINFO_EXTENSION) == 'ini')
            {
                $fname = $avactis_layouts_path.'/'.$file;
                //: hack
                $fname = str_replace("//", "/", $fname);
                if(is_file($fname))
                {
                    $config_parsing_results = LayoutConfigurationManager::static_parse_layout_config_file($fname);
                    if(empty($config_parsing_results["MAIN_ERROR_PARAMETERS"]))
                    {
                        //      ,                       :
                        //:                                         ,
                        //                             .                                 .
                        $config_check_results = LayoutConfigurationManager::static_checkLayoutFile($fname, $config_parsing_results["SITE_PATH"], $config_parsing_results["PATH_LAYOUTS_CONFIG_FILE"]);
                        //                        -        -               .
                        if(empty($config_check_results["MAIN_ERROR_PARAMETERS"]))
                        {
                            $res[$fname] = $config_parsing_results;
                        }
                    }
                }
            }
        }
        return $res;
    }

    /**
     * [                                             ].
     *                                           ,                              .
     *                                                             ,
     *                       static_get_cz_layouts_list().
     *
     *         .                    application                                    ,
     *                      .                                        :                    .
     */
    function static_activate_cz_layout($layout_config_ini_path)
    {
        global $application;
        $config_parsing_results = LayoutConfigurationManager::static_parse_layout_config_file($layout_config_ini_path);
        if(empty($config_parsing_results["MAIN_ERROR_PARAMETERS"]))
        {
            //      ,                       :
            //:                                         ,
            //                             .                                 .
            $config_check_results = LayoutConfigurationManager::static_checkLayoutFile($layout_config_ini_path, $config_parsing_results["SITE_PATH"], $config_parsing_results["PATH_LAYOUTS_CONFIG_FILE"]);
            //                        -        -               .
            if(empty($config_check_results["MAIN_ERROR_PARAMETERS"]))
            {
                $application->readLayoutsINI();
                //Merge parse results with appIni
                $application->appIni = array_merge($application->appIni, $config_parsing_results);
            }
        }
    }
    /**#@-*/
}


?>