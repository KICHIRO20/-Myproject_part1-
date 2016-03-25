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
 * Hint module.
 *
 * @package Hint
 * @author Alexander Girin
 * @access public
 */
class Hint
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Hint module constructor.
     */
    function Hint()
    {
        global $application;
        $this->MessageResources = &$application->getInstance('MessageResources');
    }

    /**
     * Restores the module state from session.
     */
    function loadState()
    {
        if(modApiFunc('Session', 'is_Set', 'hintField'))
        {
            $this->field = modApiFunc('Session', 'get', 'hintField');
        }
        else
        {
            $this->field = "";
        }
        if(modApiFunc('Session', 'is_Set', 'hintEntity'))
        {
            $this->entity = modApiFunc('Session', 'get', 'hintEntity');
        }
        else
        {
            $this->entity = "";
        }
    }

    /**
     * Saves the module state.
     */
    function saveState()
    {
        modApiFunc('Session', 'set', 'hintField', $this->field);
        modApiFunc('Session', 'set', 'hintEntity', $this->entity);
    }

    /**
     * Returns the meta description of database tables.
     *
     * @return array tables meta info
     */
    function getTables ()
    {
    }

    /**
     * Installs the module.
     */
    function install()
    {
    }

    /**
     * Uninstalls the module.
     */
    function uninstall()
    {
    }

    /**
     *
     *
     * @
     * @param
     * @return
     */
    function setHintContent($field, $entity)
    {
        $this->field = $field;
        $this->entity = $entity;
    }

    /**
     *
     *
     * @param
     * @return
     */
    function getFieldName()
    {
        global $application;
        if ($this->entity)
        {
            $this->MessageResources = &$application->getInstance('MessageResources', '', "AdminZone", modApiFunc("Modules_Manager", 'getShortNameByResFile', $this->entity));
        }
        if ($this->MessageResources->isDefined($this->field) === false)
        {
            return $this->field;
        }
        return $this->MessageResources->getMessage($this->field);
    }

    /**
     *
     *
     * @param
     * @return
     */
    function getFieldDescription()
    {
        global $application;
        if ($this->entity)
        {
            $this->MessageResources = &$application->getInstance('MessageResources',$this->entity, "AdminZone", modApiFunc("Modules_Manager", 'getShortNameByResFile', $this->entity));
        }
        if ($this->MessageResources->isDefined($this->field."_DESCR") === false)
        {
            return $this->entity;
        }
        return $this->MessageResources->getMessage($this->field."_DESCR");
    }

    /**
     *
     *
     * @
     * @param
     * @return
     */

    function getHintLink($args)
    {
        global $application;

        $request = new Request();
        $request->setView  ('Hint');
        $request->setAction('SetHintContent');
        $request->setKey('Field', $args[0]);

        if (isset($args[1]))
        {
            $request->setKey('Entity', $args[1]);
            $this->MessageResources = &$application->getInstance('MessageResources', '', 'AdminZone', modApiFunc("Modules_Manager", "getShortNameByResFile", $args[1]));
        }

        $width = 'false';
        if (isset($args[2]))
        {
            $width = $args[2];
        }

        $height = 'false';
        if (isset($args[3]))
        {
            $height = $args[3];
        }
        $HintLink = $request->getURL();
        return "javascript: ShowHint('".$HintLink."',".$width.",".$height.");";
    }

    function getHintText($args)
    {
        global $application;
        if (isset($args[1]))
        {
            $this->MessageResources = &$application->getInstance('MessageResources', '', 'AdminZone', modApiFunc("Modules_Manager", "getShortNameByResFile", $args[1]));
			$this->setHintContent($args[0], $args[1]);
		}
        else
		{
			$this->setHintContent($args[0], "");
		}
		return '<a href="javascript:;"><i class="fa fa-question-circle popovers" data-container="body" data-placement="right" data-html="true" data-content="'.$this->getFieldDescription().'" data-original-title="'.$this->getFieldName().'"></i></a>';
    }

    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    var $field;
    var $entity;

    /**#@-*/

}
?>