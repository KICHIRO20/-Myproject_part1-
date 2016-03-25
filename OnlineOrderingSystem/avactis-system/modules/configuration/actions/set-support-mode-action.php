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
 * Action handler on set support mode.
 *
 * @package Configuration
 * @access  public
 * @author Alex Girin
 */
class SetSupportMode extends AjaxAction
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
     */
    function SetSupportMode()
    {
    }

    /**
     *
     */
    function onAction()
    {
        global $application;
        $request = $application->getInstance('Request');
        $mode = $request->getValueByKey('mode');
        if (!$mode && modApiFunc('Session','is_Set','SupportMode'))
        {
            modApiFunc('Session','un_Set','SupportMode');
        }
        else
        {
            $_mode = explode("|", $mode);
            $mode = modApiFunc('Session','is_Set','SupportMode')? modApiFunc('Session','get','SupportMode'):ASC_S_DISABLE;
            foreach ($_mode as $flag)
            {
                if (Validator::isValidInt($flag))
                {
                    $mode |= $flag;
                }
                elseif (defined($flag))
                {
                    $mode |= constant($flag);
                }
            }

            if ($mode == ASC_S_DISABLE)
            {
                if (modApiFunc('Session','is_Set','SupportMode'))
                {
                    modApiFunc('Session','un_Set','SupportMode');
                }
            }
            else
            {
                modApiFunc('Session','set','SupportMode', $mode);
            }
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