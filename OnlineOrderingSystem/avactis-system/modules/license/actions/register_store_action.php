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
 * This action is responsible for store registration
 *
 * @package License
 * @access  public
 * @author  Andrei Zhuravlev
 */
class RegisterStore extends AjaxAction
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
    function RegisterStore()
    {
    }

    /**
     *
     */
    function onAction()
    {
        global $application;

        // check post data

        loadCoreFile('licensekey.php');
        loadCoreFile('licenseaccount.php');

        // sending the registration request
        $lk = new LicenseKey();
        $lac = new LicenseAccountClient();

        $response = $lac->requestRegisterLicense(array("LicenseKey"=>$lk->getLicenseKey(), "LicenseDomain"=>$lk->getLicenseURL(), "LicenseType"=>PRODUCT_VERSION_INTERNAL_TYPE));
        $result_msg = "UNKNOWN_ERROR";

        if ($response['CODE'] == 0 && !empty($response['CERT'])) //success
        {
            loadCoreFile('licensecert.php');

            $lc = new LicenseCertificate();
            if ($lc->_save_cert_to_file($response['CERT']) === false)
            {
                // error saving certificate
                $result_msg = "CERT_ERROR_CANNOT_SAVE";
            }
            else if ($lc->certificate_status === CERTIFICATE_OK)
            {
                // certificate saved successfully
                $result_msg = "REGISTRATION_SUCCESS";

            }
            else
            {
                // invalid certificate received
                $result_msg = "CERT_ERROR_INVALID";
            }
        }
        else if (!empty($response['CODE']))
        {
            // error handling
            $result_msg = "RESPONSE_".$response['CODE'];
        }

        //$request = $application->getInstance('Request');
        modApiFunc('Session','set','ResultMessage',$result_msg);

        $this->redirect();
    }

    /**
     * Redirects after action.
     */
    function redirect()
    {
        global $application;

        $request = new Request();
        $request->setView('LicenseInfo');
        $application->redirect($request);
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