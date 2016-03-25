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
class HTTPS
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Localization  constructor.
     */
    function HTTPS()
    {
    }

    /**
     *
     * @param
     * @return
     */
    function install()
    {

    }

    /**
     *
     * @param
     * @return
     */
    function uninstall()
    {

    }

    /**
     *
     *
     * @param
     * @return
     */
    function getHTTPSSettings()
    {
        global $application;

        $settings = array(
                          "URLS"            => array(
                                                     "HTTPS_URL"       => ""
                                                    )
                         ,"SECURE_SECTIONS" => array(
                                                     "AllAdminArea"        => false
                                                    ,"SignIn_AdminMembers" => false
                                                    ,"Orders_Customers"    => false
                                                    ,"Payment_Shipping"    => false
                                                    )
                         );

        $https_config_file = $application->getAppIni('PATH_CONF_DIR').'https_config.php';
        if (file_exists($https_config_file))
        {
            $_settings = @_parse_ini_file($https_config_file, true);
            if (is_array($_settings))
            {
                $settings = $_settings;
            }
        }
        return $settings;
    }

    function tryToFindHttpsUrl($URL = "")
    {
        global $application;
        if (!$URL)
        {
            $URL = $application->getAppIni('HTTP_URL');
        }

        $url_parts = @parse_url($URL);

        $test_connection_url = 'https://'.$url_parts["host"].$url_parts["path"]."avactis-system/admin/test_connection.php";
        $_get = array("request" => "is_connection_available");
        $test_request = $this->__makeTestHttpsRequest($_get, $test_connection_url);

        if ($test_request === 'SSL_not_available')
        {
            return "SSL_not_available";
        }
        if ($test_request === false)
        {
            return '';
        }
        else
        {
            return $url_parts["host"].$url_parts["path"];
        }
    }

    function __makeTestHttpsRequest($get_array, $url)
    {
        loadCoreFile('bouncer.php');
        $bnc = new Bouncer();
        if (!$bnc->isSSLavailable())
        {
            return "SSL_not_available";
        }
        $bnc->setMethod('GET');
        $bnc->setGETstring($bnc->prepareDATAstring($get_array));
        $bnc->setURL($url);
        $result = $bnc->RunRequest();

        if (is_array($result) && isset($result["body"]) && strstr($result["body"], "YES"))
        {
            return true;
        }
        else
        {
            return false;
        }
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