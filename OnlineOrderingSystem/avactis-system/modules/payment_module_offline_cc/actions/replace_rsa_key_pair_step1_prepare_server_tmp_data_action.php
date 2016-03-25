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
 *
 * Removes old temporary data (if it exists) from the server. The first step of
 * replacing a pair of RSA keys. It prepares new current data on the server.
 * The step of replacing RSA keys.
 *
 * @package PaymentModuleOffline CC
 * @access  public
 * @author Vadim Lyalikov
 */
class replace_rsa_key_pair_step1_prepare_server_tmp_data
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
    function replace_rsa_key_pair_step1_prepare_server_tmp_data()
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

        /**
         * Close the shop (?).
         * Delete the temporary table with the reencrypted data order_person_info,
         * if it exists. Create the same one, cloning order data.
         * Initialize and save required ancillary variables to the
         * Offline CC module settings.
         */


        modApiFunc("Payment_Module_Offline_CC", "ReplaceRSAKeyPairStep1PrepareServerTmpData");

        /**
         * Output javascript which indicates that the current step is performed
         * successfully and starts the jump on the next step.
         */

        echo "<script language='javascript'>parent.ReplaceRSAKeyPairStep1PrepareServerTmpDataOnSuccessHandler();</script>";
        exit();
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