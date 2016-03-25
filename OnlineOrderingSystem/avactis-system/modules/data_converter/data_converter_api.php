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
 * @package DataConverter
 * @author Egor V. Derevyankin
 *
 */

class Data_Converter
{
    function Data_Converter()
    {
        $this->__readScriptsDefinition();
        $this->_errors=array();
        $this->_warnings=array();
        $this->_messages=array();
        $this->_process_info=array();
    }

    function install()
    {
    }

    function uninstall()
    {
    }

    function initDataConvert($worker_settings)
    {
        global $application;
        $worker = &$application->getInstance('Convert_Worker');
        $worker->init($worker_settings);

        $this->_errors=array_merge($this->_errors,$worker->getErrors());
        $this->_warnings=array_merge($this->_warnings,$worker->getWarnings());
        $this->_messages=array_merge($this->_messages,$worker->getMessages());
        $this->_process_info=array_merge($this->_process_info,$worker->getProcessInfo());
    }

    function doDataConvert()
    {
        global $application;
        $worker = &$application->getInstance('Convert_Worker');
        $worker->run();

        $this->_errors=array_merge($this->_errors,$worker->getErrors());
        $this->_warnings=array_merge($this->_warnings,$worker->getWarnings());
        $this->_messages=array_merge($this->_messages,$worker->getMessages());
        $this->_process_info=array_merge($this->_process_info,$worker->getProcessInfo());
    }

    function getErrors()
    {
        return $this->_errors;
    }

    function getWarnings()
    {
        return $this->_warnings;
    }

    function getMessages()
    {
        return $this->_messages;
    }

    function getProcessInfo()
    {
        return $this->_process_info;
    }

    function isDataConvertComplete()
    {
        //                .
    }

    function getScriptDefinition($script_code)
    {
        return $this->_scripts_definition[$script_code];
    }

    function __readScriptsDefinition()
    {
        loadCoreFile('obj_xml.php');
        $parser = new xml_doc(file_get_contents(DC_SCRIPTS_DEFINITION_FILE));
        $parser->parse();

        foreach($parser->xml_index as $tag)
        {
            if($tag->name=='SCRIPTS')
            {
                foreach($tag->children as $scripts_child)
                {
                    if($scripts_child->name=='SCRIPT')
                    {
                        $script_info = array('steps'=>array());
                        $script_code = '';
                        foreach($scripts_child->children as $script_child)
                        {
                            switch($script_child->name)
                            {
                                case 'CODE':
                                        $script_code = $script_child->contents;
                                        break;
                                case 'NAME':
                                        $script_info[_ml_strtolower($script_child->name)]=$script_child->contents;
                                        break;
                                case 'STEPS':
                                        $script_info['steps_count']=$script_child->contents;
                                        break;
                                case 'STEP':
                                        $step_info = array();
                                        $step_number = 0;
                                        foreach($script_child->children as $step_child)
                                        {
                                            switch($step_child->name)
                                            {
                                                case 'NUMBER':
                                                        $step_number=$step_child->contents;
                                                        break;
                                                case 'READER':
                                                case 'FILTER':
                                                case 'WRITER':
                                                        $step_info[_ml_strtolower($step_child->name)]=$step_child->contents;
                                                        break;
                                            };
                                        }
                                        if($step_number>0)
                                            $script_info['steps'][$step_number]=$step_info;
                                        break;
                            };
                        };
                        if($script_code!='')
                            $this->_scripts_definition[$script_code]=$script_info;
                    };
                };
            };
        };
    }

    var $_scripts_definition;
    var $_settings;
    var $_errors;
    var $_warnings;
    var $_messages;
    var $_process_info;
}

?>