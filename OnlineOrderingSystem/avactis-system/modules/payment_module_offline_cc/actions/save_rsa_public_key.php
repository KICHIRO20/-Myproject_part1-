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
 * Payment Module.
 * This action is responsible for update OfflineCC RSA public key settings.
 *
 * @package PaymentModuleOffline CC
 * @access  public
 * @author Vadim Lyalikov
 *
 * Save a new value of the system (one in the system) RSA key
 * to the setting table of the "Offline Credit Cards" payment module.
 */
class save_rsa_public_key
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
    function save_rsa_public_key()
    {
        global $application;
        $this->MessageResources = &$application->getInstance('MessageResources',"payment-module-offline-messages", "AdminZone");
    }

    /**
     *
     */
    function onAction()
    {
        global $application;
        $request = $application->getInstance('Request');

        $SessionPost = array();
        if(modApiFunc('Session', 'is_Set', 'SessionPost'))
        {
            _fatal(array( "CODE" => "CORE_050"), __CLASS__, __FUNCTION__);
        }

        $SessionPost = $_POST;
        //Check
        $rsa_public_key_asc_format = $SessionPost["rsa_public_key_asc_format"];
        $rsa_public_key_cryptrsa_format = modApiFunc("Crypto", "convert_rsa_public_key_from_asc_into_cryptrsa_format", $rsa_public_key_asc_format);
        if($rsa_public_key_cryptrsa_format === false ||
           empty($rsa_public_key_asc_format))
        {
            //The key format is invalid.
            //: report error
            echo "<script language='javascript'>alert('ERROR: Incorrect RSA public key format.');</script>";
            exit();
        }
        else
        {
            modApiFunc("Payment_Module_Offline_CC", "updateRSAPublicKey", $rsa_public_key_asc_format);
            //Output a Javascript, hiding a group of controllers "Generate a pair of RSA key"
            //: Make other necessary changes in the interface.
            echo "<script language='javascript'>parent.".$SessionPost['callback_function']."();</script>";
            exit();
        }
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