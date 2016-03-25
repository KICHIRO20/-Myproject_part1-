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
 * Configuration module.
 *
 * @package Configuration
 * @author Alexey Florinsky
 */
class Settings
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */


    function Settings()
    {

    }

    function createGroup($group_info)
    {
//        $group_info = array('GROUP_NAME'         => 'name',
//                            'GROUP_DESCRIPTION'  => array( 'NAME' => array('msn', 'rn'),
//                                                           'DESCRIPTION' => array('msn', 'rn'),
//                            'GROUP_VISIBILITY'   => 'show' // 'hide'
//                                                    ),
//        );

        if (!$this->__isValidGroupStructure($group_info))
        {
            return false; //
        }

        if ($this->isGroupExist($group_info['GROUP_NAME']))
        {
            return false; //
        }

        if ($group_info['GROUP_VISIBILITY'] == "SHOW" || $group_info['GROUP_VISIBILITY'] == "YES")
        {
        	$group_visibility = 1;
        }
        else
        {
        	$group_visibility = 0;
        }

        $description_id = $this->__addDescription($group_info['GROUP_DESCRIPTION']);
        if ($description_id === false)
        {
            return false; //
        }

        $params = array( 'group_name'           => $group_info['GROUP_NAME'],
                         'group_description_id' => $description_id,
                         'group_visibility'     => $group_visibility
        );

        return execQuery('INSERT_SETTINGS_GROUP', $params);
    }

    function createParam($param_info)
    {
//        $params = array( 'GROUP_NAME'        => 'group_name',
//                         'PARAM_NAME'        => 'param_name',
//                         'PARAM_DESCRIPTION' => array( 'NAME'        => array('msn', 'rn'),
//                                                       'DESCRIPTION' => array('msn', 'rn') ),
//                         'PARAM_TYPE'          => PARAM_TYPE_FLOAT,
//                         'PARAM_VALIDATOR'     => array('CLASS'=>'', 'METHOD'=>''),
//                         'PARAM_CURRENT_VALUE' => 'param_current_value',
//                         'PARAM_DEFAULT_VALUE' => 'param_default_value',
//                         'PARAM_VALUE_LIST'    => array( array(  'VALUE' => 'value',
//                                                                 'VALUE_DESCRIPTION' => array( 'NAME'        => array('msn', 'rn'),
//                                                                                               'DESCRIPTION' => array('msn', 'rn') ),
//                                                               ),
//                                                         ...
//                                                       )
//        );

        if (!isset($param_info['PARAM_VALIDATOR']))
        {
            $param_info['PARAM_VALIDATOR'] = array('CLASS'=>'Validator', 'METHOD'=>'alwaysValid');
        }

        if (!$this->__isValidParamStructure($param_info))
        {
            return false; //
        }

        if (!$this->isGroupExist($param_info['GROUP_NAME']))
        {
            return false; //
        }

        if ($this->isParamExist($param_info['GROUP_NAME'], $param_info['PARAM_NAME']))
        {
            return false; //
        }

        $description_id = $this->__addDescription($param_info['PARAM_DESCRIPTION']);
        if ($description_id === false)
        {
            return false; //
        }

        $params = array( 'group_name'               => $param_info['GROUP_NAME'],
                         'param_name'               => $param_info['PARAM_NAME'],
                         'param_description_id'     => $description_id,
                         'param_type'               => $param_info['PARAM_TYPE'],
                         'param_validator_class'    => $param_info['PARAM_VALIDATOR']['CLASS'],
                         'param_validator_method'   => $param_info['PARAM_VALIDATOR']['METHOD'],
                         'param_current_value'      => $param_info['PARAM_CURRENT_VALUE'],
                         'param_default_value'      => $param_info['PARAM_DEFAULT_VALUE'],
        );
        if (execQuery('INSERT_SETTING', $params) == false )
        {
            return false; //
        }

        if ($param_info['PARAM_TYPE'] == PARAM_TYPE_LIST)
        {
            foreach ($param_info['PARAM_VALUE_LIST'] as $item)
            {
                $description_id = $this->__addDescription($item['VALUE_DESCRIPTION']);
                if ($description_id === false)
                {
                    return false; //
                }

                $params = array( 'param_name' => $param_info['PARAM_NAME'],
                                 'group_name' => $param_info['GROUP_NAME'],
                                 'param_list_value' => $item['VALUE'],
                                 'param_list_value_description_id' => $description_id );
                execQuery('INSERT_SETTINGS_LIST_VALUE', $params);
            }
        }

        return true;
    }

    function setParamValue($group_name, $param_name, $value)
    {
        if (!$this->isParamExist($group_name, $param_name))
        {
            return false; //
        }

        if ($this->__isValidParamValue($group_name, $param_name, $value))
        {
            $params = array('group_name' => $group_name,
                            'param_name' => $param_name,
                            'value' => $value);
            return execQuery('UPDATE_SETTINGS_PARAM_VALUE', $params);
        }
        else
        {
            return false;
        }
    }

    function fetchAllParams()
    {
        if (! isset($this->all_params)) {
            $data = execQuery('SELECT_ALL_SETTINGS_PARAMS_BASE_INFO', array());
            $this->all_params = array();
            foreach ($data as $rec) {
                $this->all_params[ $rec['group_name'] ][ $rec['param_name'] ] = $rec;
            }
        }
    }

    function getParamValue($group_name, $param_name)
    {
        $param_info = $this->getParamBaseInfo($group_name, $param_name);
        if ($param_info === false)
        {
            return false; //
        }
        return $param_info['PARAM_CURRENT_VALUE'];
    }

    function getParamFullInfo($group_name, $param_name)
    {
        $param_info = $this->getParamBaseInfo($group_name, $param_name);
        if ($param_info === false)
        {
            return false; //
        }

        if ($param_info['PARAM_TYPE'] == PARAM_TYPE_LIST)
        {
            $param_info['PARAM_VALUE_LIST'] = $this->getParamValueList($param_info['GROUP_NAME'], $param_info['PARAM_NAME'], $with_descr);
        }
        return $param_info;
    }

    function getParamBaseInfo($group_name, $param_name)
    {
        if (!$this->isParamExist($group_name, $param_name))
        {
            return false; //  exception needed
        }

        $param = $this->all_params[$group_name][$param_name];
        $param_info = array(
                             'GROUP_NAME'          => $param['group_name'],
                             'PARAM_NAME'          => $param['param_name'],
                             'PARAM_DESCRIPTION_ID'=> $param['param_description_id'],
                             'PARAM_TYPE'          => $param['param_type'],
                             'PARAM_VALIDATOR'     => array('CLASS'  => $param['param_validator_class'],
                                                            'METHOD' => $param['param_validator_method']),
                             'PARAM_CURRENT_VALUE' => $param['param_current_value'],
                             'PARAM_DEFAULT_VALUE' => $param['param_default_value'],
                           );
        return $param_info;
    }

    function getParamNameDescription($group_name, $param_name)
    {
        if (!$this->isParamExist($group_name, $param_name))
        {
            return false; //  exception needed
        }

        $descr = $this->getDescription($this->all_params[$group_name][$param_name]['param_description_id']);
        return $descr['NAME'];
    }

    function getParamValueList($group_name, $param_name, $with_descr = SETTINGS_WITHOUT_DESCRIPTION)
    {
        $list = execQuery('SELECT_SETTINGS_PARAM_VALUE_LIST', array('group_name' => $group_name, 'param_name' => $param_name));
        if (empty($list))
        {
            return false; //
        }
        else
        {
            $result = array();
            foreach ($list as $item)
            {
                if ($with_descr == SETTINGS_WITHOUT_DESCRIPTION)
                {
                    $result[] = $item['param_list_value'];
                }
                else
                {
                    $result[] = array( 'VALUE'             => $item['param_list_value'],
                                       'VALUE_DESCRIPTION' => $this->getDescription($item['param_list_value_description_id']));
                }
            }
        }
        return $result;
    }

    function getDescriptionStructure($id)
    {
        $result = execQuery('SELECT_SETTINGS_DESCRIPTION', array('description_id' => $id));
        if (empty($result))
        {
            return false; // this id does not exist
        }
        else
        {
            $description = array( 'NAME'        => array($result[0]['name_module_short_name'], $result[0]['name_resource_name']),
                                  'DESCRIPTION' => array($result[0]['description_module_short_name'], $result[0]['description_resource_name']) );
            return $description;
        }
    }

    function getDescription($id)
    {
        $description = $this->getDescriptionStructure($id);
        return array( 'NAME'        => getMsg($description['NAME'][0], $description['NAME'][1]),
                      'DESCRIPTION' => getMsg($description['DESCRIPTION'][0], $description['DESCRIPTION'][1]));
    }

    function isGroupExist($group_name)
    {
        $result = execQuery('SELECT_SETTINGS_GROUP_BASE_INFO', array('group_name'=>$group_name));
        if (empty($result))
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    function isParamExist($group_name, $param_name)
    {
        $this->fetchAllParams();

        return isset($this->all_params[$group_name][$param_name]);
    }

    function getGroupList($with_descr = SETTINGS_WITHOUT_DESCRIPTION)
    {
    	$result = execQuery('SELECT_SETTINGS_GROUP_LIST', array());
        if ($with_descr === SETTINGS_WITH_DESCRIPTION)
        {
            foreach ($result as $key=>$group)
            {
                $result[$key]['name'] = getMsg($group['name_module_short_name'], $group['name_resource_name']);
                $result[$key]['description'] = getMsg($group['description_module_short_name'], $group['description_resource_name']);

            }
            usort($result, array(&$this, '__sortGroupList'));
        }
        return $result;
    }

    function getGroupInfo($group_name, $with_descr = SETTINGS_WITHOUT_DESCRIPTION)
    {
    	$result = execQuery('SELECT_SETTINGS_GROUP_FULL_INFO', array('group_name'=>$group_name));
        $result = $result[0];
        if ($with_descr === SETTINGS_WITH_DESCRIPTION)
        {
            $result['name'] = getMsg($result['name_module_short_name'], $result['name_resource_name']);
            $result['description'] = getMsg($result['description_module_short_name'], $result['description_resource_name']);
        }
        return $result;
    }

    function getGroupNameDescription($group_name)
    {
        $result = execQuery('SELECT_SETTINGS_GROUP_FULL_INFO', array('group_name'=>$group_name));
        return getMsg($result[0]['name_module_short_name'], $result[0]['name_resource_name']);
    }

    function getParamListByGroup($group_name, $with_descr = SETTINGS_WITHOUT_DESCRIPTION)
    {
        $result = execQuery('SELECT_SETTINGS_PARAM_FULL_INFO_LIST_BY_GROUP', array('group_name'=>$group_name));
        if ($with_descr === SETTINGS_WITH_DESCRIPTION)
        {
            foreach ($result as $key=>$param)
            {
                $result[$key]['name'] = getMsg($param['name_module_short_name'], $param['name_resource_name']);
                $result[$key]['description'] = getMsg($param['description_module_short_name'], $param['description_resource_name']);
            }
            usort($result, array(&$this, '__sortParamList'));
        }
        return $result;
    }

    function getParamHTMLControl($group_name, $param_name)
    {
        loadCoreFile('html_form.php');
        $param_info = $this->getParamBaseInfo($group_name, $param_name);

        $control_value = $param_info['PARAM_CURRENT_VALUE'];
        $control_name = SETTIGS_POST_MAP_NAME.'['.$param_info['GROUP_NAME'].']['.$param_info['PARAM_NAME'].']';

        $html = '';
        switch ($param_info['PARAM_TYPE'])
        {
            case PARAM_TYPE_FLOAT:
            case PARAM_TYPE_INT:
            case PARAM_TYPE_STRING:
                $html = '<input class="form-control form-filter input-large" type="text" '. HtmlForm::genInputTextField(250, $control_name, 50, $control_value) .' />';
                break;

            case PARAM_TYPE_LIST:
                $param_values = modApiFunc('Settings','getParamValueList', $param_info['GROUP_NAME'], $param_info['PARAM_NAME'], SETTINGS_WITH_DESCRIPTION);
                $select_data =  array(
                                        'onChange' => '',
                                        'select_name' => $control_name,
                                        'selected_value' => $control_value,
                                        'values' => array( /*array( 'value' => '', 'contents' => '' )*/ )
                                     );
                foreach ($param_values as $value)
                {
                    $select_data['values'][] = array('value'=>$value['VALUE'], 'contents'=>$value['VALUE_DESCRIPTION']['NAME']);
                }
                $html = HtmlForm::genDropdownSingleChoice($select_data);
                break;
        }
        return $html;
    }


    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    function __sortGroupList($a, $b)
    {
        return strcmp($a['name'], $b['name']);
    }

    /**
     * Returns params in order as they were added to the settings
     * (not in alphabetical order)
     */
    function __sortParamList($a, $b)
    {
        return ($a['param_description_id'] < $b['param_description_id']) ? -1 : 1;
    }

    function __isValidGroupStructure($group_info)
    {
        if (!isset($group_info['GROUP_NAME']) or
            !isset($group_info['GROUP_DESCRIPTION']) or
            !$this->__isValidGroupName($group_info['GROUP_NAME']) or
            !$this->__isValidDescriptionStructure($group_info['GROUP_DESCRIPTION']))
        {
            return false;
        }

        return true;
    }

    function __isValidParamStructure($param_info)
    {
        if (!isset($param_info['GROUP_NAME']) or
            !isset($param_info['PARAM_NAME']) or
            !isset($param_info['PARAM_TYPE']) or
            !isset($param_info['PARAM_DESCRIPTION']) or
            !isset($param_info['PARAM_VALIDATOR']) or
            !isset($param_info['PARAM_CURRENT_VALUE']) or
            !isset($param_info['PARAM_DEFAULT_VALUE']) )
        {
            return false;
        }

        if (
                $param_info['PARAM_TYPE'] == PARAM_TYPE_LIST
                and
                (
                    !isset($param_info['PARAM_VALUE_LIST']) or
                    !is_array($param_info['PARAM_VALUE_LIST']) or
                    empty($param_info['PARAM_VALUE_LIST'])
                )
           )
        {
            return false;
        }

        if (!$this->__isValidGroupName($param_info['GROUP_NAME']) or
            !$this->__isValidParamName($param_info['PARAM_NAME']) or
            !$this->__isValidParamType($param_info['PARAM_TYPE']) or
            !$this->__isValidDescriptionStructure($param_info['PARAM_DESCRIPTION']) or
            !$this->__isValidValidator($param_info['PARAM_VALIDATOR'])
           )
        {
            return false;
        }

        if ($param_info['PARAM_TYPE'] == PARAM_TYPE_LIST)
        {
            $value_list = array();
            foreach ($param_info['PARAM_VALUE_LIST'] as $item)
            {
                if (!$this->__isValidParamValueListStructure($item))
                {
                    return false;
                }
                $value_list[] = $item['VALUE'];
            }

            if (!in_array($param_info['PARAM_CURRENT_VALUE'], $value_list) or
                !in_array($param_info['PARAM_DEFAULT_VALUE'], $value_list))
            {
                return false;
            }
        }

        return true;
    }

    function __isValidParamValueListStructure($value)
    {
        if (!isset($value['VALUE']) or
            !isset($value['VALUE_DESCRIPTION']) or
            !$this->__isValidDescriptionStructure($value['VALUE_DESCRIPTION']))
        {
            return false;
        }

        return true;
    }

    function __isValidDescriptionStructure($description_info)
    {
        if (is_array($description_info) and
            isset($description_info['NAME']) and
            isset($description_info['DESCRIPTION']) and
            is_array($description_info['NAME']) and
            is_array($description_info['DESCRIPTION']) and
            isset($description_info['NAME'][0]) and
            isset($description_info['NAME'][1]) and
            isset($description_info['DESCRIPTION'][0]) and
            isset($description_info['DESCRIPTION'][1]) and
            $this->__isValidModuleShortName($description_info['NAME'][0]) and
            $this->__isValidModuleShortName($description_info['DESCRIPTION'][0]) and
            $this->__isValidResourceName($description_info['NAME'][1]) and
            $this->__isValidResourceName($description_info['DESCRIPTION'][1])  )
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    function __isValidModuleShortName($msn)
    {
        return true;
    }

    function __isValidResourceName($rn)
    {
        return true;
    }

    function __isValidGroupName($group_name)
    {
        return true;
    }

    function __isValidParamName($param_name)
    {
        return true;
    }

    function __isValidParamType($param_type)
    {
        return true;
    }

    function __isValidValidator($validator)
    {
        if (!is_array($validator) or
            !isset($validator['CLASS'])  or
            !isset($validator['METHOD']) )
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    function __isValidParamValue($group_name, $param_name, $value)
    {
        loadCoreFile('validator.php');
        $param_info = $this->getParamBaseInfo($group_name, $param_name);
        $result = false;
        switch ($param_info['PARAM_TYPE'])
        {
            case PARAM_TYPE_FLOAT:
                $result = Validator::isValidFloat($value);
                break;

            case PARAM_TYPE_INT:
                $result = Validator::isValidInt($value);
                break;

            case PARAM_TYPE_LIST:
                $result = in_array($value, $this->getParamValueList($param_info['GROUP_NAME'], $param_info['PARAM_NAME']));
                break;

            case PARAM_TYPE_STRING:
                $result = true;
                break;
        }
        if ($result == true)
        {
            $validator_class  = $param_info['PARAM_VALIDATOR']['CLASS'];
            $validator_method = $param_info['PARAM_VALIDATOR']['METHOD'];
            if (!class_exists($validator_class))
            {
                loadClass($validator_class);
                if (!class_exists($validator_class) or !method_exists($validator_class, $validator_method))
                {
                    _fatal(__CLASS__.'::'.__FUNCTION__.":<br>Method <i>$validator_method</i> or class <i>$validator_class</i> does not exist!");
                }
            }
            return call_user_func(array($validator_class, $validator_method), $value);
        }
        else
        {
            return false;
        }
    }

    function __addDescription($description_info)
    {
        $params = array( 'name_module_short_name'           => $description_info['NAME'][0],
                         'name_resource_name'               => $description_info['NAME'][1],
                         'description_module_short_name'    => $description_info['DESCRIPTION'][0],
                         'description_resource_name'        => $description_info['DESCRIPTION'][1],
        );

        if (execQuery('INSERT_SETTINGS_DESCRIPTION', $params) === true)
        {
            global $application;
            return $application->db->DB_Insert_Id();
        }
        else
        {
            return false; //
        }
    }

    var $all_params;

    /**#@-*/

}

?>