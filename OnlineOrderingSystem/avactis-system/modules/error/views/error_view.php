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
 * Abstract_Error_View is an abstract class of outputting the view of errors
 * or warnings.
 *
 * @package Error
 * @access  private
 */
class Abstract_Error_View
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
    function Abstract_Error_View()
    {
        global $application;

        $this->pError = &$application->getInstance('Error');
        $this->pError->addView(get_class($this));
    }

    /**
     * Returns the HTML code of Error view.
     *
     * @ finish the functions on this page
     * @return string HTML code
     */
    function output()
    {
        global $application;
        $res = &$application->getInstance('Resource');
        $replacer = &$application->getInstance('Replacer');
        $HTMLCode = '';
        foreach ($this->pError->getError() as $val)
        {
            $text = $res->getRes($val['CODE']);
            $HTMLCode.= $replacer->Replace($text, $val['VARS']);
            //select the required view
            $HTMLCode.= ' Template is '.$this->getTemplate();
            switch ($val['TYPE'])
            {
                case 'error':break;
            }
        }
        return $HTMLCode;
    }

    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    /**
     * The abstract method, that gets the template name of the view.
     *
     * @ finish the functions on this page
     * @return string template name
     */
    function getTemplate()
    {

    }

    /**
     * Pointer to the module object.
     */
    var $pError;

    /**#@-*/

}
?>