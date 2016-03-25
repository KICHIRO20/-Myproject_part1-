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
 * Error module. It stores and outputs error and warning messages.
 *
 * @package Error
 * @access  private
 */
class Error
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Error constructor.
     *
     * @ finish the functions on this page
     */
    function Error()
    {
        global $application;

        $this->ActionHandlersList = array();
        $this->ViewsList = array();
        $this->Errors = array();
    }

    function install ()
    {
    }

    /**
     * @ describe the function Error->uninstall.
     */
    function uninstall()
    {

    }
    /**
     * Adds a view to the class module.
     *
     * @ finish the functions on this page
     * @param string $Classname Name of view class
     */
    function addView($Classname)
    {
        array_push($this->ViewsList, $Classname);
    }

    /**
     * Adds the occurred error to the error array $this->Errors.
     *
     * @ finish the functions on this page
     * @return
     * @param string $Code error code
     * @param array $vars the array of variables
     * @param string $type error type
     */
    function setErrorCode($code, $vars, $type)
    {
        $error = array(
                        'CODE' => $code,
                        'VARS' => $vars,
                        'TYPE' => $type
                        );
        array_push($this->Errors, $error);
        if ($type=='fatal')
        {
            global $application;
            $res = &$application->getInstance('Resource');
            $replacer = &$application->getInstance('Replacer');
            $HTMLCode = '';
            $text = $res->getRes($code);
            $HTMLCode.= $replacer->Replace($text, $vars);
            die($HTMLCode);
        }
    }

    /**
     * Gets the array of errors.
     *
     * @ finish the functions on this page
     * @return array array of errors
     */
    function getError()
    {
        return $this->Errors;
    }

    /**
     * Processes the current action.
     *
     * @ finish the functions on this page
     */
    function processAction()
    {
    }

    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    /**
     * The list of Error views.
     */
    var $ViewsList;

    /**
     * The list of action handlers is empty for the error module.
     */
    var $ActionHandlersList;

    /**
     * The array of errors.
     */
    var $Errors;
    /**#@-*/

}
?>