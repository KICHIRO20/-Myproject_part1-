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
 * @copyright Copyright &copy; 2005, HBWSL.
 * @package Error
 * @author ag
 */
loadModuleFile('error/views/error_view.php');
/**
 * C_Error is a view of errors or warnings in the CZ.
 *
 * @package Error
 * @access  public
 */
class C_Error extends Abstract_Error_View
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * The view constructor.
     *
     * @ finish the functions on this page
     */
    function C_Error()
    {
        parent::Abstract_Error_View();
    }

    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    /**
     * Gets the template name of the view.
     *
     * @ finish the functions on this page
     * @return string template name
     */
    function getTemplate()
    {
        return 'Client View Template';
    }

    /**#@-*/

}
?>