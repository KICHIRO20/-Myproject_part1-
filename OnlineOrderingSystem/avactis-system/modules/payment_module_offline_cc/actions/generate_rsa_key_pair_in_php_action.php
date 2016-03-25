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
 * Generate OfflineCC RSA key pair.
 *
 * @package PaymentModuleOffline CC
 * @access  public
 * @author Vadim Lyalikov
 *
 * Generate the RSA key using PHP and send a javascript code,
 * which will set values to the required fields in HTML document.
 */
class generate_rsa_key_pair_in_php
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
    function generate_rsa_key_pair_in_php()
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

        $rsa_obj = new Crypt_RSA();
        //Perhaps it is necessary to add one more check that mathimatical library is available for sure.
        $key_pair = new Crypt_RSA_KeyPair(1024); //1024-bit keys
        $rsa_public_key_asc_format  = "n:".bin2hex($key_pair->_public_key->_modulus).";e:" .bin2hex($key_pair->_public_key->_exp).";";
        $rsa_private_key_asc_format = "n:".bin2hex($key_pair->_private_key->_modulus).";d:".bin2hex($key_pair->_private_key->_exp).";";

        /**
         * Send data back to the customer. First you have to save a Private key and then
         * if it is exactly known that it is saved successfully, save a Public key.
         */
$js = "".
"function setValue(element_id, value, b_parent)\n".
"{\n".
"    //default value\n".
"    b_parent = typeof(b_parent) != 'undefined' ? b_parent : false;\n".
"    if(b_parent == true)\n".
"    {\n".
"        el = parent.document.getElementById(element_id);\n".
"    } \n".
"    else\n".
"    {\n".
"        el =        document.getElementById(element_id);\n".
"    }\n".
"\n".
"    //text\n".
"    //td(?)\n".
"    //select-one\n".
"    //alert(el.type);\n".
"    if(el.type == 'text')\n".
"    {\n".
"        el.value = value;\n".
"         //<input type='text'>\n".
"    }\n".
"    else if(el.type == 'hidden')\n".
"    {\n".
"        //alert('hidden element!');\n".
"        el.value = value;\n".
"         //<input type='hidden'>\n".
"    }\n".
"    else if(el.type == 'select-one')\n".
"    {\n".
"        el.selectedIndex = value;\n".
"        if(el.onchange)\n".
"        {\n".
"            el.onchange();\n".
"        }\n".
"    }\n".
"    else\n".
"    {\n".
"        //undefined type: TD (?)\n".
"       el.innerHTML = value;\n".
"    }\n".
"\n".
"}";

        $output = '<script language="javascript">';
        $output.= $js;
        $output .= "setValue('".$_POST["rsa_public_key_field_id"]."', '".$rsa_public_key_asc_format."', true);";
        $output .= "setValue('".$_POST["rsa_private_key_field_id"]."', '".$rsa_private_key_asc_format."', true);";
        $output .= "parent.".$_POST["callback_function"]."();";
        $output .= '</script>';
        echo $output;
        exit;
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