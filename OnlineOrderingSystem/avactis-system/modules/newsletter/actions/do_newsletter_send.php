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
 *                                   compose.
 *                                   .
 * @package Newsletter
 * @author Egor Makarov
 */
class do_newsletter_send extends AjaxAction
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * do_newsletter_send constructor
     */
    function do_newsletter_send()
    {
    }

    /**
     *
     */
    function onAction()
    {
        global $application;
        global $_RESULT;

    	$request = &$application->getInstance('Request');
        $subtask_id = $request->getValueByKey('subtask_id');

        switch ($subtask_id)
        {
            case 'init':
                $output = $this->mailingInit();
                $_RESULT['warnings'] = $output['Warnings'];
                $_RESULT['errors'] = $output['Errors'];
                $_RESULT['total_count'] = $output['TotalCount'];
                $_RESULT['available_customers_array'] = $output['EmailsList'];
                break;
            case 'init2':
                $letter_id = $request->getValueByKey('letter_id');
                $output = $this->mailingInit3($letter_id);
                $_RESULT['warnings'] = $output['Warnings'];
                $_RESULT['errors'] = $output['Errors'];
                $_RESULT['total_count'] = $output['TotalCount'];
                $_RESULT['num'] = $output['Num'];
                break;
            case 'do':
                $num = $request->getValueByKey('num');
                $output = modApiFunc('Newsletter', 'sendMessagesPortion3', $num);
                $_RESULT['warnings'] = $output['warnings'];
                $_RESULT['errors'] = $output['errors'];
                $_RESULT['sent_total'] = $output['sent_total'];
                $_RESULT['sending_status'] = $output['sending_status'];
                break;
            default:
                //
                //          -         !
                //
                break;
        }
    }

    function mailingInit()
    {
        global $application;

    	$request = &$application->getInstance('Request');

        $letter_id = $request->getValueByKey('letter_id');

        $output = modApiFunc('Newsletter', 'prepareSendMessage', $letter_id);
        return $output;
    }

    function mailingInit2($NewSelectedModules)
    {
        global $application;

        $request = &$application->getInstance('Request');

        $letter_id = $request->getValueByKey('letter_id');

        $output = modApiFunc('Newsletter', 'prepareSendMessage2', $letter_id, $NewSelectedModules);
        return $output;
    }

    function mailingInit3($letter_id)
    {
        global $application;
        $output = modApiFunc('Newsletter', 'prepareSendMessage3', $letter_id);
        return $output;
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