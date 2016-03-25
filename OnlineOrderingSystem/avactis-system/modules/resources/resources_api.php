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
 * Resources module.
 *
 * @package Resources
 * @author Ravil Garafutdinov
 */
class Resources
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Resources constructor.
     */
    function Resources()
    {
        $this->_res_language = modApiFunc('MultiLang', 'getResourceLanguageNumber');
        $this->getSettings();
    }

    /**
     * Reads out module settings from the database.
     *
     * @return
     */
    function getSettings()
    {

    }


    function getTables()
    {
        static $tables;

        if (is_array($tables))
        {
            return $tables;
        }

        $tables = array();

      //=================================================
      // resource_labels
        $table = 'resource_labels';
        $tables[$table] = array();
        $tables[$table]['columns'] = array
            (
                'id'         => $table.'.id'
               ,'res_prefix' => $table.'.res_prefix'
               ,'res_label'  => $table.'.res_label'
               ,'res_text'   => $table.'.res_text'
            );
        $tables[$table]['types'] = array
            (
                'id'         => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'res_prefix' => DBQUERY_FIELD_TYPE_CHAR32
               ,'res_label'  => DBQUERY_FIELD_TYPE_CHAR255
               ,'res_text'   => DBQUERY_FIELD_TYPE_LONGTEXT
            );
        $tables[$table]['primary'] = array
            (
                'id'
            );
        $tables[$table]['indexes'] = array
            (
                  'UNIQUE KEY IDX_uprefix_key' => 'res_prefix, res_label'
                 ,'IDX_prefix_key' => 'res_prefix'
                 ,'IDX_label_key'  => 'res_label'
            );

      //=================================================
      // resource_meta
        $table = 'resource_meta';
        $tables[$table] = array();
        $tables[$table]['columns'] = array
            (
                'id'        => $table.'.id'
               ,'shortname' => $table.'.res_shortname'
               ,'filename'  => $table.'.res_filename'
               ,'module'    => $table.'.res_module'
               ,'flag'      => $table.'.res_flag'
               ,'md5'       => $table.'.res_md5'
            );
        $tables[$table]['types'] = array
            (
                'id'       => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'shortname' => DBQUERY_FIELD_TYPE_CHAR255
               ,'filename' => DBQUERY_FIELD_TYPE_CHAR255
               ,'module'   => DBQUERY_FIELD_TYPE_CHAR255
               ,'flag'     => DBQUERY_FIELD_TYPE_CHAR255
               ,'md5'      => DBQUERY_FIELD_TYPE_CHAR32
            );
        $tables[$table]['primary'] = array
            (
                'id'
            );
        $tables[$table]['indexes'] = array
            (
                'IDX_shortname' => 'shortname'
            );

        global $application;
        return $application->addTablePrefix($tables);
    }


    /**
     *                                     .
     *
     *       install()                      .
     *
     *                                          ,         ,
     * PromoCodes::getTables()        $this->getTables()
     */
    function install()
    {
        $tables = Resources::getTables();
        $query = new DB_Table_Create($tables);
    }


    /**
     * function returns DB lable prefix produced from module short name
     *
     * @param char[] $shortname
     * @return char[]
     */
    function makePrefixFromShortname($shortname)
    {
        return $shortname;
    }

    /**
     * returns meta info by id
     *
     * @param unknown_type $id
     * @return unknown
     */
    function getMetaById($id)
    {
        $rlt = execQuery('RESOURCES_GET_META_BY_ID', array('id' => intval($id)));
        if (isset($rlt[0]))
            return $rlt[0];
        return array();
    }

    /**
     * returns meta info by shortname
     *
     * @param unknown_type $shortname
     * @return unknown
     */
    function getMetaByShortNames($shortnames)
    {
        if (!is_array($shortnames))
            $shortnames = array($shortnames);
        $rlt = execQuery('RESOURCES_GET_META_BY_SHORTNAMES', array('shortnames' => $shortnames));
        if (!isset($rlt[0]))
            return array();

        $result = array();
        foreach($rlt as $v)
            $result[$v['shortname']] = $v['id'];

        return $result;
    }

    /**
     * returns meta info by resource filename
     *
     * @param unknown_type $filename
     * @return unknown
     */
    function getMetaByFilename($filename)
    {
        $rlt = execQuery('RESOURCES_GET_META_BY_FILENAME', array('filename' => basename($filename)));
        if (isset($rlt[0]))
            return $rlt[0];
        return array();
    }

    function updateResourceIniToDB($path, $shortname)
	{
        if (!file_exists($path))
			return false;

		global $application;
		$result = array();

        $data = _parse_ini_file($path);
		$result = execQuery('RESOURCES_GET_ALL_LABELS_BY_PREFIX',array('prefix' => $shortname));
		foreach($result as $key=>$value)
		{
			unset($data[$value['res_label']]);
		}

		if (!is_array($data) || empty($data))
			return false;

		$this->updateMd5ByMetaId($this->getMetaByShortNames($shortname), md5_file($path));
		return $this->addLabelsArrayToDB($data, $shortname);
	}


    function addResourceIniToDB($path, $shortname, $module_name, $flag)
    {
        if (!file_exists($path))
        {
            _fatal("Tried to upload not existing meta via path '{$path}', shortname '{$shortname}', module_name '{$module_name}', flag '{$flag}'!");
        }

        $data = _parse_ini_file($path);
        if (!is_array($data) || empty($data))
        {
            return false;
        }

        $meta_id = $this->addMetaToDB(
            array(
                 'filename' => basename($path)
                ,'shortname' => $shortname
                ,'module' => $module_name
                ,'flag' => $flag
                ,'md5' => md5_file($path)
            )
        );

    //============================
        // multiple insert, possible bugs
        return $this->addLabelsArrayToDB($data, $shortname);
    //============================

    //============================
        // insert in foreach, slow but always true
//        foreach ($data as $key => $value)
//        {
//            $this->addLabelToDB($key, $value, $prefix);
//        }
    //============================
    }


    function addLabelsArrayToDB($data, $prefix)
    {
        global $application;

        if (empty($data))
            return false;

        loadCoreFile('db_multiple_insert.php');
        $tables = Resources::getTables();
        $table = "resource_labels";
        $columns = $tables[$table]['columns'];
        $fields = array(
                $columns['res_prefix'],
                $columns['res_label'],
                $columns['res_text'],
            );

        $query = new DB_Multiple_Insert($table);
        $query->setInsertFields($fields);

        foreach ($data as $key => $value)
        {
            $params = array(
                $columns['res_prefix'] => $prefix,
                $columns['res_label'] => $key,
                $columns['res_text']  => $value,
            );
            $query->addInsertValuesArray($params);
        }

        $application->db->PrepareSQL($query);
        $application->db->DB_Exec();
        return true;
    }


    function addLabelToDB($key, $value, $prefix = '')
    {
        global $application;
        $tables = $this->getTables();

        // if $prefix is not specified
        // assuming it is a custom label
        if (!$prefix)
        {
            $data = execQuery('RESOURCES_SELECT_CUSTOM_META_DATA', array());
            $prefix = $this -> makePrefixFromShortname($data[0]['shortname']);
        }

        // checking if the record exists...
        $tmp = execQuery('RESOURCES_GET_MESSAGE_BY_KEY',
                         array('prefix' => $prefix, 'label' => $key, 'res_lng' => $this -> _res_language));

        // if found -> do nothing...
        if (!empty($tmp))
            return false;

        $table = 'resource_labels';
        $columns = $tables[$table]['columns'];

        $query = new DB_Insert($table);
        $query->addInsertValue($prefix,         $columns['res_prefix']);
        $query->addInsertValue($key,            $columns['res_label']);
        $query->addInsertValue($value,          $columns['res_text']);
        $application->db->getDB_Result($query);

        return true;
    }

    function addMetaToDB($params)
    {
        global $application;
        $tables = $this->getTables();

        $table = 'resource_meta';
        $columns = $tables[$table]['columns'];

        $query = new DB_Insert($table);
        $query->addInsertValue($params['shortname'], $columns['shortname']);
        $query->addInsertValue($params['filename'],  $columns['filename']);
        $query->addInsertValue($params['module'],    $columns['module']);
        $query->addInsertValue($params['flag'],      $columns['flag']);
        $query->addInsertValue($params['md5'],       $columns['md5']);

        $application->db->getDB_Result($query);
        return $application->db->DB_Insert_Id();
    }

    function updateLabelText($id, $text, $label = '')
    {
        // Note: this method does not use multilang data
        //       since it is used by label editor
        //       which may manage several languages.
        //       If you need multilangual analog of the function
        //       please create a stand alone method and query
        execQuery('RESOURCES_UPDATE_LABEL_BY_KEY', array('id' => $id,
                                                         'text' => $text,
                                                         'label' => $label));
    }

    function getMsg($prefix, $key)
    {
        $rlt = execQuery('RESOURCES_GET_MESSAGE_BY_KEY', array('prefix' => $prefix, 'label' => $key, 'res_lng' => $this -> _res_language));

        if (is_array($rlt) && isset($rlt[0]) && isset($rlt[0]['res_text']))
            return $rlt[0]['res_text'];

        else
        {
            return false;
        }
    }

    /**
     * Returns label ids for pairs of label and key
     * for arrays they must have the same number of elements
     */
    function getMsgIDs($prefix, $key)
    {
        if (!is_array($prefix))
            $prefix = array($prefix);
        if (!is_array($key))
            $key = array($key);

        $rlt = execQuery('RESOURCES_GET_MESSAGE_IDS_BY_KEY', array('prefix' => $prefix, 'label' => $key));

        if (!$rlt)
            return array();

        $result = array();
        foreach($rlt as $v)
            $result[$v['prefix'] . '_' . $v['label']] = array('id' => $v['id'], 'text' => $v['text']);

        return $result;
    }

    function getMessageGroupByPrefix($shortname)
    {
        $rlt = execQuery(
             "RESOURCES_GET_MESSAGE_GROUP_BY_PREFIX"
            ,array('prefix' => $shortname, 'res_lng' => $this -> _res_language)
            ,CCACHE_DO_NOT_USE_MEMORY_CACHE
        );

        $ret = array();
        foreach ($rlt as $cell)
        {
            $ret[$cell['res_label']] = $cell['res_text'];
        }
        return $ret;
    }

    function dropMessageMetaByMetaId($meta_id)
    {
        global $application;
        $tables = $this->getTables();
        $tr = $tables['resource_meta']['columns'];

        $query = new DB_Delete('resource_meta');
        $query->WhereValue($tr['shortname'], DB_EQ, $meta_id);
        $application->db->getDB_Result($query);
    }

    function dropMessageGroupByMetaId($prefix)
    {
        global $application;
        $tables = $this->getTables();
        $tr = $tables['resource_labels']['columns'];

        $query = new DB_Delete('resource_labels');
        $query->WhereValue($tr['res_prefix'], DB_EQ, $prefix);
        $application->db->getDB_Result($query);
    }

    function updateMd5ByMetaId($meta_id, $current_md5)
    {
        global $application;
        $tables = $this->getTables();
        $tr = $tables['resource_meta']['columns'];

        $query = new DB_Update('resource_meta');
        $query->addUpdateValue($tr['md5'], $current_md5);
        $query->WhereValue($tr['id'], DB_EQ, $meta_id);
        $application->db->getDB_Result($query);
    }

    function deleteSYSLabels($res_prefix)
	{

		execQuery('DELETE_SYS_RES_LABELS',array('res_prefix' => $res_prefix));
	}

	//23sep
	function getPaymentModeName($res_prefix,$res_label)
	{
		$result = execQuery('RESOURCES_GET_MESSAGE_BY_KEY',array('prefix' => $res_prefix,'label' => $res_label));
		return $result;
	}

	function getShortnameByPaymentModuleName($res_module)
	{
		$result = execQuery('RESOURCES_GET_META_BY_RES_MODULE',array('module' => $res_module));
		return $result;
	}

    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    # resource language
    var $_res_language;

    /**#@-*/

}
?>