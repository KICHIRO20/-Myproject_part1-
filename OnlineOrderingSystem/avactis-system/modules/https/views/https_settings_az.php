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
 * HTTPS Settings module meta info.
 *
 * @package HTTPS Settings
 * @author Alexander Girin
 */
class HTTPSSettings
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * HTTPSSettings constructor.
     */
    function HTTPSSettings()
    {
        global $application;
        $this->MessageResources = &$application->getInstance('MessageResources');

        $this->SSLAvailable = true;
    }

    function outputCZLayoutHTTPSSettings()
    {
        global $application;
    	//1.                 Layout-   CZ    FS.
    	//2.                    Layout-   CZ        .
    	//3.        2                                  1,
    	//                     .
    	//4.               2.                          .
    	$layouts_from_fs = LayoutConfigurationManager::static_get_cz_layouts_list();
    	$layouts_from_bd = modApiFunc("Configuration", "getLayoutSettings");
    	foreach($layouts_from_fs as $fname => $info)
    	{
    		if(!array_key_exists($fname, $layouts_from_bd))
    		{
                execQuery('INSERT_LAYOUT_HTTPS_SETTINGS', array('layout_full_file_name' => $fname));
    		}
    	}
        $layouts_from_bd = modApiFunc("Configuration", "getLayoutSettings");

        if(sizeof($layouts_from_bd) >0)
        {
        	$CZHTTPSLayouts = "";
	        foreach($layouts_from_bd as $fname => $info)
	        {
                $config = LayoutConfigurationManager::static_parse_layout_config_file($fname);
                if(!empty($config))
                {
	                $this->_Template_Contents['CZHTTPSLayoutId'] = $info['id'];

		        	//CZHTTPSLayouts
		        	//CZHTTPSLayoutSections
		        	$map = modApiFunc("Configuration", "getLayoutSettingNameByCZLayoutSectionNameMap");
		        	$CZHTTPSLayoutSections = "";
		        	$checked_sections = array();
		        	foreach($info as $key => $value)
		        	{
		        		if(in_array($key, $map))
		        		{
		        			//
	                        $this->_Template_Contents['_hinttext'] = gethinttext('HTTPS_FIELD_'. $key);
	                        $this->_Template_Contents['CZHTTPSSectionName'] = getMsg('CFG','HTTPS_KEY_NAME_'.$key);
	                        $this->_Template_Contents['CZHTTPSSectionKey'] = $key;
	                        $this->_Template_Contents['CZHTTPSSectionValue'] = ($value == DB_TRUE) ? " CHECKED " : "";
	                        if($value == DB_TRUE)
	                        {
	                            $checked_sections[] = $key;
	                        }
	                        $application->registerAttributes($this->_Template_Contents);
	                        $CZHTTPSLayoutSections .= modApiFunc('TmplFiller', 'fill', "configuration/cz_https/","section.tpl.html", array());
		        		}
		        	}
	                $this->_Template_Contents['CZHTTPSLayoutFileName'] = $fname;
	                $this->_Template_Contents['CZHTTPSLayoutURL'] = $config['SITE_URL'];
	                $this->_Template_Contents['CZHTTPSLayoutId'] = $info['id'];
	                $this->_Template_Contents['CZHTTPSLayoutSections'] = $CZHTTPSLayoutSections;
	                $this->_Template_Contents['CZHTTPSLayoutCheckedSections'] = implode('|', $checked_sections);

	                $application->registerAttributes($this->_Template_Contents);
		        	$CZHTTPSLayouts .= modApiFunc('TmplFiller', 'fill', "configuration/cz_https/","item.tpl.html", array());
                }
	        }
            $this->_Template_Contents['CZHTTPSLayouts'] = $CZHTTPSLayouts;
            $application->registerAttributes($this->_Template_Contents);
            return  modApiFunc('TmplFiller', 'fill', "configuration/cz_https/","container.tpl.html", array());
        }
        else
        {
        	//              : layout                    .
        	return  modApiFunc('TmplFiller', 'fill', "configuration/cz_https/","container-empty.tpl.html", array());
        }
    }

    function checkPerms()
    {
        global $application;

        $file_name = $application->getAppIni("PATH_SYSTEM_DIR")."https_config.php";

        return ( (file_exists($file_name) && is_writable($file_name) || (!file_exists($file_name) && is_writable(dirname($file_name))) ) ? "" : "HTTPS_WRN_012");
    }

    function outputResultMessage()
     {
         global $application;
		if(modApiFunc("Session","is_set","ResultMessage"))
		{
			$msg=modApiFunc("Session","get","ResultMessage");

			modApiFunc("Session","un_set","ResultMessage");
			$template_contents=array("ResultMessage" => getMsg('SYS', $msg));
			$this->_Template_Contents=$template_contents;
			$application->registerAttributes($this->_Template_Contents);
			$this->mTmplFiller = &$application->getInstance('TmplFiller');
			$res = $this->mTmplFiller->fill("https/https_settings/", "result-message.tpl.html",array());
			return $res;
		}
		else
		{
			return "";
		}
     }


    /**
     *
     */
    function output()
    {
        global $application;

        $request = new Request();
        $request->setView  ('HTTPSSettings');
        $request->setAction('UpdateHTTPSSettings');
        $formAction = $request->getURL();

        loadCoreFile('bouncer.php');
        $bnc = new Bouncer();
        if (!$bnc->isSSLavailable())
        {
            $this->SSLAvailable = false;
        }

        if (modApiFunc("Session", "is_set", "SessionPost"))
        {
            $HTTPSSettings = modApiFunc("Session", "get", "SessionPost");
            modApiFunc("Session", "un_set", "SessionPost");
            if(isset($HTTPSSettings["hasCloseScript"]) && $HTTPSSettings["hasCloseScript"] == "true")
            {
                modApiFunc("application", "closeChild_UpdateTop");
                return;
            }
            if ($HTTPSSettings["Message"] != "")
            {
                $HTTPSSettings["Message"] = $this->MessageResources->getMessage($HTTPSSettings["Message"]);
            }
        }
        else
        {
            $HTTPSSettings = modApiFunc("HTTPS", "getHTTPSSettings");
            $HTTPSSettings["Message"] = "";
            if ($HTTPSSettings["URLS"]["HTTPS_URL"] == "")
            {
                if (($URL = modApiFunc("HTTPS", "tryToFindHttpsUrl")) == "SSL_not_available")
                {
                     $this->SSLAvailable = false;
                }
                else
                {
                    $HTTPSSettings["URLS"]["HTTPS_URL"] = $URL;
                }
                $HTTPSSettings["FirstTimeSettings"] = "true";
            }
            else
            {
                $HTTPSSettings["FirstTimeSettings"] = "false";
            }
        }

        $HTTPSSettings['Message'] = $this->checkPerms();
        if ($HTTPSSettings["Message"] != "")
        {
            $HTTPSSettings["Message"] = $this->MessageResources->getMessage($HTTPSSettings["Message"]);
        }

        $template_contents = array(
                                   "FormAction"          => $formAction
                                  ,"HTTPSURL"            => strtr($HTTPSSettings["URLS"]["HTTPS_URL"], array("https://" => ""))
                                  ,"All"                 => $HTTPSSettings["SECURE_SECTIONS"]["AllAdminArea"]? "checked":""
                                  ,"SignIn_AdminMembers" => $HTTPSSettings["SECURE_SECTIONS"]["SignIn_AdminMembers"]? "checked":""
                                  ,"Orders_Customers"    => $HTTPSSettings["SECURE_SECTIONS"]["Orders_Customers"]? "checked":""
                                  ,"Payment_Shipping"    => $HTTPSSettings["SECURE_SECTIONS"]["Payment_Shipping"]? "checked":""
                                  ,"Message"             => $HTTPSSettings["Message"]
                                  ,"FirstTimeSettings"   => $HTTPSSettings["FirstTimeSettings"]
                                  ,"SaveButton"          => $this->SSLAvailable? "block":"none"
                                  ,"CheckButton"         => $this->SSLAvailable? "none":"block"
                                  ,"SSLAvailable"        => $this->SSLAvailable? "true":"false"
                                  ,"CZHTTPSSettings"     => $this->outputCZLayoutHTTPSSettings()
                                  ,"ResultMessageRow"    => $this->outputResultMessage()
                                  );
        $this->_Template_Contents = $template_contents;
        $application->registerAttributes($this->_Template_Contents);
        return modApiFunc('TmplFiller', 'fill', "https/https_settings/","container.tpl.html", array());
    }


    function getTag($tag)
    {
        global $application;
        $value = null;
        if (array_key_exists($tag, $this->_Template_Contents))
        {
            $value = $this->_Template_Contents[$tag];
        }
        return $value;
    }
    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */


    /**#@-*/

}
?>