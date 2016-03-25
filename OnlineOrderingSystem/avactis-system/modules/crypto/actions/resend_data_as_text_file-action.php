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
 * Checkout module.
 * Action handler on DecryptRsaBlowfish.
 *
 * @package Crypto
 * @access  public
 * @author Vadim Lyalikov
 */
class resend_data_as_text_file extends AjaxAction
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Action constructor.
     *
     * @ finish the functions on this page
     */
    function resend_data_as_text_file()
    {
    }

    /**
     * @ describe the function
     */
    function onAction()
    {
        global $application;
        $request = $application->getInstance('Request');

        /**
         * The file name and its contents come in the request. Output the file
         * with the required name and contents.
         */
        $file_name = $request->getValueByKey('file_name');
        $file_content = $request->getValueByKey('file_content');
        if(!empty($file_name))
        {
            _send_data_as_file_in_one_chunk($file_name, $file_content);
            exit();
        }
        else
        {
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

    /**#@-*/
}

?>