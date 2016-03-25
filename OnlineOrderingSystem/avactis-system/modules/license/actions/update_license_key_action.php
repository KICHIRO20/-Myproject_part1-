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
 * This action is responsible for license key update.
 *
 * @package License
 * @access  public
 * @author  Alexander Girin
 */
class UpdateLicenseKey extends AjaxAction
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
    function UpdateLicenseKey()
    {
    }

    /**
     *
     */
    function onAction()
    {
        global $application;

        loadCoreFile('licensekey.php');

        $request = $application->getInstance('Request');
        $new_key = $request->getValueByKey("NewLicenseKey");




        $lk_class = new LicenseKey();

        $result = $lk_class->saveLicenseKey($request->getValueByKey("NewLicenseKey"));
        if ($result === LICENSE_KEY_FILE_OK) {
	        $result_msg = 'MSG_LICENSE_UPDATED';
        }
        else {
            $result_msg = 'MSG_'.$result;
        }


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
        $request->setView(CURRENT_REQUEST_URL);
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