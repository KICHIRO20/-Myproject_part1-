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
 * Replace the old encrypted data with the new one reencrypted with the new key.
 * The step of replacing RSA keys. (Rename the tables in the database)
 *
 * @package PaymentModuleOffline CC
 * @access  public
 * @author Vadim Lyalikov
 */
class replace_rsa_key_pair_step5_replace_old_encrypted_data_with_new_reencrypted_tmp_data
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
    function replace_rsa_key_pair_step5_replace_old_encrypted_data_with_new_reencrypted_tmp_data()
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

        $res = modApiFunc("Payment_Module_Offline_CC", "ReplaceRSAKeyPairStep5SwapCurrentDataWithTmpData");
        /**
         * : check if the action was performed successfully or not.
         */
            //Go to the next step.
        echo "<script language='javascript'>parent.ReplaceRSAKeyPairStep5SwapCurrentDataWithTmpDataOnSuccessHanlder();</script>";
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