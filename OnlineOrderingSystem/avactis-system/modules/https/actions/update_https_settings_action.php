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
 * HTTPS Settings module.
 * This action is responsible for https settings update.
 *
 * @package HTTPS Settings
 * @access  public
 * @author  Alexander Girin
 */
class UpdateHTTPSSettings extends AjaxAction
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Action constructor.
     */
    function UpdateHTTPSSettings()
    {
    }

    /**
     *
     */
    function onAction()
    {
        global $application;

        $URL_correct = false;

        $request = &$application->getInstance('Request');
        $HTTPSURL = $request->getValueByKey('HTTPSURL');

        $SessionPost = array(
                             "URLS" => array(
                                             "HTTPS_URL" => $HTTPSURL
                                            )
                            ,"SECURE_SECTIONS" => array(
                                                        "AllAdminArea"        => $request->getValueByKey('All')? "true":""
                                                       ,"SignIn_AdminMembers" => $request->getValueByKey('SignIn_AdminMembers')? "true":""
                                                       ,"Orders_Customers"    => $request->getValueByKey('Orders_Customers')? "true":""
                                                       ,"Payment_Shipping"    => $request->getValueByKey('Payment_Shipping')? "true":""
                                                       )
                            ,"Message" => ""
                            ,"FirstTimeSettings" => $request->getValueByKey('FirstTimeSettings')
                            );
        if ($HTTPSURL)
        {
            $HTTPSURL = "https://".$HTTPSURL;

            $parsedURL = @parse_url($HTTPSURL);
            if (isset($parsedURL["host"]))
            {
                if (isset($parsedURL["path"]))
                {
                    $pos = _ml_strpos($parsedURL["path"], "/avactis-system");
                     if (!($pos === false))
                    {
                        $parsedURL["path"] = _ml_substr($parsedURL["path"], 0, $pos+1);
                    }
                }

                $HTTPSURL = $parsedURL["host"];
                $HTTPSURL.= (isset($parsedURL["port"]) && $parsedURL["port"] != ""? ":".$parsedURL["port"]:"");
                $HTTPSURL.= (isset($parsedURL["path"]) && $parsedURL["path"] != ""? $parsedURL["path"]:"/");
                $HTTPSURL.= $HTTPSURL[_byte_strlen($HTTPSURL)-1] != "/"? "/":"";
                $SessionPost["URLS"]["HTTPS_URL"] = "https://".$HTTPSURL;

                $URL_correct = $this->sendRequest("https://".$HTTPSURL);
/*
                if (!$URL_correct)
                {
                    $HTTPSURL = $parsedURL["host"];
                    $HTTPSURL.= (isset($parsedURL["port"]) && $parsedURL["port"] != ""? ":".$parsedURL["port"]:"");
                    $HTTPSURL.= "/".@parse_url($application->getAppIni('HTTP_URL'), PHP_URL_PATH);
                    $HTTPSURL.= $HTTPSURL[_byte_strlen($HTTPSURL)-1] != "/"? "/":"";
                    $URL_correct = $this->sendRequest("https://".$HTTPSURL);
                }
*/
                if ($URL_correct || $request->getValueByKey('SSLAvailable') == "false")
                {
                    $SessionPost["URLS"]["HTTPS_URL"] = "https://".$HTTPSURL;
                    #https_config.php file content
                    $file_cotent = ";<?php  exit(); >\n\n";
                    $file_cotent.= "[URLS]\n";
                    $file_cotent.= "HTTPS_URL = \"https://".$HTTPSURL."\"\n";
                    #if ($SessionPost["SECURE_SECTIONS"]["AllAdminArea"] == "true")
                    #{
                    #    $file_cotent.= "HTTP_URL = \"https://".$HTTPSURL."\"\n";
                    #}
                    $file_cotent.= "\n[SECURE_SECTIONS]\n";
                    foreach ($SessionPost["SECURE_SECTIONS"] as $key => $val)
                    {
                        $file_cotent.= $key." = ".$val."\n";
                    }

                    $file_name = $application->getAppIni("PATH_CONF_DIR")."https_config.php";
                    $fp = @fopen($file_name, "w");
                    if ($fp)
                    {
                        @fwrite($fp, $file_cotent);
                        @fclose($fp);
                        if (!file_exists($file_name))
                        {
                            #Can't create file $file_name
                            $SessionPost["Message"] = "HTTPS_WRN_005";
                        }
                    }
                    else
                    {
                        #Can't write to the folder 'avactis-system'
                        $SessionPost["Message"] = "HTTPS_WRN_004";
                    }
                }
                else
                {
                    //Could't connect
                    $SessionPost["Message"] = "HTTPS_WRN_003";
                }
            }
            else
            {
                //Wrong URL syntax
                $SessionPost["Message"] = "HTTPS_WRN_002";
            }
        }
        else
        {
            if ($SessionPost["FirstTimeSettings"] == "true")
            {
                //URL - isn't entered
                $SessionPost["Message"] = "HTTPS_WRN_001";
            }
            elseif ($SessionPost["FirstTimeSettings"] == "false")
            {
                $SessionPost["Message"] = "HTTPS_WRN_006";
                $SessionPost["FirstTimeSettings"] = "";
            }
            else
            {
                $file_name = $application->getAppIni("PATH_CONF_DIR")."https_config.php";
                @unlink($file_name);
                if (file_exists($file_name))
                {
                    #Can't remove file $file_name
                    $SessionPost["Message"] = "HTTPS_WRN_007";
                }
            }
        }

        if ($SessionPost["Message"])
        {
            modApiFunc('Session', 'set', 'SessionPost', $SessionPost);
        }
        else
        {
           // $SessionPost["hasCloseScript"] = "true";
            modApiFunc('Session', 'set', 'SessionPost', $SessionPost);
        }

        if ($URL_correct)
        {
            //                    CZ         :
            $layouts_from_bd = modApiFunc("Configuration", "getLayoutSettings");
            foreach($layouts_from_bd as $fname => $info)
            {
            	$info =& $layouts_from_bd[$fname];

            	//               ,                  ,               :
            	$k = 'layout_'.$info['id'].'_res';
            	$res = $request->getValueByKey($k);
            	if($res === NULL)
            	{
            		$res = array();
            	}
            	else
            	{
            		$res = explode('|', $res);
            	}

            	//                 Configuration        :
            	$sections = array_unique(array_values(modApiFunc("Configuration", "getLayoutSettingNameByCZLayoutSectionNameMap")));
            	foreach($sections as $section)
            	{
            		if(in_array($section, $res))
            		{
            			$info[$section] = DB_TRUE;
            		}
            		else
            		{
                        $info[$section] = DB_FALSE;
            		}
            	}
            	unset($info);
            }
            modApiFunc("Session","set","ResultMessage",'HTTPS_SETTINGS_SAVED');
            modApiFunc("Configuration", "setLayoutSettings", $layouts_from_bd);
            $request = new Request();
        $request->setView('HTTPSSettings');
        $application->redirect($request);
        }
    }

    function sendRequest($HTTPSURL)
    {
        loadCoreFile('bouncer.php');
        $bnc = new Bouncer();
        $bnc->setMethod('GET');
        $bnc->setGETstring($bnc->prepareDATAstring(array("request" => "is_connection_available")));
        $bnc->setURL($HTTPSURL."avactis-system/admin/test_connection.php");
        $result = $bnc->RunRequest();
        $URL_correct = false;
        if (is_array($result) && isset($result["body"]) && strstr($result["body"], "YES"))
        {
            $URL_correct = true;
        }

        return $URL_correct;
    }

    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    /**
     * Corresponding View file.
     */
    var $ViewFilename;

    /**#@-*/
}
?>