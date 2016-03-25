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
 * Class AjaxAction is used to prepare response for AJAX request.
 *
 * @access public
 * @author Alexey Astafyev
 * @package Core
 */
class AjaxAction
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * AjaxAction class constructor.
     */
    function AjaxAction()
    {
    }

    function getStatus()
    {
        return $this->status;
    }

    function setStatusSuccess()
    {
        $this->status = STATUS_SUCCESS;
    }

    function setStatusError()
    {
        $this->status = STATUS_ERROR;
    }

    function getMessage()
    {
        return $this->message;
    }

    function setMessage($message='')
    {
        $this->message = str_replace("\n", "<br>", $this->prepareMessage($message));
    }

    function prepareMessage($message=null)
    {
        if(empty($message)) return '';  // null, '', array()
        if(is_array($message))
        {
            $text = '';
            foreach($message as $i=>$m)
            {
                if(is_string($i)) $text .= "$i:\n";
                $text .= $this->prepareMessage($m). "\n";
            }
            return $text;
        }
        if(is_string($message)) return $message;
        return '';
    }

    function generateResponse()
    {
        return array(
            'status' => $this->getStatus(),
            'message' => $this->getMessage(),
            'data' => array()
        );
    }

    /**#@-*/

    var $status = 'success';
    var $message = '';

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    /**#@-*/
}
?>