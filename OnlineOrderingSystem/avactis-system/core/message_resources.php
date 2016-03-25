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
 * Controls the output process of text messages from the resource file.
 *
 * @package Core
 * @access  public
 * @author Alexey Kolesnikov
 */
class MessageResources
{

    /**
     * Constructs an object for the specified resource file.
     * Reads the resource file as the INI-file.
     *
     *               $filename -                            ,
     *                          .                 ,
     *                              .
     *
     * @param string $filename - the resource filename without extension
     * @param string $zone  is necessary to specify the resource file path
     * @access public
     */
	function MessageResources($filename = "", $zone = "CustomerZone", $shortname = null)
	{
	    global $application;
	    $this->filename = $filename;
	    $this->zone = $zone;

        if ($shortname == null)
        {
            $this->shortname = modApiFunc("Modules_Manager", "getShortNameByResFile", $filename);
            if ($this->shortname == null)
            {
                $this->shortname = 'SYS';
                if ($this->filename == '')
                    $this->filename = $application->getAppIni('PATH_ADMIN_RESOURCES').'system-messages-eng.ini';
            }
        }
        else
        {
            $this->shortname = $shortname;
            if ($this->shortname == 'CZ')
            {
                if ($this->filename == '')
                    $this->filename = getTemplateFileAbsolutePath('resources/messages.ini');
            }
        }


        $this->messages = modApiFunc("Resources", "getMessageGroupByPrefix", $this->shortname);

        if (($this->shortname == 'SYS')
            && isset($this->messages['COPYRIGHT_TEXT']))
        {
            $this->messages['COPYRIGHT_TEXT'] = sprintf($this->messages['COPYRIGHT_TEXT'], date("Y"));
        }
    }

    function escapeSymbols(&$str, $pos, $symb, &$newstr)
    {
        while ($str[$pos]!=$symb)
        {
            $newstr.= $str[$pos];
            $pos++;
        }
        $newstr.= $str[$pos];
        return $pos++;
    }

	/**
	 * Returns a message from the resource file.
	 * $key can be either an object of the class ActionMessage or a string
	 * associated with the key from the resource file. If it is a string, then $objs is used
	 * to generate the message. If it is an object, $objs is skipped.
         *
	 * @param mixed $key
	 * @param array $objs
	 * @return string
	 * @access public
	 */
	function getMessage($key, $objs = array())
	{
	    $result = "";
	    $msg = "";
	    $values = "";
	    // if it is an object, its type must be ActionMessage
		if (is_object($key))
		{
			if (_ml_strtolower(get_class($key)) == 'actionmessage')
			{
				$msg = $key->getMessage();
				$values = $key->getValues();
			}
		}
		else
		{
			$msg = $key;
			$values = $objs;
		}

        if ($msg == "" || $msg == null || !array_key_exists($msg, $this->messages))
		{
                return "???" . $this->shortname . ' - ' . $msg . "??!";
		}

        $value = $this->messages[$msg];
        return $this->format($this->messages[$msg], $values);
	}

	/**
	 *                                                                       .
	 * @param string|object $key
	 * @return bool
	 */
	function hasMessage($key)
	{
        $msg = '';
        if (is_object($key)) {
            if (_ml_strtolower(get_class($key)) == 'actionmessage') {
                $msg = $key->getMessage();
            }
        }
        else {
            $msg = $key;
        }

        return array_key_exists($msg, $this->messages);
	}

    /**
     *
     */
    function isDefined($key)
    {
        if (_ml_substr($this->getMessage($key), 0, 3) != RESOURCE_NOT_DEFINED)
            return true;
        return false;
    }

	/**
	 * Formates a specified message with defined values.
         *
	 * @param string $msg resource file message
	 * @param array $values value array to format
	 * @return string
	 * @access private
	 */
	function format($msg, $values)
	{
	    // had to do the additional encoding in base64
	    $values = base64_encode(serialize($values));
	    return preg_replace("/\{(\d)\}/e", "MessageResources::replaceValue('\\1', '$values')", $msg);
	}

	/**
	 * The internal function is used to replace values in the text message.
         *
         * @param integer $id the identifier of replacement value.
	 * @param array $values the value array.
	 * @return string
	 * @access private
	 */
	function replaceValue($id, $values)
	{
	    $values = unserialize(base64_decode($values));
		if ($id == null || $id == "" || !is_array($values) || !array_key_exists($id, $values))
		{
			return '';
		}
		return $values[$id];
	}


	/**
	 * loads resource strings from file
	 *
	 */
	function loadResFromFile($filename = "", $zone = "CustomerZone")
	{
        global $application;
        $lang = _ml_strtolower($application->getAppIni('LANGUAGE'));

        if (file_exists($filename) && $filename != "")
        {
            $full_filename = $filename;
            $this->file_messages = _parse_ini_file($full_filename);
        }
        else
        {
            if ($filename == "")
            {
                $path = $application->getAppIni('PATH_ADMIN_RESOURCES');
                $filename = 'system-messages-'.$lang.'.ini';
            }
            elseif($zone == "CustomerZone")
            {
                $filename .= '.ini';
                $path = getTemplateDirAbsolutePath('resources/');
            }
            else
            {
                $path = $application->getAppIni('PATH_ADMIN_RESOURCES');
                $filename = $filename.'-'.$lang.'.ini';
            }

            $full_filename = $path . $filename;
            if (@file_exists($full_filename))
            {
                $this->file_messages = _parse_ini_file($full_filename);
            }
            else
            {
                $this->file_messages = _parse_ini_file($path . "system-messages-eng.ini");
            }
        }
	}

    function getMessageFromResFile($key, $objs = array(), &$value)
    {
        $result = "";
        $msg = "";
        $values = "";
        // if it is an object, its type must be ActionMessage
        if (is_object($key))
        {
            if (_ml_strtolower(get_class($key)) == 'actionmessage')
            {
                $msg = $key->getMessage();
                $values = $key->getValues();
            }
        }
        else
        {
            $msg = $key;
            $values = $objs;
        }

        if ($msg == "" || $msg == null || !array_key_exists($msg, $this->file_messages))
        {
          return false;//"???" . $msg . "???";
        }

        $value = $this->file_messages[$msg];
        return $this->format($this->file_messages[$msg], $values);
    }
	/**
	 * The array of resource file messages.
         *
	 * @access private
	 */
	var $messages = array();
	var $file_messages = array();

	/**
	 * short name for the module who's resourcs we want
	 * SYS if undefined
	 *
	 * @var char[]
	 */
	var $shortname;

	var $filename;
	var $zone;
    var $meta;
}
?>