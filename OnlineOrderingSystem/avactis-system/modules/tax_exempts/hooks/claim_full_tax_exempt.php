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
 *
 * @package TaxExempts
 * @author Vadim Lyalikov
 */
class ClaimFullTaxExemptHook
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * ClaimFullTaxExempt constructor
     */
    function ClaimFullTaxExemptHook()
    {
    }

    /**
     *
     */
    function onHook()
    {
        global $application;
        $request = $application->getInstance('Request');

        $full_tax_exempt_customer_input = $request->getValueByKey('full_tax_exempt_customer_input');
        if($full_tax_exempt_customer_input !== NULL)
        {
            //     FullTaxExempt                .
            $full_tax_exempt_status = $request->getValueByKey('full_tax_exempt_status') === NULL ? DB_FALSE : DB_TRUE;
            modApiFunc("TaxExempts", "setFullTaxExemptStatus", $full_tax_exempt_status);
            modApiFunc("TaxExempts", "setFullTaxExemptCustomerInput", $full_tax_exempt_customer_input);
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