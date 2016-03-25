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
 * Views separate message, which is used by resource file.
 * Each message is specified with the required key (key) and with the several
 * parameters (value),that should not be more than 10 .The key is a message
 * id in the resource file.
 *
 * @package Core
 * @access  public
 * @author Alexey Kolesnikov
 */
class ActionMessage
{


    /**
     * Creates the ActionMessage object for specified parameters.
     *
     * @param mixed $msg array('key', 'param0', 'param1', ...) or string('key')
     * @access public
     */
	function ActionMessage($msg)
	{
		if (is_array($msg))
		{
			$this->message = $msg[0];
			for ($i = 1; $i < count($msg); $i++)
			{
			    $this->values[] = $msg[$i];
			}
		}
		else
		{
			$this->message = $msg;
		}
	}


    /**
	 * Returns the key value.
	 */
	function getMessage()
	{
		return $this->message;
	}


    /**
	 * Returns message parameters.
	 */
	function getValues()
	{
		return $this->values;
	}

	/**
	 * The message key for this message.
	 * @access private
	 */
	var $message;

	/**
	 * The replacement values for this message.
	 * @access private
	 */
	var $values = array();
}
?>