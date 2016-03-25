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
 * License module.
 *
 * @package License
 * @author Alexander Girin
 */

define("STATE_1","LK_NA");
define('STATE_2','NOT_REG');
define('STATE_3','APP_REG');
define('STATE_4','LK_INVALID');
define('STATE_5','CERT_INVALID');

class License
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
    function License()
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
    function getLicenseInfo($state)
    {
        global $application;
        loadCoreFile('licensekey.php');
        loadCoreFile('licensecert.php');
        //loadCoreFile('licenseclient.class.php');

        $licenseKeyObj = new LicenseKey();
        $licenseKey = $licenseKeyObj->getLicenseKey();

        $licenseCertObj = new LicenseCertificate();

        $mr = &$application->getInstance('MessageResources');

        if ($state !== "LK_NA" || $state !== "APP_REG")
            $link = '<a href="license_info.php">License Info</a>';
        else
            $link = "";

        $license_info = array(
                         "license_key"                      => $licenseKey
                        ,"license_message"                  => $this->storeStatusMessage($state)//$mr->getMessage('STORE_STATE_'.$state).$link
                        ,"license_message_home"             => str_replace('{LIP_LINK}',$link,$mr->getMessage('STORE_STATE_HOME_'.$state))
                        ,"license_url"                      => $licenseCertObj->getLicenseURL()
                        ,"current_url"                      => $licenseKeyObj->getLicenseURL()
                        ,"current_version_type"             => PRODUCT_VERSION_TYPE
                        ,"license_registration_date"        => "NA"
                        );

        return $license_info;

/*        if ($licenseKey == LICENSE_KEY_UNDEFINED)
        {
            return $license_info;
        }
        else
        {
            $licenseClient = new LicenseClient();
            $licenseInfo = $licenseClient->requestCheckLicense($licenseKey, $licenseKeyObj->getLicenseURL());

            if($licenseInfo === false)
            {
                return null;
            };

            if (is_array($licenseInfo))
            {
                return array(
                             "license_key"          => $licenseKey
                            ,"license_status"       => ($licenseInfo["LK_STATUS"] == "INVALID"? "0":"1")
                            ,"license_url"          => ($licenseInfo["LK_STATUS"] == "INVALID"? "NA":$licenseKeyObj->getLicenseURL())
//                            ,"current_url"          => $licenseKeyObj->getLicenseURL()
                            ,"version_type"         => ($licenseInfo["LK_PRODUCT_TYPE"] == "INVALID"? "NA":$licenseInfo["LK_PRODUCT_TYPE"])
                            ,"current_version_type" => PRODUCT_VERSION_TYPE
                            ,"license_update"       => ($licenseInfo["LK_UPDATE_DATE"] == "INVALID"? "NA":$licenseInfo["LK_UPDATE_DATE"])
                            ,"license_support"      => ($licenseInfo["LK_SUPPORT_DATE"] == "INVALID"? "NA":$licenseInfo["LK_SUPPORT_DATE"])
                            ,"message"              => ""
                            );
            }
            else
            {
                $license_info["message"] = "LICENSE_WARNING_001";
                return $license_info;
            }
        }*/

//        }
    }


    function checkLicense()
    {
        global $application;
        loadCoreFile('licensekey.php');
        loadCoreFile('licensecert.php');

        $lk = new LicenseKey();
        $lc = new LicenseCertificate();

        if ($lk->license_key_status !== LICENSE_KEY_FILE_OK)
        {
            // LICENCE KEY FILE INVALID OR NOT ACCESSIBLE
            return STATE_1;
        }
        else
        {
            // LICENSE KEY FILE OK
            if ($lk->license_key === LICENSE_KEY_UNDEFINED || $lk->license_key_status === LICENSE_KEY_FORMAT_INVALID)
            {
                // LICENSE KEY NOT SET OR HAS INVALID FORMAT
                return STATE_4;
            }
            else if ($lc->certificate_status === CERTIFICATE_NOT_EXISTS)
            {
                return STATE_2;
            }
            else if ($lc->certificate_status === CERTIFICATE_INVALID)
            {
                // INVALID CERTIFICATE OR CERTIFICATE FILE
                return STATE_5;
            }
            else if ($lc->certificate_status === CERTIFICATE_OK)
            {
                // VALID CERTIFICATE
                return STATE_3;
            }
        }
    }

    function getCertificateData()
    {
        $_state = $this->checkLicense();

        if ($_state === STATE_3)
        {
            loadCoreFile('licensecert.php');

            $lc = new LicenseCertificate();

            if ($lc->certificate_status === CERTIFICATE_OK && !empty($lc->certificate) && is_array($lc->certificate))
                return $lc->certificate;
            else
                return false;
        }

        return false;
    }

    function storeStatusMessage($state)
    {
        $msg = getMsg('SYS','STORE_STATE_'.$state);

        switch ($state)
        {
            case "LK_NA":
                loadCoreFile('licensekey.php');
                $lk = new LicenseKey();
				$link = 'http://www.avactis.com/avactisnext-free-version/?src=storeadmin&utm_source=storeadmin&utm_medium=web&utm_content='.$_SERVER["HTTP_HOST"].'&utm_campaign=avactis_free_license';
                $msg = str_replace('{GET_LICENSE_URL}',$link,$msg);
            break;
            case "NOT_REG": break;
            case "APP_REG": break;
            case "LK_INVALID": break;
            case "CERT_INVALID": break;
        }

        return $msg;
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